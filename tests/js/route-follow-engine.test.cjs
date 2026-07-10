'use strict';

const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

global.window = {};
const source = fs.readFileSync(require.resolve('../../public/assets/itqan-external-guest-map/route-follow-engine.js'), 'utf8');
vm.runInThisContext(source, {filename: 'route-follow-engine.js'});
const RouteFollower = global.window.ResortRouteFollower;

const metersPerPixel = 1.45;
const route = [
  {x: 652, y: 294},
  {x: 538, y: 334},
  {x: 604, y: 505},
];

const total = RouteFollower.lengthPixels(route) * metersPerPixel;
assert.ok(Math.abs(total - 440.95768884704006) < 0.001);

let remainingPath = route;
let remainingMeters = total;
let moved = 0;

// Phone heading is intentionally absent: route progress must not depend on it.
for (let i = 0; i < 100; i++) {
  const result = RouteFollower.advance(remainingPath, 0.72, metersPerPixel);
  assert.ok(result.traveledMeters > 0);
  assert.ok(result.remainingMeters < remainingMeters);
  remainingPath = result.remainingPath;
  remainingMeters = result.remainingMeters;
  moved += result.traveledMeters;
}
assert.ok(Math.abs(moved - 72) < 0.001);
assert.ok(Math.abs(remainingMeters - (total - 72)) < 0.001);
assert.ok(remainingPath[0].x < route[0].x); // progressed toward n_lawn

// Advance across the corner; the first point must continue on the second leg.
const toCornerMeters = RouteFollower.distance(remainingPath[0], route[1]) * metersPerPixel;
let result = RouteFollower.advance(remainingPath, toCornerMeters + 12, metersPerPixel);
assert.ok(result.point.y > route[1].y);
assert.ok(result.point.x > route[1].x);
assert.ok(Math.abs(result.remainingMeters - (remainingMeters - toCornerMeters - 12)) < 0.001);

// Overshooting ends exactly at the destination and never reverses.
result = RouteFollower.advance(result.remainingPath, 10000, metersPerPixel);
assert.equal(result.complete, true);
assert.deepEqual(result.point, route.at(-1));
assert.equal(result.remainingMeters, 0);
assert.equal(result.remainingPath.length, 1);

console.log('route-follow-engine tests passed');
