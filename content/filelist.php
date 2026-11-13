    <p class="total"><?php echo count($files); ?> <?php echo count($files) === 1 ? 'file' : 'files'; ?><?php if ($files): ?>, <?php echo $total_text; ?><?php endif; ?></p>

<?php if (!$files && !$folders): ?>
    <p class="empty">No files yet.</p>
<?php else: ?>
    <table class="<?php echo $thumb_side === 'right' ? 'thumbs-right' : 'thumbs-left'; ?>">
        <thead>
            <tr>
                <th>Name</th>
                <th class="hide-small">Type</th>
                <th class="meta">Size</th>
                <th class="meta hide-small">Modified</th>
            </tr>
        </thead>
        <tbody>
<?php foreach ($folders as $folder): ?>
            <tr>
                <td class="name"><a href="<?php echo htmlspecialchars($list_url . rawurlencode($folder), ENT_QUOTES, 'UTF-8'); ?>/"><?php echo htmlspecialchars($folder, ENT_QUOTES, 'UTF-8'); ?>/</a></td>
                <td class="hide-small"><span class="tag">FOLDER</span></td>
                <td class="meta">—</td>
                <td class="meta hide-small">—</td>
            </tr>
<?php endforeach; ?>
<?php foreach ($files as $file): ?>
<?php $src = htmlspecialchars($file['url'], ENT_QUOTES, 'UTF-8'); ?>
            <tr>
                <td class="name">
<?php if ($file['media'] === 'image'): ?>
                    <div class="file-row">
                        <a class="thumb" href="<?php echo $src; ?>"><img src="<?php echo $src; ?>" alt="" loading="lazy" decoding="async"></a>
                        <a href="<?php echo $src; ?>"><?php echo htmlspecialchars($file['name'], ENT_QUOTES, 'UTF-8'); ?></a>
                    </div>
<?php elseif ($file['media'] === 'pdf'): ?>
                    <div class="file-row">
                        <a class="thumb pdf-thumb" href="<?php echo $src; ?>" data-pdf="<?php echo $src; ?>"><span class="tag">PDF</span></a>
                        <a href="<?php echo $src; ?>"><?php echo htmlspecialchars($file['name'], ENT_QUOTES, 'UTF-8'); ?></a>
                    </div>
<?php else: ?>
                    <a href="<?php echo $src; ?>"><?php echo htmlspecialchars($file['name'], ENT_QUOTES, 'UTF-8'); ?></a>
<?php endif; ?>
<?php if ($file['media'] === 'audio'): ?>
                    <audio class="player" src="<?php echo $src; ?>" controls preload="none"></audio>
<?php elseif ($file['media'] === 'video'): ?>
                    <video class="player" src="<?php echo $src; ?>" controls preload="metadata" playsinline></video>
<?php endif; ?>
                </td>
                <td class="hide-small"><span class="tag"><?php echo htmlspecialchars($file['type'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                <td class="meta"><?php echo $file['size']; ?></td>
                <td class="meta hide-small"><?php echo date('M j, Y', $file['time']); ?></td>
            </tr>
<?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php if ($has_pdfs): ?>
<!-- Draws page 1 of each PDF into its thumbnail box, only once the box scrolls
     into view. If this fails to load, the boxes just keep their "PDF" label. -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
(function () {
    if (!window.pdfjsLib) return;
    pdfjsLib.GlobalWorkerOptions.workerSrc =
        'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    function render(box) {
        pdfjsLib.getDocument(box.dataset.pdf).promise
            .then(function (pdf) { return pdf.getPage(1); })
            .then(function (page) {
                var size     = 72 * (window.devicePixelRatio || 1);
                var base     = page.getViewport({ scale: 1 });
                var viewport = page.getViewport({ scale: size / Math.max(base.width, base.height) });
                var canvas   = document.createElement('canvas');
                canvas.width  = viewport.width;
                canvas.height = viewport.height;
                return page.render({ canvasContext: canvas.getContext('2d'), viewport: viewport }).promise
                    .then(function () { box.innerHTML = ''; box.appendChild(canvas); });
            })
            .catch(function () { /* leave the PDF label */ });
    }

    var boxes = document.querySelectorAll('.pdf-thumb');
    if (!('IntersectionObserver' in window)) { boxes.forEach(render); return; }
    var watcher = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) { watcher.unobserve(entry.target); render(entry.target); }
        });
    }, { rootMargin: '200px' });
    boxes.forEach(function (box) { watcher.observe(box); });
})();
</script>
<?php endif; ?>
