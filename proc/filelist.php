<?php
// ===========================================================================
// H.O.P.E. FILE LIST
// Reads one folder and shows every file in it, updating on its own as files
// are added or removed.
// The folder's index.php sets, before including this:
//   $list_dir  = full server path of the folder, with trailing slash
//   $list_url  = full web address of the folder, with trailing slash
// ===========================================================================

// Hide files by extension (lowercase, no dot). PHP files are hidden so that
// scripts holding passwords are never advertised.
$hidden_extensions = ['php', 'htaccess', 'ini', 'log', 'sql'];

// 'name' = A to Z, 'date' = newest first
if (!isset($sort_by)) {
    $sort_by = 'name';
}

// Files with these extensions get a player under their name instead of just a link.
$audio_extensions = ['mp3', 'wav', 'ogg', 'oga', 'm4a', 'aac', 'flac', 'opus'];
$video_extensions = ['mp4', 'webm', 'ogv', 'm4v', 'mov'];

// Images and PDFs get a small preview beside their name.
$image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg', 'bmp'];
$pdf_previews     = true;     // false = PDFs show as a plain link

// Which side the previews sit on: 'left' or 'right'
$thumb_side = 'left';

// Size units, used to turn bytes into "52.7 MB".
$size_units = ['B', 'KB', 'MB', 'GB'];

$folders    = [];
$files      = [];
$total_size = 0;
$has_pdfs   = false;

foreach (scandir($list_dir) ?: [] as $name) {
    if ($name[0] === '.') {
        continue; // hidden files, and the . and .. entries
    }
    $path = $list_dir . $name;

    if (is_dir($path)) {
        $folders[] = $name;
        continue;
    }
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    if (in_array($ext, $hidden_extensions, true)) {
        continue;
    }
    if (in_array($ext, $audio_extensions, true)) {
        $media = 'audio';
    } elseif (in_array($ext, $video_extensions, true)) {
        $media = 'video';
    } elseif (in_array($ext, $image_extensions, true)) {
        $media = 'image';
    } elseif ($ext === 'pdf' && $pdf_previews) {
        $media = 'pdf';
        $has_pdfs = true;
    } else {
        $media = '';
    }

    $bytes = (int) filesize($path);
    $total_size += $bytes;

    // Bytes to "52.7 MB"
    $size = (float) $bytes;
    $unit = 0;
    while ($size >= 1024 && $unit < count($size_units) - 1) {
        $size /= 1024;
        $unit++;
    }
    $size_text = ($unit === 0 ? (string) $bytes : number_format($size, 1)) . ' ' . $size_units[$unit];

    $files[$name] = [
        'name'  => $name,
        'url'   => $list_url . rawurlencode($name),
        'media' => $media,
        'type'  => $ext !== '' ? strtoupper($ext) : '—',
        'size'  => $size_text,
        'time'  => (int) filemtime($path),
    ];
}

natcasesort($folders);
if ($sort_by === 'date') {
    array_multisort(array_column($files, 'time'), SORT_DESC, $files);
} else {
    uksort($files, 'strnatcasecmp');
}

// Total size of all files, as "370.6 MB"
$size = (float) $total_size;
$unit = 0;
while ($size >= 1024 && $unit < count($size_units) - 1) {
    $size /= 1024;
    $unit++;
}
$total_text = ($unit === 0 ? (string) $total_size : number_format($size, 1)) . ' ' . $size_units[$unit];
?>
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
