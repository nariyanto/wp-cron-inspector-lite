#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PLUGIN_SLUG="snworks-cron-diagnostics"
DIST_DIR="$ROOT_DIR/dist"
BUILD_DIR="$DIST_DIR/build"
PACKAGE_DIR="$BUILD_DIR/$PLUGIN_SLUG"
ZIP_PATH="$DIST_DIR/$PLUGIN_SLUG.zip"

rm -rf "$BUILD_DIR" "$ZIP_PATH"
mkdir -p "$PACKAGE_DIR" "$DIST_DIR"

copy_file() {
  local src="$1"
  local dest="$PACKAGE_DIR/$src"
  mkdir -p "$(dirname "$dest")"
  cp "$ROOT_DIR/$src" "$dest"
}

copy_file "snworks-cron-diagnostics.php"
copy_file "readme.txt"
copy_file "CHANGELOG.md"
copy_file "assets/admin.js"
copy_file "languages/snworks-cron-diagnostics.pot"
copy_file "src/AdminPage.php"
copy_file "src/CronEventInspector.php"

python3 - "$BUILD_DIR" "$ZIP_PATH" "$PLUGIN_SLUG" <<'PY'
import sys
import zipfile
from pathlib import Path

build_dir = Path(sys.argv[1])
zip_path = Path(sys.argv[2])
plugin_slug = sys.argv[3]

with zipfile.ZipFile(zip_path, 'w', compression=zipfile.ZIP_DEFLATED) as archive:
    for path in sorted((build_dir / plugin_slug).rglob('*')):
        if path.is_file():
            archive.write(path, path.relative_to(build_dir))
PY

printf 'Built %s\n' "$ZIP_PATH"
