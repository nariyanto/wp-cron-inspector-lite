<?php
/**
 * Plugin Name: SNWorks Cron Diagnostics
 * Description: Find duplicate, stuck, and suspicious WordPress cron events before they cause support issues.
 * Version: 0.1.2
 * Author: Septiyan Nariyanto
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: snworks-cron-diagnostics
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

define('SNWORKS_CRON_DIAGNOSTICS_VERSION', '0.1.2');
define('SNWORKS_CRON_DIAGNOSTICS_FILE', __FILE__);

require_once __DIR__ . '/src/CronEventInspector.php';
require_once __DIR__ . '/src/AdminPage.php';

add_action(
    'admin_menu',
    static function (): void {
        (new SNWorks\CronDiagnostics\AdminPage())->register();
    }
);
