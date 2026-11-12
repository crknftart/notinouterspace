<?php
session_start();

// The name is set by /proc/process_membership.php just before it redirects here.
// It is removed after one view, so a refresh shows the plain version.
$thanks_name = '';
if (isset($_SESSION['hope_thanks_name']) && is_string($_SESSION['hope_thanks_name'])) {
    $thanks_name = $_SESSION['hope_thanks_name'];
    unset($_SESSION['hope_thanks_name']);
}

$page_title = 'Thank You';
include $_SERVER['DOCUMENT_ROOT'] . '/inc/header.php';
include $_SERVER['DOCUMENT_ROOT'] . '/inc/nav.php';
?>
    <div class="card">
        <h1>Submission Received</h1>
<?php if ($thanks_name !== ''): ?>
        <p>Thank you, <strong><?php echo htmlspecialchars($thanks_name, ENT_QUOTES, 'UTF-8'); ?></strong>. Your application has been successfully recorded, and we will respond as soon as possible to communicate as Humans, to provide you with your membership card and other needs. Thank you!</p>
<?php else: ?>
        <p>Your application has been successfully recorded, and we will respond as soon as possible to communicate as Humans, to provide you with your membership card and other needs. Thank you!</p>
<?php endif; ?>
        <a class="back" href="https://not.inouter.space/apply/">&larr; Return to Form</a>
    </div>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/inc/footer.php'; ?>
