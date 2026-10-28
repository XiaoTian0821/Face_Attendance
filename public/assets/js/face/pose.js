/**
 * pose.js - Face orientation and stability checking
 */
class FacePose {
    constructor(settings = {}) {
        this.settings = settings;
        this.stableFrames = settings.stable_frames || 3;
        this.facePositions = [];
    }

    /**
     * Check face stability (face not moving too much)
     * @param {object} face - face detection result
     * @returns {object} { isStable, count, totalNeeded, drift }
     */
    checkStability(face) {
        if (!face) {
            this.facePositions = [];
            return { isStable: false, count: 0, totalNeeded: this.stableFrames, drift: 0 };
        }

        const box = face.detection.box;
        const cx = box.x + box.width / 2;
        const cy = box.y + box.height / 2;

        this.facePositions.push({ cx, cy, ts: Date.now() });
        if (this.facePositions.length > this.stableFrames) {
            this.facePositions.shift();
        }

        const count = this.facePositions.length;
        if (count < this.stableFrames) {
            return { isStable: false, count, totalNeeded: this.stableFrames, drift: 0 };
        }

        // Check drift across stable frames
        const first = this.facePositions[0];
        const last = this.facePositions[count - 1];
        const driftX = Math.abs(last.cx - first.cx);
        const driftY = Math.abs(last.cy - first.cy);
        const drift = Math.sqrt(driftX * driftX + driftY * driftY);
        const maxDrift = 30; // pixels

        const isStable = drift <= maxDrift;
        return {
            isStable,
            count,
            totalNeeded: this.stableFrames,
            drift: Math.round(drift),
        };
    }

    /**
     * Check face orientation using landmarks
     * @param {object} face - face with landmarks
     * @returns {object} { isFrontal, yaw, pitch }
     */
    checkOrientation(face) {
        if (!face || !face.landmarks) {
            return { isFrontal: false, yaw: 0, pitch: 0 };
        }

        const lm = face.landmarks;
        // Approximate yaw from nose tip vs mouth center
        const nose = lm.getNoseTip();
        const mouth = lm.getMouth();
        const faceCenterX = (mouth.start.x + mouth.end.x) / 2;
        const yaw = (nose.x - faceCenterX) / face.detection.box.width;

        // Approximate pitch from chin vs forehead
        const chin = lm.getChin();
        const foreheadMidX = (lm.getForeheadInner().x + lm.getForeheadOuter().x) / 2;
        const pitch = (chin.y - foreheadMidX * 0) / face.detection.box.height; // simplified

        // Yaw threshold: face should be roughly frontal
        const isFrontal = Math.abs(yaw) < 0.15;

        return { isFrontal, yaw: Math.round(yaw * 100) / 100, pitch: Math.round(pitch * 100) / 100 };
    }

    reset() {
        this.facePositions = [];
    }
}
