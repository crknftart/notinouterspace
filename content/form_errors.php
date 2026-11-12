    <div class="form-errors">
        <h2>Please Check Your Answers</h2>
        <ul>
<?php foreach ($form_errors as $message): ?>
            <li><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></li>
<?php endforeach; ?>
        </ul>
    </div>
