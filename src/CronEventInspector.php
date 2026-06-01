<?php

declare(strict_types=1);

namespace Nariyanto\WPCronInspectorLite;

/**
 * Turns WordPress' internal cron array into a support-friendly report.
 */
final class CronEventInspector
{
    /** @var array<string|int,mixed> */
    private array $cron;

    private int $now;

    /**
     * @param array<string|int,mixed> $cron WordPress cron array from _get_cron_array().
     */
    public function __construct(array $cron, ?int $now = null)
    {
        $this->cron = $cron;
        $this->now = $now ?? time();
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function events(): array
    {
        $events = [];

        foreach ($this->cron as $timestamp => $hooks) {
            if ('version' === (string) $timestamp || ! is_array($hooks)) {
                continue;
            }

            foreach ($hooks as $hook => $instances) {
                if (! is_array($instances)) {
                    continue;
                }

                foreach ($instances as $event_key => $event) {
                    if (! is_array($event)) {
                        continue;
                    }

                    $run_at = (int) $timestamp;
                    $interval = isset($event['interval']) ? (int) $event['interval'] : null;
                    $schedule = $event['schedule'] ?? false;

                    $hour_in_seconds = defined('HOUR_IN_SECONDS') ? (int) constant('HOUR_IN_SECONDS') : 3600;

                    $events[] = [
                        'hook' => (string) $hook,
                        'event_key' => (string) $event_key,
                        'timestamp' => $run_at,
                        'next_run_gmt' => gmdate('Y-m-d H:i:s', $run_at),
                        'schedule' => false === $schedule ? 'single' : (string) $schedule,
                        'interval' => $interval,
                        'args' => $event['args'] ?? [],
                        'is_overdue' => $run_at < $this->now,
                        'is_frequent' => null !== $interval && $interval > 0 && $interval < $hour_in_seconds,
                    ];
                }
            }
        }

        usort(
            $events,
            static function (array $a, array $b): int {
                return [$a['timestamp'], $a['hook']] <=> [$b['timestamp'], $b['hook']];
            }
        );

        return $events;
    }

    /**
     * @return array{summary:array<string,int>,duplicates:array<int,string>,frequent_hooks:array<int,string>,events:array<int,array<string,mixed>>}
     */
    public function report(): array
    {
        $events = $this->events();
        $hook_counts = [];
        $frequent_hooks = [];
        $overdue_count = 0;

        foreach ($events as $event) {
            $hook = (string) $event['hook'];
            $hook_counts[$hook] = ($hook_counts[$hook] ?? 0) + 1;

            if (! empty($event['is_frequent'])) {
                $frequent_hooks[$hook] = true;
            }

            if (! empty($event['is_overdue'])) {
                $overdue_count++;
            }
        }

        $duplicates = [];
        $duplicate_event_count = 0;

        foreach ($hook_counts as $hook => $count) {
            if ($count > 1) {
                $duplicates[] = $hook;
                $duplicate_event_count += $count;
            }
        }

        sort($duplicates);
        $frequent_hook_names = array_keys($frequent_hooks);
        sort($frequent_hook_names);

        return [
            'summary' => [
                'total_events' => count($events),
                'duplicate_hook_count' => $duplicate_event_count,
                'frequent_hook_count' => count($frequent_hook_names),
                'overdue_event_count' => $overdue_count,
            ],
            'duplicates' => $duplicates,
            'frequent_hooks' => $frequent_hook_names,
            'events' => $events,
        ];
    }
}
