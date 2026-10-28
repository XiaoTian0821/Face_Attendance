/**
 * quality.js - Face quality checking (brightness, sharpness, face size)
 */
class FaceQuality {
    constructor(settings = {}) {
        this.settings = settings;
    }

    /**
     * Analyze a face detection result and return quality assessment
     * @param {object} face - face detection result from face-api
     * @param {HTMLVideoElement|HTMLCanvasElement} input - source input
     * @returns {object} { passed, message, details }
     */
    analyze(face, input) {
        if (!face) {
            return { passed: false, message: 'No face detected.', details: {} };
        }

        const box = face.detection.box;
        const faceWidth = box.width;
        const faceHeight = box.height;
        const confidence = face.detection.score;

        // --- Face size check ---
        const minSize = this.settings.min_face_size || 80;
        const maxSize = this.settings.max_face_size || 420;
        if (faceWidth < minSize) {
            return {
                passed: false,
                message: 'Face too small. Move closer to the camera.',
                details: { faceSize: faceWidth, minSize, maxSize, confidence }
            };
        }
        if (faceWidth > maxSize) {
            return {
                passed: false,
                message: 'Face too large. Move further from the camera.',
                details: { faceSize: faceWidth, minSize, maxSize, confidence }
            };
        }

        // --- Detection confidence check ---
        const minConfidence = this.settings.detection_confidence || 0.5;
        if (confidence < minConfidence) {
            return {
                passed: false,
                message: 'Low face detection confidence.',
                details: { confidence, minConfidence }
            };
        }

        // --- Brightness check (sample from video frame) ---
        if (input instanceof HTMLVideoElement) {
            const brightness = this._getBrightness(input);
            const minBright = this.settings.min_brightness || 40;
            const maxBright = this.settings.max_brightness || 220;
            if (brightness < minBright) {
                return {
                    passed: false,
                    message: 'Lighting is too low. Face the light source.',
                    details: { brightness, minBright, maxBright }
                };
            }
            if (brightness > maxBright) {
                return {
                    passed: false,
                    message: 'Image is too bright. Reduce lighting.',
                    details: { brightness, minBright, maxBright }
                };
            }

            // --- Sharpness check ---
            const sharpness = this._getSharpness(input, box);
            const minSharp = this.settings.min_sharpness || 20;
            if (sharpness < minSharp) {
                return {
                    passed: false,
                    message: 'Image is too blurry. Hold still.',
                    details: { sharpness, minSharp }
                };
            }
        }

        return {
            passed: true,
            message: 'Face quality OK',
            details: {
                faceSize: faceWidth,
                faceHeight: faceHeight,
                confidence,
            }
        };
    }

    /**
     * Get average brightness of the face region
     * @private
     */
    _getBrightness(video) {
        const tempCanvas = document.createElement('canvas');
        const size = Math.min(video.videoWidth, 320);
        const scale = size / video.videoWidth;
        tempCanvas.width = size;
        tempCanvas.height = Math.round(video.videoHeight * scale);
        const ctx = tempCanvas.getContext('2d', { willReadFrequently: true });
        ctx.drawImage(video, 0, 0, tempCanvas.width, tempCanvas.height);

        try {
            const data = ctx.getImageData(0, 0, tempCanvas.width, tempCanvas.height).data;
            let sum = 0;
            const pixelCount = data.length / 4;
            for (let i = 0; i < data.length; i += 4) {
                sum += (0.299 * data[i] + 0.587 * data[i + 1] + 0.114 * data[i + 2]);
            }
            return Math.round(sum / pixelCount);
        } catch (e) {
            return 128; // fallback
        }
    }

    /**
     * Estimate sharpness (Laplacian variance approximation)
     * @private
     */
    _getSharpness(video, box) {
        const tempCanvas = document.createElement('canvas');
        const size = Math.min(video.videoWidth, 320);
        const scale = size / video.videoWidth;
        tempCanvas.width = size;
        tempCanvas.height = Math.round(video.videoHeight * scale);
        const ctx = tempCanvas.getContext('2d', { willReadFrequently: true });
        ctx.drawImage(video, 0, 0, tempCanvas.width, tempCanvas.height);

        try {
            const data = ctx.getImageData(0, 0, tempCanvas.width, tempCanvas.height).data;
            // Convert to grayscale array
            const w = tempCanvas.width;
            const h = tempCanvas.height;
            const gray = new Float32Array(w * h);
            for (let i = 0; i < w * h; i++) {
                gray[i] = 0.299 * data[i * 4] + 0.587 * data[i * 4 + 1] + 0.114 * data[i * 4 + 2];
            }
            // Laplacian approximation
            let sum = 0, sumSq = 0, count = 0;
            for (let y = 1; y < h - 1; y++) {
                for (let x = 1; x < w - 1; x++) {
                    const i = y * w + x;
                    const lap = gray[i - 1] + gray[i + 1] + gray[i - w] + gray[i + w] - 4 * gray[i];
                    sum += lap;
                    sumSq += lap * lap;
                    count++;
                }
            }
            if (count === 0) return 0;
            const mean = sum / count;
            const variance = sumSq / count - mean * mean;
            return Math.round(Math.sqrt(Math.max(0, variance)));
        } catch (e) {
            return 100;
        }
    }
}
