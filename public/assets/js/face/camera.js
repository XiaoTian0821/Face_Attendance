/**
 * camera.js - Camera initialization and management
 */
class CameraManager {
    constructor(videoEl, canvasEl, options = {}) {
        this.video = videoEl;
        this.canvas = canvasEl;
        this.ctx = canvasEl ? canvasEl.getContext('2d') : null;
        this.stream = null;
        this.isRunning = false;
        this.options = options;
    }

    /**
     * Start the camera
     * @returns {Promise<void>}
     */
    async start() {
        if (this.isRunning) return;
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            throw new Error('Camera not supported in this browser.');
        }

        this.stream = await navigator.mediaDevices.getUserMedia({
            video: {
                width: { ideal: 640 },
                height: { ideal: 480 },
                facingMode: 'user'
            },
            audio: false
        });

        this.video.srcObject = this.stream;
        await this.video.play();
        this.isRunning = true;
    }

    /**
     * Stop the camera and clean up
     */
    stop() {
        if (this.stream) {
            this.stream.getTracks().forEach(t => t.stop());
            this.stream = null;
        }
        this.video.srcObject = null;
        this.isRunning = false;
    }

    /**
     * Check if camera is active
     */
    isActive() {
        return this.isRunning && this.stream && this.stream.active;
    }

    /**
     * Capture a still frame from the video
     * @returns {Promise<canvas>}
     */
    captureFrame() {
        return new Promise((resolve, reject) => {
            if (!this.isActive()) {
                reject(new Error('Camera is not active'));
                return;
            }
            const capCanvas = document.createElement('canvas');
            capCanvas.width = this.video.videoWidth || 640;
            capCanvas.height = this.video.videoHeight || 480;
            const capCtx = capCanvas.getContext('2d');
            capCtx.drawImage(this.video, 0, 0, capCanvas.width, capCanvas.height);
            resolve(capCanvas);
        });
    }

    /**
     * Get a base64 image of the current frame
     * @returns {Promise<string>}
     */
    async captureBase64() {
        const canvas = await this.captureFrame();
        return canvas.toDataURL('image/jpeg', 0.7);
    }

    /**
     * Cleanup - call on page unload
     */
    destroy() {
        this.stop();
    }
}
