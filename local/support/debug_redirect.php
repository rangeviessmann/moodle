<?php
/**
 * Diagnostic script - finds old domain references causing redirect issues.
 * DELETE THIS FILE after use.
 */

define('CLI_SCRIPT', false);
define('NO_MOODLE_COOKIES', true);
require_once(__DIR__ . '/../../config.php');

// Basic protection.
if (!is_siteadmin()) {
    die('Admin only.');
}

$oldurl = optional_param('url', 'srv107651', PARAM_TEXT);

echo '<html><head><meta charset="utf-8"><title>Redirect Diagnostic</title>';
echo '<style>body{font-family:monospace;font-size:13px;padding:20px;}
h2{background:#333;color:#fff;padding:6px 10px;}
.ok{color:green;} .warn{color:orange;} .err{color:red;font-weight:bold;}
table{border-collapse:collapse;width:100%;margin-bottom:20px;}
td,th{border:1px solid #ccc;padding:4px 8px;text-align:left;}
th{background:#eee;}
.hit{background:#fff3cd;}
</style></head><body>';

echo '<h1>Redirect Diagnostic</h1>';
echo '<form method="get"><label>Szukana fraza: <input name="url" value="' . s($oldurl) . '" size="40"></label> <input type="submit" value="Szukaj"></form><hr>';

if (empty($oldurl)) {
    die('Podaj frazę do szukania.');
}

echo '<h2>1. Runtime — co Moodle widzi teraz</h2>';
echo '<table><tr><th>Zmienna</th><th>Wartość</th></tr>';
echo '<tr><td>$CFG->wwwroot</td><td>' . s($CFG->wwwroot) . '</td></tr>';
echo '<tr><td>$_SERVER[HTTP_HOST]</td><td>' . s($_SERVER['HTTP_HOST'] ?? 'brak') . '</td></tr>';
echo '<tr><td>$_SERVER[SERVER_NAME]</td><td>' . s($_SERVER['SERVER_NAME'] ?? 'brak') . '</td></tr>';
echo '<tr><td>$_SERVER[HTTPS]</td><td>' . s($_SERVER['HTTPS'] ?? 'brak') . '</td></tr>';
echo '<tr><td>$_SERVER[REQUEST_SCHEME]</td><td>' . s($_SERVER['REQUEST_SCHEME'] ?? 'brak') . '</td></tr>';
echo '<tr><td>$_SERVER[HTTP_X_FORWARDED_PROTO]</td><td>' . s($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'brak') . '</td></tr>';
echo '<tr><td>$_SERVER[SCRIPT_NAME]</td><td>' . s($_SERVER['SCRIPT_NAME'] ?? 'brak') . '</td></tr>';
$qualified = (new moodle_url('/'))->out(false);
$class = (strpos($qualified, $oldurl) !== false) ? 'err' : 'ok';
echo '<tr><td>moodle_url(\'/\')</td><td class="' . $class . '">' . s($qualified) . '</td></tr>';
echo '</table>';

echo '<h2>2. Tabela config (wszystkie wpisy z URL)</h2>';
$rows = $DB->get_records_select('config', "value LIKE '%http%' OR name LIKE '%url%' OR name LIKE '%wwwroot%'", [], 'name');
echo '<table><tr><th>name</th><th>value</th></tr>';
foreach ($rows as $r) {
    $hit = (stripos($r->value, $oldurl) !== false);
    echo '<tr' . ($hit ? ' class="hit"' : '') . '><td>' . s($r->name) . '</td><td>' . s(substr($r->value, 0, 300)) . '</td></tr>';
}
echo '</table>';

echo '<h2>3. Tabela config_plugins — dopasowania do "' . s($oldurl) . '"</h2>';
$rows = $DB->get_records_select('config_plugins', $DB->sql_like('value', ':search', false), ['search' => '%' . $oldurl . '%']);
if (empty($rows)) {
    echo '<p class="ok">Brak wyników.</p>';
} else {
    echo '<table><tr><th>plugin</th><th>name</th><th>value</th></tr>';
    foreach ($rows as $r) {
        echo '<tr class="hit"><td>' . s($r->plugin) . '</td><td>' . s($r->name) . '</td><td>' . s(substr($r->value, 0, 300)) . '</td></tr>';
    }
    echo '</table>';
}

echo '<h2>4. Wszystkie config_plugins z URL (auth / tool / core)</h2>';
$rows = $DB->get_records_select('config_plugins', "(plugin LIKE 'auth%' OR plugin LIKE 'tool%' OR plugin = 'core') AND " . $DB->sql_like('value', ':s', false), ['s' => '%http%'], 'plugin, name');
echo '<table><tr><th>plugin</th><th>name</th><th>value</th></tr>';
foreach ($rows as $r) {
    $hit = (stripos($r->value, $oldurl) !== false);
    echo '<tr' . ($hit ? ' class="hit"' : '') . '><td>' . s($r->plugin) . '</td><td>' . s($r->name) . '</td><td>' . s(substr($r->value, 0, 300)) . '</td></tr>';
}
echo '</table>';

echo '<h2>5. Sesje ze starym URL</h2>';
try {
    $count = $DB->count_records_select('sessions', $DB->sql_like('sessdata', ':s', false), ['s' => '%' . $oldurl . '%']);
    if ($count > 0) {
        echo '<p class="err">Znaleziono ' . $count . ' sesji ze starym URL. <a href="?url=' . urlencode($oldurl) . '&clearsessions=1">Wyczyść sesje</a></p>';
        if (optional_param('clearsessions', 0, PARAM_INT)) {
            $DB->delete_records('sessions');
            echo '<p class="ok">Sesje wyczyszczone.</p>';
        }
    } else {
        echo '<p class="ok">Brak sesji ze starym URL.</p>';
    }
} catch (Exception $e) {
    echo '<p class="warn">Nie udało się sprawdzić sesji: ' . s($e->getMessage()) . '</p>';
}

echo '<h2>6. Tabela user_preferences z URL</h2>';
try {
    $rows = $DB->get_records_select('user_preferences', $DB->sql_like('value', ':s', false), ['s' => '%' . $oldurl . '%'], '', 'id, userid, name, value', 0, 20);
    if (empty($rows)) {
        echo '<p class="ok">Brak wyników.</p>';
    } else {
        echo '<table><tr><th>userid</th><th>name</th><th>value</th></tr>';
        foreach ($rows as $r) {
            echo '<tr class="hit"><td>' . s($r->userid) . '</td><td>' . s($r->name) . '</td><td>' . s(substr($r->value, 0, 300)) . '</td></tr>';
        }
        echo '</table>';
    }
} catch (Exception $e) {
    echo '<p class="warn">Błąd: ' . s($e->getMessage()) . '</p>';
}

echo '<h2>7. Auth plugin — aktywne metody i ich ustawienia URL</h2>';
$authsenabled = get_string('authsenabled', 'auth');
$enabledauths = get_enabled_auth_plugins();
echo '<p>Aktywne: <strong>' . implode(', ', $enabledauths) . '</strong></p>';
echo '<table><tr><th>plugin</th><th>name</th><th>value</th></tr>';
foreach ($enabledauths as $auth) {
    $rows = $DB->get_records_select('config_plugins', "plugin = :p AND (" . $DB->sql_like('value', ':s', false) . " OR " . $DB->sql_like('name', ':n', false) . ")", ['p' => 'auth_' . $auth, 's' => '%http%', 'n' => '%url%'], 'name');
    foreach ($rows as $r) {
        $hit = (stripos($r->value, $oldurl) !== false);
        echo '<tr' . ($hit ? ' class="hit"' : '') . '><td>auth_' . s($auth) . '</td><td>' . s($r->name) . '</td><td>' . s(substr($r->value, 0, 300)) . '</td></tr>';
    }
}
echo '</table>';

echo '<h2>8. Plik .htaccess w katalogu Moodle</h2>';
$htaccess = $CFG->dirroot . '/.htaccess';
if (file_exists($htaccess)) {
    $content = file_get_contents($htaccess);
    $hit = stripos($content, $oldurl) !== false;
    echo '<pre' . ($hit ? ' class="err"' : '') . '>' . s($content) . '</pre>';
} else {
    echo '<p class="ok">Brak pliku .htaccess w katalogu Moodle.</p>';
}

echo '<h2>9. config.php — fragment</h2>';
$configfile = $CFG->dirroot . '/config.php';
$content = file_get_contents($configfile);
$hit = stripos($content, $oldurl) !== false;
echo '<pre' . ($hit ? ' class="err"' : '') . '>' . s(substr($content, 0, 2000)) . '</pre>';

echo '<hr><p><em>Usuń ten plik po użyciu: <code>local/support/debug_redirect.php</code></em></p>';
echo '</body></html>';
