(function (root, factory) {
  const RouteFollower = factory();
  if (typeof module === 'object' && module.exports) module.exports = RouteFollower;
  if (root) root.ResortRouteFollower = RouteFollower;
})(typeof window !== 'undefined' ? window : globalThis, function () {
  'use strict';

  class ResortRouteFollower {
    static normalizeDegrees(value) {
      return ((Number(value) % 360) + 360) % 360;
    }

    static distance(a, b) {
      return Math.hypot(Number(b.x) - Number(a.x), Number(b.y) - Number(a.y));
    }

    static bearing(a, b) {
      const dx = Number(b.x) - Number(a.x);
      const dy = Number(b.y) - Number(a.y);
      if (Math.hypot(dx, dy) < 1e-9) return null;
      return ResortRouteFollower.normalizeDegrees(Math.atan2(dx, -dy) * 180 / Math.PI);
    }

    static cleanPath(path) {
      const clean = [];
      for (const item of path || []) {
        const point = {x: Number(item && item.x), y: Number(item && item.y)};
        if (!Number.isFinite(point.x) || !Number.isFinite(point.y)) continue;
        const previous = clean[clean.length - 1];
        if (!previous || ResortRouteFollower.distance(previous, point) > 1e-7) clean.push(point);
      }
      return clean;
    }

    static lengthPixels(path) {
      const points = ResortRouteFollower.cleanPath(path);
      let total = 0;
      for (let i = 1; i < points.length; i++) total += ResortRouteFollower.distance(points[i - 1], points[i]);
      return total;
    }

    /**
     * Advance from the first point of a remaining-route polyline.
     * The returned remainingPath always starts at the new marker position,
     * which makes repeated calls monotonic and prevents heading-based drift.
     */
    static advance(path, distanceMeters, metersPerPixel) {
      const points = ResortRouteFollower.cleanPath(path);
      const scale = Number(metersPerPixel);
      const requestedMeters = Math.max(0, Number(distanceMeters) || 0);
      if (points.length === 0 || !Number.isFinite(scale) || scale <= 0) return null;
      if (points.length === 1 || requestedMeters === 0) {
        return {
          point: {...points[0]},
          bearing: points.length > 1 ? ResortRouteFollower.bearing(points[0], points[1]) : null,
          remainingPath: points,
          traveledMeters: 0,
          remainingMeters: ResortRouteFollower.lengthPixels(points) * scale,
          complete: points.length === 1,
        };
      }

      let remainingPixelsToWalk = requestedMeters / scale;
      let traveledPixels = 0;

      for (let i = 1; i < points.length; i++) {
        const a = points[i - 1];
        const b = points[i];
        const segmentPixels = ResortRouteFollower.distance(a, b);
        if (segmentPixels <= 1e-9) continue;

        if (remainingPixelsToWalk < segmentPixels) {
          const ratio = remainingPixelsToWalk / segmentPixels;
          const point = {
            x: a.x + (b.x - a.x) * ratio,
            y: a.y + (b.y - a.y) * ratio,
          };
          traveledPixels += remainingPixelsToWalk;
          const remainingPath = ResortRouteFollower.cleanPath([point, b, ...points.slice(i + 1)]);
          return {
            point,
            bearing: ResortRouteFollower.bearing(a, b),
            remainingPath,
            traveledMeters: traveledPixels * scale,
            remainingMeters: ResortRouteFollower.lengthPixels(remainingPath) * scale,
            complete: false,
          };
        }

        remainingPixelsToWalk -= segmentPixels;
        traveledPixels += segmentPixels;
      }

      const point = {...points[points.length - 1]};
      return {
        point,
        bearing: points.length > 1 ? ResortRouteFollower.bearing(points[points.length - 2], point) : null,
        remainingPath: [point],
        traveledMeters: traveledPixels * scale,
        remainingMeters: 0,
        complete: true,
      };
    }
  }

  return ResortRouteFollower;
});
