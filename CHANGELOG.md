# Changelog

All notable changes to Cron Inspector Lite will be documented in this file.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project uses semantic versioning for GitHub releases.

## [0.1.0] - 2026-06-02

### Added

- Read-only admin page under **Tools → Cron Inspector**.
- Scheduled WP-Cron event list with hook, next run time, schedule, interval, and flags.
- Duplicate hook detection for support/debugging workflows.
- Unusually frequent recurring hook detection for intervals shorter than one hour.
- Overdue event counting.
- Copyable plain-text support report.
- Translation template for the `cron-inspector-lite` text domain.
- Local PHP test runner and GitHub Actions CI.

### Notes

- Version 0.1.0 does not delete, reschedule, or mutate cron events.
- The plugin does not send data to external services.
