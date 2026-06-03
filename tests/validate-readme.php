<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$readme = file_get_contents($root . '/readme.txt');
$plugin = file_get_contents($root . '/snworks-cron-diagnostics.php');

if (false === $readme || false === $plugin) {
    fwrite(STDERR, "Unable to read plugin metadata files.\n");
    exit(1);
}

$failures = [];

$required_readme_patterns = [
    '/^=== SNWorks Cron Diagnostics ===$/m' => 'readme plugin title',
    '/^Contributors:\s*nariyanto$/m' => 'readme contributors',
    '/^Requires at least:\s*6\.0$/m' => 'minimum WordPress version',
    '/^Requires PHP:\s*7\.4$/m' => 'minimum PHP version',
    '/^Stable tag:\s*0\.1\.2$/m' => 'stable tag',
    '/^License:\s*GPLv2 or later$/m' => 'license',
    '/== Changelog ==/' => 'changelog section',
    '/= 0\.1\.2 =/' => '0.1.2 changelog entry',
];

foreach ($required_readme_patterns as $pattern => $label) {
    if (1 !== preg_match($pattern, $readme)) {
        $failures[] = "Missing or invalid {$label}.";
    }
}

$required_plugin_patterns = [
    '/Plugin Name:\s*SNWorks Cron Diagnostics/' => 'plugin name',
    '/Version:\s*0\.1\.2/' => 'plugin version',
    '/Text Domain:\s*snworks-cron-diagnostics/' => 'text domain',
    '/Domain Path:\s*\/languages/' => 'domain path',
    '/Requires at least:\s*6\.0/' => 'plugin minimum WordPress version',
    '/Requires PHP:\s*7\.4/' => 'plugin minimum PHP version',
    '/License:\s*GPL-2\.0-or-later/' => 'plugin license',
];

foreach ($required_plugin_patterns as $pattern => $label) {
    if (1 !== preg_match($pattern, $plugin)) {
        $failures[] = "Missing or invalid {$label}.";
    }
}

if (str_contains($readme, 'Cron Inspector Lite') || str_contains($readme, 'cron-inspector-lite')) {
    $failures[] = 'readme.txt still contains old plugin slug/name text.';
}

if ($failures) {
    foreach ($failures as $failure) {
        fwrite(STDERR, "FAIL: {$failure}\n");
    }
    exit(1);
}

echo "Readme and plugin metadata validation passed.\n";
