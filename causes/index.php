<?php
// ===========================================================================
// H.O.P.E. CAUSE PAGE
// One page for every cause: https://not.inouter.space/causes/?save=bats
// A cause is approved when its button exists: /sharethis/buttons/savethe<cause>.png
// The video can come later; until then the page says it is in production.
// ===========================================================================

// Shown on every cause page. Leave empty to show "coming soon".
$merch_url = '';

// Which cause: letters, numbers and dashes only, lower case.
$save = strtolower(preg_replace('/[^A-Za-z0-9-]/', '', (string) ($_GET['save'] ?? '')));

// No cause given, or not approved (no button): back to the gallery.
if ($save === '' || !is_file($_SERVER['DOCUMENT_ROOT'] . '/sharethis/buttons/savethe' . $save . '.png')) {
    header('Location: https://not.inouter.space/gallery/');
    exit();
}

$cause_title = 'Save the ' . ucfirst($save);
$file_base   = 'savethe' . $save;

// An application counts toward this cause when its whole answer to
// "What are you saving?" is one of these forms (upper/lower case ignored):
//   bat, bats, the bat, the bats
$singular = substr($save, -1) === 's' ? substr($save, 0, -1) : $save;
$plural   = $singular . 's';
$forms    = [$singular, $plural, 'the ' . $singular, 'the ' . $plural];

require $_SERVER['DOCUMENT_ROOT'] . '/inc/config.php';

// Full addresses for this cause.
$page_url   = 'https://not.inouter.space/causes/?save=' . $save;
$video_url  = 'https://not.inouter.space/sharethis/videos/' . $file_base . '.mp4';
$emblem_url = 'https://not.inouter.space/sharethis/buttons/' . $file_base;   // + .png / .jpg / .svg
// Which files exist yet.
$has_video = is_file($_SERVER['DOCUMENT_ROOT'] . '/sharethis/videos/' . $file_base . '.mp4');
$has_jpg   = is_file($_SERVER['DOCUMENT_ROOT'] . '/sharethis/buttons/' . $file_base . '.jpg');
$has_svg   = is_file($_SERVER['DOCUMENT_ROOT'] . '/sharethis/buttons/' . $file_base . '.svg');

$embed_code = '<video src="' . $video_url . '" poster="' . $emblem_url . '.png" controls width="640"></video>';

// ---------------------------------------------------------------------------
// Gather supporters, haves and needs for this cause
// ---------------------------------------------------------------------------
$names      = [];
$anonymous  = 0;
$haves      = [];
$needs      = [];
$data_error = false;

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

    $where  = [];
    $params = [];
    foreach ($forms as $i => $form) {
        $where[]        = ":f$i";
        $params[":f$i"] = $form;
    }

    if ($where) {
        // Only the columns this page needs. Email is used to count each person once, never shown.
        $stmt = $pdo->prepare(
            'SELECT full_name, email, anonymize_name, have_hope, need_hope
               FROM membership_submissions
              WHERE LOWER(TRIM(q1_saving)) IN (' . implode(', ', $where) . ')
              ORDER BY submitted_at ASC'
        );
        $stmt->execute($params);

        $seen = [];
        while ($row = $stmt->fetch()) {
            // One person who applied twice is counted once.
            $key = strtolower(trim((string) $row['email']));
            if ($key !== '' && isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;

            $name = trim((string) $row['full_name']);
            if ($row['anonymize_name'] || $name === '') {
                $anonymous++;
            } else {
                $names[] = $name;
            }

            // have_hope and need_hope are stored as JSON lists.
            $have_list = json_decode((string) $row['have_hope'], true);
            if (is_array($have_list)) {
                foreach ($have_list as $item) {
                    if (is_string($item)) {
                        $haves[$item] = ($haves[$item] ?? 0) + 1;
                    }
                }
            }
            $need_list = json_decode((string) $row['need_hope'], true);
            if (is_array($need_list)) {
                foreach ($need_list as $item) {
                    if (is_string($item)) {
                        $needs[$item] = ($needs[$item] ?? 0) + 1;
                    }
                }
            }
        }
        arsort($haves);
        arsort($needs);
    }
} catch (Throwable $ex) {
    error_log('Cause page error (' . $file_base . '): ' . $ex->getMessage());
    $data_error = true;
}

$total = count($names) + $anonymous;

$page_title = $cause_title;
include $_SERVER['DOCUMENT_ROOT'] . '/inc/header.php';
include $_SERVER['DOCUMENT_ROOT'] . '/inc/nav.php';
include $_SERVER['DOCUMENT_ROOT'] . '/causes/inc_cause.php';
include $_SERVER['DOCUMENT_ROOT'] . '/inc/footer.php';
