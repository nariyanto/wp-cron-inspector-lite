<?php

declare(strict_types=1);

namespace Nariyanto\WPCronInspectorLite;

final class AdminPage
{
    public const CAPABILITY = 'manage_options';

    public function register(): void
    {
        add_management_page(
            __('Cron Inspector Lite', 'cron-inspector-lite'),
            __('Cron Inspector', 'cron-inspector-lite'),
            self::CAPABILITY,
            'cron-inspector-lite',
            [$this, 'render']
        );
    }

    public function render(): void
    {
        if (! current_user_can(self::CAPABILITY)) {
            wp_die(esc_html__('You do not have permission to view this page.', 'cron-inspector-lite'));
        }

        $cron = function_exists('_get_cron_array') ? _get_cron_array() : [];
        $inspector = new CronEventInspector(is_array($cron) ? $cron : []);
        $report = $inspector->report();
        $report_text = $inspector->reportText();
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('Cron Inspector Lite', 'cron-inspector-lite'); ?></h1>
            <p><?php echo esc_html__('Read-only overview of scheduled WP-Cron events, duplicate hooks, and unusually frequent schedules.', 'cron-inspector-lite'); ?></p>

            <h2><?php echo esc_html__('Summary', 'cron-inspector-lite'); ?></h2>
            <ul>
                <li><?php /* translators: %d: total number of scheduled cron events. */ printf(esc_html__('Total events: %d', 'cron-inspector-lite'), (int) $report['summary']['total_events']); ?></li>
                <li><?php /* translators: %d: number of cron event instances whose hook appears more than once. */ printf(esc_html__('Duplicate event instances: %d', 'cron-inspector-lite'), (int) $report['summary']['duplicate_hook_count']); ?></li>
                <li><?php /* translators: %d: number of cron hooks scheduled more frequently than hourly. */ printf(esc_html__('Frequent hooks: %d', 'cron-inspector-lite'), (int) $report['summary']['frequent_hook_count']); ?></li>
                <li><?php /* translators: %d: number of cron events scheduled before the current time. */ printf(esc_html__('Overdue events: %d', 'cron-inspector-lite'), (int) $report['summary']['overdue_event_count']); ?></li>
            </ul>

            <?php if (! empty($report['duplicates'])) : ?>
                <h2><?php echo esc_html__('Duplicate hooks', 'cron-inspector-lite'); ?></h2>
                <p><?php echo esc_html(implode(', ', $report['duplicates'])); ?></p>
            <?php endif; ?>

            <?php if (! empty($report['frequent_hooks'])) : ?>
                <h2><?php echo esc_html__('Unusually frequent hooks', 'cron-inspector-lite'); ?></h2>
                <p><?php echo esc_html(implode(', ', $report['frequent_hooks'])); ?></p>
            <?php endif; ?>

            <h2><?php echo esc_html__('Copy support report', 'cron-inspector-lite'); ?></h2>
            <p><?php echo esc_html__('Use this read-only report in support tickets or debugging notes. Review it before sharing externally.', 'cron-inspector-lite'); ?></p>
            <p>
                <button type="button" class="button button-secondary" id="cron-inspector-lite-copy-report">
                    <?php echo esc_html__('Copy report', 'cron-inspector-lite'); ?>
                </button>
                <span id="cron-inspector-lite-copy-status" aria-live="polite" style="margin-left: 8px;"></span>
            </p>
            <textarea id="cron-inspector-lite-report" class="large-text code" rows="12" readonly><?php echo esc_textarea($report_text); ?></textarea>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var button = document.getElementById('cron-inspector-lite-copy-report');
                    var report = document.getElementById('cron-inspector-lite-report');
                    var status = document.getElementById('cron-inspector-lite-copy-status');

                    if (!button || !report || !status) {
                        return;
                    }

                    button.addEventListener('click', function () {
                        report.focus();
                        report.select();

                        var markCopied = function () {
                            status.textContent = <?php echo wp_json_encode(__('Copied.', 'cron-inspector-lite')); ?>;
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

            <h2><?php echo esc_html__('Events', 'cron-inspector-lite'); ?></h2>
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th><?php echo esc_html__('Hook', 'cron-inspector-lite'); ?></th>
                        <th><?php echo esc_html__('Next run (GMT)', 'cron-inspector-lite'); ?></th>
                        <th><?php echo esc_html__('Schedule', 'cron-inspector-lite'); ?></th>
                        <th><?php echo esc_html__('Interval', 'cron-inspector-lite'); ?></th>
                        <th><?php echo esc_html__('Flags', 'cron-inspector-lite'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($report['events'] as $event) : ?>
                        <?php $flags = array_filter([! empty($event['is_overdue']) ? __('overdue', 'cron-inspector-lite') : '', ! empty($event['is_frequent']) ? __('frequent', 'cron-inspector-lite') : '']); ?>
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
