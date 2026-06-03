<?php

declare(strict_types=1);

namespace SNWorks\CronDiagnostics;

final class AdminPage
{
    public const CAPABILITY = 'manage_options';

    /** @var string */
    private string $page_hook = '';

    public function register(): void
    {
        $this->page_hook = (string) add_management_page(
            __('SNWorks Cron Diagnostics', 'snworks-cron-diagnostics'),
            __('Cron Diagnostics', 'snworks-cron-diagnostics'),
            self::CAPABILITY,
            'snworks-cron-diagnostics',
            [$this, 'render']
        );

        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
    }

    public function enqueueAssets(string $hook_suffix): void
    {
        if ($hook_suffix !== $this->page_hook) {
            return;
        }

        wp_enqueue_script(
            'snworks-cron-diagnostics-admin',
            plugins_url('assets/admin.js', SNWORKS_CRON_DIAGNOSTICS_FILE),
            [],
            SNWORKS_CRON_DIAGNOSTICS_VERSION,
            true
        );
    }

    public function render(): void
    {
        if (! current_user_can(self::CAPABILITY)) {
            wp_die(esc_html__('You do not have permission to view this page.', 'snworks-cron-diagnostics'));
        }

        $cron = function_exists('_get_cron_array') ? _get_cron_array() : [];
        $inspector = new CronEventInspector(is_array($cron) ? $cron : []);
        $report = $inspector->report();
        $report_text = $inspector->reportText();
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('SNWorks Cron Diagnostics', 'snworks-cron-diagnostics'); ?></h1>
            <p><?php echo esc_html__('Read-only overview of scheduled WP-Cron events, duplicate hooks, and unusually frequent schedules.', 'snworks-cron-diagnostics'); ?></p>

            <h2><?php echo esc_html__('Summary', 'snworks-cron-diagnostics'); ?></h2>
            <ul>
                <li><?php /* translators: %d: total number of scheduled cron events. */ printf(esc_html__('Total events: %d', 'snworks-cron-diagnostics'), (int) $report['summary']['total_events']); ?></li>
                <li><?php /* translators: %d: number of cron event instances whose hook appears more than once. */ printf(esc_html__('Duplicate event instances: %d', 'snworks-cron-diagnostics'), (int) $report['summary']['duplicate_hook_count']); ?></li>
                <li><?php /* translators: %d: number of cron hooks scheduled more frequently than hourly. */ printf(esc_html__('Frequent hooks: %d', 'snworks-cron-diagnostics'), (int) $report['summary']['frequent_hook_count']); ?></li>
                <li><?php /* translators: %d: number of cron events scheduled before the current time. */ printf(esc_html__('Overdue events: %d', 'snworks-cron-diagnostics'), (int) $report['summary']['overdue_event_count']); ?></li>
            </ul>

            <?php if (! empty($report['duplicates'])) : ?>
                <h2><?php echo esc_html__('Duplicate hooks', 'snworks-cron-diagnostics'); ?></h2>
                <p><?php echo esc_html(implode(', ', $report['duplicates'])); ?></p>
            <?php endif; ?>

            <?php if (! empty($report['frequent_hooks'])) : ?>
                <h2><?php echo esc_html__('Unusually frequent hooks', 'snworks-cron-diagnostics'); ?></h2>
                <p><?php echo esc_html(implode(', ', $report['frequent_hooks'])); ?></p>
            <?php endif; ?>

            <h2><?php echo esc_html__('Copy support report', 'snworks-cron-diagnostics'); ?></h2>
            <p><?php echo esc_html__('Use this read-only report in support tickets or debugging notes. Review it before sharing externally.', 'snworks-cron-diagnostics'); ?></p>
            <p>
                <button type="button" class="button button-secondary" id="snworks-cron-diagnostics-copy-report">
                    <?php echo esc_html__('Copy report', 'snworks-cron-diagnostics'); ?>
                </button>
                <span id="snworks-cron-diagnostics-copy-status" aria-live="polite" data-copied-text="<?php echo esc_attr__('Copied.', 'snworks-cron-diagnostics'); ?>"></span>
            </p>
            <textarea id="snworks-cron-diagnostics-report" class="large-text code" rows="12" readonly><?php echo esc_textarea($report_text); ?></textarea>

            <h2><?php echo esc_html__('Events', 'snworks-cron-diagnostics'); ?></h2>
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th><?php echo esc_html__('Hook', 'snworks-cron-diagnostics'); ?></th>
                        <th><?php echo esc_html__('Next run (GMT)', 'snworks-cron-diagnostics'); ?></th>
                        <th><?php echo esc_html__('Schedule', 'snworks-cron-diagnostics'); ?></th>
                        <th><?php echo esc_html__('Interval', 'snworks-cron-diagnostics'); ?></th>
                        <th><?php echo esc_html__('Flags', 'snworks-cron-diagnostics'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($report['events'] as $event) : ?>
                        <?php $flags = array_filter([! empty($event['is_overdue']) ? __('overdue', 'snworks-cron-diagnostics') : '', ! empty($event['is_frequent']) ? __('frequent', 'snworks-cron-diagnostics') : '']); ?>
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
