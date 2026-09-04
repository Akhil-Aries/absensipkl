<?php $__env->startSection('content'); ?>
    <header class="header">
        <div class="header-top">
            <h1>Rekap Absensi</h1>
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
        <p id="current-date">Panel admin</p>
    </header>

    <div class="navbar">
        <span class="user-info">Login sebagai <strong><?php echo e(auth()->user()->name); ?></strong> (Admin)</span>
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
        
        <div class="stat-cards">
            <div class="stat-card stat-card--total">
                <div class="stat-card__icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div class="stat-card__body">
                    <span class="stat-card__value"><?php echo e($totalSiswa); ?></span>
                    <span class="stat-card__label">Total Siswa</span>
                </div>
            </div>

            <div class="stat-card stat-card--masuk">
                <div class="stat-card__icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                        <polyline points="10 17 15 12 10 7"/>
                        <line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                </div>
                <div class="stat-card__body">
                    <span class="stat-card__value"><?php echo e($absenMasukHariIni); ?></span>
                    <span class="stat-card__label">Masuk Hari Ini</span>
                </div>
            </div>

            <div class="stat-card stat-card--pulang">
                <div class="stat-card__icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                </div>
                <div class="stat-card__body">
                    <span class="stat-card__value"><?php echo e($absenPulangHariIni); ?></span>
                    <span class="stat-card__label">Pulang Hari Ini</span>
                </div>
            </div>

            <div class="stat-card stat-card--log">
                <div class="stat-card__icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                    </svg>
                </div>
                <div class="stat-card__body">
                    <span class="stat-card__value"><?php echo e($totalLogHariIni); ?></span>
                    <span class="stat-card__label">Total Log Hari Ini</span>
                </div>
            </div>
        </div>

        
        <div class="filter-export-row">
            <form method="GET" action="<?php echo e(route('admin.dashboard')); ?>" class="filter-form">
                <input type="text" name="nama" placeholder="Cari nama siswa..." value="<?php echo e($filterNama); ?>">
                <input type="date" name="tanggal" value="<?php echo e($filterTanggal); ?>">
                <button type="submit">Filter</button>
                <?php if($filterNama || $filterTanggal): ?>
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn-logout" style="text-decoration:none; display:flex; align-items:center;">Reset</a>
                <?php endif; ?>
            </form>
            <a href="<?php echo e(route('admin.export', request()->only(['nama', 'tanggal']))); ?>" class="btn-export">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Export CSV
            </a>
        </div>

        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Jenis</th>
                        <th>Foto</th>
                        <th>Lokasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $absensis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td data-label="Nama"><?php echo e($record->user->name); ?></td>
                            <td data-label="Tanggal"><?php echo e(\Carbon\Carbon::parse($record->tanggal)->translatedFormat('d M Y')); ?></td>
                            <td data-label="Jam"><?php echo e(\Carbon\Carbon::parse($record->jam)->format('H:i')); ?></td>
                            <td data-label="Jenis">
                                <span class="badge <?php echo e($record->type === 'Pulang' ? 'badge-pulang' : 'badge-masuk'); ?>">
                                    <?php echo e($record->type); ?>

                                </span>
                            </td>
                            <td data-label="Foto">
                                <?php if($record->foto): ?>
                                    <button type="button" class="btn-admin-photo" data-src="<?php echo e(asset('storage/'.$record->foto)); ?>" data-user="<?php echo e($record->user->name); ?>" data-time="<?php echo e(\Carbon\Carbon::parse($record->jam)->format('H:i')); ?> - <?php echo e(\Carbon\Carbon::parse($record->tanggal)->translatedFormat('d M Y')); ?> (Absen <?php echo e($record->type); ?>)" title="Klik untuk memperbesar">
                                        <img src="<?php echo e(asset('storage/'.$record->foto)); ?>" alt="Foto <?php echo e($record->user->name); ?>" class="admin-photo-thumb">
                                    </button>
                                <?php else: ?>
                                    <span style="opacity: 0.4;">-</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Lokasi">
                                <?php if($record->latitude && $record->longitude): ?>
                                    <a href="https://www.google.com/maps/search/?api=1&query=<?php echo e($record->latitude); ?>,<?php echo e($record->longitude); ?>" target="_blank" class="location-link">Lihat</a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="empty-state">Belum ada data absensi.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <?php echo e($absensis->links()); ?>

        </div>

        
        <div id="admin-photo-lightbox" class="modal-backdrop" style="display: none;">
            <div class="photo-lightbox-content">
                <div class="photo-lightbox-header">
                    <span id="admin-lightbox-caption">Foto Selfie</span>
                    <button type="button" id="btn-close-admin-lightbox" class="btn-modal-close">&times;</button>
                </div>
                <div class="photo-lightbox-body">
                    <img id="admin-lightbox-img" src="" alt="Foto Siswa">
                </div>
            </div>
        </div>
    </main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<style>
/* ===== Stat Cards ===== */
.stat-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.stat-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem 1.5rem;
    border-radius: 16px;
    background: var(--card-bg, rgba(255,255,255,.08));
    backdrop-filter: blur(12px);
    border: 1px solid var(--card-border, rgba(255,255,255,.12));
    transition: transform .2s ease, box-shadow .2s ease;
}
.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,.15);
}
.stat-card__icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    border-radius: 12px;
    flex-shrink: 0;
}
.stat-card--total .stat-card__icon  { background: rgba(99,102,241,.18); color: #818cf8; }
.stat-card--masuk .stat-card__icon  { background: rgba(16,185,129,.18); color: #34d399; }
.stat-card--pulang .stat-card__icon { background: rgba(245,158,11,.18); color: #fbbf24; }
.stat-card--log .stat-card__icon    { background: rgba(236,72,153,.18); color: #f472b6; }

.stat-card__body {
    display: flex;
    flex-direction: column;
}
.stat-card__value {
    font-size: 1.75rem;
    font-weight: 800;
    line-height: 1;
}
.stat-card__label {
    font-size: .8rem;
    opacity: .65;
    margin-top: .25rem;
}

/* ===== Filter + Export Row ===== */
.filter-export-row {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1rem;
}
.filter-export-row .filter-form {
    margin-bottom: 0;
}
.btn-export {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    padding: .6rem 1.2rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: .85rem;
    text-decoration: none;
    color: #fff;
    background: linear-gradient(135deg, #10b981, #059669);
    transition: opacity .2s ease, transform .15s ease;
    white-space: nowrap;
}
.btn-export:hover {
    opacity: .85;
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 600px) {
    .stat-cards { grid-template-columns: repeat(2, 1fr); }
    .filter-export-row { flex-direction: column; align-items: stretch; }
    .btn-export { justify-content: center; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const lightboxModal = document.getElementById('admin-photo-lightbox');
    const lightboxImg = document.getElementById('admin-lightbox-img');
    const lightboxCaption = document.getElementById('admin-lightbox-caption');
    const btnCloseLightbox = document.getElementById('btn-close-admin-lightbox');

    document.querySelectorAll('.btn-admin-photo').forEach(btn => {
        btn.addEventListener('click', () => {
            const src = btn.getAttribute('data-src');
            const user = btn.getAttribute('data-user');
            const time = btn.getAttribute('data-time');

            if (lightboxModal && lightboxImg) {
                lightboxImg.src = src;
                if (lightboxCaption) lightboxCaption.textContent = `${user} • ${time}`;
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
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', ['title' => 'Rekap Absensi - Admin', 'wide' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\php-8.5.9-Win32-vs17-x64\project\absensi-pkl-laravel\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>