<?php
// ===========================================================================
// H.O.P.E. FILE LIST (logic)
// Reads one folder and builds $folders and $files for content/filelist.php.
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
