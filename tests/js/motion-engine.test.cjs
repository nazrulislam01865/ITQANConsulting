'use strict';

const assert = require('node:assert/strict');

const listeners = new Map();
const screenListeners = new Map();
global.window = {
  isSecureContext: true,
  addEventListener(name, callback) { listeners.set(name, callback); },
  removeEventListener(name) { listeners.delete(name); },
};
global.screen = {
  orientation: {
    angle: 0,
    addEventListener(name, callback) { screenListeners.set(name, callback); },
    removeEventListener(name) { screenListeners.delete(name); },
  },
};
global.DeviceOrientationEvent = {requestPermission: async () => 'granted'};
global.DeviceMotionEvent = {requestPermission: async () => 'granted'};

const fs = require('node:fs');
const vm = require('node:vm');
const source = fs.readFileSync(require.resolve('../../public/assets/itqan-external-guest-map/motion-engine.js'), 'utf8');
vm.runInThisContext(source, {filename: 'motion-engine.js'});
const MotionEngine = global.window.ResortMotionEngine;

(async () => {
  const headings = [];
  const steps = [];
  const statuses = [];
  const engine = new MotionEngine({
    onHeading: value => headings.push(value),
    onStep: value => steps.push(value),
    onStatus: value => statuses.push(value),
  });

  const permissions = await engine.start(90);
  assert.equal(permissions.orientationPermission, 'granted');
  assert.equal(permissions.motionPermission, 'granted');
  assert.equal(engine.orientationActive, true);
  assert.equal(engine.motionActive, true);
  assert.equal(listeners.has('deviceorientationabsolute'), true);
  assert.equal(listeners.has('deviceorientation'), true);
  assert.equal(listeners.has('devicemotion'), true);
  assert.equal(listeners.has('orientationchange'), true);
  assert.equal(screenListeners.has('change'), true);

  engine.processOrientation({alpha: 350, absolute: false}); // raw heading 10°; baseline -> map 90°
  assert.equal(Math.round(headings.at(-1).heading), 90);
  engine.lastOrientationAt = 0;
  engine.processOrientation({alpha: 320, absolute: false}); // raw heading 40°; +30° turn
  assert.equal(Math.round(headings.at(-1).heading), 120);

  engine.recalibrate(200);
  assert.equal(Math.round(headings.at(-1).heading), 200);
  engine.lastOrientationAt = 0;
  engine.processOrientation({alpha: 300, absolute: false}); // raw 60°, +20° from recalibration
  assert.equal(Math.round(headings.at(-1).heading), 220);

  // Rotating the screen must preserve map heading and wait for a new sensor baseline.
  engine.handleScreenOrientationChange();
  assert.equal(Math.round(engine.mapHeadingBaseline), 220);
  assert.equal(engine.headingBaseline, null);

  const originalNow = Date.now;
  let now = 1000;
  Date.now = () => now;
  try {
    engine.processMotion({acceleration: {x: 0, y: 0, z: 0}, interval: 16});
    now = 1300;
    engine.processMotion({acceleration: {x: 4, y: 0, z: 0}, interval: 16});
    assert.equal(steps.length, 1);
    assert.equal(steps[0].count, 1);
    assert.ok(steps[0].stepLengthMeters >= 0.72 && steps[0].stepLengthMeters <= 0.90);

    for (const t of [1400, 1450, 1500, 1550]) {
      now = t;
      engine.processMotion({acceleration: {x: 0, y: 0, z: 0}, interval: 16});
    }
    now = 1700;
    engine.processMotion({acceleration: {x: 4, y: 0, z: 0}, interval: 16});
    assert.equal(steps.length, 2);
    assert.equal(steps[1].count, 2);

    // A violent shake/drop is rejected as a walking step.
    for (const t of [1800, 1850, 1900, 1950]) {
      now = t;
      engine.processMotion({acceleration: {x: 0, y: 0, z: 0}, interval: 16});
    }
    now = 2100;
    engine.processMotion({acceleration: {x: 30, y: 0, z: 0}, interval: 16});
    assert.equal(steps.length, 2);
  } finally {
    Date.now = originalNow;
  }

  engine.stop();
  assert.equal(engine.active, false);
  assert.equal(listeners.has('deviceorientation'), false);
  assert.equal(listeners.has('devicemotion'), false);
  assert.equal(listeners.has('orientationchange'), false);
  assert.equal(screenListeners.has('change'), false);
  assert.ok(statuses.length >= 1);

  // Chrome correctly refuses sensor startup from an insecure mobile page.
  window.isSecureContext = false;
  const insecureEngine = new MotionEngine();
  const insecure = await insecureEngine.start(0);
  assert.equal(insecure.orientationPermission, 'insecure');
  assert.equal(insecure.motionPermission, 'insecure');
  assert.equal(insecureEngine.orientationActive, false);
  assert.equal(insecureEngine.motionActive, false);
  insecureEngine.stop();
  window.isSecureContext = true;

  console.log('motion-engine tests passed');
})().catch(error => {
  console.error(error);
  process.exit(1);
});
