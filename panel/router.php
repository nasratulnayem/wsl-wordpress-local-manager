<?php
declare(strict_types=1);

// ═══════════════════════════════════════════════════════════════════
//  WORDPRESS LOCAL CONSOLE  //  KERNEL v2.0
// ═══════════════════════════════════════════════════════════════════

$root      = realpath(dirname(__DIR__));
$sitesDir  = $root . '/sites';
$dbSock    = '/tmp/wordpress-wsl/mariadb.sock';

$uri    = $_SERVER['REQUEST_URI'] ?? '/';
$path   = rawurldecode((string) parse_url($uri, PHP_URL_PATH));
$qs     = [];
parse_str((string) parse_url($uri, PHP_URL_QUERY), $qs);
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

// ── Utility ──────────────────────────────────────────────────────

function h(string $v): string {
    return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function fmt_size(int $bytes): string {
    if ($bytes <= 0) return '0 B';
    $u = ['B','KB','MB','GB'];
    $i = min((int) floor(log($bytes, 1024)), 3);
    return round($bytes / (1024 ** $i), 1) . ' ' . $u[$i];
}

function fmt_age(int $ts): string {
    $d = time() - $ts;
    if ($d < 60)     return $d . 's ago';
    if ($d < 3600)   return (int)($d / 60) . 'm ago';
    if ($d < 86400)  return (int)($d / 3600) . 'h ago';
    if ($d < 604800) return (int)($d / 86400) . 'd ago';
    return date('Y-m-d', $ts);
}

function run_cmd(string $cmd): array {
    $out = []; $code = 0;
    exec($cmd . ' 2>&1', $out, $code);
    return ['code' => $code, 'out' => implode("\n", $out)];
}

// ── Site Operations ──────────────────────────────────────────────

function list_sites(string $dir): array {
    $r = [];
    foreach ((scandir($dir) ?: []) as $e) {
        if ($e[0] !== '.' && is_dir($dir . '/' . $e)) $r[] = $e;
    }
    sort($r, SORT_NATURAL);
    return $r;
}

function next_site_name(string $dir): string {
    $max = 0;
    foreach (list_sites($dir) as $s) {
        if (preg_match('/^site(\d+)$/', $s, $m)) $max = max($max, (int) $m[1]);
    }
    return 'site' . ($max + 1);
}

function do_create_site(string $root, string $name): array {
    return run_cmd('bash ' . escapeshellarg($root . '/bin/create-site') . ' ' . escapeshellarg($name));
}

function do_delete_site(string $root, string $sitesDir, string $name, string $sock): void {
    $dir  = $sitesDir . '/' . $name;
    $pass = $root . '/data/' . $name . '.dbpass';
    if (is_dir($dir))  run_cmd('rm -rf ' . escapeshellarg($dir));
    if (is_file($pass)) @unlink($pass);
    $sql = sprintf(
        "DROP DATABASE IF EXISTS `%s`; DROP USER IF EXISTS '%s'@'localhost'; FLUSH PRIVILEGES;",
        str_replace('`', '``', $name),
        str_replace("'", "\\'", $name . '_user')
    );
    run_cmd('mysql --socket=' . escapeshellarg($sock) . ' -uroot -e ' . escapeshellarg($sql));
}

// ── File System ──────────────────────────────────────────────────

function site_resolve(string $sitesDir, string $site, string $rel): string|false {
    if (!preg_match('/^[A-Za-z0-9_-]+$/', $site)) return false;
    $base = realpath($sitesDir . '/' . $site);
    if (!$base || !is_dir($base)) return false;
    if ($rel === '' || $rel === '/') return $base;
    $parts = [];
    foreach (explode('/', $rel) as $p) {
        if ($p === '' || $p === '.') continue;
        if ($p === '..') { array_pop($parts); continue; }
        $parts[] = $p;
    }
    $candidate = $base . '/' . implode('/', $parts);
    if (file_exists($candidate)) {
        $real = realpath($candidate);
        return ($real && str_starts_with($real, $base)) ? $real : false;
    }
    $pr = realpath(dirname($candidate));
    return ($pr && str_starts_with($pr, $base)) ? $candidate : false;
}

function site_rel(string $sitesDir, string $site, string $abs): string {
    $base = (string) realpath($sitesDir . '/' . $site);
    return $abs === $base ? '' : ltrim(substr($abs, strlen($base)), '/');
}

function is_text_file(string $path): bool {
    $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $name = basename($path);
    if (in_array($name, ['.htaccess','.env','.gitignore','.gitattributes','.editorconfig'], true)) return true;
    return in_array($ext, [
        'php','phtml','js','mjs','cjs','ts','tsx','jsx','css','scss','sass','less',
        'html','htm','twig','blade','json','yaml','yml','toml','xml','xsl',
        'md','markdown','txt','sh','bash','zsh','bat','sql','log','ini',
        'conf','cfg','lock','map','svg','htaccess','htpasswd','env','sample',
    ], true);
}

function ext_tag(string $path, bool $isDir): string {
    if ($isDir) return '<span class="xt xt-d">DIR</span>';
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $label = $ext !== '' ? strtoupper($ext) : 'FILE';
    $cls = match($ext) {
        'php'                    => 'xt-php',
        'js','mjs','cjs','ts','tsx','jsx' => 'xt-js',
        'css','scss','sass','less'        => 'xt-css',
        'html','htm','twig'               => 'xt-html',
        'json','yaml','yml','toml'        => 'xt-json',
        'md','txt'                        => 'xt-txt',
        'sql'                             => 'xt-sql',
        'sh','bash','zsh'                 => 'xt-sh',
        'png','jpg','jpeg','gif','svg','webp','ico' => 'xt-img',
        'zip','tar','gz','bz2','rar'      => 'xt-zip',
        default                           => 'xt-f',
    };
    return '<span class="xt ' . $cls . '">' . h($label) . '</span>';
}

function dir_size(string $p): int {
    $size = 0;
    try {
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($p, FilesystemIterator::SKIP_DOTS));
        foreach ($it as $f) { if ($f->isFile()) $size += $f->getSize(); }
    } catch (\Throwable) {}
    return $size;
}

function mime_for(string $p): string {
    return match (strtolower(pathinfo($p, PATHINFO_EXTENSION))) {
        'css'        => 'text/css; charset=utf-8',
        'js','mjs'   => 'application/javascript; charset=utf-8',
        'json'       => 'application/json; charset=utf-8',
        'svg'        => 'image/svg+xml',
        'png'        => 'image/png',
        'jpg','jpeg' => 'image/jpeg',
        'gif'        => 'image/gif',
        'ico'        => 'image/x-icon',
        'woff'       => 'font/woff',
        'woff2'      => 'font/woff2',
        'ttf'        => 'font/ttf',
        default      => 'application/octet-stream',
    };
}

