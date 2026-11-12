<?php
// ===========================================================================
// H.O.P.E. VIDEO GALLERY
// Shows every approved cause: each button in /sharethis/buttons/ named
// savethe<cause>.png. Causes with a video show the video; the rest show the
// button until the video is made. No database.
// ===========================================================================

$page_title = 'Video Gallery';
include_once '/home/artmagproject/not.inouter.space/inc/header.php';
include_once '/home/artmagproject/not.inouter.space/inc/nav.php';

// Where the media lives (full addresses, trailing slash).
$videos_url  = 'https://not.inouter.space/sharethis/videos/';
$buttons_url = 'https://not.inouter.space/sharethis/buttons/';   // savethe<cause>.png = video cover

// Cause pages: https://not.inouter.space/causes/?save=<cause>
$cause_pages_url = 'https://not.inouter.space/causes/?save=';

// The folders as the server sees them.
$buttons_dir = '/home/artmagproject/not.inouter.space/sharethis/buttons/';
$videos_dir  = '/home/artmagproject/not.inouter.space/sharethis/videos/';

// Used only when the folder above can't be read. List the causes by hand here.
$manual_causes = ['bees', 'birds', 'gorrilla', 'oceans', 'bats', 'music'];

// ===========================================================================

$found = [];
foreach (glob($buttons_dir . 'savethe*.png') ?: [] as $path) {
    $found[] = substr(basename($path, '.png'), strlen('savethe'));
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
        'has_video' => is_file($videos_dir . $base . '.mp4'),
        'video'  => $videos_url . $base . '.mp4',
        'poster' => $buttons_url . $base . '.png',
        'page'   => $cause_pages_url . $cause,
    ];
}

include_once '/home/artmagproject/not.inouter.space/content/gallery.php';
include_once '/home/artmagproject/not.inouter.space/inc/footer.php';
