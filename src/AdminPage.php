<?php

declare(strict_types=1);

namespace Nariyanto\WPCronInspectorLite;

final class AdminPage
{
    public const CAPABILITY = 'manage_options';

    public function register(): void
    {
        add_management_page(
            __('WP Cron Inspector Lite', 'wp-cron-inspector-lite'),
            __('Cron Inspector', 'wp-cron-inspector-lite'),
            self::CAPABILITY,
            'wp-cron-inspector-lite',
            [$this, 'render']
        );
    }

    public function render(): void
    {
        if (! current_user_can(self::CAPABILITY)) {
            wp_die(esc_html__('You do not have permission to view this page.', 'wp-cron-inspector-lite'));
        }

        $cron = function_exists('_get_cron_array') ? _get_cron_array() : [];
        $inspector = new CronEventInspector(is_array($cron) ? $cron : []);
        $report = $inspector->report();
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('WP Cron Inspector Lite', 'wp-cron-inspector-lite'); ?></h1>
            <p><?php echo esc_html__('Read-only overview of scheduled WP-Cron events, duplicate hooks, and unusually frequent schedules.', 'wp-cron-inspector-lite'); ?></p>

            <h2><?php echo esc_html__('Summary', 'wp-cron-inspector-lite'); ?></h2>
            <ul>
                <li><?php printf(esc_html__('Total events: %d', 'wp-cron-inspector-lite'), (int) $report['summary']['total_events']); ?></li>
                <li><?php printf(esc_html__('Duplicate event instances: %d', 'wp-cron-inspector-lite'), (int) $report['summary']['duplicate_hook_count']); ?></li>
                <li><?php printf(esc_html__('Frequent hooks: %d', 'wp-cron-inspector-lite'), (int) $report['summary']['frequent_hook_count']); ?></li>
                <li><?php printf(esc_html__('Overdue events: %d', 'wp-cron-inspector-lite'), (int) $report['summary']['overdue_event_count']); ?></li>
            </ul>

            <?php if (! empty($report['duplicates'])) : ?>
                <h2><?php echo esc_html__('Duplicate hooks', 'wp-cron-inspector-lite'); ?></h2>
                <p><?php echo esc_html(implode(', ', $report['duplicates'])); ?></p>
            <?php endif; ?>

            <?php if (! empty($report['frequent_hooks'])) : ?>
                <h2><?php echo esc_html__('Unusually frequent hooks', 'wp-cron-inspector-lite'); ?></h2>
                <p><?php echo esc_html(implode(', ', $report['frequent_hooks'])); ?></p>
            <?php endif; ?>

            <h2><?php echo esc_html__('Events', 'wp-cron-inspector-lite'); ?></h2>
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th><?php echo esc_html__('Hook', 'wp-cron-inspector-lite'); ?></th>
                        <th><?php echo esc_html__('Next run (GMT)', 'wp-cron-inspector-lite'); ?></th>
                        <th><?php echo esc_html__('Schedule', 'wp-cron-inspector-lite'); ?></th>
                        <th><?php echo esc_html__('Interval', 'wp-cron-inspector-lite'); ?></th>
                        <th><?php echo esc_html__('Flags', 'wp-cron-inspector-lite'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($report['events'] as $event) : ?>
                        <?php $flags = array_filter([! empty($event['is_overdue']) ? __('overdue', 'wp-cron-inspector-lite') : '', ! empty($event['is_frequent']) ? __('frequent', 'wp-cron-inspector-lite') : '']); ?>
                        <tr>
                            <td><code><?php echo esc_html((string) $event['hook']); ?></code></td>
                            <td><?php echo esc_html((string) $event['next_run_gmt']); ?></td>
                            <td><?php echo esc_html((string) $event['schedule']); ?></td>
                            <td><?php echo esc_html(null === $event['interval'] ? '-' : (string) $event['interval']); ?></td>
                            <td><?php echo esc_html(implode(', ', $flags)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}
