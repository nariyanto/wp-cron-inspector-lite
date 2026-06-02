<?php
/**
 * Plugin Name: Cron Inspector Lite
 * Plugin URI: https://github.com/nariyanto/wp-cron-inspector-lite
 * Description: Find duplicate, stuck, and suspicious WordPress cron events before they cause support issues.
 * Version: 0.1.0
 * Author: Septiyan Nariyanto
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cron-inspector-lite
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

define('CRON_INSPECTOR_LITE_VERSION', '0.1.0');
define('CRON_INSPECTOR_LITE_FILE', __FILE__);

require_once __DIR__ . '/src/CronEventInspector.php';
require_once __DIR__ . '/src/AdminPage.php';

add_action(
    'admin_menu',
    static function (): void {
        (new Nariyanto\WPCronInspectorLite\AdminPage())->register();
    }
);
