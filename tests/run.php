<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/CronEventInspector.php';

use Nariyanto\WPCronInspectorLite\CronEventInspector;

$failures = 0;

function assert_true(bool $condition, string $message): void {
    global $failures;
    if (!$condition) {
        $failures++;
        fwrite(STDERR, "FAIL: {$message}\n");
    }
}

function assert_same($expected, $actual, string $message): void {
    global $failures;
    if ($expected !== $actual) {
        $failures++;
        fwrite(STDERR, "FAIL: {$message}\nExpected: " . var_export($expected, true) . "\nActual: " . var_export($actual, true) . "\n");
    }
}

$cron = [
    'version' => 2,
    1700000000 => [
        'peepso_daily_email' => [
            'abc' => [
                'schedule' => 'daily',
                'args' => ['site_id' => 1],
                'interval' => 86400,
            ],
        ],
        'backup_every_minute' => [
            'def' => [
                'schedule' => 'every_minute',
                'args' => [],
                'interval' => 60,
            ],
        ],
    ],
    1700003600 => [
        'peepso_daily_email' => [
            'ghi' => [
                'schedule' => 'daily',
                'args' => ['site_id' => 1],
                'interval' => 86400,
            ],
        ],
        'single_cleanup' => [
            'jkl' => [
                'schedule' => false,
                'args' => [],
            ],
        ],
    ],
];

$inspector = new CronEventInspector($cron, 1700000100);
$events = $inspector->events();
$report = $inspector->report();

assert_same(4, count($events), 'it flattens the WordPress cron array into four events');
$hooks = array_column($events, 'hook');
assert_true(in_array('peepso_daily_email', $hooks, true), 'it preserves hook names');
assert_true((bool) array_filter($events, static fn (array $event): bool => 'peepso_daily_email' === $event['hook'] && true === $event['is_overdue']), 'it marks past events as overdue');
assert_same(2, $report['summary']['duplicate_hook_count'], 'it counts duplicate hook event instances');
assert_true(in_array('peepso_daily_email', $report['duplicates'], true), 'it reports duplicate hooks by name');
assert_true(in_array('backup_every_minute', $report['frequent_hooks'], true), 'it reports unusually frequent recurring hooks');
assert_same(1, $report['summary']['frequent_hook_count'], 'it counts frequent hooks');

$report_text = $inspector->reportText();
assert_true(strpos($report_text, 'Cron Inspector Lite Report') !== false, 'it creates a copy-friendly text report title');
assert_true(strpos($report_text, 'Total events: 4') !== false, 'it includes summary totals in the text report');
assert_true(strpos($report_text, 'Duplicate hooks: peepso_daily_email') !== false, 'it includes duplicate hooks in the text report');
assert_true(strpos($report_text, 'Frequent hooks: backup_every_minute') !== false, 'it includes frequent hooks in the text report');
assert_true(strpos($report_text, 'peepso_daily_email | 2023-11-14 22:13:20 | daily | 86400 | overdue') !== false, 'it includes event rows in the text report');

if ($failures > 0) {
    fwrite(STDERR, "{$failures} test failure(s).\n");
    exit(1);
}

echo "All tests passed.\n";
