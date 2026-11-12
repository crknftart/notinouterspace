<?php
// ===========================================================================
// H.O.P.E. MEMBERS LIST -- every applicant's name and the cause they chose
// ===========================================================================

$page_title = 'Members';
include_once '/home/artmagproject/not.inouter.space/inc/header.php';
include_once '/home/artmagproject/not.inouter.space/inc/nav.php';

// Approved causes have a button here: savethe<cause>.png
$buttons_dir = '/home/artmagproject/not.inouter.space/sharethis/buttons/';

$members    = [];
$data_error = false;

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

    // Email is read only to skip duplicates. It is never displayed.
    $stmt = $pdo->query(
        'SELECT full_name, email, anonymize_name, q1_saving
           FROM membership_submissions
          ORDER BY submitted_at ASC'
    );

    $seen = [];
    while ($row = $stmt->fetch()) {
        $answer = trim((string) $row['q1_saving']);

        // The same person applying twice for the same cause is listed once.
        $dedupe = strtolower(trim((string) $row['email'])) . '|' . strtolower($answer);
        if (isset($seen[$dedupe])) {
            continue;
        }
        $seen[$dedupe] = true;

        // Link the answer to its cause page when the cause is approved.
        // "the bats", "Bats" and "bat" all lead to causes/?save=bats if that button exists.
        $word = strtolower($answer);
        if (substr($word, 0, 4) === 'the ') {
            $word = substr($word, 4);
        }
        $word = preg_replace('/[^a-z0-9-]/', '', $word);
        $page = null;
        if ($word !== '') {
            $singular = substr($word, -1) === 's' ? substr($word, 0, -1) : $word;
            foreach ([$word, $singular, $singular . 's'] as $form) {
                if (is_file($buttons_dir . 'savethe' . $form . '.png')) {
                    $page = 'https://not.inouter.space/causes/?save=' . $form;
                    break;
                }
            }
        }

        $name = trim((string) $row['full_name']);
        $members[] = [
            'name'   => ($row['anonymize_name'] || $name === '') ? 'Anonymous' : $name,
            'answer' => $answer,
            'page'   => $page,
        ];
    }
} catch (Throwable $ex) {
    error_log('Members list error: ' . $ex->getMessage());
    $data_error = true;
}

?>
<div class="page">
    <header class="page-header">
        <h1>H.O.P.E. Members</h1>
    </header>

<?php if ($data_error): ?>
    <p class="empty">The list is unavailable right now.</p>
<?php elseif (!$members): ?>
    <p class="empty">No members yet. <a href="https://not.inouter.space/apply/">Be the first.</a></p>
<?php else: ?>
    <p class="total"><?php echo count($members); ?> <?php echo count($members) === 1 ? 'member' : 'members'; ?></p>
    <ul class="members-grid">
<?php foreach ($members as $member): ?>
        <li>
            <span class="name"><?php echo htmlspecialchars($member['name'], ENT_QUOTES, 'UTF-8'); ?></span>
<?php if ($member['page'] !== null): ?>
            <a href="<?php echo htmlspecialchars($member['page'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($member['answer'], ENT_QUOTES, 'UTF-8'); ?></a>
<?php elseif ($member['answer'] !== ''): ?>
            <span class="muted"><?php echo htmlspecialchars($member['answer'], ENT_QUOTES, 'UTF-8'); ?></span>
<?php endif; ?>
        </li>
<?php endforeach; ?>
    </ul>
<?php endif; ?>
</div>
<?php include_once '/home/artmagproject/not.inouter.space/inc/footer.php'; ?>
