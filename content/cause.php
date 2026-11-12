<div class="page cause">
    <header class="page-header">
        <h1><?php echo htmlspecialchars($cause_title, ENT_QUOTES, 'UTF-8'); ?></h1>
    </header>

    <div class="media">
        <!-- LEFT: video, 40% -->
        <div class="video-col">
<?php if ($has_video): ?>
            <video controls preload="metadata" poster="<?php echo htmlspecialchars($emblem_url, ENT_QUOTES, 'UTF-8'); ?>.png">
                <source src="<?php echo htmlspecialchars($video_url, ENT_QUOTES, 'UTF-8'); ?>" type="video/mp4">
            </video>
            <div class="links">
                <a href="<?php echo htmlspecialchars($video_url, ENT_QUOTES, 'UTF-8'); ?>" download>Download</a>
                <button type="button" id="shareBtn">Share</button>
                <button type="button" id="embedBtn">Embed</button>
            </div>
            <div class="note" id="note" aria-live="polite"></div>
            <div class="embed-box" id="embedBox">
                <textarea id="embedCode" readonly><?php echo htmlspecialchars($embed_code, ENT_QUOTES, 'UTF-8'); ?></textarea>
                <div class="links"><button type="button" id="copyEmbed">Copy embed code</button></div>
            </div>
<?php else: ?>
            <p class="in-production">Video in production</p>
<?php endif; ?>
        </div>

        <!-- RIGHT: emblem in the upper left corner -->
        <div class="emblem-col">
            <img class="emblem" src="<?php echo htmlspecialchars($emblem_url, ENT_QUOTES, 'UTF-8'); ?>.png" alt="<?php echo htmlspecialchars($cause_title, ENT_QUOTES, 'UTF-8'); ?> emblem">
            <div class="links">
                <a href="<?php echo htmlspecialchars($emblem_url, ENT_QUOTES, 'UTF-8'); ?>.png" download>Download</a>
<?php if ($has_jpg): ?>
                <a href="<?php echo htmlspecialchars($emblem_url, ENT_QUOTES, 'UTF-8'); ?>.jpg" download>Share Version</a>
<?php endif; ?>
<?php if ($has_svg): ?>
                <a href="<?php echo htmlspecialchars($emblem_url, ENT_QUOTES, 'UTF-8'); ?>.svg" download>Print Version</a>
<?php endif; ?>
            </div>
        </div>
    </div>

    <div class="lists">
        <section>
            <h2>Saving This<?php if ($total): ?> (<?php echo $total; ?>)<?php endif; ?></h2>
<?php if ($data_error): ?>
            <p class="empty">The list is unavailable right now.</p>
<?php elseif ($total === 0): ?>
            <p class="empty">No one yet. <a href="https://not.inouter.space/apply/">Be the first.</a></p>
<?php else: ?>
            <ul>
<?php foreach ($names as $name): ?>
                <li><?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></li>
<?php endforeach; ?>
<?php if ($anonymous > 0): ?>
                <li>Anonymous<?php if ($anonymous > 1): ?><span class="count">&times; <?php echo $anonymous; ?></span><?php endif; ?></li>
<?php endif; ?>
            </ul>
<?php endif; ?>
        </section>

        <section>
            <h2>We Have</h2>
<?php if ($data_error): ?>
            <p class="empty">The list is unavailable right now.</p>
<?php elseif (!$haves): ?>
            <p class="empty">Nothing listed yet.</p>
<?php else: ?>
            <ul>
<?php foreach ($haves as $item => $count): ?>
                <li><?php echo htmlspecialchars($item, ENT_QUOTES, 'UTF-8'); ?><span class="count"><?php echo (int) $count; ?></span></li>
<?php endforeach; ?>
            </ul>
<?php endif; ?>
        </section>

        <section>
            <h2>We Need</h2>
<?php if ($data_error): ?>
            <p class="empty">The list is unavailable right now.</p>
<?php elseif (!$needs): ?>
            <p class="empty">Nothing listed yet.</p>
<?php else: ?>
            <ul>
<?php foreach ($needs as $item => $count): ?>
                <li><?php echo htmlspecialchars($item, ENT_QUOTES, 'UTF-8'); ?><span class="count"><?php echo (int) $count; ?></span></li>
<?php endforeach; ?>
            </ul>
<?php endif; ?>
        </section>
    </div>

    <div class="merch">
<?php if ($merch_url !== ''): ?>
        <a href="<?php echo htmlspecialchars($merch_url, ENT_QUOTES, 'UTF-8'); ?>">Order stickers and merch &rarr;</a>
<?php else: ?>
        <span class="soon">Stickers and merch: coming soon</span>
<?php endif; ?>
    </div>
</div>

<script>
    const pageUrl   = <?php echo json_encode($page_url, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    const pageTitle = <?php echo json_encode($cause_title . ' - H.O.P.E.', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    const note      = document.getElementById('note');

    function say(text) {
        note.textContent = text;
        setTimeout(() => { if (note.textContent === text) note.textContent = ''; }, 3000);
    }

    async function copyText(text, done) {
        try {
            await navigator.clipboard.writeText(text);
            say(done);
        } catch (err) {
            window.prompt('Copy this:', text);
        }
    }

    if (document.getElementById('shareBtn')) {
    document.getElementById('shareBtn').addEventListener('click', async () => {
        if (navigator.share) {
            try { await navigator.share({ title: pageTitle, url: pageUrl }); } catch (err) { /* cancelled */ }
        } else {
            copyText(pageUrl, 'Link copied.');
        }
    });

    document.getElementById('embedBtn').addEventListener('click', () => {
        const box = document.getElementById('embedBox');
        box.style.display = box.style.display === 'block' ? 'none' : 'block';
    });

    document.getElementById('copyEmbed').addEventListener('click', () => {
        copyText(document.getElementById('embedCode').value, 'Embed code copied.');
    });
    }
</script>
