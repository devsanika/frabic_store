<script>
<?php if (!empty($success_msg)): ?>
    <?php foreach ($success_msg as $msg): ?>
    Swal.fire({
        title: 'Success!',
        text: '<?php echo addslashes(htmlspecialchars($msg)); ?>',
        icon: 'success',
        confirmButtonColor: '#2ecc71',
        confirmButtonText: 'OK',
        timer: 3000,
        timerProgressBar: true
    });
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($warning_msg)): ?>
    <?php foreach ($warning_msg as $msg): ?>
    Swal.fire({
        title: 'Warning!',
        text: '<?php echo addslashes(htmlspecialchars($msg)); ?>',
        icon: 'warning',
        confirmButtonColor: '#e67e22',
        confirmButtonText: 'OK'
    });
    <?php endforeach; ?>
<?php endif; ?>
</script>