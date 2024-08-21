<?php

namespace RYSE\GitHubUpdaterDemo;

/**
 * WordPress plugin to demonstrate how `GitHubUpdater` can enable WordPress to
 * check for and update a custom plugin that's hosted in either a public or
 * private repository on GitHub.
 *
 * @author Ryan Sechrest
 * @package RYSE\GitHubUpdaterDemo
 */
class Plugin
{
    /**
     * Create and configure new Updater to keep plugin updated.
     *
     * @param string $file .../wp-content/plugins/github-updater-demo/github-updater-demo.php
     */
    public function __construct(string $file)
    {
        (new GitHubUpdater($file))
            ->setBranch('master')
            ->setPluginIcon('assets/icon.png')
            ->setPluginBannerSmall('assets/banner-772x250.jpg')
            ->setPluginBannerLarge('assets/banner-1544x500.jpg')
            ->setChangelog('CHANGELOG.md')
            ->add();
    }
}
