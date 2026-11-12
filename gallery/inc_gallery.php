<div class="page">
    <header class="page-header">
        <h1>H.O.P.E. Gallery</h1>
    </header>

<?php if (!$videos): ?>
    <p class="empty">No videos yet.</p>
<?php else: ?>
    <ul class="grid">
<?php foreach ($videos as $video): ?>
        <li>
            <video controls preload="none" poster="<?php echo htmlspecialchars($video['poster'], ENT_QUOTES, 'UTF-8'); ?>">
                <source src="<?php echo htmlspecialchars($video['video'], ENT_QUOTES, 'UTF-8'); ?>" type="video/mp4">
            </video>
            <h2><?php echo htmlspecialchars($video['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
            <div class="links">
                <a href="<?php echo htmlspecialchars($video['page'], ENT_QUOTES, 'UTF-8'); ?>">Cause Page</a>
                <button type="button" class="share" data-title="<?php echo htmlspecialchars($video['title'], ENT_QUOTES, 'UTF-8'); ?>" data-url="<?php echo htmlspecialchars($video['page'], ENT_QUOTES, 'UTF-8'); ?>">Share</button>
                <a href="<?php echo htmlspecialchars($video['video'], ENT_QUOTES, 'UTF-8'); ?>" download>Download</a>
            </div>
        </li>
<?php endforeach; ?>
    </ul>
<?php endif; ?>
</div>

<div class="note" id="note" aria-live="polite"></div>

<script>
    const note   = document.getElementById('note');
    const videos = Array.from(document.querySelectorAll('video'));

    // Starting one video pauses the others.
    videos.forEach(video => video.addEventListener('play', () => {
        videos.forEach(other => { if (other !== video) other.pause(); });
    }));

    function say(text) {
        note.textContent = text;
        note.classList.add('show');
        setTimeout(() => note.classList.remove('show'), 2500);
    }

    document.querySelectorAll('.share').forEach(button => {
        button.addEventListener('click', async () => {
            const url = button.dataset.url;
            if (navigator.share) {
                try { await navigator.share({ title: button.dataset.title + ' - H.O.P.E.', url }); } catch (err) { /* cancelled */ }
                return;
            }
            try {
                await navigator.clipboard.writeText(url);
                say('Link copied.');
            } catch (err) {
                window.prompt('Copy this link:', url);
            }
        });
    });
</script>
