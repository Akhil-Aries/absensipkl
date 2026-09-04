<?php $__env->startSection('content'); ?>
    <header class="header">
        <div class="header-top">
            <h1>Absensi PKL</h1>
            <button id="theme-toggle" class="btn-theme" title="Ubah Tema">
                <svg id="theme-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="5"></circle>
                    <line x1="12" y1="1" x2="12" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="23"></line>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                    <line x1="1" y1="12" x2="3" y2="12"></line>
                    <line x1="21" y1="12" x2="23" y2="12"></line>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                </svg>
            </button>
        </div>
        <p id="current-date">Memuat tanggal...</p>
        <div class="clock" id="current-time">00:00:00</div>
    </header>

    <div class="navbar">
        <span class="user-info">Halo, <strong><?php echo e(auth()->user()->name); ?></strong></span>
        <div class="navbar-actions">
            <a href="<?php echo e(route('password.edit')); ?>" class="btn-nav" title="Ganti Password">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <span>Password</span>
            </a>
            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn-logout">Keluar</button>
            </form>
        </div>
    </div>

    <main class="main-content">
        <div class="action-section">
            <form id="absen-form" method="POST" action="<?php echo e(route('absen.store')); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
                <input type="hidden" name="foto" id="foto-data">
                <input type="file" name="foto_file" id="foto-file-input" accept="image/*" capture="user" style="display: none;">

                <button type="button" id="absen-trigger-btn" class="btn-absen <?php echo e($nextType === 'Pulang' ? 'pulang' : ''); ?>" <?php echo e($nextType === null ? 'disabled' : ''); ?>>
                    <span class="btn-text">
                        <?php if($nextType === 'Masuk'): ?> Absen Masuk
                        <?php elseif($nextType === 'Pulang'): ?> Absen Pulang
                        <?php else: ?> Selesai Hari Ini
                        <?php endif; ?>
                    </span>
                    <span class="btn-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 6V12L16 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </button>
            </form>
        </div>

        
        <div id="camera-modal" class="modal-backdrop" style="display: none;">
            <div class="camera-modal-content">
                <div class="camera-modal-header">
                    <div class="camera-modal-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                            <circle cx="12" cy="13" r="4"/>
                        </svg>
                        <span>Selfie Absen <?php echo e($nextType ?? 'PKL'); ?></span>
                    </div>
                    <button type="button" id="btn-close-camera" class="btn-modal-close" title="Tutup Kamera">&times;</button>
                </div>

                <div class="camera-viewfinder-container">
                    <div id="camera-loading" class="camera-loading-overlay">
                        <div class="camera-spinner"></div>
                        <span>Menghubungkan kamera...</span>
                    </div>

                    <div id="camera-error" class="camera-error-overlay" style="display: none;">
                        <p id="camera-error-msg">Akses kamera tidak diizinkan atau tidak tersedia.</p>
                        <button type="button" id="btn-use-file-fallback" class="btn-secondary-action">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            Pilih File Foto
                        </button>
                    </div>

                    <video id="camera-stream" autoplay playsinline muted></video>
                    <img id="captured-preview" alt="Preview Selfie" style="display: none;" />
                    <canvas id="camera-canvas" style="display: none;"></canvas>

                    
                    <div id="camera-frame-guide" class="camera-frame-guide">
                        <div class="camera-guide-oval"></div>
                        <span class="camera-guide-text">Posisikan wajah Anda di dalam lingkaran</span>
                    </div>

                    <div id="camera-flash" class="camera-flash-effect"></div>
                </div>

                <div class="camera-controls">
                    
                    <div id="camera-live-controls" class="camera-controls-group">
                        <button type="button" id="btn-switch-camera" class="btn-camera-util" title="Ganti Kamera">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/>
                            </svg>
                        </button>

                        <button type="button" id="btn-capture" class="btn-shutter" title="Ambil Foto">
                            <div class="shutter-inner"></div>
                        </button>

                        <button type="button" id="btn-file-alt" class="btn-camera-util" title="Upload Foto dari Galeri">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <polyline points="21 15 16 10 5 21"/>
                            </svg>
                        </button>
                    </div>

                    
                    <div id="camera-preview-controls" class="camera-controls-group" style="display: none;">
                        <button type="button" id="btn-retake" class="btn-action-retake">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 4v6h6"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                            Foto Ulang
                        </button>
                        <button type="button" id="btn-confirm-absen" class="btn-action-confirm">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            Kirim Absen
                        </button>
                    </div>
                </div>
            </div>
        </div>

        
        <div id="photo-lightbox-modal" class="modal-backdrop" style="display: none;">
            <div class="photo-lightbox-content">
                <div class="photo-lightbox-header">
                    <span id="lightbox-caption">Foto Selfie</span>
                    <button type="button" id="btn-close-lightbox" class="btn-modal-close">&times;</button>
                </div>
                <div class="photo-lightbox-body">
                    <img id="lightbox-img" src="" alt="Foto Absen">
                </div>
            </div>
        </div>

        <div class="history-section">
            <div class="history-header">
                <h2>Riwayat Kehadiran Anda</h2>
            </div>
            <div class="history-list" id="history-list">
                <?php $__empty_1 = true; $__currentLoopData = $riwayat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="history-item" style="animation-delay: <?php echo e($index * 0.1); ?>s">
                        <div class="status">
                            <div class="status-dot" style="background-color: <?php echo e($record->type === 'Pulang' ? 'var(--accent-color)' : 'var(--success-color)'); ?>; box-shadow: 0 0 10px <?php echo e($record->type === 'Pulang' ? 'var(--accent-color)' : 'var(--success-color)'); ?>"></div>
                            <div class="status-info">
                                <span>Absen <?php echo e($record->type); ?></span>
                                <div style="display: flex; gap: 8px; align-items: center; margin-top: 2px; flex-wrap: wrap;">
                                    <span class="status-date" style="margin-top: 0;">
                                        <?php echo e(\Carbon\Carbon::parse($record->tanggal)->translatedFormat('l, d M Y')); ?>

                                    </span>
                                    <?php if($record->latitude && $record->longitude): ?>
                                        <a href="https://www.google.com/maps/search/?api=1&query=<?php echo e($record->latitude); ?>,<?php echo e($record->longitude); ?>" target="_blank" class="location-link" title="Lihat Lokasi">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                                <circle cx="12" cy="10" r="3"></circle>
                                            </svg>
                                            Lokasi
                                        </a>
                                    <?php endif; ?>
                                    <?php if($record->foto): ?>
                                        <button type="button" class="photo-link btn-view-photo" data-src="<?php echo e(asset('storage/'.$record->foto)); ?>" data-title="Selfie <?php echo e($record->type); ?> - <?php echo e(\Carbon\Carbon::parse($record->jam)->format('H:i')); ?>">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                                                <circle cx="12" cy="13" r="4"/>
                                            </svg>
                                            Foto Selfie
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="time"><?php echo e(\Carbon\Carbon::parse($record->jam)->format('H:i')); ?></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="empty-state">Belum ada data kehadiran.</div>
                <?php endif; ?>
            </div>
        </div>
    </main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('js/absen.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', ['title' => 'Absensi PKL'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\php-8.5.9-Win32-vs17-x64\project\absensi-pkl-laravel\resources\views/siswa/dashboard.blade.php ENDPATH**/ ?>