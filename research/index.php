<?php
$page_title = 'Research Files';
include_once '/home/artmagproject/not.inouter.space/inc/header.php';
include_once '/home/artmagproject/not.inouter.space/inc/nav.php';

// The folder this page lists.
$list_dir = '/home/artmagproject/not.inouter.space/research/';
$list_url = 'https://not.inouter.space/research/';
$sort_by  = 'name';   // 'name' = A to Z, 'date' = newest first

include_once '/home/artmagproject/not.inouter.space/inc/filelist.php';
?>
<div class="page filelist">
    <header class="page-header">
        <h1><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></h1>
<?php include '/home/artmagproject/not.inouter.space/content/research_intro.php'; ?>
    </header>
<?php include '/home/artmagproject/not.inouter.space/content/filelist.php'; ?>
</div>
<?php include_once '/home/artmagproject/not.inouter.space/inc/footer.php'; ?>
