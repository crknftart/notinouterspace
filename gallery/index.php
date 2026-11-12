<?php
// ===========================================================================
// H.O.P.E. VIDEO GALLERY
// Shows every video in /sharethis/videos/ named savethe<cause>.mp4. Upload a
// new video with that naming and it appears here on its own. No database.
// ===========================================================================

// Where the media lives (full addresses, trailing slash).
$videos_url  = 'https://not.inouter.space/sharethis/videos/';
$buttons_url = 'https://not.inouter.space/sharethis/buttons/';   // savethe<cause>.png = video cover

// Where the cause pages live (full address, trailing slash). CONFIRM this folder.
$cause_pages_url = 'https://not.inouter.space/content/';

// The videos folder as the server sees it, used to find the videos.
$videos_dir = $_SERVER['DOCUMENT_ROOT'] . '/sharethis/videos/';

// Used only when the folder above can't be read. List the causes by hand here.
$manual_causes = ['bees', 'birds', 'gorrilla', 'oceans', 'bats', 'music'];

// ===========================================================================

$found = [];
foreach (glob($videos_dir . 'savethe*.mp4') ?: [] as $path) {
    $found[] = substr(basename($path, '.mp4'), strlen('savethe'));
}
if (!$found) {
    $found = $manual_causes;
}

$videos = [];
foreach ($found as $cause) {
    $cause = strtolower(preg_replace('/[^A-Za-z0-9_-]/', '', (string) $cause));
    if ($cause === '') {
        continue;
    }
    $base = 'savethe' . $cause;
    $videos[] = [
        'title'  => 'Save the ' . ucfirst($cause),
        'video'  => $videos_url . $base . '.mp4',
        'poster' => $buttons_url . $base . '.png',
        'page'   => $cause_pages_url . $base . '.php',
    ];
}

$page_title = 'Video Gallery';
include $_SERVER['DOCUMENT_ROOT'] . '/inc/header.php';
include $_SERVER['DOCUMENT_ROOT'] . '/inc/nav.php';
include $_SERVER['DOCUMENT_ROOT'] . '/gallery/inc_gallery.php';
include $_SERVER['DOCUMENT_ROOT'] . '/inc/footer.php';
