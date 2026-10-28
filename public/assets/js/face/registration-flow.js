/**
 * registration-flow.js - Face registration flow (student/admin)
 */
class RegistrationFlow {
    constructor(config = {}) {
        this.config = {
            modelsBasePath: config.modelsBasePath || '/assets/models/',
            samplesNeeded: config.samplesNeeded || 3,
            submitUrl: config.submitUrl || '',
            csrfToken: config.csrfToken || '',
            videoId: 'faceRegVideo',
            canvasId: 'faceRegCanvas',
            statusId: 'faceRegStatus',
            sampleCountId: 'sampleCount',
            sampleThumbsId: 'sampleThumbs',
            btnStartId: 'btnStartCamera',
            btnStopId: 'btnStopCamera',
            btnCaptureId: 'btnCapture',
            btnSubmitId: 'btnSubmitReg',
            ...config,
        };

        this.camera = null;
        this.engine = null;
        this.quality = null;
        this.pose = null;
        this.descriptors = [];
        this.thumbs = [];
        this.currentFace = null;
        this.stableCount = 0;
        this.isCapturing = false;
    }

    /**
     * Initialize the registration flow
     */
    async init() {
        const settings = this.config.settings || {};

        this.camera = new CameraManager(
            document.getElementById(this.config.videoId),
            document.getElementById(this.config.canvasId)
        );
        this.engine = new FaceEngine(this.config.modelsBasePath, settings);
        this.quality = new FaceQuality(settings);
        this.pose = new FacePose(settings);

        // Bind buttons
        const btnStart = document.getElementById(this.config.btnStartId);
        const btnStop = document.getElementById(this.config.btnStopId);
        const btnCapture = document.getElementById(this.config.btnCaptureId);
        const btnSubmit = document.getElementById(this.config.btnSubmitId);

        if (btnStart) btnStart.addEventListener('click', () => this.start());
        if (btnStop) btnStop.addEventListener('click', () => this.stop());
        if (btnCapture) btnCapture.addEventListener('click', () => this.captureSample());
        if (btnSubmit) btnSubmit.addEventListener('click', () => this.submit());

        // Load models
        this.setStatus('Loading face models...', 'neutral');
        await this.engine.load();
        this.setStatus('Ready. Click Start Camera to begin.', 'neutral');
    }

    /**
     * Start camera and detection loop
     */
    async start() {
        try {
            await this.camera.start();
            document.getElementById(this.config.btnStartId).classList.add('d-none');
            document.getElementById(this.config.btnStopId).classList.remove('d-none');
            document.getElementById(this.config.btnCaptureId).classList.remove('d-none');
            this.setStatus('Camera started. Position your face in frame.', 'warn');
            this._detectionLoop();
        } catch (err) {
            this.setStatus('Camera error: ' + err.message, 'error');
        }
    }

    stop() {
        this._stopLoop = true;
        this.camera.stop();
        document.getElementById(this.config.btnStartId).classList.remove('d-none');
        document.getElementById(this.config.btnStopId).classList.add('d-none');
        document.getElementById(this.config.btnCaptureId).classList.add('d-none');
        document.getElementById(this.config.btnSubmitId).classList.add('d-none');
        this.setStatus('Camera stopped.', 'neutral');
        this.clearCanvas();
    }

    /**
     * Detection loop - runs every 200ms
     */
    _detectionLoop() {
        this._stopLoop = false;
        const loop = async () => {
            if (this._stopLoop) return;
            if (!this.camera.isActive()) { setTimeout(loop, 500); return; }

            try {
                const settings = this.config.settings || {};
                const faces = await this.engine.detect(this.camera.video, settings.detection_confidence || 0.5);

                if (faces.length === 0) {
                    this.currentFace = null;
                    this.stableCount = 0;
                    this.pose.reset();
                    this.setStatus('No face detected. Position your face in the camera.', 'warn');
                    this.drawBox(null);
                } else if (faces.length > 1) {
                    this.currentFace = null;
                    this.stableCount = 0;
                    this.pose.reset();
                    this.setStatus('Multiple faces detected. Please ensure only one face is visible.', 'error');
                    this.drawBox(null);
                } else {
                    this.currentFace = faces[0];
                    const q = this.quality.analyze(faces[0], this.camera.video);
                    const stability = this.pose.checkStability(faces[0]);

                    if (!q.passed) {
                        this.setStatus(q.message, 'warn');
                        this.stableCount = 0;
                    } else if (!stability.isStable) {
                        this.setStatus('Hold still... ' + stability.count + '/' + stability.totalNeeded + ' frames', 'warn');
                    } else {
                        this.stableCount++;
                        this.drawBox(faces[0]);
                        this.setStatus('Face detected. You can capture a sample.', 'ok');
                        // Enable capture button
                        const btnCap = document.getElementById(this.config.btnCaptureId);
                        if (btnCap) btnCap.disabled = false;
                    }
                }
            } catch (err) {
                console.error('[RegFlow]', err);
            }

            setTimeout(loop, 200);
        };
        loop();
    }

