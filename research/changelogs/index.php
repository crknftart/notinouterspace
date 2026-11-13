<?php
$page_title = 'H.O.P.E. Research Change Logs';

// The folder this page lists.
$list_dir = '/home/artmagproject/not.inouter.space/research/changelogs/';
$list_url = 'https://not.inouter.space/research/changelogs/';
$sort_by  = 'name';   // 'name' = A to Z, 'date' = newest first

include_once '/home/artmagproject/not.inouter.space/inc/header.php';
include_once '/home/artmagproject/not.inouter.space/inc/nav.php';
?>
<div class="page filelist">
    <header class="page-header">
        <h1><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></h1>
<?php include '/home/artmagproject/not.inouter.space/content/changelogs_intro.php'; ?>
    </header>
<?php include '/home/artmagproject/not.inouter.space/proc/filelist.php'; ?>
</div>
<?php include_once '/home/artmagproject/not.inouter.space/inc/footer.php'; ?>