// ── Layout ───────────────────────────────────────────────────────

function page(string $title, string $body, string $js = ''): never {
    header('Content-Type: text/html; charset=utf-8');
    $css = page_css();
    $t   = h($title);
    echo <<<HTML
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>{$t} // WP:CONSOLE</title>
<style>{$css}</style>
</head>
<body>
<div class="scanlines"></div>
<div class="grid-bg"></div>
<div class="vignette"></div>
<div class="wrap">
{$body}
</div>
{$js}
</body>
</html>
HTML;
    exit;
}

function page_css(): string { return <<<'CSS'
:root{
  --bg:#030507;--bgc:#060a0f;--bgi:#020406;
  --bd:#0b1e2e;--bdg:#0d3020;
  --g:#00ff41;--g2:#00c832;--g3:#006618;--g4:#002a0a;
  --c:#00d4ff;--c2:#0099bb;
  --r:#ff003c;--a:#ffaa00;--m:#cc44ff;--w:#ffffff;
  --tx:#8fffb0;--tx2:#3a6045;--tx3:#d4ffe0;--tx4:#527060;
  --gw-g:0 0 8px rgba(0,255,65,.6),0 0 22px rgba(0,255,65,.18);
  --gw-c:0 0 8px rgba(0,212,255,.6),0 0 22px rgba(0,212,255,.18);
  --gw-r:0 0 8px rgba(255,0,60,.6),0 0 22px rgba(255,0,60,.18);
  --gw-a:0 0 8px rgba(255,170,0,.6),0 0 22px rgba(255,170,0,.18);
  --tr:.14s ease;
  --f:'JetBrains Mono','Fira Code','Cascadia Code','Courier New',monospace;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:var(--f);background:var(--bg);color:var(--tx);min-height:100vh;font-size:13px;line-height:1.6;overflow-x:hidden}
.scanlines{position:fixed;inset:0;z-index:9999;pointer-events:none;background:repeating-linear-gradient(0deg,transparent,transparent 1px,rgba(0,0,0,.1) 1px,rgba(0,0,0,.1) 2px)}
.grid-bg{position:fixed;inset:0;z-index:0;pointer-events:none;
  background-image:linear-gradient(rgba(0,255,65,.035) 1px,transparent 1px),
    linear-gradient(90deg,rgba(0,255,65,.035) 1px,transparent 1px);
  background-size:52px 52px}
.vignette{position:fixed;inset:0;z-index:1;pointer-events:none;
  background:radial-gradient(ellipse at center,transparent 42%,rgba(0,0,0,.65) 100%)}
.wrap{position:relative;z-index:10;max-width:1280px;margin:0 auto;padding:24px 20px 60px}

/* ── Header ── */
.hdr{display:flex;align-items:center;justify-content:space-between;
  padding:14px 22px;border:1px solid var(--bd);border-top:2px solid var(--g);
  background:var(--bgc);margin-bottom:22px;position:relative;overflow:hidden}
.hdr::after{content:'';position:absolute;top:0;left:-120%;width:35%;height:2px;
  background:linear-gradient(90deg,transparent,rgba(0,255,65,.85),transparent);
  animation:sweep 4s linear infinite}
@keyframes sweep{to{left:200%}}
.logo{font-size:14px;font-weight:700;color:var(--g);text-shadow:var(--gw-g);
  letter-spacing:.15em;text-transform:uppercase;user-select:none}
.logo em{color:var(--c);font-style:normal}
.hdr-right{display:flex;align-items:center;gap:22px;font-size:10px;
  color:var(--tx2);text-transform:uppercase;letter-spacing:.12em}
.dot{display:inline-block;width:7px;height:7px;border-radius:50%;
  background:var(--g);box-shadow:0 0 8px var(--g);
  animation:pulse 2.4s ease infinite;margin-right:5px;flex-shrink:0}
@keyframes pulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.3;transform:scale(.75)}}
.hdr-right a{color:var(--tx2);text-decoration:none;transition:color var(--tr)}
.hdr-right a:hover{color:var(--g)}

/* ── Stats bar ── */
.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:22px}
.stat{padding:14px 16px;border:1px solid var(--bd);background:var(--bgc);
  position:relative;overflow:hidden;transition:border-color var(--tr),box-shadow var(--tr)}
.stat::after{content:'';position:absolute;bottom:0;left:0;right:0;height:1px;
  background:linear-gradient(90deg,transparent,var(--g4),transparent)}
.stat:hover{border-color:var(--g3);box-shadow:inset 0 0 30px rgba(0,255,65,.03)}
.st-lbl{font-size:9px;text-transform:uppercase;letter-spacing:.16em;color:var(--tx2);margin-bottom:4px}
.st-val{font-size:20px;font-weight:700;color:var(--g);text-shadow:var(--gw-g);letter-spacing:.03em}
.st-sub{font-size:9px;color:var(--tx4);margin-top:2px}

/* ── Section header ── */
.sec-hdr{display:flex;align-items:center;justify-content:space-between;
  margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid var(--bd)}
.sec-title{font-size:10px;text-transform:uppercase;letter-spacing:.16em;color:var(--tx2)}
.sec-title::before{content:'// ';color:var(--g3)}

/* ── Create bar ── */
.create-bar{display:flex;align-items:center;gap:10px;padding:13px 20px;
  border:1px solid var(--bd);border-left:2px solid var(--g);
  background:var(--bgc);margin-bottom:22px}
.create-bar::before{content:'>';color:var(--g);font-size:15px;font-weight:700;
  text-shadow:var(--gw-g);flex-shrink:0}
.inp{background:var(--bgi);border:1px solid var(--bd);color:var(--tx3);
  font-family:var(--f);font-size:13px;padding:8px 12px;outline:none;
  transition:border-color var(--tr),box-shadow var(--tr);caret-color:var(--g)}
.inp::placeholder{color:var(--tx2)}
.inp:focus{border-color:var(--g3);box-shadow:0 0 0 1px var(--g3),inset 0 0 16px rgba(0,255,65,.03)}
.inp-grow{flex:1}

/* ── Buttons ── */
.btn{display:inline-flex;align-items:center;gap:5px;padding:8px 13px;
  font-family:var(--f);font-size:10px;font-weight:700;text-transform:uppercase;
  letter-spacing:.14em;border:1px solid;cursor:pointer;text-decoration:none;
  transition:all var(--tr);white-space:nowrap;background:transparent;line-height:1}
