<?php
declare(strict_types=1);

// Database credentials live in /inc/config.php (not in the repo).
require $_SERVER['DOCUMENT_ROOT'] . '/inc/config.php';

error_reporting(E_ALL);
ini_set('display_errors', '1'); // set to '0' once the form is working

session_start();

// ---------------------------------------------------------------------------
// Addresses (full URLs)
// ---------------------------------------------------------------------------
$form_url    = 'https://not.inouter.space/apply/index.php';
$thanks_url  = 'https://not.inouter.space/apply/thanks.php';

// Longest value accepted for name, email and question 1.
// Match this to the VARCHAR sizes in membership_submissions.
$max_text = 255;

// Allowed answers, copied from the form.
$yes_no         = ['Yes', 'No'];
$yes_no_unknown = ['Yes', 'No', 'Unknown'];

$have_hope_options = [
    'Skills', 'Donations', 'Land', 'Buildings or Facilities', 'Equipment',
    'Marketing and/or Advertising', 'Sponsorship', 'Publications', 'Research',
    'Technology', 'Staff', 'Additional Resources', 'Online Presence',
    'Online Influence', 'Other Influence', 'Authority',
];

$need_hope_options = [
    'Any above', 'Hope Emblem', 'Additional Hope Button Stickers', 'Web Graphics',
    'Merchandise', 'Media Contacts', 'Counseling', 'Other Professional Services',
    'Peer Review', 'Publishing', 'Video Production', 'Editing', 'Copywriting',
    'Additional Staff or Consulting',
];

// ---------------------------------------------------------------------------
// What the page will show. The HTML at the bottom reads these.
//   'invalid' = answers need fixing,  'failed' = database error
// ---------------------------------------------------------------------------
$state  = '';
$errors = [];

// Anyone arriving here without submitting the form goes back to the form.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $form_url);
    exit();
}

// ---------------------------------------------------------------------------
// Read and check the answers (only on a form submission)
// ---------------------------------------------------------------------------
if ($state === '') {

    // Text fields: anything that is not plain text (e.g. an array) counts as empty.
    $full_name = is_string($_POST['full_name'] ?? null) ? trim($_POST['full_name']) : '';
    $email     = is_string($_POST['email'] ?? null)     ? trim($_POST['email'])     : '';
    $q1_saving = is_string($_POST['q1_saving'] ?? null) ? trim($_POST['q1_saving']) : '';
    $anonymize_name = isset($_POST['anonymize_name']) ? 1 : 0;

    // Questions 2-8: field name => [question number, allowed answers]
    $yes_no_fields = [
        'q2_told_seven'                => ['2', $yes_no],
        'q3_got_emails'                => ['3', $yes_no],
        'q4_others_told_seven'         => ['4', $yes_no_unknown],
        'q5_can_save'                  => ['5', $yes_no],
        'q6_will_save'                 => ['6', $yes_no],
        'q7_available_till_completion' => ['7', $yes_no],
        'q8_see_until_launch'          => ['8', $yes_no],
    ];

    // Checkbox groups: keep only options that exist on the form.
    $have_hope = [];
    if (is_array($_POST['have_hope'] ?? null)) {
        foreach ($_POST['have_hope'] as $choice) {
            if (is_string($choice) && in_array($choice, $have_hope_options, true) && !in_array($choice, $have_hope, true)) {
                $have_hope[] = $choice;
            }
        }
    }
    $need_hope = [];
    if (is_array($_POST['need_hope'] ?? null)) {
        foreach ($_POST['need_hope'] as $choice) {
            if (is_string($choice) && in_array($choice, $need_hope_options, true) && !in_array($choice, $need_hope, true)) {
                $need_hope[] = $choice;
            }
        }
    }

    if ($full_name === '') {
        $errors[] = 'Please enter your full name.';
    } elseif (mb_strlen($full_name, 'UTF-8') > $max_text) {
        $errors[] = 'Your name is too long (maximum ' . $max_text . ' characters).';
    }

    if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $errors[] = 'Please enter a valid email address.';
    } elseif (mb_strlen($email, 'UTF-8') > $max_text) {
        $errors[] = 'Your email address is too long (maximum ' . $max_text . ' characters).';
    }

    if ($q1_saving === '') {
        $errors[] = 'Please answer question 1.';
    } elseif (mb_strlen($q1_saving, 'UTF-8') > $max_text) {
        $errors[] = 'Your answer to question 1 is too long (maximum ' . $max_text . ' characters).';
    }

    $answers = [];
    foreach ($yes_no_fields as $field => [$number, $allowed]) {
        $answers[$field] = is_string($_POST[$field] ?? null) ? trim($_POST[$field]) : '';
        if (!in_array($answers[$field], $allowed, true)) {
            $errors[] = 'Please answer question ' . $number . '.';
        }
    }

    if ($errors !== []) {
        $state = 'invalid';
        http_response_code(400);
    }
}

