<?php
$page_title = 'Join H.O.P.E.';
include_once '/home/artmagproject/not.inouter.space/inc/header.php';
include_once '/home/artmagproject/not.inouter.space/inc/nav.php';

// After a successful save, /proc/process_membership.php stores the applicant's
// name in the session and sends them back here. If it is there, show the thanks
// instead of the form. It is removed after one view, so a refresh shows the form.
$thanks_name = '';
$form_errors = [];
if (isset($_SESSION['hope_form_errors']) && is_array($_SESSION['hope_form_errors'])) {
    $form_errors = $_SESSION['hope_form_errors'];
    unset($_SESSION['hope_form_errors']);
}
if (isset($_SESSION['hope_thanks_name']) && is_string($_SESSION['hope_thanks_name'])) {
    $thanks_name = $_SESSION['hope_thanks_name'];
    unset($_SESSION['hope_thanks_name']);
}

// Approved causes (each has a button in /sharethis/buttons/), offered as
// suggestions on question 1. People can still type a new one.
$approved_causes = [];
foreach (glob('/home/artmagproject/not.inouter.space/sharethis/buttons/savethe*.png') ?: [] as $path) {
    $approved_causes[] = ucfirst(substr(basename($path, '.png'), strlen('savethe')));
}

?>
<?php if ($thanks_name !== ''): ?>
<?php include '/home/artmagproject/not.inouter.space/content/thanks.php'; ?>
<?php else: ?>
<div id="formResult">
<?php if ($form_errors): ?>
<?php include '/home/artmagproject/not.inouter.space/content/form_errors.php'; ?>
<?php endif; ?>
</div>
<div class="form-container">
    <header class="form-header">
        <h1>H.O.P.E. Membership Form</h1>
        <p>Humans on Planet Earth — Save One Thing</p>
    </header>

    <form method="post" action="https://not.inouter.space/proc/process_membership.php" id="membershipForm">
        
        <!-- SECTION 0: CONTACT INFORMATION -->
        <div class="form-section">
            <h2 class="section-title">Contact Information</h2>

            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" required placeholder="John Doe">
                <label class="checkbox-inline">
                    <input type="checkbox" name="anonymize_name" value="1">
                    Please anonymize my name
                </label>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="you@example.com">
                <div class="privacy-disclaimer">
                    🔒 <strong>Privacy Assurance:</strong> We respect your privacy. We will never share, sell, or disclose your email address to third parties.
                </div>
            </div>
        </div>

        <!-- SECTION 1: QUESTIONNAIRE -->
        <div class="form-section">
            <h2 class="section-title">Initial Questionnaire</h2>

            <div class="form-group">
                <label for="q1">1. What are you saving?</label>
                <input type="text" id="q1" name="q1_saving" required placeholder="Start typing to see current causes..." list="approved-causes" autocomplete="off">
                <datalist id="approved-causes">
<?php foreach ($approved_causes as $cause): ?>
                    <option value="<?php echo htmlspecialchars($cause, ENT_QUOTES, 'UTF-8'); ?>">
