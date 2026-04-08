<script>
<?php if (!empty($success_msg)): ?>
    <?php foreach ($success_msg as $msg): ?>
    Swal.fire({
        title: 'Done',
        text: '<?php echo addslashes(htmlspecialchars($msg)); ?>',
        icon: 'success',
        confirmButtonColor: '#4A7C5F',
        confirmButtonText: 'Continue',
        timer: 3200,
        timerProgressBar: true,
        customClass: { popup: 'swal-ink' }
    });
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($warning_msg)): ?>
    <?php foreach ($warning_msg as $msg): ?>
    Swal.fire({
        title: 'Notice',
        text: '<?php echo addslashes(htmlspecialchars($msg)); ?>',
        icon: 'warning',
        confirmButtonColor: '#1C1C1C',
        confirmButtonText: 'OK',
        customClass: { popup: 'swal-ink' }
    });
    <?php endforeach; ?>
<?php endif; ?>
</script>
<style>
.swal-ink {
    font-family: 'DM Sans', 'Segoe UI', sans-serif !important;
    border-radius: 8px !important;
    border: 1px solid rgba(28, 28, 28, 0.08) !important;
    box-shadow: 0 8px 40px rgba(28, 28, 28, 0.12) !important;
}
.swal2-title {
    font-family: 'Shippori Mincho', Georgia, serif !important;
    color: #1C1C1C !important;
    font-size: 20px !important;
    font-weight: 600 !important;
    letter-spacing: 0.5px !important;
}
.swal2-content, .swal2-html-container {
    color: #6B6560 !important;
    font-size: 14px !important;
}
.swal2-timer-progress-bar { background: #C6A96B !important; }
.swal2-icon.swal2-success { border-color: #4A7C5F !important; }
.swal2-icon.swal2-success [class^='swal2-success-line'] { background: #4A7C5F !important; }
.swal2-icon.swal2-success .swal2-success-ring { border-color: rgba(74, 124, 95, 0.3) !important; }
.swal2-icon.swal2-warning { border-color: #C6A96B !important; color: #C6A96B !important; }
</style>