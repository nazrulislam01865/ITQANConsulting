(function (root, factory) {
  'use strict';
  const MotionEngine = factory();
  if (typeof module === 'object' && module.exports) module.exports = MotionEngine;
  if (root) root.ResortMotionEngine = MotionEngine;
})(typeof window !== 'undefined' ? window : globalThis, function () {
  'use strict';

  class ResortMotionEngine {
    constructor(options = {}) {
      this.options = {
        stepThreshold: Number(options.stepThreshold || 0.88),
        stepReleaseThreshold: Number(options.stepReleaseThreshold || 0.30),
        minStepIntervalMs: Number(options.minStepIntervalMs || 280),
        maxStepIntervalMs: Number(options.maxStepIntervalMs || 1800),
        defaultStepLengthMeters: Number(options.defaultStepLengthMeters || 0.72),
        maxAcceleration: Number(options.maxAcceleration || 15),
        orientationIntervalMs: Number(options.orientationIntervalMs || 28),
        motionCallbackIntervalMs: Number(options.motionCallbackIntervalMs || 160),
        onHeading: typeof options.onHeading === 'function' ? options.onHeading : function () {},
        onStep: typeof options.onStep === 'function' ? options.onStep : function () {},
        onMotion: typeof options.onMotion === 'function' ? options.onMotion : function () {},
        onStatus: typeof options.onStatus === 'function' ? options.onStatus : function () {},
      };

      this.active = false;
      this.orientationActive = false;
      this.motionActive = false;
      this.orientationPermission = 'unknown';
      this.motionPermission = 'unknown';
      this.rawHeading = null;
      this.headingBaseline = null;
      this.mapHeadingBaseline = 0;
      this.mapHeading = null;
      this.lastOrientationAt = 0;
      this.lastAbsoluteOrientationAt = 0;
      this.lastMotionAt = 0;
      this.lastMotionCallbackAt = 0;
      this.lastStepAt = 0;
      this.stepCount = 0;
      this.aboveThreshold = false;
      this.filteredAcceleration = 0;
      this.noiseFloor = 0.08;
      this.gravity = {x: 0, y: 0, z: 0, ready: false};
      this.boundOrientation = this.processOrientation.bind(this);
      this.boundMotion = this.processMotion.bind(this);
      this.boundScreenChange = this.handleScreenOrientationChange.bind(this);
    }

    static normalizeDegrees(value) {
      const n = Number(value);
      if (!Number.isFinite(n)) return null;
      return ((n % 360) + 360) % 360;
    }

    static signedAngleDelta(value, reference) {
      const normalizedValue = ResortMotionEngine.normalizeDegrees(value);
      const normalizedReference = ResortMotionEngine.normalizeDegrees(reference);
      if (!Number.isFinite(normalizedValue) || !Number.isFinite(normalizedReference)) return 0;
      let delta = normalizedValue - normalizedReference;
      if (delta > 180) delta -= 360;
      if (delta < -180) delta += 360;
      return delta;
    }

    static screenAngle() {
      if (typeof screen !== 'undefined' && screen.orientation && Number.isFinite(Number(screen.orientation.angle))) {
        return Number(screen.orientation.angle);
      }
      if (typeof window !== 'undefined' && Number.isFinite(Number(window.orientation))) return Number(window.orientation);
      return 0;
    }

    support() {
      return {
        secureContext: typeof window === 'undefined' || typeof window.isSecureContext === 'undefined' ? true : !!window.isSecureContext,
        orientation: typeof DeviceOrientationEvent !== 'undefined',
        motion: typeof DeviceMotionEvent !== 'undefined',
      };
    }

    resetRuntime(initialMapHeading) {
      this.mapHeadingBaseline = ResortMotionEngine.normalizeDegrees(initialMapHeading) || 0;
      this.mapHeading = this.mapHeadingBaseline;
      this.headingBaseline = null;
      this.rawHeading = null;
      this.lastOrientationAt = 0;
      this.lastAbsoluteOrientationAt = 0;
      this.lastMotionAt = 0;
      this.lastMotionCallbackAt = 0;
      this.stepCount = 0;
      this.lastStepAt = 0;
      this.filteredAcceleration = 0;
      this.noiseFloor = 0.08;
      this.gravity = {x: 0, y: 0, z: 0, ready: false};
      this.aboveThreshold = false;
    }

    async start(initialMapHeading = 0) {
      this.stop();
      this.active = true;
      this.resetRuntime(initialMapHeading);

      const support = this.support();
      let orientationPermission = support.orientation ? 'granted' : 'unsupported';
      let motionPermission = support.motion ? 'granted' : 'unsupported';

      if (!support.secureContext && typeof window !== 'undefined') {
        orientationPermission = support.orientation ? 'insecure' : 'unsupported';
        motionPermission = support.motion ? 'insecure' : 'unsupported';
      } else {
        // Start both requests from the same user gesture. Android Chrome normally
        // grants sensor event access directly; iOS exposes requestPermission().
        let orientationRequest = Promise.resolve(orientationPermission);
        let motionRequest = Promise.resolve(motionPermission);
        try {
          if (support.orientation && typeof DeviceOrientationEvent.requestPermission === 'function') {
            orientationRequest = DeviceOrientationEvent.requestPermission();
          }
        } catch (_) {
          orientationRequest = Promise.resolve('denied');
        }
        try {
          if (support.motion && typeof DeviceMotionEvent.requestPermission === 'function') {
            motionRequest = DeviceMotionEvent.requestPermission();
          }
        } catch (_) {
          motionRequest = Promise.resolve('denied');
        }

        try {
          [orientationPermission, motionPermission] = await Promise.all([orientationRequest, motionRequest]);
        } catch (_) {
          orientationPermission = orientationPermission === 'granted' ? 'granted' : 'denied';
          motionPermission = motionPermission === 'granted' ? 'granted' : 'denied';
        }
      }

      this.orientationPermission = orientationPermission;
      this.motionPermission = motionPermission;
      if (!this.active) return {orientationPermission, motionPermission};

      this.attachListeners();
      this.emitStatus();
      return {orientationPermission, motionPermission};
    }

    attachListeners() {
      if (typeof window === 'undefined') return;

      if (this.orientationPermission === 'granted' && !this.orientationActive) {
        window.addEventListener('deviceorientationabsolute', this.boundOrientation, true);
        window.addEventListener('deviceorientation', this.boundOrientation, true);
        window.addEventListener('orientationchange', this.boundScreenChange, true);
        if (typeof screen !== 'undefined' && screen.orientation && typeof screen.orientation.addEventListener === 'function') {
          screen.orientation.addEventListener('change', this.boundScreenChange);
        }
        this.orientationActive = true;
      }

      if (this.motionPermission === 'granted' && !this.motionActive) {
        window.addEventListener('devicemotion', this.boundMotion, true);
        this.motionActive = true;
      }
    }

    resume() {
      if (!this.active) return;
      this.attachListeners();
      this.emitStatus();
    }

    stop() {
      if (typeof window !== 'undefined') {
        window.removeEventListener('deviceorientationabsolute', this.boundOrientation, true);
        window.removeEventListener('deviceorientation', this.boundOrientation, true);
        window.removeEventListener('orientationchange', this.boundScreenChange, true);
        window.removeEventListener('devicemotion', this.boundMotion, true);
      }
      if (typeof screen !== 'undefined' && screen.orientation && typeof screen.orientation.removeEventListener === 'function') {
        screen.orientation.removeEventListener('change', this.boundScreenChange);
      }
      this.active = false;
      this.orientationActive = false;
      this.motionActive = false;
    }

    emitStatus(extra = {}) {
      this.options.onStatus({
        active: this.active,
        orientation: this.orientationActive,
        motion: this.motionActive,
        orientationPermission: this.orientationPermission,
        motionPermission: this.motionPermission,
        ...extra,
      });
    }

    recalibrate(mapHeading) {
      if (Number.isFinite(Number(mapHeading))) {
        this.mapHeadingBaseline = ResortMotionEngine.normalizeDegrees(mapHeading);
      }
      this.headingBaseline = Number.isFinite(this.rawHeading) ? this.rawHeading : null;
      this.mapHeading = this.mapHeadingBaseline;
      this.options.onHeading({
        heading: this.mapHeading,
        rawHeading: this.rawHeading,
        calibrated: true,
        source: 'device_orientation_recalibrated',
        at: Date.now(),
      });
    }

    handleScreenOrientationChange() {
      if (!this.active) return;
      // Preserve the current map heading but establish a fresh raw baseline so a
      // portrait/landscape switch does not rotate the resort arrow by 90 degrees.
      if (Number.isFinite(this.mapHeading)) this.mapHeadingBaseline = this.mapHeading;
      this.headingBaseline = null;
      this.rawHeading = null;
      this.lastOrientationAt = 0;
      this.emitStatus({screenOrientationChanged: true});
    }

    processOrientation(event) {
      if (!this.active) return;
      const now = Date.now();
      if (now - this.lastOrientationAt < this.options.orientationIntervalMs) return;

      let raw = null;
      let source = 'device_orientation';
      const isAbsoluteEvent = !!(event && (event.absolute === true || event.type === 'deviceorientationabsolute'));

      if (Number.isFinite(Number(event && event.webkitCompassHeading))) {
        raw = Number(event.webkitCompassHeading);
        source = 'device_compass';
        this.lastAbsoluteOrientationAt = now;
      } else if (Number.isFinite(Number(event && event.alpha))) {
        if (!isAbsoluteEvent && now - this.lastAbsoluteOrientationAt < 600) return;
        raw = 360 - Number(event.alpha);
        source = isAbsoluteEvent ? 'device_orientation_absolute' : 'device_orientation_relative';
        if (isAbsoluteEvent) this.lastAbsoluteOrientationAt = now;
      }
      if (!Number.isFinite(raw)) return;

      this.lastOrientationAt = now;
      raw = ResortMotionEngine.normalizeDegrees(raw + ResortMotionEngine.screenAngle());
      this.rawHeading = raw;
      if (!Number.isFinite(this.headingBaseline)) this.headingBaseline = raw;
      const relativeTurn = ResortMotionEngine.signedAngleDelta(raw, this.headingBaseline);
      this.mapHeading = ResortMotionEngine.normalizeDegrees(this.mapHeadingBaseline + relativeTurn);

      this.options.onHeading({
        heading: this.mapHeading,
        rawHeading: raw,
        relativeTurn,
        calibrated: Number.isFinite(this.headingBaseline),
        source,
        at: now,
      });
    }

    processMotion(event) {
      if (!this.active) return;
      const now = Date.now();
      this.lastMotionAt = now;
      const linearMagnitude = this.linearAccelerationMagnitude(event);
      if (!Number.isFinite(linearMagnitude)) return;

      if (now - this.lastMotionCallbackAt >= this.options.motionCallbackIntervalMs) {
        this.lastMotionCallbackAt = now;
        this.options.onMotion({
          acceleration: linearMagnitude,
          interval: Number(event && event.interval) || null,
          at: now,
        });
      }

      // A hard shake/drop should not be counted as a walking step.
      if (linearMagnitude > this.options.maxAcceleration) {
        this.aboveThreshold = true;
        return;
      }

      this.filteredAcceleration = this.filteredAcceleration * 0.66 + linearMagnitude * 0.34;
      if (this.filteredAcceleration < this.options.stepThreshold) {
        this.noiseFloor = this.noiseFloor * 0.97 + this.filteredAcceleration * 0.03;
      }

      const adaptiveThreshold = Math.max(
        this.options.stepThreshold * 0.78,
        Math.min(this.options.stepThreshold * 1.42, this.noiseFloor * 2.6 + 0.34)
      );
      const releaseThreshold = Math.max(
        this.options.stepReleaseThreshold,
        Math.min(adaptiveThreshold * 0.46, adaptiveThreshold - 0.14)
      );
      const above = this.filteredAcceleration >= adaptiveThreshold;

      if (above && !this.aboveThreshold) {
        const interval = this.lastStepAt ? now - this.lastStepAt : Infinity;
        if (!this.lastStepAt || interval >= this.options.minStepIntervalMs) {
          this.registerStep(now, interval, adaptiveThreshold);
        }
      }

      if (this.filteredAcceleration <= releaseThreshold) this.aboveThreshold = false;
      else if (above) this.aboveThreshold = true;
    }

    linearAccelerationMagnitude(event) {
      const direct = event && event.acceleration;
      if (direct && [direct.x, direct.y, direct.z].some(value => Number.isFinite(Number(value)))) {
        const x = Number(direct.x || 0);
        const y = Number(direct.y || 0);
        const z = Number(direct.z || 0);
        return Math.sqrt(x * x + y * y + z * z);
      }

      const total = event && event.accelerationIncludingGravity;
      if (!total || ![total.x, total.y, total.z].some(value => Number.isFinite(Number(value)))) return null;
      const x = Number(total.x || 0);
      const y = Number(total.y || 0);
      const z = Number(total.z || 0);

      if (!this.gravity.ready) {
        this.gravity = {x, y, z, ready: true};
        return 0;
      }

      // Low-pass gravity estimate; the residual is movement acceleration. This
      // path is important on Android devices that omit event.acceleration.
      const factor = 0.84;
      this.gravity.x = factor * this.gravity.x + (1 - factor) * x;
      this.gravity.y = factor * this.gravity.y + (1 - factor) * y;
      this.gravity.z = factor * this.gravity.z + (1 - factor) * z;
      const lx = x - this.gravity.x;
      const ly = y - this.gravity.y;
      const lz = z - this.gravity.z;
      return Math.sqrt(lx * lx + ly * ly + lz * lz);
    }

    registerStep(now, interval, threshold) {
      this.lastStepAt = now;
      this.stepCount += 1;
      this.aboveThreshold = true;
      const validInterval = Number.isFinite(interval) && interval <= this.options.maxStepIntervalMs ? interval : 650;
      const cadence = Math.max(0, Math.min(1, (900 - validInterval) / 600));
      const intensity = Math.max(0, Math.min(1, (this.filteredAcceleration - threshold) / 2.5));
      const stepLengthMeters = this.options.defaultStepLengthMeters + cadence * 0.11 + intensity * 0.05;

      this.options.onStep({
        count: this.stepCount,
        heading: Number.isFinite(this.mapHeading) ? this.mapHeading : this.mapHeadingBaseline,
        rawHeading: this.rawHeading,
        stepLengthMeters,
        intervalMs: validInterval,
        acceleration: this.filteredAcceleration,
        at: now,
      });
    }
  }

  return ResortMotionEngine;
});
