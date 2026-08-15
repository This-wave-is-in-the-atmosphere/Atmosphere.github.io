<?php
// mobile/docs.php — 移动版文档索引（扫描主仓库全部 md 文档）
ob_start();
$src = __DIR__ . '/../_atmosphere-src';
$items = [];
if (is_dir($src)) {
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($src, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($it as $file) {
        if (!$file->isFile()) continue;
        if (strtolower($file->getExtension()) !== 'md') continue;
        $rel = substr($file->getPathname(), strlen($src) + 1);
        if (strpos($rel, '.git/') === 0) continue;
        $items[] = ['rel' => $rel, 'name' => $file->getFilename(), 'date' => date('Y-m-d', $file->getMTime())];
    }
    usort($items, function ($a, $b) { return strcmp($b['date'], $a['date']); });
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文档索引 · AtmoWave 移动版</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f5f6f8; color: #333; -webkit-text-size-adjust: 100%; }
        .topbar { position: sticky; top: 0; z-index: 10; background: #0e1a26; padding: 12px 14px; display: flex; align-items: center; justify-content: space-between; }
        .topbar a { color: #4fc3f7; text-decoration: none; font-size: 13px; }
        .topbar .t { color: #e0e0e0; font-size: 14px; font-weight: 600; }
        .list { padding: 10px 12px 30px; max-width: 720px; margin: 0 auto; }
        .list a { display: block; background: #fff; border: 1px solid #e0e0e0; border-radius: 10px; padding: 12px 14px; margin: 8px 0; color: #333; text-decoration: none; }
        .list a:active { background: #eef4f8; }
        .list .t { font-size: 14px; font-weight: 600; }
        .list .p { font-size: 11px; color: #8a949e; margin-top: 3px; word-break: break-all; }
        .count { text-align: center; font-size: 12px; color: #8a949e; padding: 6px 0 0; }
        .footer { text-align: center; padding: 16px 14px 24px; font-size: 12px; color: #8a949e; }
    </style>
</head>
<body>
    <div class="topbar">
        <a href="index.html">← 返回首页</a>
        <span class="t">📄 全部文档</span>
        <a href="../docs.html">电脑版</a>
    </div>
    <div class="count">共 <?php echo count($items); ?> 篇文档 · 来自 GitHub 自动同步</div>
    <div class="list">
        <?php foreach ($items as $it):
            $title = htmlspecialchars(pathinfo($it['name'], PATHINFO_FILENAME));
            $href = 'view.php?src=main&path=' . rawurlencode($it['rel']);
        ?>
        <a href="<?php echo $href; ?>">
            <div class="t"><?php echo $title; ?></div>
            <div class="p"><?php echo $it['date']; ?> · <?php echo htmlspecialchars($it['rel']); ?></div>
        </a>
        <?php endforeach; ?>
    </div>
    <div class="footer">
        <p>AtmoWave Technology · 移动版</p>
    </div>
</body>
</html>
