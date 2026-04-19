<?php
/**
 * Replace hardcoded brand colours in compiled CSS with CSS custom property references.
 *
 * #e63612  ->  var(--bs-primary, #e63612)
 * #ca3120  ->  var(--bs-danger,  #ca3120)
 *
 * Run after every "Purge all caches" that rebuilds theme CSS:
 *   C:\xampp82\php\php.exe local/support/apply_color_vars.php
 *
 * Safe to run multiple times (idempotent).
 */

define('CLI_SCRIPT', true);
require_once(__DIR__ . '/../../config.php');

$cssfiles = [
    $CFG->dirroot . '/theme/boost/style/moodle.css',
    $CFG->dirroot . '/theme/classic/style/moodle.css',
];

foreach ($cssfiles as $file) {
    if (!file_exists($file)) {
        echo "SKIP (not found): $file\n";
        continue;
    }

    $original = file_get_contents($file);

    // Step 1: Replace all hardcoded occurrences with variable references.
    $patched = str_replace('#e63612', 'var(--bs-primary, #e63612)', $original);
    $patched = str_replace('#ca3120', 'var(--bs-danger,  #ca3120)', $patched);

    // Step 2: Fix the two circular definitions in :root.
    $patched = str_replace('--bs-primary: var(--bs-primary, #e63612)', '--bs-primary: #e63612', $patched);
    $patched = str_replace('--bs-danger: var(--bs-danger,  #ca3120)',  '--bs-danger: #ca3120',  $patched);

    if ($patched === $original) {
        echo "ALREADY PATCHED: $file\n";
        continue;
    }

    file_put_contents($file, $patched);

    $n_primary = substr_count($patched, 'var(--bs-primary, #e63612)');
    $n_danger  = substr_count($patched, 'var(--bs-danger,  #ca3120)');
    echo "PATCHED: $file  (primary: $n_primary, danger: $n_danger)\n";
}

echo "\nDone. Hard-refresh browser (Ctrl+Shift+R).\n";
