<?php

namespace RYSE\GitHubUpdaterDemo;

/*
 * Plugin Name:        GitHub Updater Demo
 * Plugin URI:         https://ryansechrest.github.io/github-updater-demo
 * Version:            1.1.0
 * Description:        Demonstrate how to keep your custom plugin updated using a public or private GitHub repository.
 * Author:             Ryan Sechrest
 * Author URI:         https://ryansechrest.com/
 * Text Domain:        ryse-github-updater-demo
 * Requires at least:  6.5
 * Requires PHP:       8.2
 * Update URI:         https://github.com/ryansechrest/github-updater-demo
 */

if (!defined('ABSPATH')) exit;

require_once 'autoloader.php';

new Plugin(__FILE__);