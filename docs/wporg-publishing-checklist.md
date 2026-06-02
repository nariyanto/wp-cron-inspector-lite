# WordPress.org Publishing Checklist

Repository: `wp-cron-inspector-lite`
Plugin slug target: `cron-inspector-lite`

## Current status

- Public GitHub repository exists.
- Plugin has been tested on the staging site `peepso.nariyanto.id`.
- Plugin is read-only and does not delete or mutate cron events.
- CI runs PHP tests, readme metadata validation, and PHP syntax checks.
- Release workflow builds `dist/cron-inspector-lite.zip` for GitHub release assets.
- A screenshot from the staging site is available in `docs/screenshots/` for GitHub documentation.
- WordPress.org banner/icon assets are prepared in `.wordpress-org/`.
- Submission notes are prepared in `docs/wordpress-org-submission-notes.md`.
- Translation template exists at `languages/cron-inspector-lite.pot`.

## Before WordPress.org submission

- [ ] Finalize the v0.1.0 feature scope; avoid adding destructive cleanup in the first submission.
- [ ] Review all user-facing strings for text domain `cron-inspector-lite`.
- [ ] Regenerate `languages/cron-inspector-lite.pot` after any string changes.
- [ ] Run local tests: `php tests/run.php`.
- [ ] Run PHP syntax checks: `find . -path ./vendor -prune -o -name '*.php' -print0 | xargs -0 -n1 php -l`.
- [ ] Confirm `readme.txt` metadata: contributors, tags, requires, tested up to, requires PHP, stable tag, license.
- [ ] Confirm plugin header metadata: version, license, text domain, domain path, minimum WP/PHP versions.
- [ ] Prepare a clean submission zip that excludes `.git`, tests, docs, screenshots, and development-only files unless intentionally included.
- [ ] Add WordPress.org assets after SVN approval: banner, icon, and screenshot files under the SVN `assets/` directory.
- [ ] Submit through the WordPress.org plugin submission form using the `nariyanto` WordPress.org account.

## Recommended first submission scope

Keep the first submission intentionally small:

- Read-only admin page under Tools → Cron Inspector.
- Cron event list.
- Duplicate hook detection.
- Frequent recurring hook detection.
- Overdue event count.
- Copyable support report.

Do not include cron deletion/cleanup actions until after the plugin is approved and users have a safe baseline release.

## WordPress.org review notes to emphasize

- The plugin is read-only.
- It does not send data to external services.
- It does not collect personal data.
- It only reads WordPress' cron array and displays it to administrators with `manage_options` capability.
- It is intended for support/debugging workflows.

## After approval

- [ ] Check out the assigned WordPress.org SVN repository.
- [ ] Copy plugin files into `trunk/`.
- [ ] Put screenshots/banner/icon in SVN `assets/`.
- [ ] Create `tags/0.1.0/` from the approved trunk.
- [ ] Commit to SVN.
- [ ] Verify the public plugin page renders correctly.
- [ ] Tag a matching GitHub release `v0.1.0`.
