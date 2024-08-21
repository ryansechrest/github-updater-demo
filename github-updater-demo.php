<?php

namespace RYSE\GitHubUpdaterDemo;

/**
 * Plugin Name:        GitHub Updater Demo
 * Plugin URI:         https://github.com/ryansechrest/github-updater-demo
 * Version:            1.1.0
 * Description:        WordPress plugin to demonstrate how `GitHubUpdater` can enable WordPress to check for and update a custom plugin that's hosted in either a public or private repository on GitHub.
 * Author:             Ryan Sechrest
 * Author URI:         https://ryansechrest.com/
 * Text Domain:        ryse-github-updater-demo
 * Tested up to:       6.6.1
 * Requires at least:  6.5
 * Requires PHP:       8.2
 * Update URI:         https://github.com/ryansechrest/github-updater-demo
 * License:            GPLv2
 * License URI:        https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) exit;

require_once 'autoloader.php';

new Plugin(__FILE__);
