/**
 * kiosk-flow.js - Continuous kiosk scanning flow
 * Runs in the kiosk page: detects faces, matches against registered
 * students, and records attendance via the server endpoint.
 */
class KioskFlow {
    constructor(config = {}) {
        this.config = {
            modelsBasePath: config.modelsBasePath || '/assets/models/',
            students: config.students || [],
            settings: config.settings || {},
            scanUrl: config.scanUrl || '',
            csrfToken: config.csrfToken || '',
            videoId: 'kioskVideo',
            canvasId: 'kioskOverlay',
            statusId: 'kioskStatus',
            resultId: 'kioskResult',
            nameId: 'kioskStudentName',
            codeId: 'kioskStudentCode',
            confId: 'kioskConfidence',
            msgId: 'kioskResultMsg',
            ...config,
        };

        this.camera = null;
        this.engine = null;
        this.quality = null;
        this.matcher = null;
        this.pose = null;

        this._running = false;
        this._lastScanTime = {};   // studentId -> timestamp
        this._lastScanResult = null;
        this._autoContinue = true;
    }

    /**
     * Initialize kiosk flow
     */
    async init() {
        this.camera = new CameraManager(
            document.getElementById(this.config.videoId),
            document.getElementById(this.config.canvasId)
        );
        this.engine = new FaceEngine(this.config.modelsBasePath, this.config.settings);
        this.quality = new FaceQuality(this.config.settings);
        this.pose = new FacePose(this.config.settings);
        this.matcher = new FaceMatcher(this.config.students, this.config.settings);

        await this.engine.load();
        this._setStatus('Camera initializing...', 'neutral');

        try {
            await this.camera.start();
        } catch (err) {
            this._setStatus('Camera error: ' + err.message, 'error');
            return;
        }

        this._setStatus('Ready. Waiting for face...', 'idle');
        this._running = true;
        this._loop();

        // Update clock
        this._updateClock();
        this._clockInterval = setInterval(() => this._updateClock(), 1000);
    }

    /**
     * Continuous detection loop
     */
    _loop() {
        if (!this._running) return;

        const detect = async () => {
            if (!this._running || !this.camera.isActive()) return;

            try {
                const confidence = this.config.settings.detection_confidence || 0.5;
                const faces = await this.engine.detect(this.camera.video, confidence);

                if (faces.length === 0) {
                    this._drawOverlay(null);
                    if (!this._lastScanResult || Date.now() - this._lastScanResult.ts > 4000) {
                        this._setStatus('No face detected. Please position your face in the camera.', 'idle');
                        this._hideResult();
                    }
                } else if (faces.length > 1) {
                    this._drawOverlay(null);
                    this._setStatus('Multiple faces detected. Please scan one face at a time.', 'error');
                } else {
                    const face = faces[0];
                    this._drawOverlay(face);

                    // Quality check
                    const q = this.quality.analyze(face, this.camera.video);
                    if (!q.passed) {
                        this._setStatus(q.message, 'warning');
                        return;
                    }

                    // Match
                    const match = this.matcher.match(face.descriptor);
                    if (!match) {
                        this._setStatus('Unknown face. No registered student found.', 'error');
                        return;
                    }

                    const s = match.student;
                    const dupInterval = (this.config.settings.duplicate_scan_interval || 300) * 1000;

                    // Check duplicate scan
                    const lastScan = this._lastScanTime[s.id] || 0;
                    if (Date.now() - lastScan < dupInterval) {
                        const remaining = Math.ceil((dupInterval - (Date.now() - lastScan)) / 1000);
                        this._showResult(s, match.confidence,
                            'Attendance already recorded recently. Please wait ' + remaining + 's before scanning again.');
                        this._setStatus('Duplicate scan blocked. Wait before scanning again.', 'warning');
                        return;
                    }

                    // Send to server
                    this._setStatus('Recording attendance for ' + s.full_name + '...', 'neutral');

                    const photo = await this.camera.captureBase64();
                    await this._sendScan(s.id, match.confidence, photo);

                    this._lastScanTime[s.id] = Date.now();
                }

            } catch (err) {
                console.error('[Kiosk]', err);
                this._setStatus('Detection error: ' + err.message, 'error');
            }

            setTimeout(detect, 250);
        };

        detect();
    }