<?php endforeach; ?>
                </datalist>
            </div>

            <div class="form-group">
                <label for="q2">2. Have you told seven others?</label>
                <select id="q2" name="q2_told_seven" required>
                    <option value="" disabled selected>Select an option</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>

            <div class="form-group">
                <label for="q3">3. Did you get their emails?</label>
                <select id="q3" name="q3_got_emails" required>
                    <option value="" disabled selected>Select an option</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>

            <div class="form-group">
                <label for="q4">4. Have they told seven people?</label>
                <select id="q4" name="q4_others_told_seven" required>
                    <option value="" disabled selected>Select an option</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                    <option value="Unknown">Unknown</option>
                </select>
            </div>

            <div class="form-group">
                <label for="q5">5. Can you save this?</label>
                <select id="q5" name="q5_can_save" required>
                    <option value="" disabled selected>Select an option</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>

            <div class="form-group">
                <label for="q6">6. Will you save this?</label>
                <select id="q6" name="q6_will_save" required>
                    <option value="" disabled selected>Select an option</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>

            <div class="form-group">
                <label for="q7">7. Are you available till completion?</label>
                <select id="q7" name="q7_available_till_completion" required>
                    <option value="" disabled selected>Select an option</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>

            <div class="form-group">
                <label for="q8">8. Will you see the campaign until the launch?</label>
                <select id="q8" name="q8_see_until_launch" required>
                    <option value="" disabled selected>Select an option</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <!-- SECTION 2: HAVE H.O.P.E. (OPTIONAL) -->
        <div class="form-section">
            <h2 class="section-title">HAVE H.O.P.E. (Optional)</h2>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 12px;">Select all resources, skills, or assets you currently possess:</p>
            
            <div class="checkbox-grid">
                <label class="checkbox-item"><input type="checkbox" name="have_hope[]" value="Skills"><span>Skills</span></label>
                <label class="checkbox-item"><input type="checkbox" name="have_hope[]" value="Donations"><span>Donations</span></label>
                <label class="checkbox-item"><input type="checkbox" name="have_hope[]" value="Land"><span>Land</span></label>
                <label class="checkbox-item"><input type="checkbox" name="have_hope[]" value="Buildings or Facilities"><span>Buildings or Facilities</span></label>
                <label class="checkbox-item"><input type="checkbox" name="have_hope[]" value="Equipment"><span>Equipment</span></label>
                <label class="checkbox-item"><input type="checkbox" name="have_hope[]" value="Marketing and/or Advertising"><span>Marketing / Advertising</span></label>
                <label class="checkbox-item"><input type="checkbox" name="have_hope[]" value="Sponsorship"><span>Sponsorship</span></label>
                <label class="checkbox-item"><input type="checkbox" name="have_hope[]" value="Publications"><span>Publications</span></label>
                <label class="checkbox-item"><input type="checkbox" name="have_hope[]" value="Research"><span>Research</span></label>
                <label class="checkbox-item"><input type="checkbox" name="have_hope[]" value="Technology"><span>Technology</span></label>
                <label class="checkbox-item"><input type="checkbox" name="have_hope[]" value="Staff"><span>Staff</span></label>
                <label class="checkbox-item"><input type="checkbox" name="have_hope[]" value="Additional Resources"><span>Additional Resources</span></label>
                <label class="checkbox-item"><input type="checkbox" name="have_hope[]" value="Online Presence"><span>Online Presence</span></label>
                <label class="checkbox-item"><input type="checkbox" name="have_hope[]" value="Online Influence"><span>Online Influence</span></label>
                <label class="checkbox-item"><input type="checkbox" name="have_hope[]" value="Other Influence"><span>Other Influence</span></label>
                <label class="checkbox-item"><input type="checkbox" name="have_hope[]" value="Authority"><span>Authority</span></label>
            </div>
        </div>

        <!-- SECTION 3: NEED H.O.P.E. (OPTIONAL) -->
        <div class="form-section">
            <h2 class="section-title">NEED H.O.P.E. (Optional)</h2>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 12px;">Select all areas where support or resources are needed:</p>
            
            <div class="checkbox-grid">
                <label class="checkbox-item"><input type="checkbox" name="need_hope[]" value="Any above"><span>Any above</span></label>
                <label class="checkbox-item"><input type="checkbox" name="need_hope[]" value="Hope Emblem"><span>Hope Emblem</span></label>
                <label class="checkbox-item"><input type="checkbox" name="need_hope[]" value="Additional Hope Button Stickers"><span>Additional Hope Stickers</span></label>
                <label class="checkbox-item"><input type="checkbox" name="need_hope[]" value="Web Graphics"><span>Web Graphics</span></label>
                <label class="checkbox-item"><input type="checkbox" name="need_hope[]" value="Merchandise"><span>Merchandise</span></label>
                <label class="checkbox-item"><input type="checkbox" name="need_hope[]" value="Media Contacts"><span>Media Contacts</span></label>
                <label class="checkbox-item"><input type="checkbox" name="need_hope[]" value="Counseling"><span>Counseling</span></label>
                <label class="checkbox-item"><input type="checkbox" name="need_hope[]" value="Other Professional Services"><span>Other Professional Services</span></label>
                <label class="checkbox-item"><input type="checkbox" name="need_hope[]" value="Peer Review"><span>Peer Review</span></label>
                <label class="checkbox-item"><input type="checkbox" name="need_hope[]" value="Publishing"><span>Publishing</span></label>
                <label class="checkbox-item"><input type="checkbox" name="need_hope[]" value="Video Production"><span>Video Production</span></label>
                <label class="checkbox-item"><input type="checkbox" name="need_hope[]" value="Editing"><span>Editing</span></label>
                <label class="checkbox-item"><input type="checkbox" name="need_hope[]" value="Copywriting"><span>Copywriting</span></label>
                <label class="checkbox-item"><input type="checkbox" name="need_hope[]" value="Additional Staff or Consulting"><span>Additional Staff / Consulting</span></label>
            </div>
        </div>

        <button type="submit" class="submit-btn">Submit Application</button>
    </form>
</div>
<script src="https://not.inouter.space/inc/form.js"></script>
<?php endif; ?>
<?php include_once '/home/artmagproject/not.inouter.space/inc/footer.php'; ?>
