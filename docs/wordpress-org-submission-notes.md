# WordPress.org Submission Notes — Nariyanto Cron Diagnostics

Repository: https://github.com/nariyanto/wp-cron-inspector-lite
GitHub release: https://github.com/nariyanto/wp-cron-inspector-lite/releases/tag/v0.1.2
Release asset: `nariyanto-cron-diagnostics.zip`
Target plugin slug: `nariyanto-cron-diagnostics`
Version: `0.1.2`

## Short description

Find duplicate, stuck, and suspicious WordPress cron events before they cause support issues.

## Suggested submission description

Nariyanto Cron Diagnostics is a read-only admin utility for WordPress support and debugging workflows. It lists scheduled WP-Cron events, highlights duplicate hooks, flags unusually frequent recurring events, counts overdue events, and provides a copyable support report.

The first release is intentionally small and safe. It does not delete, reschedule, pause, or mutate cron events. It only reads WordPress' existing cron array and displays the information to administrators.

## Reviewer notes

- The plugin is read-only in version `0.1.2`.
- It does not create, delete, reschedule, pause, or mutate cron events.
- It does not send data to external services.
- It does not collect analytics or personal data.
- It does not store settings or write options in version `0.1.2`.
- It only displays cron information to users with the `manage_options` capability.
- The admin page is registered under **Tools → Cron Diagnostics**.
- All admin output is escaped.
- The plugin uses the `nariyanto-cron-diagnostics` text domain.
- The release ZIP excludes development-only files such as `.git`, `.github`, `tests`, `docs`, and `scripts`.

## Privacy statement

Nariyanto Cron Diagnostics does not collect, store, transmit, or share personal data. The plugin reads scheduled WP-Cron events from the local WordPress installation and displays them only inside the WordPress admin area to administrators.

The copyable support report is generated locally in the browser/admin page. Site administrators should review the report before sharing it in support tickets.

## Security and safety notes

- Access is restricted to users with `manage_options`.
- Version `0.1.2` has no destructive actions.
- The plugin does not include remote API calls.
- The plugin does not include tracking, telemetry, or third-party scripts.
- The plugin does not expose secrets intentionally; however, hook names and schedules may reveal operational details, so support reports should be reviewed before sharing externally.

## Manual submission steps

1. Log in to WordPress.org as `nariyanto`.
2. Open the plugin submission page: https://wordpress.org/plugins/developers/add/
3. Upload the GitHub release asset: `nariyanto-cron-diagnostics.zip`.
4. Use the short description above.
5. Add reviewer notes from this document if the form provides a notes field.
6. Submit for review.
7. Wait for the WordPress.org review result and assigned SVN repository.
8. After approval, copy plugin files to SVN `trunk/`, copy banner/icon/screenshot assets to SVN `assets/`, create `tags/0.1.2/`, and commit.

## Post-approval SVN checklist

- [ ] Check out the assigned WordPress.org SVN repository.
- [ ] Copy release files into `trunk/`.
- [ ] Copy `.wordpress-org` assets into SVN `assets/`.
- [ ] Create `tags/0.1.2/` from `trunk/`.
- [ ] Commit SVN changes.
- [ ] Verify plugin page metadata, screenshots, and download link.
- [ ] Link the WordPress.org plugin page from the GitHub README.