    /**
     * Send scan to server
     */
    async _sendScan(studentId, confidence, photo) {
        try {
            const fd = new FormData();
            fd.append('student_id', String(studentId));
            fd.append('confidence', String(confidence));
            if (photo) fd.append('photo', photo);
            if (this.config.csrfToken) fd.append('csrf_token', this.config.csrfToken);

            const res = await fetch(this.config.scanUrl, {
                method: 'POST',
                body: fd,
            });
            const data = await res.json();

            this._lastScanResult = { ts: Date.now(), data };

            if (data.success) {
                this._showResult({ full_name: '—', student_code: '—' }, confidence, data.message, data.status);
                const cls = data.status === 'late' ? 'warning' : 'success';
                this._setStatus('✓ ' + data.message, cls);
            } else {
                this._showResult(null, confidence, data.message || 'Scan failed.');
                this._setStatus(data.message || 'Scan failed.', 'error');
            }
        } catch (err) {
            console.error('[Kiosk] sendScan error:', err);
            this._setStatus('Network error: ' + err.message, 'error');
        }
    }

    /**
     * Show result card
     */
    _showResult(student, confidence, message, status) {
        const el = document.getElementById(this.config.resultId);
        const nameEl = document.getElementById(this.config.nameId);
        const codeEl = document.getElementById(this.config.codeId);
        const confEl = document.getElementById(this.config.confId);
        const msgEl = document.getElementById(this.config.msgId);

        if (el) el.classList.remove('d-none');

        if (student) {
            if (nameEl) nameEl.textContent = student.full_name || 'Unknown Student';
            if (codeEl) codeEl.textContent = student.student_code || '';
        } else {
            if (nameEl) nameEl.textContent = 'Unknown Face';
            if (codeEl) codeEl.textContent = '';
        }

        if (confEl) {
            const pct = Math.round((confidence * 100).toFixed(1));
            confEl.textContent = 'Confidence: ' + pct + '%';
        }

        if (msgEl) {
            const cls = status === 'late' ? 'text-warning' :
                        status === 'on_leave' ? 'text-info' :
                        status === 'duplicate' ? 'text-warning' :
                        'text-success';
            msgEl.className = 'mt-2 ' + cls;
            msgEl.textContent = message;
        }

        // Auto-hide after 8 seconds
        clearTimeout(this._hideTimer);
        this._hideTimer = setTimeout(() => this._hideResult(), 8000);
    }

    _hideResult() {
        const el = document.getElementById(this.config.resultId);
        if (el) el.classList.add('d-none');
    }

    /**
     * Draw face detection box on overlay canvas
     */
    _drawOverlay(face) {
        const canvas = document.getElementById(this.config.canvasId);
        const video = document.getElementById(this.config.videoId);
        if (!canvas || !video) return;

        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        if (face) {
            const box = face.detection.box;
            const scale = canvas.width / video.videoWidth;
            ctx.strokeStyle = '#3b82f6';
            ctx.lineWidth = 3;
            ctx.strokeRect(box.x * scale, box.y * scale, box.width * scale, box.height * scale);
        }
    }

    _setStatus(message, cls) {
        const el = document.getElementById(this.config.statusId);
        if (el) {
            el.className = 'kiosk-status ' + (cls || 'idle');
            const icons = {
                idle: 'fa-circle-notch fa-spin',
                success: 'fa-check-circle',
                error: 'fa-xmark-circle',
                warning: 'fa-exclamation-triangle',
                neutral: 'fa-circle-notch fa-spin',
            };
            el.innerHTML = '<i class="fa-solid ' + (icons[cls] || icons.idle) + ' me-2"></i>' + message;
        }
    }

    _updateClock() {
        const el = document.getElementById('kioskClock');
        if (el) el.textContent = new Date().toLocaleString('en-US', {
            year: 'numeric', month: '2-digit', day: '2-digit',
            hour: '2-digit', minute: '2-digit', second: '2-digit'
        });
    }

    /**
     * Stop the kiosk loop
     */
    stop() {
        this._running = false;
        if (this.camera) this.camera.destroy();
        clearInterval(this._clockInterval);
    }
}
