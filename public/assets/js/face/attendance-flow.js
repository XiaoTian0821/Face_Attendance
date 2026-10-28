/**
 * attendance-flow.js - Single-scan attendance flow (student self-service)
 */
class AttendanceFlow {
    constructor(config = {}) {
        this.config = {
            modelsBasePath: config.modelsBasePath || '/assets/models/',
            settings: config.settings || {},
            scanUrl: config.scanUrl || '',
            csrfToken: config.csrfToken || '',
            videoId: 'attVideo',
            canvasId: 'attCanvas',
            statusId: 'attStatus',
            btnStartId: 'attBtnStart',
            btnScanId: 'attBtnScan',
            resultCardId: 'attResult',
            ...config,
        };
        this.camera = null;
        this.engine = null;
        this.quality = null;
        this.matcher = null;
    }

    async init() {
        this.camera = new CameraManager(
            document.getElementById(this.config.videoId),
            document.getElementById(this.config.canvasId)
        );
        this.engine = new FaceEngine(this.config.modelsBasePath, this.config.settings);
        this.quality = new FaceQuality(this.config.settings);

        await this.engine.load();
        this._bindButtons();
    }

    _bindButtons() {
        const btnStart = document.getElementById(this.config.btnStartId);
        const btnScan = document.getElementById(this.config.btnScanId);
        if (btnStart) btnStart.addEventListener('click', () => this._startCam());
        if (btnScan) btnScan.addEventListener('click', () => this._scan());
    }

    async _startCam() {
        try {
            await this.camera.start();
            document.getElementById(this.config.btnStartId).classList.add('d-none');
            document.getElementById(this.config.btnScanId).classList.remove('d-none');
            this._setStatus('Camera ready. Click Scan to check in.', 'neutral');
            this._detectionLoop();
        } catch (err) {
            this._setStatus('Camera error: ' + err.message, 'error');
        }
    }

    _detectionLoop() {
        const loop = async () => {
            if (!this.camera.isActive()) return;
            try {
                const faces = await this.engine.detect(this.camera.video, this.config.settings.detection_confidence || 0.5);
                if (faces.length === 1) {
                    this._drawBox(faces[0]);
                    this._currentFace = faces[0];
                } else {
                    this._drawBox(null);
                    this._currentFace = null;
                }
            } catch (e) {}
            setTimeout(loop, 250);
        };
        loop();
    }

    async _scan() {
        if (!this._currentFace) {
            this._setStatus('No face detected. Position your face in the camera.', 'warn');
            return;
        }

        const q = this.quality.analyze(this._currentFace, this.camera.video);
        if (!q.passed) {
            this._setStatus(q.message, 'warn');
            return;
        }

        this._setStatus('Scanning...', 'neutral');

        try {
            // Match locally first, then send to server
            // For kiosk/attendance, we send to the server for proper processing
            const photo = await this.camera.captureBase64();
            const fd = new FormData();
            fd.append('photo', photo);

            // If we have student data embedded, include it
            // Otherwise server will do matching
            if (this.config.studentId) {
                fd.append('student_id', this.config.studentId);
                fd.append('confidence', String(this._currentFace.detection.score));
            }

            if (this.config.csrfToken) fd.append('csrf_token', this.config.csrfToken);

            const res = await fetch(this.config.scanUrl, { method: 'POST', body: fd });
            const data = await res.json();

            if (data.success) {
                this._showResult(data.status, data.message, data.student_id ? this._currentFace : null);
            } else {
                this._setStatus(data.message || 'Scan failed.', 'error');
            }
        } catch (err) {
            this._setStatus('Network error: ' + err.message, 'error');
        }
    }

    _showResult(status, message, face) {
        const resultCard = document.getElementById(this.config.resultCardId);
        if (resultCard) {
            resultCard.classList.remove('d-none');
            const color = status === 'late' ? 'text-warning' : (status === 'on_leave' ? 'text-info' : 'text-success');
            resultCard.innerHTML = `
                <div class="${color} fw-bold fs-5 mb-2"><i class="fa-solid fa-check-double me-2"></i>${message}</div>
                <small class="text-muted">Status: ${status.toUpperCase()}</small>`;
        }
        this._setStatus('Scan complete. ' + message, status === 'late' ? 'warn' : 'ok');
    }

    _drawBox(face) {
        const canvas = document.getElementById(this.config.canvasId);
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        const video = document.getElementById(this.config.videoId);
        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        if (face) {
            const box = face.detection.box;
            const scale = canvas.width / video.videoWidth;
            ctx.strokeStyle = '#10b981';
            ctx.lineWidth = 3;
            ctx.strokeRect(box.x * scale, box.y * scale, box.width * scale, box.height * scale);
        }
    }

    _setStatus(message, type) {
        const el = document.getElementById(this.config.statusId);
        if (el) {
            el.textContent = message;
            el.className = 'alert alert-' + ({ok:'success',warn:'warning',error:'danger',neutral:'secondary'})[type] + ' text-center';
        }
    }

    destroy() {
        if (this.camera) this.camera.destroy();
    }
}
