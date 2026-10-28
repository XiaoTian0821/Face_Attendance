/**
 * matcher.js - Compare descriptors against registered students
 */
class FaceMatcher {
    /**
     * @param {Array} students - [{ id, student_code, full_name, class_code, descriptor: number[], ... }]
     * @param {object} settings
     */
    constructor(students, settings = {}) {
        this.students = students;
        this.settings = settings;
    }

    /**
     * Match a face descriptor against all registered students
     * @param {number[]} descriptor - 128-dim descriptor from face-api
     * @returns {object|null} best match { student, confidence, distance }
     */
    match(descriptor) {
        if (!ArrayBuffer.isView(descriptor) || descriptor.length !== 128) {
            console.error('[FaceMatcher] Invalid descriptor:', descriptor);
            return null;
        }

        const minConfidence = this.settings.min_confidence || 0.45;
        const matchThreshold = this.settings.match_threshold || 0.55;
        let best = null;

        for (const s of this.students) {
            if (!s.descriptor || !Array.isArray(s.descriptor)) continue;

            const distance = FaceMatcher.euclideanDistance(descriptor, s.descriptor);
            // Convert distance to a 0-1 similarity score (1 = identical)
            // face-api distances are typically 0-2 range
            const confidence = Math.max(0, 1 - distance / 2);

            // Better match = lower distance, higher confidence
            if (confidence >= minConfidence) {
                if (!best || confidence > best.confidence) {
                    best = {
                        student: s,
                        confidence,
                        distance,
                        passedThreshold: distance <= matchThreshold,
                    };
                }
            }
        }

        // Only return if passed threshold
        if (best && best.passedThreshold) {
            return best;
        }
        return null;
    }

    /**
     * Euclidean distance between two 128-dim vectors
     * @static
     */
    static euclideanDistance(a, b) {
        let sum = 0;
        for (let i = 0; i < a.length; i++) {
            const d = a[i] - b[i];
            sum += d * d;
        }
        return Math.sqrt(sum);
    }

    /**
     * Get all students with descriptors
     */
    getStudents() {
        return this.students;
    }
}
