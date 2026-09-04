<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title ?? 'Sistem Absensi PKL'); ?></title>
    <meta name="description" content="Sistem absensi PKL berbasis Laravel">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
</head>
<body>
    <div class="background-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>

    <div class="container <?php echo e($wide ?? false ? 'wide' : ''); ?>">
        <?php echo $__env->yieldContent('content'); ?>
    </div>

    <div id="toast" class="toast"><?php echo e(session('success') ?? session('error') ?? ''); ?></div>

    <script src="<?php echo e(asset('js/theme.js')); ?>"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>

    <?php if(session('success')): ?>
        <script>window.addEventListener('DOMContentLoaded', () => showToast(<?php echo json_encode(session('success'), 15, 512) ?>, '#10b981'));</script>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <script>window.addEventListener('DOMContentLoaded', () => showToast(<?php echo json_encode(session('error'), 15, 512) ?>, '#ef4444'));</script>
    <?php endif; ?>
</body>
</html>
<?php /**PATH C:\php-8.5.9-Win32-vs17-x64\project\absensi-pkl-laravel\resources\views/layouts/app.blade.php ENDPATH**/ ?>