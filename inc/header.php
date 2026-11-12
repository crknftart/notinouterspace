<?php
// ===========================================================================
// Included first by every page:
//   $page_title = '...';   (optional, before this include)
//   include_once '/home/artmagproject/not.inouter.space/inc/header.php';
// Loads the database settings, starts the session, and opens the page.
// ===========================================================================

require_once '/home/artmagproject/not.inouter.space/inc/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hold the page in memory until it is finished, so a page can still redirect
// after this header has been included. Before redirecting, a page calls
// ob_end_clean() to drop what was held.
ob_start();

if (!isset($page_title)) {
    $page_title = 'H.O.P.E.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?> - H.O.P.E.</title>
    <link rel="stylesheet" href="https://not.inouter.space/inc/style.css">
</head>
<body>
