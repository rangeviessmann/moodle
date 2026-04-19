<?php
require_once(__DIR__ . '/../../config.php');
require_login();

echo '<pre>';
echo 'active_direction_id: ' . ($SESSION->active_direction_id ?? 'NOT SET') . "\n";
echo 'Body classes queued: ' . $PAGE->bodyclasses . "\n";
echo 'additionalhtmlhead: ' . ($CFG->additionalhtmlhead ?? 'EMPTY') . "\n";
echo '</pre>';
