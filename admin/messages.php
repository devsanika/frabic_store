<?php
require_once '../components/connect.php';

// Auth check
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Handle Delete
if (isset($_POST['delete_message'])) {
    $mid = filter_var($_POST['message_id'], FILTER_SANITIZE_SPECIAL_CHARS);
    $chk = $conn->prepare("SELECT id FROM message WHERE id = ?");
    $chk->execute([$mid]);
    if ($chk->fetch()) {
        $del = $conn->prepare("DELETE FROM message WHERE id = ?");
        $del->execute([$mid]);
        $_SESSION['success_msg'] = 'Message deleted successfully.';
    } else {
        $_SESSION['warning_msg'] = 'Message not found.';
    }
    header('Location: messages.php');
    exit();
}

// Fetch all messages
$stmt = $conn->prepare("SELECT * FROM message");
$stmt->execute();
$messages = $stmt->fetchAll();

include '../components/admin_header.php';
?>

<div class="page-header">
    <div class="title2">
        <a href="dashboard.php">Dashboard</a>
        <i class='bx bx-chevron-right'></i>
        <span>Messages</span>
    </div>
    <h2 class="heading">Customer Messages</h2>
</div>

<?php if (empty($messages)): ?>
    <div class="empty-state">
        <i class='bx bx-message-dots'></i>
        <h3>No Messages</h3>
        <p>Customer messages will appear here.</p>
    </div>
<?php else: ?>
    <div class="messages-grid">
        <?php foreach ($messages as $msg): ?>
            <div class="message-card">
                <div class="message-card-header">
                    <div class="message-avatar">
                        <?php echo strtoupper(substr($msg['name'], 0, 1)); ?>
                    </div>
                    <div class="message-meta">
                        <h4><?php echo htmlspecialchars($msg['name']); ?></h4>
                        <span class="message-email"><?php echo htmlspecialchars($msg['email']); ?></span>
                    </div>
                    <form method="POST" action="" style="margin-left:auto;">
                        <input type="hidden" name="message_id" value="<?php echo htmlspecialchars($msg['id']); ?>">
                        <button type="submit" name="delete_message" class="btn btn-sm btn-danger"
                                onclick="return confirm('Delete this message permanently?')">
                            <i class='bx bx-trash'></i>
                        </button>
                    </form>
                </div>
                <div class="message-subject">
                    <i class='bx bx-bookmark'></i>
                    <strong><?php echo htmlspecialchars($msg['subject']); ?></strong>
                </div>
                <p class="message-body"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

</div><!-- end .main-content -->
<?php include '../components/alert.php'; ?>
</body>
</html>