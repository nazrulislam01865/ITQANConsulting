(function (root, factory) {
  const GraphWalker = factory();
  if (typeof module === 'object' && module.exports) module.exports = GraphWalker;
  if (root) root.ResortGraphWalker = GraphWalker;
})(typeof window !== 'undefined' ? window : globalThis, function () {
  'use strict';

  class ResortGraphWalker {
    constructor(options = {}) {
      this.metersPerPixel = Math.max(0.0001, Number(options.metersPerPixel) || 1);
      this.nodes = options.nodes || {};
      this.edges = (options.edges || []).map(edge => this.prepareEdge(edge));
      this.edgeById = new Map(this.edges.map(edge => [String(edge.id), edge]));
      this.adj = {};
      Object.keys(this.nodes).forEach(code => { this.adj[code] = []; });
      this.edges.forEach(edge => {
        if (!this.adj[edge.from]) this.adj[edge.from] = [];
        if (!this.adj[edge.to]) this.adj[edge.to] = [];
        this.adj[edge.from].push(this.directedCandidate(edge, edge.from, edge.to, 1));
        this.adj[edge.to].push(this.directedCandidate(edge, edge.to, edge.from, -1));
      });

      this.routeBiasDegrees = Number(options.routeBiasDegrees ?? 10);
      this.reversePenaltyDegrees = Number(options.reversePenaltyDegrees ?? 42);
      this.reverseAllowanceDegrees = Number(options.reverseAllowanceDegrees ?? 42);
      this.reverseSwitchMarginDegrees = Number(options.reverseSwitchMarginDegrees ?? 30);
      this.branchLockMeters = Number(options.branchLockMeters ?? 3.2);
      this.nodeSnapMeters = Number(options.nodeSnapMeters ?? 1.8);
      this.maxHopsPerStep = Number(options.maxHopsPerStep ?? 12);
      this.position = null;
      this.previousEdgeId = null;
      this.previousNodeCode = null;
      this.committedMeters = Infinity;
    }

    static normalizeDegrees(value) {
      return ((Number(value) % 360) + 360) % 360;
    }

    static signedAngle(value) {
      return ((Number(value) + 540) % 360) - 180;
    }

    static angleDifference(a, b) {
      return Math.abs(ResortGraphWalker.signedAngle(Number(a) - Number(b)));
    }

    static distance(a, b) {
      return Math.hypot(Number(b.x) - Number(a.x), Number(b.y) - Number(a.y));
    }

    static bearing(a, b) {
      const dx = Number(b.x) - Number(a.x);
      const dy = Number(b.y) - Number(a.y);
      if (Math.hypot(dx, dy) < 1e-9) return null;
      return ResortGraphWalker.normalizeDegrees(Math.atan2(dx, -dy) * 180 / Math.PI);
    }

    static projectPointToSegment(point, a, b) {
      const vx = Number(b.x) - Number(a.x);
      const vy = Number(b.y) - Number(a.y);
      const lengthSquared = vx * vx + vy * vy;
      if (lengthSquared <= 1e-12) {
        const p = {x: Number(a.x), y: Number(a.y)};
        return {point: p, t: 0, distancePx: ResortGraphWalker.distance(point, p)};
      }
      const t = Math.max(0, Math.min(1, ((Number(point.x) - Number(a.x)) * vx + (Number(point.y) - Number(a.y)) * vy) / lengthSquared));
      const projected = {x: Number(a.x) + vx * t, y: Number(a.y) + vy * t};
      return {point: projected, t, distancePx: ResortGraphWalker.distance(point, projected)};
    }

    prepareEdge(raw) {
      const path = [];
      for (const item of raw.path || raw.path_points || []) {
        const point = {x: Number(item.x ?? item[0]), y: Number(item.y ?? item[1])};
        if (!Number.isFinite(point.x) || !Number.isFinite(point.y)) continue;
        const previous = path[path.length - 1];
        if (!previous || ResortGraphWalker.distance(previous, point) > 1e-7) path.push(point);
      }
      const fromNode = this.nodes[raw.from];
      const toNode = this.nodes[raw.to];
      if (path.length && fromNode && toNode) {
        const normalError = ResortGraphWalker.distance(path[0], fromNode) + ResortGraphWalker.distance(path[path.length - 1], toNode);
        const reverseError = ResortGraphWalker.distance(path[0], toNode) + ResortGraphWalker.distance(path[path.length - 1], fromNode);
        if (reverseError + 1e-7 < normalError) path.reverse();
        if (ResortGraphWalker.distance(path[0], fromNode) > 1e-7) path.unshift({x: Number(fromNode.x), y: Number(fromNode.y)});
        if (ResortGraphWalker.distance(path[path.length - 1], toNode) > 1e-7) path.push({x: Number(toNode.x), y: Number(toNode.y)});
      }
      const cumulative = [0];
      for (let i = 1; i < path.length; i++) cumulative.push(cumulative[i - 1] + ResortGraphWalker.distance(path[i - 1], path[i]));
      return {
        ...raw,
        id: raw.id,
        from: raw.from,
        to: raw.to,
        path,
        cumulative,
        lengthPx: cumulative[cumulative.length - 1] || 0,
      };
    }

    directedCandidate(edge, fromNode, toNode, direction) {
      return {
        edgeId: String(edge.id),
        edge,
        fromNode,
        toNode,
        direction,
        bearing: this.bearingAwayFromNode(edge, fromNode, direction),
      };
    }

    bearingAwayFromNode(edge, nodeCode, direction) {
      if (!edge.path.length) return null;
      if (direction > 0) {
        for (let i = 1; i < edge.path.length; i++) {
          const bearing = ResortGraphWalker.bearing(edge.path[0], edge.path[i]);
          if (Number.isFinite(bearing)) return bearing;
        }
      } else {
        const last = edge.path.length - 1;
        for (let i = last - 1; i >= 0; i--) {
          const bearing = ResortGraphWalker.bearing(edge.path[last], edge.path[i]);
          if (Number.isFinite(bearing)) return bearing;
        }
      }
      const node = this.nodes[nodeCode];
      const other = this.nodes[direction > 0 ? edge.to : edge.from];
      return node && other ? ResortGraphWalker.bearing(node, other) : null;
    }

    setPosition(start, heading = null, preferredNodeCode = null) {
      const point = {x: Number(start && start.x), y: Number(start && start.y)};
      if (!Number.isFinite(point.x) || !Number.isFinite(point.y)) throw new Error('A valid map start point is required.');

      const snapPx = this.nodeSnapMeters / this.metersPerPixel;
      let nodeCode = preferredNodeCode && this.nodes[preferredNodeCode] ? preferredNodeCode : null;
      if (!nodeCode) {
        let nearestNodeDistance = Infinity;
        Object.entries(this.nodes).forEach(([code, node]) => {
          const d = ResortGraphWalker.distance(point, node);
          if (d < nearestNodeDistance) {
            nearestNodeDistance = d;
            nodeCode = code;
          }
        });
        if (nearestNodeDistance > snapPx) nodeCode = null;
      }

      if (nodeCode) {
        this.position = {kind: 'node', nodeCode, point: this.nodePoint(nodeCode), bearing: null};
        this.previousEdgeId = null;
        this.previousNodeCode = null;
        this.committedMeters = Infinity;
        return this.snapshot();
      }

      const projection = this.nearestProjection(point);
      if (!projection) throw new Error('The start point is not on the route graph.');
      const edge = projection.edge;
      const forwardBearing = this.tangentBearing(edge, projection.offsetPx, 1);
      const backwardBearing = this.tangentBearing(edge, projection.offsetPx, -1);
      let direction = 1;
      if (Number.isFinite(heading) && Number.isFinite(backwardBearing) && Number.isFinite(forwardBearing)) {
        direction = ResortGraphWalker.angleDifference(heading, backwardBearing) < ResortGraphWalker.angleDifference(heading, forwardBearing) ? -1 : 1;
      }
      this.position = {
        kind: 'edge', edgeId: String(edge.id), offsetPx: projection.offsetPx,
        direction, point: projection.point, bearing: direction > 0 ? forwardBearing : backwardBearing,
      };
      this.previousEdgeId = null;
      this.previousNodeCode = null;
      this.committedMeters = Infinity;
      return this.snapshot();
    }

    nodePoint(code) {
      const node = this.nodes[code];
      return {x: Number(node.x), y: Number(node.y)};
    }

    nearestProjection(point) {
      let best = null;
      for (const edge of this.edges) {
        for (let i = 1; i < edge.path.length; i++) {
          const projection = ResortGraphWalker.projectPointToSegment(point, edge.path[i - 1], edge.path[i]);
          if (!best || projection.distancePx < best.distancePx) {
            const segmentLength = ResortGraphWalker.distance(edge.path[i - 1], edge.path[i]);
            best = {
              edge,
              segmentIndex: i - 1,
              t: projection.t,
              point: projection.point,
              distancePx: projection.distancePx,
              offsetPx: edge.cumulative[i - 1] + segmentLength * projection.t,
            };
          }
        }
      }
      return best;
    }

    step(distanceMeters, heading = null, routePreferredEdgeId = null) {
      if (!this.position) throw new Error('Call setPosition before step.');
      let remainingPx = Math.max(0, Number(distanceMeters) || 0) / this.metersPerPixel;
      let movedPx = 0;
      let hops = 0;
      let branch = null;
      const safeHeading = Number.isFinite(Number(heading)) ? ResortGraphWalker.normalizeDegrees(heading) : null;

      while (remainingPx > 1e-8 && hops++ < this.maxHopsPerStep) {
        if (this.position.kind === 'node') {
          const candidate = this.chooseBranch(this.position.nodeCode, safeHeading, routePreferredEdgeId);
          if (!candidate) break;
          branch = candidate;
          this.position = {
            kind: 'edge', edgeId: candidate.edgeId,
            offsetPx: candidate.direction > 0 ? 0 : candidate.edge.lengthPx,
            direction: candidate.direction,
            point: this.nodePoint(candidate.fromNode),
            bearing: candidate.bearing,
          };
          this.committedMeters = 0;
        }

        const edge = this.edgeById.get(String(this.position.edgeId));
        if (!edge) break;
        this.maybeReverseOnEdge(edge, safeHeading);
        const direction = this.position.direction;
        const availablePx = direction > 0 ? edge.lengthPx - this.position.offsetPx : this.position.offsetPx;
        const consumePx = Math.min(remainingPx, Math.max(0, availablePx));
        this.position.offsetPx += direction * consumePx;
        movedPx += consumePx;
        remainingPx -= consumePx;
        this.committedMeters += consumePx * this.metersPerPixel;
        const projected = this.pointAtOffset(edge, this.position.offsetPx);
        this.position.point = projected.point;
        this.position.bearing = this.tangentBearing(edge, this.position.offsetPx, direction);

        if (availablePx - consumePx <= 1e-7) {
          const reachedNode = direction > 0 ? edge.to : edge.from;
          const cameFromNode = direction > 0 ? edge.from : edge.to;
          this.previousEdgeId = String(edge.id);
          this.previousNodeCode = cameFromNode;
          this.position = {kind: 'node', nodeCode: reachedNode, point: this.nodePoint(reachedNode), bearing: this.position.bearing};
          this.committedMeters = Infinity;
        }
      }

      const snapshot = this.snapshot();
      return {
        ...snapshot,
        traveledMeters: movedPx * this.metersPerPixel,
        requestedMeters: Math.max(0, Number(distanceMeters) || 0),
        heading: safeHeading,
        selectedBranch: branch ? {
          edgeId: branch.edgeId,
          fromNode: branch.fromNode,
          toNode: branch.toNode,
          bearing: branch.bearing,
          angleError: branch.angleError,
        } : null,
      };
    }

    maybeReverseOnEdge(edge, heading) {
      if (!Number.isFinite(heading)) return;
      const direction = this.position.direction;
      const forwardBearing = this.tangentBearing(edge, this.position.offsetPx, direction);
      const reverseBearing = this.tangentBearing(edge, this.position.offsetPx, -direction);
      if (!Number.isFinite(forwardBearing) || !Number.isFinite(reverseBearing)) return;
      const forwardError = ResortGraphWalker.angleDifference(heading, forwardBearing);
      const reverseError = ResortGraphWalker.angleDifference(heading, reverseBearing);
      const locked = this.committedMeters < this.branchLockMeters;
      const strongUTurn = reverseError <= this.reverseAllowanceDegrees && forwardError >= 120;
      if ((!locked && reverseError + this.reverseSwitchMarginDegrees < forwardError) || strongUTurn) {
        this.position.direction *= -1;
        this.position.bearing = reverseBearing;
        this.committedMeters = Infinity;
      }
    }

    chooseBranch(nodeCode, heading, routePreferredEdgeId = null) {
      const candidates = (this.adj[nodeCode] || []).map(candidate => ({...candidate}));
      if (!candidates.length) return null;
      const hasHeading = Number.isFinite(heading);
      let best = null;
      for (const candidate of candidates) {
        const angleError = hasHeading && Number.isFinite(candidate.bearing)
          ? ResortGraphWalker.angleDifference(heading, candidate.bearing)
          : 90;
        let score = angleError;
        const isReverse = this.previousEdgeId && String(candidate.edgeId) === String(this.previousEdgeId) && candidate.toNode === this.previousNodeCode;
        if (isReverse && !(hasHeading && angleError <= this.reverseAllowanceDegrees)) score += this.reversePenaltyDegrees;
        if (routePreferredEdgeId !== null && String(candidate.edgeId) === String(routePreferredEdgeId)) score -= this.routeBiasDegrees;
        candidate.angleError = angleError;
        candidate.score = score;
        candidate.isReverse = !!isReverse;
        if (!best || candidate.score < best.score - 1e-9 || (Math.abs(candidate.score - best.score) < 1e-9 && String(candidate.edgeId) < String(best.edgeId))) best = candidate;
      }
      return best;
    }

    pointAtOffset(edge, offsetPx) {
      const clamped = Math.max(0, Math.min(edge.lengthPx, Number(offsetPx) || 0));
      if (edge.path.length === 1) return {point: {...edge.path[0]}, segmentIndex: 0, t: 0};
      for (let i = 1; i < edge.cumulative.length; i++) {
        if (clamped <= edge.cumulative[i] + 1e-9) {
          const segmentStart = edge.cumulative[i - 1];
          const segmentLength = edge.cumulative[i] - segmentStart;
          const t = segmentLength > 0 ? (clamped - segmentStart) / segmentLength : 0;
          const a = edge.path[i - 1];
          const b = edge.path[i];
          return {
            point: {x: a.x + (b.x - a.x) * t, y: a.y + (b.y - a.y) * t},
            segmentIndex: i - 1,
            t: Math.max(0, Math.min(1, t)),
          };
        }
      }
      return {point: {...edge.path[edge.path.length - 1]}, segmentIndex: edge.path.length - 2, t: 1};
    }

    tangentBearing(edge, offsetPx, direction) {
      const current = this.pointAtOffset(edge, offsetPx);
      const lookPx = Math.max(0.5, 2 / this.metersPerPixel);
      const target = this.pointAtOffset(edge, offsetPx + direction * lookPx);
      let bearing = ResortGraphWalker.bearing(current.point, target.point);
      if (!Number.isFinite(bearing)) {
        const fallback = this.pointAtOffset(edge, offsetPx - direction * lookPx);
        bearing = ResortGraphWalker.bearing(fallback.point, current.point);
      }
      return bearing;
    }

    snapshot() {
      if (!this.position) return null;
      if (this.position.kind === 'node') {
        return {
          kind: 'node', nodeCode: this.position.nodeCode,
          point: {...this.position.point}, bearing: this.position.bearing,
          projection: null,
        };
      }
      const edge = this.edgeById.get(String(this.position.edgeId));
      const at = this.pointAtOffset(edge, this.position.offsetPx);
      return {
        kind: 'edge', edgeId: String(edge.id), offsetPx: this.position.offsetPx,
        direction: this.position.direction, point: {...at.point}, bearing: this.position.bearing,
        projection: {
          edge,
          segmentIndex: at.segmentIndex,
          t: at.t,
          point: {...at.point},
          distancePx: 0,
          bearing: this.position.bearing,
          score: 0,
        },
      };
    }
  }

  return ResortGraphWalker;
});
