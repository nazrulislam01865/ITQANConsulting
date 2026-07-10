(function (root, factory) {
  const api = factory();
  if (typeof module === 'object' && module.exports) module.exports = api;
  root.ResortPathGeometry = api;
})(typeof globalThis !== 'undefined' ? globalThis : this, function () {
  'use strict';

  function finite(value) {
    const number = Number(value);
    return Number.isFinite(number) ? number : null;
  }

  function distance(a, b) {
    return Math.hypot(Number(b.x) - Number(a.x), Number(b.y) - Number(a.y));
  }

  function sanitizePoints(input) {
    const clean = [];
    (Array.isArray(input) ? input : []).forEach((point) => {
      const x = finite(point && point.x);
      const y = finite(point && point.y);
      if (x === null || y === null) return;
      const candidate = {x, y};
      if (!clean.length || distance(clean[clean.length - 1], candidate) > 0.01) clean.push(candidate);
    });
    return clean;
  }

  function subtleCurve(points, seed) {
    const clean = sanitizePoints(points);
    if (clean.length !== 2) return clean;
    const start = clean[0];
    const end = clean[1];
    const dx = end.x - start.x;
    const dy = end.y - start.y;
    const length = Math.max(1, Math.hypot(dx, dy));
    const sign = Number(seed || 0) % 2 === 0 ? 1 : -1;
    const offset = Math.min(12, Math.max(2, length * 0.045)) * sign;
    const nx = -dy / length;
    const ny = dx / length;
    return [
      start,
      {x: start.x + dx / 3 + nx * offset, y: start.y + dy / 3 + ny * offset},
      {x: start.x + (2 * dx) / 3 + nx * offset, y: start.y + (2 * dy) / 3 + ny * offset},
      end,
    ];
  }

  function capVector(x, y, maxLength) {
    const length = Math.hypot(x, y);
    if (!length || length <= maxLength) return {x, y};
    const scale = maxLength / length;
    return {x: x * scale, y: y * scale};
  }

  function cubic(p0, p1, p2, p3, t) {
    const mt = 1 - t;
    const mt2 = mt * mt;
    const t2 = t * t;
    return {
      x: mt2 * mt * p0.x + 3 * mt2 * t * p1.x + 3 * mt * t2 * p2.x + t2 * t * p3.x,
      y: mt2 * mt * p0.y + 3 * mt2 * t * p1.y + 3 * mt * t2 * p2.y + t2 * t * p3.y,
    };
  }

  function smoothPolyline(input, options) {
    const opts = Object.assign({tension: 0.68, sampleEvery: 9, maxHandleRatio: 0.28, seed: 0, subtleBend: true}, options || {});
    let points = sanitizePoints(input);
    if (opts.subtleBend && points.length === 2) points = subtleCurve(points, opts.seed);
    if (points.length < 3) return points;

    const output = [{...points[0]}];
    for (let index = 0; index < points.length - 1; index += 1) {
      const p0 = points[Math.max(0, index - 1)];
      const p1 = points[index];
      const p2 = points[index + 1];
      const p3 = points[Math.min(points.length - 1, index + 2)];
      const segmentLength = Math.max(0.01, distance(p1, p2));
      const maxHandle = segmentLength * opts.maxHandleRatio;
      const firstTangent = capVector(
        ((p2.x - p0.x) * opts.tension) / 6,
        ((p2.y - p0.y) * opts.tension) / 6,
        maxHandle,
      );
      const secondTangent = capVector(
        ((p3.x - p1.x) * opts.tension) / 6,
        ((p3.y - p1.y) * opts.tension) / 6,
        maxHandle,
      );
      const c1 = {x: p1.x + firstTangent.x, y: p1.y + firstTangent.y};
      const c2 = {x: p2.x - secondTangent.x, y: p2.y - secondTangent.y};
      const steps = Math.max(4, Math.min(24, Math.ceil(segmentLength / Math.max(3, opts.sampleEvery))));
      for (let step = 1; step <= steps; step += 1) {
        output.push(cubic(p1, c1, c2, p2, step / steps));
      }
    }

    output[0] = {...points[0]};
    output[output.length - 1] = {...points[points.length - 1]};
    return output;
  }

  function svgPath(input, options) {
    const points = smoothPolyline(input, options);
    if (!points.length) return '';
    return points.map((point, index) => `${index ? 'L' : 'M'} ${point.x.toFixed(2)} ${point.y.toFixed(2)}`).join(' ');
  }

  function polylineLength(input) {
    const points = sanitizePoints(input);
    let length = 0;
    for (let index = 1; index < points.length; index += 1) length += distance(points[index - 1], points[index]);
    return length;
  }

  return {sanitizePoints, subtleCurve, smoothPolyline, svgPath, polylineLength, distance};
});
