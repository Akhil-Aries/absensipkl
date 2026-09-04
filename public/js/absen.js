document.addEventListener('DOMContentLoaded', () => {
    // ===== Clock & Date =====
    const timeDisplay = document.getElementById('current-time');
    const dateDisplay = document.getElementById('current-date');

    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        if (timeDisplay) timeDisplay.textContent = `${hours}:${minutes}:${seconds}`;

        if (dateDisplay) {
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            dateDisplay.textContent = now.toLocaleDateString('id-ID', options);
        }
    }

    if (timeDisplay) {
        updateClock();
        setInterval(updateClock, 1000);
    }

    // ===== Camera Selfie Elements =====
    const absenTriggerBtn = document.getElementById('absen-trigger-btn');
    const absenForm = document.getElementById('absen-form');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const fotoDataInput = document.getElementById('foto-data');
    const fotoFileInput = document.getElementById('foto-file-input');

    const cameraModal = document.getElementById('camera-modal');
    const btnCloseCamera = document.getElementById('btn-close-camera');
    const videoStream = document.getElementById('camera-stream');
    const capturedPreview = document.getElementById('captured-preview');
    const cameraCanvas = document.getElementById('camera-canvas');
    const cameraLoading = document.getElementById('camera-loading');
    const cameraError = document.getElementById('camera-error');
    const cameraErrorMsg = document.getElementById('camera-error-msg');
    const cameraFrameGuide = document.getElementById('camera-frame-guide');
    const cameraFlash = document.getElementById('camera-flash');

    const cameraLiveControls = document.getElementById('camera-live-controls');
    const cameraPreviewControls = document.getElementById('camera-preview-controls');
    const btnCapture = document.getElementById('btn-capture');
    const btnRetake = document.getElementById('btn-retake');
    const btnConfirmAbsen = document.getElementById('btn-confirm-absen');
    const btnSwitchCamera = document.getElementById('btn-switch-camera');
    const btnFileAlt = document.getElementById('btn-file-alt');
    const btnUseFileFallback = document.getElementById('btn-use-file-fallback');

    let activeStream = null;
    let currentFacingMode = 'user'; // 'user' (front) or 'environment' (back)
    let capturedDataUrl = null;

    // Open Camera Modal
    if (absenTriggerBtn && cameraModal) {
        absenTriggerBtn.addEventListener('click', () => {
            cameraModal.style.display = 'flex';
            resetCameraView();
            startCamera(currentFacingMode);
        });
    }

    // Close Camera Modal
    if (btnCloseCamera && cameraModal) {
        btnCloseCamera.addEventListener('click', () => {
            closeCameraModal();
        });
    }

    function closeCameraModal() {
        stopCamera();
        if (cameraModal) cameraModal.style.display = 'none';
        resetCameraView();
    }

    function resetCameraView() {
        if (capturedPreview) capturedPreview.style.display = 'none';
        if (videoStream) videoStream.style.display = 'block';
        if (cameraFrameGuide) cameraFrameGuide.style.display = 'flex';
        if (cameraLiveControls) cameraLiveControls.style.display = 'flex';
        if (cameraPreviewControls) cameraPreviewControls.style.display = 'none';
        if (cameraError) cameraError.style.display = 'none';
        if (cameraLoading) cameraLoading.style.display = 'flex';
        capturedDataUrl = null;
        if (fotoDataInput) fotoDataInput.value = '';
    }

    async function startCamera(facing = 'user') {
        stopCamera();
        if (cameraLoading) cameraLoading.style.display = 'flex';
        if (cameraError) cameraError.style.display = 'none';

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showCameraError('Browser Anda tidak mendukung akses kamera langsung. Silakan gunakan opsi upload foto.');
            return;
        }

        try {
            const constraints = {
                video: {
                    facingMode: facing,
                    width: { ideal: 640 },
                    height: { ideal: 640 }
                },
                audio: false
            };

            const stream = await navigator.mediaDevices.getUserMedia(constraints);
            activeStream = stream;
            if (videoStream) {
                videoStream.srcObject = stream;
                // Add mirror effect if front camera
                if (facing === 'user') {
                    videoStream.style.transform = 'scaleX(-1)';
                } else {
                    videoStream.style.transform = 'scaleX(1)';
                }
                await videoStream.play();
            }
            if (cameraLoading) cameraLoading.style.display = 'none';
        } catch (err) {
            console.error('Camera access error:', err);
            let msg = 'Izin kamera ditolak atau kamera tidak ditemukan.';
            if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                msg = 'Izin akses kamera ditolak. Berikan izin di pengaturan browser Anda atau upload foto.';
            } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
                msg = 'Kamera tidak ditemukan pada perangkat Anda.';
            }
            showCameraError(msg);
        }
    }

    function stopCamera() {
        if (activeStream) {
            activeStream.getTracks().forEach(track => track.stop());
            activeStream = null;
        }
        if (videoStream) {
            videoStream.srcObject = null;
        }
    }

    function showCameraError(msg) {
        if (cameraLoading) cameraLoading.style.display = 'none';
        if (cameraErrorMsg) cameraErrorMsg.textContent = msg;
        if (cameraError) cameraError.style.display = 'flex';
        if (videoStream) videoStream.style.display = 'none';
        if (cameraFrameGuide) cameraFrameGuide.style.display = 'none';
    }

    // Switch Front/Back Camera
    if (btnSwitchCamera) {
        btnSwitchCamera.addEventListener('click', () => {
            currentFacingMode = (currentFacingMode === 'user') ? 'environment' : 'user';
            startCamera(currentFacingMode);
        });
    }

    // Trigger File Input Fallback
    if (btnFileAlt) {
        btnFileAlt.addEventListener('click', () => {
            if (fotoFileInput) fotoFileInput.click();
        });
    }
    if (btnUseFileFallback) {
        btnUseFileFallback.addEventListener('click', () => {
            if (fotoFileInput) fotoFileInput.click();
        });
    }

    // Handle File Input Change
    if (fotoFileInput) {
        fotoFileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (event) => {
                stopCamera();
                capturedDataUrl = event.target.result;
                if (fotoDataInput) fotoDataInput.value = capturedDataUrl;

                if (capturedPreview) {
                    capturedPreview.src = capturedDataUrl;
                    capturedPreview.style.display = 'block';
                    capturedPreview.style.transform = 'none';
                }
                if (videoStream) videoStream.style.display = 'none';
                if (cameraFrameGuide) cameraFrameGuide.style.display = 'none';
                if (cameraLoading) cameraLoading.style.display = 'none';
                if (cameraError) cameraError.style.display = 'none';
                if (cameraLiveControls) cameraLiveControls.style.display = 'none';
                if (cameraPreviewControls) cameraPreviewControls.style.display = 'flex';
            };
            reader.readAsDataURL(file);
        });
    }

    // Capture Snapshot
    if (btnCapture) {
        btnCapture.addEventListener('click', () => {
            if (!videoStream || !videoStream.videoWidth) return;

            // Flash effect
            if (cameraFlash) {
                cameraFlash.classList.add('flash-active');
                setTimeout(() => cameraFlash.classList.remove('flash-active'), 300);
            }

            const width = videoStream.videoWidth;
            const height = videoStream.videoHeight;
            cameraCanvas.width = width;
            cameraCanvas.height = height;

            const ctx = cameraCanvas.getContext('2d');

            // Mirror canvas if front camera
            if (currentFacingMode === 'user') {
                ctx.translate(width, 0);
                ctx.scale(-1, 1);
            }

            ctx.drawImage(videoStream, 0, 0, width, height);

            capturedDataUrl = cameraCanvas.toDataURL('image/jpeg', 0.85);
            if (fotoDataInput) fotoDataInput.value = capturedDataUrl;

            // Display captured image
            if (capturedPreview) {
                capturedPreview.src = capturedDataUrl;
                capturedPreview.style.display = 'block';
                capturedPreview.style.transform = 'none';
            }
            if (videoStream) videoStream.style.display = 'none';
            if (cameraFrameGuide) cameraFrameGuide.style.display = 'none';
            if (cameraLiveControls) cameraLiveControls.style.display = 'none';
            if (cameraPreviewControls) cameraPreviewControls.style.display = 'flex';

            stopCamera();
        });
    }

    // Retake Photo
    if (btnRetake) {
        btnRetake.addEventListener('click', () => {
            resetCameraView();
            startCamera(currentFacingMode);
        });
    }

    // Confirm & Submit Attendance
    if (btnConfirmAbsen && absenForm) {
        btnConfirmAbsen.addEventListener('click', () => {
            btnConfirmAbsen.disabled = true;
            btnConfirmAbsen.innerHTML = `
                <div class="camera-spinner" style="width:16px;height:16px;border-width:2px;margin:0 6px 0 0;"></div>
                Menyimpan...
            `;

            // Get Geolocation
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        if (latInput) latInput.value = position.coords.latitude;
                        if (lngInput) lngInput.value = position.coords.longitude;
                        absenForm.submit();
                    },
                    (error) => {
                        console.warn('Geolocation warning:', error);
                        absenForm.submit();
                    },
                    { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
                );
            } else {
                absenForm.submit();
            }
        });
    }

    // ===== Lightbox Preview for History Photos =====
    const lightboxModal = document.getElementById('photo-lightbox-modal');
    const lightboxImg = document.getElementById('lightbox-img');
    const lightboxCaption = document.getElementById('lightbox-caption');
    const btnCloseLightbox = document.getElementById('btn-close-lightbox');

    document.querySelectorAll('.btn-view-photo').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const src = btn.getAttribute('data-src');
            const title = btn.getAttribute('data-title') || 'Foto Selfie Absensi';

            if (lightboxModal && lightboxImg) {
                lightboxImg.src = src;
                if (lightboxCaption) lightboxCaption.textContent = title;
                lightboxModal.style.display = 'flex';
            }
        });
    });

    if (btnCloseLightbox && lightboxModal) {
        btnCloseLightbox.addEventListener('click', () => {
            lightboxModal.style.display = 'none';
        });
    }

    if (lightboxModal) {
        lightboxModal.addEventListener('click', (e) => {
            if (e.target === lightboxModal) {
                lightboxModal.style.display = 'none';
            }
        });
    }

    if (cameraModal) {
        cameraModal.addEventListener('click', (e) => {
            if (e.target === cameraModal) {
                closeCameraModal();
            }
        });
    }
});
