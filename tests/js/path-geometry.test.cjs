'use strict';

const assert = require('assert');
const fs = require('fs');
const path = require('path');
const vm = require('vm');

const source = fs.readFileSync(path.join(__dirname, '../../public/assets/itqan-external-guest-map/path-geometry.js'), 'utf8');
const context = {};
context.globalThis = context;
vm.createContext(context);
vm.runInContext(source, context);
const geometry = context.ResortPathGeometry;

assert(geometry && typeof geometry.smoothPolyline === 'function', 'Geometry module should load in browsers and tests.');

const straight = [{x: 10, y: 20}, {x: 110, y: 20}];
const bent = geometry.subtleCurve(straight, 1);
assert.strictEqual(bent.length, 4, 'Two-point paths should receive curve controls.');
assert.deepStrictEqual({...bent[0]}, straight[0]);
assert.deepStrictEqual({...bent[bent.length - 1]}, straight[1]);
assert.notStrictEqual(bent[1].y, 20, 'The generated path should have a gentle bend.');

const controls = [{x: 0, y: 0}, {x: 50, y: 30}, {x: 100, y: 0}, {x: 150, y: 40}];
const smooth = geometry.smoothPolyline(controls, {subtleBend: false});
assert(smooth.length > controls.length, 'Curved rendering should sample extra points.');
assert.deepStrictEqual({...smooth[0]}, controls[0], 'The first exact vertex must be preserved.');
assert.deepStrictEqual({...smooth[smooth.length - 1]}, controls[controls.length - 1], 'The last exact vertex must be preserved.');
for (const control of controls) {
  assert(smooth.some((point) => Math.hypot(point.x - control.x, point.y - control.y) < 1e-8), 'Every control vertex must remain on the rendered path.');
}

const clean = geometry.sanitizePoints([{x: 1, y: 2}, {x: 1, y: 2}, {x: '3', y: '4'}, {x: 'bad', y: 2}]);
assert.strictEqual(JSON.stringify(clean), JSON.stringify([{x: 1, y: 2}, {x: 3, y: 4}]), 'Duplicate and invalid points should be removed.');
assert(geometry.svgPath(controls).startsWith('M 0.00 0.00'));

console.log('Path geometry tests passed.');