.btn-g{border-color:var(--g3);color:var(--g)}
.btn-g:hover{background:rgba(0,255,65,.08);border-color:var(--g);box-shadow:var(--gw-g);color:var(--tx3)}
.btn-c{border-color:#002d3d;color:var(--c)}
.btn-c:hover{background:rgba(0,212,255,.08);border-color:var(--c);box-shadow:var(--gw-c);color:#e0f8ff}
.btn-a{border-color:#3a2800;color:var(--a)}
.btn-a:hover{background:rgba(255,170,0,.08);border-color:var(--a);box-shadow:var(--gw-a)}
.btn-r{border-color:#380010;color:var(--r)}
.btn-r:hover{background:rgba(255,0,60,.09);border-color:var(--r);box-shadow:var(--gw-r)}
.btn-ghost{border-color:var(--bd);color:var(--tx2)}
.btn-ghost:hover{border-color:var(--tx4);color:var(--tx)}
.btn-sm{padding:5px 9px;font-size:9px}
.btn:disabled{opacity:.35;cursor:default;pointer-events:none}

/* ── Site grid ── */
.site-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:14px;margin-bottom:30px}

.card{position:relative;background:var(--bgc);border:1px solid var(--bd);
  padding:18px 20px;transition:border-color var(--tr),box-shadow var(--tr)}
.card::before,.card::after{content:'';position:absolute;transition:all .28s ease}
.card::before{top:-1px;left:-1px;width:10px;height:10px;
  border-top:2px solid var(--g3);border-left:2px solid var(--g3)}
.card::after{bottom:-1px;right:-1px;width:10px;height:10px;
  border-bottom:2px solid var(--g3);border-right:2px solid var(--g3)}
.card:hover{border-color:var(--g3);
  box-shadow:0 0 28px rgba(0,255,65,.07),inset 0 0 40px rgba(0,255,65,.02)}
.card:hover::before,.card:hover::after{width:16px;height:16px;border-color:var(--g)}

.c-seq{font-size:9px;text-transform:uppercase;letter-spacing:.14em;color:var(--tx2);margin-bottom:3px}
.c-name{font-size:19px;font-weight:700;color:var(--tx3);margin-bottom:6px;letter-spacing:.02em}
.c-name::before{content:'> ';color:var(--g);text-shadow:var(--gw-g)}
.c-url{font-size:11px;color:var(--tx2);margin-bottom:14px;cursor:pointer;
  transition:color var(--tr);user-select:none;display:inline-flex;align-items:center;gap:5px}
.c-url:hover{color:var(--c)}
.c-url-text{font-size:10px}
.c-acts{display:flex;gap:6px;flex-wrap:wrap}

/* ── Flash messages ── */
.flash{padding:10px 14px;border:1px solid;margin-bottom:14px;font-size:12px}
.flash-ok{border-color:var(--g3);color:var(--g);background:rgba(0,255,65,.04)}
.flash-err{border-color:#380010;color:var(--r);background:rgba(255,0,60,.04)}

/* ── File manager ── */
.path-bar{display:flex;align-items:center;flex-wrap:wrap;padding:9px 14px;
  background:var(--bgc);border:1px solid var(--bd);margin-bottom:10px;
  font-size:12px;color:var(--tx2)}
.path-bar::before{content:'~/';color:var(--g3);margin-right:2px;flex-shrink:0}
.path-bar a{color:var(--tx2);text-decoration:none;padding:1px 3px;transition:color var(--tr)}
.path-bar a:hover{color:var(--c)}
.path-bar .cur{color:var(--tx3)}
.path-sep{color:var(--g3);margin:0 2px}

.fm-toolbar{display:flex;align-items:center;gap:8px;margin-bottom:10px;flex-wrap:wrap}

.mini-panel{display:none;padding:10px 14px;background:rgba(0,255,65,.022);
  border:1px solid var(--g3);margin-bottom:10px}
.mini-panel.open{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.mini-panel label{font-size:10px;color:var(--tx2);text-transform:uppercase;letter-spacing:.1em;white-space:nowrap}

.ftable{width:100%;border-collapse:collapse;border:1px solid var(--bd);background:var(--bgc)}
.ftable th{padding:9px 13px;font-size:9px;text-transform:uppercase;letter-spacing:.14em;
  color:var(--tx2);border-bottom:1px solid var(--bd);text-align:left;
  background:rgba(0,255,65,.02)}
.ftable td{padding:8px 13px;border-bottom:1px solid rgba(11,30,46,.45);
  font-size:12px;vertical-align:middle}
.ftable tr:last-child td{border-bottom:none}
.ftable tr:not(.fthead):hover td{background:rgba(0,255,65,.025)}
.fname{display:flex;align-items:center;gap:7px;text-decoration:none;
  transition:color var(--tr);color:var(--tx3)}
.fname:hover{color:var(--c)}
.fname-plain{display:flex;align-items:center;gap:7px;color:var(--tx2)}
.fmeta{color:var(--tx2);font-size:11px}
.factions{display:flex;gap:5px;justify-content:flex-end}

/* ── Extension tags ── */
.xt{display:inline-block;padding:1px 5px;font-size:9px;letter-spacing:.07em;
  font-weight:700;border:1px solid;flex-shrink:0}
.xt-d   {border-color:var(--g3);color:var(--g2)}
.xt-php {border-color:#552200;color:#ff7733}
.xt-js  {border-color:#3a3200;color:#f0c000}
.xt-css {border-color:#002244;color:#55aaff}
.xt-html{border-color:#003322;color:#33ddaa}
.xt-json{border-color:#002233;color:var(--c)}
.xt-txt {border-color:var(--bd);color:var(--tx2)}
.xt-sql {border-color:#220044;color:var(--m)}
.xt-sh  {border-color:var(--g3);color:var(--g)}
.xt-img {border-color:#002244;color:#66aaff}
.xt-zip {border-color:#332200;color:#cc7700}
.xt-f   {border-color:var(--bd);color:var(--tx2)}

/* ── Modal ── */
.modal-bg{display:none;position:fixed;inset:0;background:rgba(2,4,8,.92);
  z-index:2000;align-items:center;justify-content:center;backdrop-filter:blur(6px)}
.modal-bg.open{display:flex}
.modal{background:var(--bgc);border:1px solid var(--bd);border-top:2px solid var(--r);
  padding:28px;max-width:430px;width:90%;animation:min-in .18s ease}
@keyframes min-in{from{opacity:0;transform:translateY(-14px) scale(.97)}to{opacity:1;transform:none}}
.modal h3{font-size:13px;color:var(--r);text-shadow:var(--gw-r);
  margin-bottom:10px;text-transform:uppercase;letter-spacing:.12em}
.modal p{color:var(--tx);font-size:12px;margin-bottom:20px;line-height:1.8}
.modal p strong{color:var(--tx3)}
.modal-acts{display:flex;gap:10px;justify-content:flex-end}

/* ── File editor ── */
.ed-hdr{display:flex;align-items:center;justify-content:space-between;
  padding:11px 18px;background:var(--bgc);border:1px solid var(--bd);
  border-bottom:1px solid var(--g4)}
.ed-fname{font-size:13px;font-weight:700;color:var(--tx3)}
.ed-fname span{color:var(--tx2);font-weight:normal}
.ed-acts{display:flex;gap:8px;align-items:center}
.ed-wrap{border:1px solid var(--bd);position:relative;transition:border-color var(--tr)}
.ed-wrap:focus-within{border-color:var(--g3);box-shadow:0 0 0 1px var(--g3)}
#editor{width:100%;min-height:580px;background:#020405;color:#d4ffe0;
  font-family:var(--f);font-size:13px;line-height:1.78;padding:16px 18px;
  border:none;outline:none;resize:vertical;tab-size:4;caret-color:var(--g);
  white-space:pre;overflow-wrap:normal;overflow-x:auto}
#editor::selection{background:rgba(0,255,65,.2)}
.ed-footer{display:flex;align-items:center;justify-content:space-between;
  padding:8px 14px;background:var(--bgc);border:1px solid var(--bd);
  border-top:1px solid var(--g4);font-size:10px;color:var(--tx2)}
.ed-hint{color:var(--g3)}

/* ── Nav back ── */
.nav-back{display:inline-flex;align-items:center;gap:6px;font-size:10px;
  color:var(--tx2);text-decoration:none;text-transform:uppercase;
  letter-spacing:.12em;transition:color var(--tr);margin-bottom:16px}
.nav-back:hover{color:var(--g)}
.nav-back::before{content:'← '}

/* ── Empty state ── */
.empty{text-align:center;padding:50px 20px;color:var(--tx2);
  border:1px solid var(--bd);background:var(--bgc)}
.empty .ei{font-size:28px;margin-bottom:12px;opacity:.3}
.empty .et{font-size:12px}

/* ── Toast ── */
#toast{position:fixed;bottom:28px;right:28px;z-index:3000;
  background:var(--bgc);border:1px solid var(--g3);color:var(--g);
  padding:10px 16px;font-size:11px;letter-spacing:.08em;
  opacity:0;transform:translateY(8px);transition:all .22s ease;pointer-events:none}
#toast.show{opacity:1;transform:none}

/* ── Scrollbar ── */
::-webkit-scrollbar{width:5px;height:5px}
::-webkit-scrollbar-track{background:var(--bg)}
::-webkit-scrollbar-thumb{background:var(--g3)}
::-webkit-scrollbar-thumb:hover{background:var(--g2)}
::selection{background:rgba(0,255,65,.2);color:var(--tx3)}

@media(max-width:820px){
  .stats{grid-template-columns:repeat(2,1fr)}
  .site-grid{grid-template-columns:1fr}
  .hdr{flex-direction:column;gap:8px;align-items:flex-start}
  .ftable th:nth-child(3),.ftable td:nth-child(3){display:none}
}
CSS; }

// ════════════════════════════════════════════════════════════════
//  ROUTE: /   Dashboard
// ════════════════════════════════════════════════════════════════

if ($path === '/' || $path === '') {

    $flash = ''; $isErr = false;

    if ($method === 'POST') {
        $action = $_POST['action'] ?? '';

        if ($action === 'create') {
            $name = trim((string) ($_POST['site_name'] ?? ''));
            if ($name === '') $name = next_site_name($sitesDir);
            if (!preg_match('/^[A-Za-z0-9_-]+$/', $name)) {
                $flash = 'Invalid site name. Use letters, numbers, dash, underscore only.'; $isErr = true;
            } elseif (is_dir($sitesDir . '/' . $name)) {
                $flash = "Site \"$name\" already exists."; $isErr = true;
            } else {
                $r = do_create_site($root, $name);
                if ($r['code'] === 0) {
                    $flash = "Site \"$name\" initialized successfully.";
                } else {
                    $flash = $r['out'] ?: 'Create failed.'; $isErr = true;
                }
            }
        }

        if ($action === 'delete') {
            $name = trim((string) ($_POST['site_name'] ?? ''));
            if (!preg_match('/^[A-Za-z0-9_-]+$/', $name)) {
                $flash = 'Invalid site name.'; $isErr = true;
            } else {
                do_delete_site($root, $sitesDir, $name, $dbSock);
                $flash = "Site \"$name\" destroyed.";
            }
        }
    }

    $sites     = list_sites($sitesDir);
    $nextName  = next_site_name($sitesDir);
    $siteCount = count($sites);
    $phpVer    = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;

    ob_start();
?>
<!-- HEADER -->
<header class="hdr">
  <div class="logo">WP<em>:</em>CONSOLE <em>//</em> LOCAL</div>
  <div class="hdr-right">
    <span><span class="dot"></span>SYSTEM ONLINE</span>
    <span>PHP <?= h($phpVer) ?></span>
    <span>MariaDB</span>
    <span>:8080</span>
  </div>
</header>

<!-- STATS -->
<div class="stats">
  <div class="stat">
    <div class="st-lbl">Active Sites</div>
    <div class="st-val" id="st-sites"><?= $siteCount ?></div>
    <div class="st-sub">in <?= h($sitesDir) ?></div>
  </div>
  <div class="stat">
    <div class="st-lbl">Panel</div>
    <div class="st-val" style="font-size:14px;padding-top:4px">127.0.0.1<em style="color:var(--c)">:8080</em></div>
    <div class="st-sub">PHP built-in server</div>
  </div>
  <div class="stat">
    <div class="st-lbl">Database</div>
    <div class="st-val" style="font-size:14px;padding-top:4px">MariaDB</div>
    <div class="st-sub">Socket-only / no network</div>
  </div>
  <div class="stat">
    <div class="st-lbl">Runtime</div>
    <div class="st-val" style="font-size:14px;padding-top:4px"><?= h(date('H:i')) ?></div>
    <div class="st-sub"><?= h(date('Y-m-d')) ?></div>
  </div>
</div>

<?php if ($flash): ?>
<div class="flash <?= $isErr ? 'flash-err' : 'flash-ok' ?>"><?= h($flash) ?></div>
<?php endif; ?>

<!-- CREATE FORM -->
<div class="create-bar">
  <form method="post" action="/" style="display:flex;align-items:center;gap:10px;flex:1">
    <input type="hidden" name="action" value="create">
    <input class="inp inp-grow" type="text" name="site_name"
      placeholder="Site name (blank = <?= h($nextName) ?>)"
      pattern="[A-Za-z0-9_-]+" title="Letters, numbers, dash, underscore">
    <button class="btn btn-g" type="submit">&#9655; DEPLOY SITE</button>
  </form>
</div>

<!-- SITE GRID -->
<div class="sec-hdr">
  <div class="sec-title">Deployed Sites</div>
  <div style="font-size:10px;color:var(--tx2)"><?= $siteCount ?> instance<?= $siteCount !== 1 ? 's' : '' ?></div>
</div>

<?php if (!$sites): ?>
<div class="empty"><div class="ei">[ ]</div><div class="et">No sites deployed. Create one above.</div></div>
<?php else: ?>
<div class="site-grid">
<?php foreach ($sites as $i => $site):
    $siteUrl = 'http://127.0.0.1:8080/' . $site;
?>
  <article class="card">
    <div class="c-seq">INSTANCE_<?= str_pad((string)($i+1), 3, '0', STR_PAD_LEFT) ?></div>
    <div class="c-name"><?= h($site) ?></div>
    <div class="c-url" onclick="copyUrl('<?= h($siteUrl) ?>')" title="Click to copy URL">
      <span class="c-url-text"><?= h($siteUrl) ?></span>
    </div>
    <div class="c-acts">
      <a class="btn btn-g btn-sm" href="/<?= h($site) ?>/" target="_blank">&#9655; OPEN</a>
      <a class="btn btn-a btn-sm" href="/<?= h($site) ?>/wp-admin/" target="_blank">ADMIN</a>
      <a class="btn btn-c btn-sm" href="/files?site=<?= h(rawurlencode($site)) ?>">FILES</a>
      <button class="btn btn-r btn-sm" onclick="confirmDelete('<?= h($site) ?>')">DESTROY</button>
    </div>
  </article>
<?php endforeach; ?>
</div>
<?php endif; ?>

<!-- DELETE MODAL -->
<div class="modal-bg" id="del-modal">
  <div class="modal">
    <h3>&#9888; CONFIRM DESTRUCTION</h3>
    <p>This will permanently delete site <strong id="del-name"></strong> including its files and database. This action cannot be undone.</p>
    <div class="modal-acts">
      <button class="btn btn-ghost" onclick="closeModal()">ABORT</button>
      <form method="post" action="/" id="del-form" style="margin:0">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="site_name" id="del-input" value="">
        <button class="btn btn-r" type="submit">CONFIRM DESTROY</button>
      </form>
    </div>
  </div>
</div>

<!-- TOAST -->
<div id="toast"></div>
<?php
    $body = (string) ob_get_clean();
    $js = <<<'JS'
<script>
function confirmDelete(name) {
  document.getElementById('del-name').textContent = '"' + name + '"';
  document.getElementById('del-input').value = name;
  document.getElementById('del-modal').classList.add('open');
}
function closeModal() {
  document.querySelectorAll('.modal-bg').forEach(m => m.classList.remove('open'));
}
document.querySelectorAll('.modal-bg').forEach(m => {
  m.addEventListener('click', e => { if (e.target === m) closeModal(); });
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

function showToast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 2200);
}
function copyUrl(url) {
  navigator.clipboard.writeText(url).then(() => showToast('URL copied: ' + url));
}
</script>
JS;
    page('Dashboard', $body, $js);
}

// ════════════════════════════════════════════════════════════════
//  ROUTE: /files/edit   File Editor
// ════════════════════════════════════════════════════════════════

if ($path === '/files/edit' || $path === '/files/edit/') {

    $site    = trim((string) ($qs['site'] ?? ''));
    $fileRel = trim((string) ($qs['file'] ?? ''));
    $dirRel  = trim((string) ($qs['dir'] ?? ''));

    if (!preg_match('/^[A-Za-z0-9_-]+$/', $site)) {
        page('Error', '<div class="flash flash-err">Invalid site.</div>');
    }

    $absPath = site_resolve($sitesDir, $site, $fileRel);
    if ($absPath === false || !is_file($absPath)) {
        page('Error', '<div class="flash flash-err">File not found.</div>');
    }
    if (!is_text_file($absPath)) {
        header('Location: /files?site=' . rawurlencode($site) . '&dir=' . rawurlencode($dirRel));
        exit;
    }

    $savedMsg = '';
    $saveErr  = false;

    if ($method === 'POST' && ($_POST['action'] ?? '') === 'save') {
        $content = $_POST['content'] ?? '';
        $result  = file_put_contents($absPath, $content);
        if ($result !== false) {
            $savedMsg = 'Saved successfully.';
        } else {
            $savedMsg = 'Save failed — check file permissions.';
            $saveErr  = true;
        }
    }

    $content  = (string) file_get_contents($absPath);
    $relPath  = site_rel($sitesDir, $site, $absPath);
    $filename = basename($absPath);
    $backUrl  = '/files?site=' . rawurlencode($site) . '&dir=' . rawurlencode($dirRel);
    $lines    = substr_count($content, "\n") + 1;
    $size     = strlen($content);

    ob_start();
?>
<header class="hdr">
  <div class="logo">WP<em>:</em>CONSOLE <em>//</em> EDITOR</div>
  <div class="hdr-right">
    <a href="/files?site=<?= h(rawurlencode($site)) ?>&dir=<?= h(rawurlencode($dirRel)) ?>">&#8592; FILE MANAGER</a>
    <a href="/">DASHBOARD</a>
  </div>
</header>
<a class="nav-back" href="<?= h($backUrl) ?>">Back to File Manager</a>

<?php if ($savedMsg): ?>
<div class="flash <?= $saveErr ? 'flash-err' : 'flash-ok' ?>"><?= h($savedMsg) ?></div>
<?php endif; ?>

<div class="ed-hdr">
  <div class="ed-fname">
    <span><?= h($site) ?> / <?= h($relPath !== $filename ? dirname($relPath) . '/' : '') ?></span><?= h($filename) ?>
  </div>
  <div class="ed-acts">
    <span style="font-size:10px;color:var(--tx2)"><?= h(ext_tag($absPath, false)) ?></span>
  </div>
</div>
<form method="post" id="editor-form">
  <input type="hidden" name="action" value="save">
  <input type="hidden" name="site" value="<?= h($site) ?>">
  <input type="hidden" name="file" value="<?= h($fileRel) ?>">
  <div class="ed-wrap">
    <textarea id="editor" name="content" spellcheck="false" autocorrect="off" autocapitalize="off"><?= h($content) ?></textarea>
  </div>
  <div class="ed-footer">
    <div>
      <span id="ln-count"><?= $lines ?> lines</span>
      &nbsp;·&nbsp;
      <span id="sz-count"><?= fmt_size($size) ?></span>
      &nbsp;·&nbsp;
      <span><?= h(strtolower(pathinfo($filename, PATHINFO_EXTENSION)) ?: 'plain') ?></span>
    </div>
    <div style="display:flex;align-items:center;gap:12px">
      <span class="ed-hint">Ctrl+S to save</span>
      <a class="btn btn-ghost btn-sm" href="<?= h($backUrl) ?>">CANCEL</a>
      <button class="btn btn-g" type="submit">&#9654; SAVE FILE</button>
    </div>
  </div>
</form>
<?php
    $body = (string) ob_get_clean();
    $js = <<<'JS'
<script>
const editor = document.getElementById('editor');
// Ctrl+S → save
document.addEventListener('keydown', e => {
  if ((e.ctrlKey || e.metaKey) && e.key === 's') {
    e.preventDefault();
    document.getElementById('editor-form').submit();
  }
});
// Tab → insert spaces
editor.addEventListener('keydown', e => {
  if (e.key === 'Tab') {
    e.preventDefault();
    const s = editor.selectionStart, en = editor.selectionEnd;
    editor.value = editor.value.substring(0, s) + '    ' + editor.value.substring(en);
    editor.selectionStart = editor.selectionEnd = s + 4;
  }
});
// Live line/size counter
editor.addEventListener('input', () => {
  const lines = (editor.value.match(/\n/g) || []).length + 1;
  document.getElementById('ln-count').textContent = lines + ' lines';
  const bytes = new Blob([editor.value]).size;
  const u = ['B','KB','MB'], i = bytes < 1024 ? 0 : bytes < 1048576 ? 1 : 2;
  document.getElementById('sz-count').textContent =
    (i === 0 ? bytes : (bytes / 1024 ** i).toFixed(1)) + ' ' + u[i];
});
</script>
JS;
    page('Editor: ' . $filename, $body, $js);
}

// ════════════════════════════════════════════════════════════════
//  ROUTE: /files   File Manager
// ════════════════════════════════════════════════════════════════

if ($path === '/files' || $path === '/files/') {

    $site   = trim((string) ($qs['site'] ?? ''));
    $dirRel = trim((string) ($qs['dir'] ?? ''));

    if (!preg_match('/^[A-Za-z0-9_-]+$/', $site)) {
        page('Error', '<a class="nav-back" href="/">Back</a><div class="flash flash-err">Invalid or missing site name.</div>');
    }

    $baseDir = site_resolve($sitesDir, $site, $dirRel);
    if ($baseDir === false || !is_dir($baseDir)) {
        $baseDir = site_resolve($sitesDir, $site, '');
        $dirRel  = '';
    }
    if ($baseDir === false) {
        page('Error', '<a class="nav-back" href="/">Back</a><div class="flash flash-err">Site directory not found.</div>');
    }
    $siteBase = (string) realpath($sitesDir . '/' . $site);

    $flash  = ''; $isErr = false;

    if ($method === 'POST') {
        $action  = $_POST['action'] ?? '';
        $postDir = $_POST['dir'] ?? '';
        $curDir  = site_resolve($sitesDir, $site, $postDir);

        if ($action === 'new_file') {
            $fname = basename(trim($_POST['filename'] ?? ''));
            if ($fname === '' || $fname === '.' || $fname === '..') {
                $flash = 'Invalid filename.'; $isErr = true;
            } elseif ($curDir === false) {
                $flash = 'Invalid directory.'; $isErr = true;
            } else {
                $target = $curDir . '/' . $fname;
                $real   = realpath(dirname($target));
                if (!$real || !str_starts_with($real, $siteBase)) {
                    $flash = 'Security: path outside site root.'; $isErr = true;
                } elseif (file_exists($target)) {
                    $flash = "\"$fname\" already exists."; $isErr = true;
                } else {
                    $initial = $_POST['filecontent'] ?? '';
                    file_put_contents($target, $initial);
                    $flash = "File \"$fname\" created.";
                    $dirRel = $postDir;
                }
            }
        }

        if ($action === 'new_folder') {
            $dname = basename(trim($_POST['dirname'] ?? ''));
            if ($dname === '' || $dname === '.' || $dname === '..') {
                $flash = 'Invalid folder name.'; $isErr = true;
            } elseif ($curDir === false) {
                $flash = 'Invalid directory.'; $isErr = true;
            } else {
                $target = $curDir . '/' . $dname;
                $real   = realpath($curDir);
                if (!$real || !str_starts_with($real, $siteBase)) {
                    $flash = 'Security: path outside site root.'; $isErr = true;
                } elseif (file_exists($target)) {
                    $flash = "\"$dname\" already exists."; $isErr = true;
                } else {
                    mkdir($target, 0755, true);
                    $flash = "Folder \"$dname\" created.";
                    $dirRel = $postDir;
                }
            }
        }

        if ($action === 'delete') {
            $delRel = $_POST['path'] ?? '';
            $delAbs = site_resolve($sitesDir, $site, $delRel);
            $dirRel = $postDir;
            if ($delAbs === false || !file_exists($delAbs)) {
                $flash = 'Path not found.'; $isErr = true;
            } elseif ($delAbs === $siteBase) {
                $flash = 'Cannot delete site root.'; $isErr = true;
            } else {
                $name = basename($delAbs);
                if (is_dir($delAbs)) {
                    run_cmd('rm -rf ' . escapeshellarg($delAbs));
                } else {
                    unlink($delAbs);
                }
                $flash = "\"$name\" deleted.";
            }
        }

        if ($action === 'rename') {
            $renRel  = $_POST['path'] ?? '';
            $newname = basename(trim($_POST['newname'] ?? ''));
            $dirRel  = $postDir;
            $renAbs  = site_resolve($sitesDir, $site, $renRel);
            if ($renAbs === false || !file_exists($renAbs)) {
                $flash = 'Path not found.'; $isErr = true;
            } elseif ($newname === '' || $newname === '.' || $newname === '..') {
                $flash = 'Invalid new name.'; $isErr = true;
            } else {
                $dest = dirname($renAbs) . '/' . $newname;
                $destReal = realpath(dirname($dest));
                if (!$destReal || !str_starts_with($destReal, $siteBase)) {
                    $flash = 'Security: target outside site root.'; $isErr = true;
                } elseif (file_exists($dest)) {
                    $flash = "\"$newname\" already exists."; $isErr = true;
                } else {
                    rename($renAbs, $dest);
                    $flash = "Renamed to \"$newname\".";
                }
            }
        }

        // Re-resolve after operations
        $baseDir = site_resolve($sitesDir, $site, $dirRel);
        if ($baseDir === false || !is_dir($baseDir)) {
            $baseDir = $siteBase; $dirRel = '';
        }
    }

    // List directory contents
    $entries = [];
    $rawList = scandir($baseDir) ?: [];
    foreach ($rawList as $entry) {
        if ($entry === '.' || $entry === '..') continue;
        $abs     = $baseDir . '/' . $entry;
        $isDir   = is_dir($abs);
        $entries[] = [
            'name'  => $entry,
            'abs'   => $abs,
            'isDir' => $isDir,
            'size'  => $isDir ? 0 : (int) filesize($abs),
            'mtime' => (int) filemtime($abs),
        ];
    }
    usort($entries, fn($a, $b) =>
        $b['isDir'] <=> $a['isDir'] ?: strnatcasecmp($a['name'], $b['name'])
    );

    // Build breadcrumb
    $crumbs = [['label' => $site, 'dir' => '']];
    if ($dirRel !== '') {
        $parts = array_filter(explode('/', $dirRel), fn($p) => $p !== '');
        $acc   = '';
        foreach ($parts as $part) {
            $acc .= ($acc ? '/' : '') . $part;
            $crumbs[] = ['label' => $part, 'dir' => $acc];
        }
    }

    ob_start();
?>
<header class="hdr">
  <div class="logo">WP<em>:</em>CONSOLE <em>//</em> FILES</div>
  <div class="hdr-right">
    <a href="/">DASHBOARD</a>
  </div>
</header>
<a class="nav-back" href="/">Back to Dashboard</a>

<?php if ($flash): ?>
<div class="flash <?= $isErr ? 'flash-err' : 'flash-ok' ?>"><?= h($flash) ?></div>
<?php endif; ?>

<!-- BREADCRUMB -->
<div class="path-bar">
<?php foreach ($crumbs as $ci => $crumb):
    $isLast = $ci === count($crumbs) - 1;
    if ($isLast): ?>
    <span class="cur"><?= h($crumb['label']) ?></span>
<?php else: ?>
    <a href="?site=<?= h(rawurlencode($site)) ?>&dir=<?= h(rawurlencode($crumb['dir'])) ?>"><?= h($crumb['label']) ?></a>
    <span class="path-sep">/</span>
<?php endif; endforeach; ?>
</div>

<!-- TOOLBAR -->
<div class="fm-toolbar">
  <?php if ($dirRel !== ''): ?>
  <a class="btn btn-ghost btn-sm" href="?site=<?= h(rawurlencode($site)) ?>&dir=<?= h(rawurlencode(dirname($dirRel) === '.' ? '' : dirname($dirRel))) ?>">&#8593; UP</a>
  <?php endif; ?>
  <button class="btn btn-g btn-sm" onclick="togglePanel('new-file-panel')">+ NEW FILE</button>
  <button class="btn btn-c btn-sm" onclick="togglePanel('new-dir-panel')">+ NEW FOLDER</button>
</div>

<!-- NEW FILE PANEL -->
<form method="post" class="mini-panel" id="new-file-panel">
  <input type="hidden" name="action" value="new_file">
  <input type="hidden" name="dir" value="<?= h($dirRel) ?>">
  <input type="hidden" name="site" value="<?= h($site) ?>">
  <label>Filename:</label>
  <input class="inp" type="text" name="filename" placeholder="e.g. functions.php" required style="width:240px">
  <button class="btn btn-g btn-sm" type="submit">CREATE</button>
  <button class="btn btn-ghost btn-sm" type="button" onclick="togglePanel('new-file-panel')">CANCEL</button>
</form>

<!-- NEW FOLDER PANEL -->
<form method="post" class="mini-panel" id="new-dir-panel">
  <input type="hidden" name="action" value="new_folder">
  <input type="hidden" name="dir" value="<?= h($dirRel) ?>">
  <input type="hidden" name="site" value="<?= h($site) ?>">
  <label>Folder name:</label>
  <input class="inp" type="text" name="dirname" placeholder="e.g. my-plugin" required style="width:240px">
  <button class="btn btn-c btn-sm" type="submit">CREATE</button>
  <button class="btn btn-ghost btn-sm" type="button" onclick="togglePanel('new-dir-panel')">CANCEL</button>
</form>

<!-- FILE TABLE -->
<?php if (!$entries): ?>
<div class="empty"><div class="ei">[ EMPTY ]</div><div class="et">This directory is empty.</div></div>
<?php else: ?>
<table class="ftable">
  <thead class="fthead">
    <tr>
      <th style="width:46%">Name</th>
      <th style="width:8%">Type</th>
      <th style="width:10%">Size</th>
      <th style="width:14%">Modified</th>
      <th style="width:22%;text-align:right">Actions</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($entries as $i => $e):
    $relPath  = site_rel($sitesDir, $site, $e['abs']);
    $subDir   = $dirRel !== '' ? $dirRel . '/' . $e['name'] : $e['name'];
    $editUrl  = '/files/edit?site=' . rawurlencode($site)
              . '&file=' . rawurlencode($relPath)
              . '&dir='  . rawurlencode($dirRel);
    $browseUrl = '?site=' . rawurlencode($site) . '&dir=' . rawurlencode($subDir);
    $canEdit  = !$e['isDir'] && is_text_file($e['abs']);
?>
    <tr>
      <td>
        <?php if ($e['isDir']): ?>
          <a class="fname" href="<?= h($browseUrl) ?>">&#9654; <?= h($e['name']) ?></a>
        <?php elseif ($canEdit): ?>
          <a class="fname" href="<?= h($editUrl) ?>"><?= h($e['name']) ?></a>
        <?php else: ?>
          <span class="fname-plain"><?= h($e['name']) ?></span>
        <?php endif; ?>
      </td>
      <td><?= ext_tag($e['abs'], $e['isDir']) ?></td>
      <td class="fmeta"><?= $e['isDir'] ? '—' : h(fmt_size($e['size'])) ?></td>
      <td class="fmeta"><?= h(fmt_age($e['mtime'])) ?></td>
      <td>
        <div class="factions">
          <?php if ($canEdit): ?>
          <a class="btn btn-g btn-sm" href="<?= h($editUrl) ?>">EDIT</a>
          <?php endif; ?>
          <?php if ($e['isDir']): ?>
          <a class="btn btn-c btn-sm" href="<?= h($browseUrl) ?>">OPEN</a>
          <?php endif; ?>
          <button class="btn btn-ghost btn-sm"
            onclick="openRename('<?= h(addslashes($e['name'])) ?>', '<?= h(addslashes($relPath)) ?>')">RENAME</button>
          <button class="btn btn-r btn-sm"
            onclick="openDelete('<?= h(addslashes($e['name'])) ?>', '<?= h(addslashes($relPath)) ?>', <?= $e['isDir'] ? 'true' : 'false' ?>)">DEL</button>
        </div>
      </td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>

<!-- DELETE MODAL -->
<div class="modal-bg" id="del-modal">
  <div class="modal">
    <h3>&#9888; CONFIRM DELETE</h3>
    <p>Permanently delete <strong id="del-name"></strong>? <span id="del-dir-warn"></span>This cannot be undone.</p>
    <div class="modal-acts">
      <button class="btn btn-ghost" onclick="closeModals()">ABORT</button>
      <form method="post" id="del-form" style="margin:0">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="site" value="<?= h($site) ?>">
        <input type="hidden" name="dir" value="<?= h($dirRel) ?>">
        <input type="hidden" name="path" id="del-path-inp" value="">
        <button class="btn btn-r" type="submit">DELETE</button>
      </form>
    </div>
  </div>
</div>

<!-- RENAME MODAL -->
<div class="modal-bg" id="ren-modal">
  <div class="modal" style="border-top-color:var(--c)">
    <h3 style="color:var(--c);text-shadow:var(--gw-c)">RENAME</h3>
    <p>Renaming: <strong id="ren-old"></strong></p>
    <form method="post" id="ren-form">
      <input type="hidden" name="action" value="rename">
      <input type="hidden" name="site" value="<?= h($site) ?>">
      <input type="hidden" name="dir" value="<?= h($dirRel) ?>">
      <input type="hidden" name="path" id="ren-path-inp" value="">
      <input class="inp" type="text" name="newname" id="ren-new-inp"
        placeholder="New name" required style="width:100%;margin-bottom:16px">
      <div class="modal-acts">
        <button class="btn btn-ghost" type="button" onclick="closeModals()">CANCEL</button>
        <button class="btn btn-c" type="submit">RENAME</button>
      </div>
    </form>
  </div>
</div>

<div id="toast"></div>
<?php
    $body = (string) ob_get_clean();
    $js = <<<'JS'
<script>
function togglePanel(id) {
  const el = document.getElementById(id);
  const wasOpen = el.classList.contains('open');
  document.querySelectorAll('.mini-panel').forEach(p => p.classList.remove('open'));
  if (!wasOpen) { el.classList.add('open'); el.querySelector('input[type=text]')?.focus(); }
}
function closeModals() {
  document.querySelectorAll('.modal-bg').forEach(m => m.classList.remove('open'));
}
function openDelete(name, path, isDir) {
  document.getElementById('del-name').textContent = '"' + name + '"';
  document.getElementById('del-dir-warn').textContent = isDir ? 'All contents will be deleted. ' : '';
  document.getElementById('del-path-inp').value = path;
  document.getElementById('del-modal').classList.add('open');
}
function openRename(name, path) {
  document.getElementById('ren-old').textContent = '"' + name + '"';
  document.getElementById('ren-path-inp').value = path;
  document.getElementById('ren-new-inp').value = name;
  document.getElementById('ren-modal').classList.add('open');
  setTimeout(() => document.getElementById('ren-new-inp').select(), 50);
}
document.querySelectorAll('.modal-bg').forEach(m => {
  m.addEventListener('click', e => { if (e.target === m) closeModals(); });
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModals(); });

function showToast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 2200);
}
</script>
JS;
    page('Files: ' . $site, $body, $js);
}

// ════════════════════════════════════════════════════════════════
//  ROUTE: /:site/...   WordPress Site Proxy  (UNCHANGED)
// ════════════════════════════════════════════════════════════════

$requestUri = $uri;
$query = $qs;

$segments = explode('/', ltrim($path, '/'));
$site = array_shift($segments);
if (!$site || !preg_match('/^[A-Za-z0-9_-]+$/', $site)) {
    http_response_code(404);
    echo 'Not found';
    exit;
}

$siteDir = realpath($sitesDir . '/' . $site);
if ($siteDir === false || !is_dir($siteDir)) {
    http_response_code(404);
    echo 'Site not found';
    exit;
}

$subPath = '/' . implode('/', $segments);
if ($subPath === '/') {
    $subPath = '/index.php';
}

$target = realpath($siteDir . $subPath);
$siteReal = realpath($siteDir);
if ($target !== false && str_starts_with($target, $siteReal) && is_dir($target)) {
    if (!str_ends_with($path, '/')) {
        $qs2 = isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] !== '' ? '?' . $_SERVER['QUERY_STRING'] : '';
        header('Location: ' . $path . '/' . $qs2, true, 301);
        exit;
    }
    $indexInDir = realpath($target . '/index.php');
    if ($indexInDir !== false && is_file($indexInDir)) {
        $target = $indexInDir;
    }
}

$_SERVER['DOCUMENT_ROOT'] = $siteDir;
$_SERVER['HTTP_HOST'] = '127.0.0.1:8080';
$_SERVER['SERVER_NAME'] = '127.0.0.1';
$_SERVER['SERVER_PORT'] = '8080';
$_SERVER['REQUEST_SCHEME'] = 'http';
$_SERVER['HTTPS'] = 'off';
$_SERVER['REQUEST_URI'] = $requestUri;

if ($target !== false && str_starts_with($target, $siteReal) && is_file($target) && pathinfo($target, PATHINFO_EXTENSION) !== 'php') {
    header('Content-Type: ' . mime_for($target));
    readfile($target);
    exit;
}

if ($target !== false && str_starts_with($target, $siteReal) && is_file($target) && pathinfo($target, PATHINFO_EXTENSION) === 'php') {
    $relativePhp = str_replace('\\', '/', substr($target, strlen($siteReal)));
    $_SERVER['SCRIPT_NAME'] = '/' . $site . $relativePhp;
    $_SERVER['PHP_SELF'] = '/' . $site . $relativePhp;
    $_SERVER['SCRIPT_FILENAME'] = $target;
    $_SERVER['PATH_INFO'] = '';
    chdir(dirname($target));
    require $target;
    exit;
}

$_SERVER['SCRIPT_NAME'] = '/' . $site . '/index.php';
$_SERVER['PHP_SELF'] = '/' . $site . '/index.php';
$_SERVER['SCRIPT_FILENAME'] = $siteDir . '/index.php';
$_SERVER['PATH_INFO'] = '';
chdir($siteDir);
require $siteDir . '/index.php';
