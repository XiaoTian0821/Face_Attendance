/**
 * face-engine.js - Load face-api.js models and run detection
 */
class FaceEngine {
    constructor(modelsBasePath, options = {}) {
        this.modelsBasePath = modelsBasePath;
        this.options = options;
        this.isLoaded = false;
        this.detectorInputSize = 416;
        this.tinyFaceDetector = null;
        this.faceLandmark68 = null;
        this.faceNet = null;
    }

    /**
     * Load all face-api.js models
     * @returns {Promise<void>}
     */
    async load() {
        if (this.isLoaded) return;

        console.log('[FaceEngine] Loading models from:', this.modelsBasePath);

        const modelUrl = (file) => this.modelsBasePath + file;

        await Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri(this.modelsBasePath),
            faceapi.nets.faceLandmark68Net.loadFromUri(this.modelsBasePath),
            faceapi.nets.faceRecognitionNet.loadFromUri(this.modelsBasePath),
        ]);

        this.detectorInputSize = 416;

        this.isLoaded = true;
        console.log('[FaceEngine] Models loaded successfully.');
    }

    /**
     * Detect faces in an input
     * @param {HTMLCanvasElement|HTMLVideoElement|HTMLImageElement} input
     * @param {number} confidence - detection threshold
     * @returns {Promise<faceapi.FaceDetectionWithLandmarks[]>}
     */
    async detect(input, confidence = 0.5) {
        const detectorOptions = new faceapi.TinyFaceDetectorOptions({
            inputSize: this.detectorInputSize,
            scoreThreshold: confidence,
        });

        return faceapi
            .detectAllFaces(input, detectorOptions)
            .withFaceLandmarks()
            .withFaceDescriptors();
    }

    /**
     * Detect a single face (best one)
     * @param {HTMLCanvasElement|HTMLVideoElement|HTMLImageElement} input
     * @param {number} confidence
     * @returns {Promise<faceapi.FaceDetectionAndLandmarksWithDistance|null>}
     */
    async detectSingle(input, confidence = 0.5) {
        const detectorOptions = new faceapi.TinyFaceDetectorOptions({
            inputSize: this.detectorInputSize,
            scoreThreshold: confidence,
        });
        return faceapi
            .detectSingleFace(input, detectorOptions)
            .withFaceLandmarks()
            .withFaceDescriptor();
    }
}