    /**
     * Draw detection box on canvas overlay
     */
    drawBox(face) {
        const canvas = document.getElementById(this.config.canvasId);
        const ctx = canvas.getContext('2d');
        const video = document.getElementById(this.config.videoId);

        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        if (face) {
            const box = face.detection.box;
            const scale = canvas.width / video.videoWidth;
            ctx.strokeStyle = '#3b82f6';
            ctx.lineWidth = 3;
            ctx.strokeRect(box.x * scale, box.y * scale, box.width * scale, box.height * scale);
        }
    }

    clearCanvas() {
        const canvas = document.getElementById(this.config.canvasId);
        if (canvas && canvas.getContext) {
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }
    }

    /**
     * Capture a face sample (descriptor + thumbnail)
     */
    async captureSample() {
        if (!this.currentFace) {
            this.setStatus('No face detected yet.', 'warn');
            return;
        }

        const settings = this.config.settings || {};
        const q = this.quality.analyze(this.currentFace, this.camera.video);
        if (!q.passed) {
            this.setStatus(q.message, 'warn');
            return;
        }

        try {
            // Get descriptor from the already-detected face
            const desc = this.currentFace.descriptor;
            if (!desc) {
                this.setStatus('Could not extract face descriptor.', 'error');
                return;
            }

            // Get thumbnail
            const thumbCanvas = await this.camera.captureFrame();
            const thumbData = thumbCanvas.toDataURL('image/jpeg', 0.5);

            this.descriptors.push(Array.from(desc));
            this.thumbs.push(thumbData);

            const countEl = document.getElementById(this.config.sampleCountId);
            if (countEl) countEl.textContent = String(this.descriptors.length);

            // Add thumbnail
            const thumbsContainer = document.getElementById(this.config.sampleThumbsId);
            if (thumbsContainer) {
                const img = document.createElement('img');
                img.src = thumbData;
                img.className = 'thumb thumb-captured';
                img.title = 'Sample ' + this.descriptors.length;
                thumbsContainer.appendChild(img);
            }

            this.setStatus('Sample ' + this.descriptors.length + ' captured. (' + this.descriptors.length + '/' + this.config.samplesNeeded + ')', 'ok');

            // Check if we have enough samples
            if (this.descriptors.length >= this.config.samplesNeeded) {
                document.getElementById(this.config.btnSubmitId).classList.remove('d-none');
                document.getElementById(this.config.btnCaptureId).disabled = true;
                this.setStatus('All samples captured. Ready to submit.', 'ok');
            }

        } catch (err) {
            console.error('[RegFlow] captureSample error:', err);
            this.setStatus('Error capturing sample: ' + err.message, 'error');
        }
    }

    /**
     * Submit descriptors to server
     */
    async submit() {
        if (this.descriptors.length === 0) {
            this.setStatus('No samples captured yet.', 'warn');
            return;
        }

        const previewImage = this.thumbs[0] || '';
        const data = new FormData();
        data.append('descriptors', JSON.stringify(this.descriptors));
        data.append('preview_image', previewImage);

        if (this.config.csrfToken) {
            data.append('csrf_token', this.config.csrfToken);
        }

        this.setStatus('Submitting registration...', 'neutral');

        try {
            const res = await fetch(this.config.submitUrl, {
                method: 'POST',
                body: data,
            });
            const json = await res.json();

            if (json.success) {
                this.setStatus('✓ ' + json.message + ' (v' + json.version + ')', 'ok');
                // Reset
                this.descriptors = [];
                this.thumbs = [];
                document.getElementById(this.config.btnSubmitId).classList.add('d-none');
                const countEl = document.getElementById(this.config.sampleCountId);
                if (countEl) countEl.textContent = '0';
                const thumbsContainer = document.getElementById(this.config.sampleThumbsId);
                if (thumbsContainer) thumbsContainer.innerHTML = '';
            } else {
                this.setStatus('Error: ' + (json.message || 'Unknown error'), 'error');
            }
        } catch (err) {
            console.error('[RegFlow] submit error:', err);
            this.setStatus('Network error: ' + err.message, 'error');
        }
    }

    /**
     * Set status message and color
     */
    setStatus(message, type) {
        const el = document.getElementById(this.config.statusId);
        if (el) {
            el.innerHTML = this._statusIcon(type) + ' ' + message;
            el.className = 'alert alert-' + {
                ok: 'success', warn: 'warning', error: 'danger', neutral: 'secondary'
            }[type] + ' text-center';
        }
    }

    _statusIcon(type) {
        const icons = {
            ok: 'fa-solid fa-check-circle',
            warn: 'fa-solid fa-exclamation-triangle',
            error: 'fa-solid fa-xmark-circle',
            neutral: 'fa-solid fa-circle-notch fa-spin',
        };
        return '<i class="' + (icons[type] || icons.neutral) + ' me-2"></i>';
    }

    /**
     * Cleanup
     */
    destroy() {
        this._stopLoop = true;
        if (this.camera) this.camera.destroy();
    }
}
