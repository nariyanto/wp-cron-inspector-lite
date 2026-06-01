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
        $report_text = $inspector->reportText();
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('WP Cron Inspector Lite', 'wp-cron-inspector-lite'); ?></h1>
            <p><?php echo esc_html__('Read-only overview of scheduled WP-Cron events, duplicate hooks, and unusually frequent schedules.', 'wp-cron-inspector-lite'); ?></p>

            <h2><?php echo esc_html__('Summary', 'wp-cron-inspector-lite'); ?></h2>
            <ul>
                <li><?php /* translators: %d: total number of scheduled cron events. */ printf(esc_html__('Total events: %d', 'wp-cron-inspector-lite'), (int) $report['summary']['total_events']); ?></li>
                <li><?php /* translators: %d: number of cron event instances whose hook appears more than once. */ printf(esc_html__('Duplicate event instances: %d', 'wp-cron-inspector-lite'), (int) $report['summary']['duplicate_hook_count']); ?></li>
                <li><?php /* translators: %d: number of cron hooks scheduled more frequently than hourly. */ printf(esc_html__('Frequent hooks: %d', 'wp-cron-inspector-lite'), (int) $report['summary']['frequent_hook_count']); ?></li>
                <li><?php /* translators: %d: number of cron events scheduled before the current time. */ printf(esc_html__('Overdue events: %d', 'wp-cron-inspector-lite'), (int) $report['summary']['overdue_event_count']); ?></li>
            </ul>

            <?php if (! empty($report['duplicates'])) : ?>
                <h2><?php echo esc_html__('Duplicate hooks', 'wp-cron-inspector-lite'); ?></h2>
                <p><?php echo esc_html(implode(', ', $report['duplicates'])); ?></p>
            <?php endif; ?>

            <?php if (! empty($report['frequent_hooks'])) : ?>
                <h2><?php echo esc_html__('Unusually frequent hooks', 'wp-cron-inspector-lite'); ?></h2>
                <p><?php echo esc_html(implode(', ', $report['frequent_hooks'])); ?></p>
            <?php endif; ?>

            <h2><?php echo esc_html__('Copy support report', 'wp-cron-inspector-lite'); ?></h2>
            <p><?php echo esc_html__('Use this read-only report in support tickets or debugging notes. Review it before sharing externally.', 'wp-cron-inspector-lite'); ?></p>
            <p>
                <button type="button" class="button button-secondary" id="wp-cron-inspector-lite-copy-report">
                    <?php echo esc_html__('Copy report', 'wp-cron-inspector-lite'); ?>
                </button>
                <span id="wp-cron-inspector-lite-copy-status" aria-live="polite" style="margin-left: 8px;"></span>
            </p>
            <textarea id="wp-cron-inspector-lite-report" class="large-text code" rows="12" readonly><?php echo esc_textarea($report_text); ?></textarea>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var button = document.getElementById('wp-cron-inspector-lite-copy-report');
                    var report = document.getElementById('wp-cron-inspector-lite-report');
                    var status = document.getElementById('wp-cron-inspector-lite-copy-status');

                    if (!button || !report || !status) {
                        return;
                    }

                    button.addEventListener('click', function () {
                        report.focus();
                        report.select();

                        var markCopied = function () {
                            status.textContent = <?php echo wp_json_encode(__('Copied.', 'wp-cron-inspector-lite')); ?>;
                        };

                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            navigator.clipboard.writeText(report.value).then(markCopied).catch(function () {
                                document.execCommand('copy');
                                markCopied();
                            });
                            return;
                        }

                        document.execCommand('copy');
                        markCopied();
                    });
                });
            </script>

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
