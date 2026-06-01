# WP Cron Inspector Lite

Find duplicate, stuck, and suspicious WordPress cron events before they cause support issues.

This plugin is read-only in the initial version. It lists scheduled WP-Cron events, highlights duplicate hooks, flags unusually frequent recurring events, and provides a support-friendly report.

## Planned v0.1 scope

- Admin page under **Tools → Cron Inspector**
- Scheduled event list
- Duplicate hook detection
- Unusually frequent event detection
- Copy/export-friendly report
- Capability checks and escaped output
- No destructive cleanup actions

## Development

```bash
php tests/run.php
find . -path ./vendor -prune -o -name '*.php' -print0 | xargs -0 -n1 php -l
```

## License

GPL-2.0-or-later
