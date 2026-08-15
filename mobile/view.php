<?php
ob_start();
// mobile/view.php — 移动版文档渲染器（手机优先布局）
require_once __DIR__ . '/../Parsedown.php';

$src = isset($_GET['src']) ? $_GET['src'] : 'main';
$path = isset($_GET['path']) ? $_GET['path'] : '';
$base = realpath(__DIR__ . '/../_atmosphere-src');
$real = realpath($base . '/' . $path);
if ($real === false || strpos($real, $base . '/') !== 0 || !preg_match('/\.md$/i', $real)) {
    http_response_code(404); exit('文档不存在');
}
$title = htmlspecialchars(pathinfo($path, PATHINFO_FILENAME));

// 渲染缓存
$cacheDir = __DIR__ . '/../.view-cache';
$cacheFile = $cacheDir . '/m-' . md5($path) . '.html';
if (is_file($cacheFile) && filemtime($cacheFile) >= filemtime($real) && (time() - filemtime($cacheFile)) < 3600) {
    header('Content-Type: text/html; charset=utf-8');
    readfile($cacheFile);
    exit;
}

$pd = new Parsedown();
$body = $pd->text(file_get_contents($real));
// 图片重写
$docDir = dirname($path);
$body = preg_replace_callback('/<img src="([^"]+)"/', function ($m) use ($docDir) {
    $u = $m[1];
    if (preg_match('/^(https?:|data:)/i', $u)) return $m[0];
    $p = ltrim($u, '/');
    if (strpos($u, '/') === 0) { $rel = $p; }
    else {
        $parts = explode('/', $docDir);
        foreach (explode('/', $p) as $seg) {
            if ($seg === '..') { array_pop($parts); }
            elseif ($seg !== '.' && $seg !== '') { $parts[] = $seg; }
        }
        $rel = implode('/', $parts);
    }
    return '<img src="../img.php?src=main&path=' . rawurlencode($rel) . '"';
}, $body);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo $title; ?> · AtmoWave 文档</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f5f6f8; color: #333; line-height: 1.75; -webkit-text-size-adjust: 100%; }
        .topbar { position: sticky; top: 0; z-index: 10; background: #0e1a26; padding: 12px 14px; display: flex; align-items: center; justify-content: space-between; }
        .topbar a { color: #4fc3f7; text-decoration: none; font-size: 13px; }
        .topbar .t { color: #e0e0e0; font-size: 14px; font-weight: 600; max-width: 60%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .wrap { padding: 16px 14px 40px; max-width: 720px; margin: 0 auto; }
        .doc-body { font-size: 15px; }
        .doc-body h1 { font-size: 24px; margin: 8px 0 18px; color: #111; }
        .doc-body h2 { font-size: 20px; margin: 26px 0 12px; color: #111; padding-bottom: 6px; border-bottom: 1px solid #e0e0e0; }
        .doc-body h3 { font-size: 17px; margin: 20px 0 10px; color: #111; }
        .doc-body p { margin: 10px 0; }
        .doc-body ul, .doc-body ol { margin: 10px 0 10px 22px; }
        .doc-body li { margin: 5px 0; }
        .doc-body code { background: #eef1f4; padding: 1px 5px; border-radius: 4px; font-size: .9em; font-family: Menlo, Consolas, monospace; }
        .doc-body pre { background: #0f1a24; color: #d8e0e8; border-radius: 10px; padding: 14px; overflow-x: auto; font-size: 12px; line-height: 1.6; margin: 12px 0; }
        .doc-body pre code { background: none; padding: 0; color: inherit; }
        .doc-body table { display: block; overflow-x: auto; border-collapse: collapse; margin: 12px 0; width: 100%; font-size: 13px; white-space: nowrap; -webkit-overflow-scrolling: touch; }
        .doc-body th, .doc-body td { border: 1px solid #dde3e8; padding: 7px 10px; text-align: left; }
        .doc-body th { background: #eef1f4; }
        .doc-body blockquote { border-left: 4px solid #4fc3f7; padding: 4px 12px; margin: 12px 0; color: #556; background: #f0f7fb; border-radius: 0 8px 8px 0; }
        .doc-body img { max-width: 100%; height: auto; border-radius: 8px; }
        .doc-body hr { border: none; border-top: 1px solid #e0e0e0; margin: 18px 0; }
        .footer { text-align: center; padding: 20px 14px 30px; font-size: 12px; color: #8a949e; }
        .footer a { color: #4fc3f7; text-decoration: none; }
    </style>
</head>
<body>
    <div class="topbar">
        <a href="docs.html">← 文档列表</a>
        <span class="t"><?php echo $title; ?></span>
        <a href="../view.php?src=main&path=<?php echo rawurlencode($path); ?>">电脑版</a>
    </div>
    <div class="wrap">
        <div class="doc-body">
            <?php echo $body; ?>
        </div>
    </div>
    <div class="footer">
        <p>AtmoWave Technology · 移动版文档</p>
        <p><a href="../view.php?src=main&path=<?php echo rawurlencode($path); ?>">切换到电脑版查看</a></p>
    </div>
    <?php
    $__html = ob_get_contents();
    if ($__html !== false && !empty($__html)) {
        @mkdir($cacheDir, 0755, true);
        @file_put_contents($cacheFile, $__html);
    }
    ?>
</body>
</html>
