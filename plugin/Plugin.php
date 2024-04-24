<?php

namespace RYSE\GitHubUpdaterDemo;

/**
 * Demonstrate updating private plugins hosted on GitHub.
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
        (new Updater($file))
            ->setGitHubAccessToken(GITHUB_ACCESS_TOKEN)
            ->add();
    }
}