// ---------------------------------------------------------------------------
// Save (only when every answer passed)
// ---------------------------------------------------------------------------
if ($state === '') {
    try {
        $pdo = new PDO(
            "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
            $db_user,
            $db_pass,
            [
                PDO::ATTR_ERRMODE          => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );

        $stmt = $pdo->prepare(
            'INSERT INTO membership_submissions (
                full_name, email, anonymize_name,
                q1_saving, q2_told_seven, q3_got_emails, q4_others_told_seven,
                q5_can_save, q6_will_save, q7_available_till_completion, q8_see_until_launch,
                have_hope, need_hope
            ) VALUES (
                :full_name, :email, :anonymize_name,
                :q1_saving, :q2_told_seven, :q3_got_emails, :q4_others_told_seven,
                :q5_can_save, :q6_will_save, :q7_available_till_completion, :q8_see_until_launch,
                :have_hope, :need_hope
            )'
        );

        $stmt->bindValue(':full_name', $full_name);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':anonymize_name', $anonymize_name, PDO::PARAM_INT);
        $stmt->bindValue(':q1_saving', $q1_saving);
        foreach ($answers as $field => $value) {
            $stmt->bindValue(':' . $field, $value);
        }
        $stmt->bindValue(':have_hope', $have_hope !== [] ? json_encode($have_hope) : null);
        $stmt->bindValue(':need_hope', $need_hope !== [] ? json_encode($need_hope) : null);

        $stmt->execute();

        // Redirect so refreshing the thank-you page cannot save a second copy.
        $_SESSION['hope_thanks_name'] = $full_name;
        header('Location: ' . $thanks_url, true, 303);
        exit();

    } catch (Throwable $ex) {
        error_log('Membership form error: ' . get_class($ex) . ': ' . $ex->getMessage()
            . ' in ' . $ex->getFile() . ':' . $ex->getLine());
        $state = 'failed';
        http_response_code(500);
    }
}

// ---------------------------------------------------------------------------
// Page (only reached when the answers need fixing or the save failed)
// ---------------------------------------------------------------------------
$page_title = $state === 'invalid' ? 'Please Check Your Answers' : 'Something Went Wrong';
include $_SERVER['DOCUMENT_ROOT'] . '/inc/header.php';
include $_SERVER['DOCUMENT_ROOT'] . '/inc/nav.php';
?>
    <div class="card">
<?php if ($state === 'invalid'): ?>
        <h1>Please Check Your Answers</h1>
        <ul>
<?php foreach ($errors as $message): ?>
            <li><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></li>
<?php endforeach; ?>
        </ul>
<?php else: ?>
        <h1>Something Went Wrong</h1>
        <p>Sorry, an unexpected error occurred while saving your response. Please try again later.</p>
<?php endif; ?>
        <a class="back" href="https://not.inouter.space/apply/">&larr; Return to Form</a>
    </div>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/inc/footer.php'; ?>
