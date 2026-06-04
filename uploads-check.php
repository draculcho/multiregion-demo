<?php
$uploadsDir = __DIR__ . '/uploads';
$files = [];

if (is_dir($uploadsDir)) {
    foreach (new DirectoryIterator($uploadsDir) as $item) {
        if ($item->isDot()) continue;
        $files[] = [
            'name'     => $item->getFilename(),
            'size'     => $item->getSize(),
            'mtime'    => date('Y-m-d H:i:s', $item->getMTime()),
            'is_image' => in_array(strtolower($item->getExtension()), ['jpg','jpeg','png','gif','svg','webp']),
        ];
    }
    usort($files, fn($a, $b) => strcmp($a['name'], $b['name']));
}

function formatBytes(int $bytes): string {
    if ($bytes < 1024) return $bytes . ' B';
    if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
    return round($bytes / 1048576, 1) . ' MB';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Uploads — Multi-Region Demo</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: Arial, sans-serif;
      background-color: #1a2332;
      min-height: 100vh;
      display: flex;
      align-items: flex-start;
      justify-content: center;
      padding: 40px 20px;
    }
    .card {
      background: rgba(255,255,255,0.95);
      padding: 40px 50px;
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.3);
      min-width: 500px;
      max-width: 800px;
      width: 100%;
    }
    h1 { color: #232f3e; margin-bottom: 6px; font-size: 22px; }
    .meta { color: #888; font-size: 13px; margin-bottom: 28px; }
    .meta span { color: #ff9900; font-weight: bold; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
    th { text-align: left; color: #888; font-size: 12px; text-transform: uppercase;
         letter-spacing: 1px; padding: 8px 10px; border-bottom: 2px solid #eee; }
    td { padding: 10px; border-bottom: 1px solid #f0f0f0; vertical-align: middle;
         font-size: 14px; color: #232f3e; }
    .fname { font-weight: bold; }
    .size  { color: #888; }
    .mtime { color: #aaa; font-size: 12px; }
    .empty { color: #bbb; font-style: italic; text-align: center; padding: 30px 0; }
    .section-title { font-size: 13px; text-transform: uppercase; letter-spacing: 1px;
                     color: #888; margin: 24px 0 12px; }
    .previews { display: flex; flex-wrap: wrap; gap: 16px; margin-top: 10px; }
    .previews figure { text-align: center; }
    .previews img { display: block; max-width: 200px; max-height: 160px; border-radius: 8px;
                    border: 1px solid #eee; object-fit: contain; background: #fafafa; }
    .previews figcaption { color: #aaa; font-size: 11px; margin-top: 4px; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Shared EFS — /uploads</h1>
    <p class="meta">
      Served by <span><?= htmlspecialchars($_SERVER['SERVER_ADDR'] ?? 'N/A') ?></span>
      &nbsp;|&nbsp; Region: <span>%%AWS_REGION%%</span>
      &nbsp;|&nbsp; <?= count($files) ?> file(s)
    </p>

    <?php if (empty($files)): ?>
      <p class="empty">No files found in /uploads</p>
    <?php else: ?>
      <table>
        <thead>
          <tr><th>File</th><th>Size</th><th>Modified</th></tr>
        </thead>
        <tbody>
          <?php foreach ($files as $f): ?>
          <tr>
            <td class="fname"><?= htmlspecialchars($f['name']) ?></td>
            <td class="size"><?= formatBytes($f['size']) ?></td>
            <td class="mtime"><?= $f['mtime'] ?></td>
          </tr>
          <?php endforeach ?>
        </tbody>
      </table>

      <?php $images = array_filter($files, fn($f) => $f['is_image']); ?>
      <?php if ($images): ?>
        <p class="section-title">Image previews</p>
        <div class="previews">
          <?php foreach ($images as $img): ?>
            <figure>
              <img src="uploads/<?= htmlspecialchars(rawurlencode($img['name'])) ?>"
                   alt="<?= htmlspecialchars($img['name']) ?>">
              <figcaption><?= htmlspecialchars($img['name']) ?></figcaption>
            </figure>
          <?php endforeach ?>
        </div>
      <?php endif ?>
    <?php endif ?>
  </div>
</body>
</html>