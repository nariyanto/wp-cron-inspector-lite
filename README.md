# Cron Inspector Lite

[![CI](https://github.com/nariyanto/wp-cron-inspector-lite/actions/workflows/ci.yml/badge.svg)](https://github.com/nariyanto/wp-cron-inspector-lite/actions/workflows/ci.yml)
[![Latest Release](https://img.shields.io/github/v/release/nariyanto/wp-cron-inspector-lite?label=release)](https://github.com/nariyanto/wp-cron-inspector-lite/releases)
[![Packagist Version](https://img.shields.io/packagist/v/nariyanto/wp-cron-inspector-lite?label=packagist)](https://packagist.org/packages/nariyanto/wp-cron-inspector-lite)
[![PHP](https://img.shields.io/badge/PHP-%3E%3D%207.4-777bb4.svg)](https://www.php.net/)
[![WordPress](https://img.shields.io/badge/WordPress-%3E%3D%206.0-21759b.svg)](https://wordpress.org/)
[![License](https://img.shields.io/badge/license-GPL--2.0--or--later-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

Find duplicate, stuck, and suspicious WordPress cron events before they cause support issues.

This plugin is read-only in the initial version. It lists scheduled WP-Cron events, highlights duplicate hooks, flags unusually frequent recurring events, and provides a support-friendly report.

## Screenshot

![Cron Inspector Lite running on PeepSo staging](docs/screenshots/peepso-staging-cron-inspector.png)

## Current v0.1 scope

- Admin page under **Tools → Cron Inspector**
- Scheduled event list
- Duplicate hook detection
- Unusually frequent event detection
- Copy support report button for support tickets/debugging notes
- Capability checks and escaped output
- No destructive cleanup actions

## Development

```bash
php tests/run.php
php tests/validate-readme.php
find . -path ./vendor -prune -o -name '*.php' -print0 | xargs -0 -n1 php -l
bash scripts/build-release.sh
```

The release package is written to `dist/cron-inspector-lite.zip`.

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## License

GPL-2.0-or-later
