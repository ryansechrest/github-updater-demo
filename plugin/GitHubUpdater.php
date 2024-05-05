<?php

namespace RYSE\GitHubUpdaterDemo;

/**
 * Enable WordPress to check for and update a custom plugin that's hosted in
 * either a public or private repository on GitHub.
 *
 * @author Ryan Sechrest
 * @package RYSE\GitHubUpdaterDemo
 * @version 1.0.3
 */
class GitHubUpdater
{
    /**
     * Option name to remember plugin.
     *
     * This value SHOULD NOT be changed.
     *
     * There is a filter further down (`update_plugins_github.com`) that targets
     * any plugin where github.com is the hostname in the Update URI.
     *
     * It's entirely possible that another plugin, not managed by GitHubUpdater,
     * uses a GitHub hostname there. Well, we don't want to touch those plugins,
     * so we need a way to make sure we only target plugins managed by
     * GitHubUpdater.
     *
     * This is where the option comes in. Any time a plugin with GitHubUpdater
     * is activated, we record it in this option, and when it's deactivated,
     * we remove from that option.
     *
     * Then, when the filter runs, we can read this option, see if that plugin
     * is one of ours, and proceed, but if not, we leave that plugin alone.
     */
    const OPTION_NAME = 'ryse_github_updater';

    /*------------------------------------------------------------------------*/

    /**
     * Absolute path to plugin file containing plugin header
     *
     * @var string .../wp-content/plugins/github-updater-demo/github-updater-demo.php
     */
    private string $file = '';

    /*------------------------------------------------------------------------*/

    /**
     * GitHub URL
     *
     * @var string https://github.com/ryansechrest/github-updater-demo
     */
    private string $gitHubUrl = '';

    /**
     * GitHub path
     *
     * @var string ryansechrest/github-updater-demo
     */
    private string $gitHubPath = '';

    /**
     * GitHub organization
     *
     * @var string ryansechrest
     */
    private string $gitHubOrg = '';

    /**
     * GitHub repository
     *
     * @var string github-updater-demo
     */
    private string $gitHubRepo = '';

    /**
     * GitHub branch
     *
     * @var string main
     */
    private string $gitHubBranch = 'main';

    /**
     * GitHub access token
     *
     * @var string github_pat_fU7xGh...
     */
    private string $gitHubAccessToken = '';

    /*------------------------------------------------------------------------*/

    /**
     * Plugin file
     *
     * @var string github-updater-demo/github-updater-demo.php
     */
    private string $pluginFile = '';

    /**
     * Plugin directory
     *
     * @var string github-updater-demo
     */
    private string $pluginDir = '';

    /**
     * Plugin filename
     *
     * @var string github-updater-demo.php
     */
    private string $pluginFilename = '';

    /**
     * Plugin slug
     *
     * @var string ryansechrest-github-updater-demo
     */
    private string $pluginSlug = '';

    /**
     * Plugin URL
     *
     * @var string https://ryansechrest.github.io/github-updater-demo
     */
    private string $pluginUrl = '';

    /**
     * Plugin version
     *
     * @var string 1.0.0
     */
    private string $pluginVersion = '';

    /*------------------------------------------------------------------------*/

    /**
     * Tested WordPress version.
     *
     * @var string 6.5.2
     */
    private string $testedWpVersion = '';

    /**************************************************************************/

    /**
     * Set absolute path to plugin file containing plugin header.
     *
     * @param string $file .../wp-content/plugins/github-updater-demo/github-updater-demo.php
     */
    public function __construct(string $file)
    {
        $this->file = $file;

        $this->load();
    }

    /**
     * Add update mechanism to plugin.
     *
     * @return void
     */
    public function add(): void
    {
        $this->updatePluginStatus();
        $this->updatePluginDetailsUrl();
        $this->checkPluginUpdates();
        $this->prepareHttpRequestArgs();
        $this->moveUpdatedPlugin();
    }

    /**************************************************************************/

    /**
     * Load properties with values based on $file.
     *
     *   $gitHubUrl       GitHub URL           https://github.com/ryansechrest/github-updater-demo
     *   $gitHubPath      GitHub path          ryansechrest/github-updater-demo
     *   $gitHubOrg       GitHub organization  ryansechrest
     *   $gitHubRepo      GitHub repository    github-updater-demo
     *   $pluginFile      Plugin file          github-updater-demo/github-updater-demo.php
     *   $pluginDir       Plugin directory     github-updater-demo
     *   $pluginFilename  Plugin filename      github-updater-demo.php
     *   $pluginSlug      Plugin slug          ryansechrest-github-updater-demo
     *   $pluginUrl       Plugin URL           https://ryansechrest.github.io/github-updater-demo
     *   $pluginVersion   Plugin version       1.0.0
     */
    private function load(): void
    {
        // Fields from plugin header
        $pluginData = get_file_data(
            $this->file,
            [
                'PluginURI' => 'Plugin URI',
                'Version' => 'Version',
                'UpdateURI' => 'Update URI',
            ]
        );

        // Extract fields from plugin header
        $pluginUri = $pluginData['PluginURI'] ?? '';
        $updateUri = $pluginData['UpdateURI'] ?? '';
        $version = $pluginData['Version'] ?? '';

        // If required fields were not set, exit
        if (!$pluginUri || !$updateUri || !$version) {
            $this->addAdminNotice('Plugin <b>%s</b> is missing one or more required header fields: <b>Plugin URI</b>, <b>Version</b>, and/or <b>Update URI</b>.');
            return;
        };

        // e.g. `https://github.com/ryansechrest/github-updater-demo`
        $this->gitHubUrl = $updateUri;

        // e.g. `ryansechrest/github-updater-demo`
        $this->gitHubPath = trim(
            wp_parse_url($updateUri, PHP_URL_PATH),
            '/'
        );

        // e.g. `ryansechrest` and `github-updater-demo`
        list($this->gitHubOrg, $this->gitHubRepo) = explode(
            '/', $this->gitHubPath
        );

        // e.g. `github-updater-demo/github-updater-demo.php`
        $this->pluginFile = str_replace(
            WP_PLUGIN_DIR . '/', '', $this->file
        );

        // e.g. `github-updater-demo` and `github-updater-demo.php`
        list($this->pluginDir, $this->pluginFilename) = explode(
            '/', $this->pluginFile
        );

        // e.g. `ryansechrest-github-updater-demo`
        $this->pluginSlug = sprintf(
            '%s-%s', $this->gitHubOrg, $this->gitHubRepo
        );

        // e.g. `https://ryansechrest.github.io/github-updater-demo`
        $this->pluginUrl = $pluginUri;

        // e.g. `1.0.0`
        $this->pluginVersion = $version;
    }

    /**
     * Add admin notice that required plugin header fields are missing.
     *
     * @param string $message Plugin <b>%s</b> is missing one or more required header fields: <b>Plugin URI</b>, <b>Version</b>, and/or <b>Update URI</b>.
     * @return void
     */
    private function addAdminNotice(string $message): void
    {
        add_action('admin_notices', function () use ($message) {
            $pluginFile = str_replace(
                WP_PLUGIN_DIR . '/', '', $this->file
            );
            echo '<div class="notice notice-error">';
            echo '<p>';
            echo wp_kses(
                sprintf($message, $pluginFile),
                ['b' => []]
            );
            echo '</p>';
            echo '</div>';
        });
    }

    /*------------------------------------------------------------------------*/

    /**
     * Add or remove plugin from option.
     *
     * @return void
     */
    private function updatePluginStatus(): void
    {
        // When plugin is activated, add plugin to option
        register_activation_hook($this->file, function () {

            // Get plugins managed by GitHubUpdater
            $plugins = get_option(self::OPTION_NAME);

            // If none exist, create an array
            if (!is_array($plugins)) $plugins = [];

            // Push this plugin to array
            $plugins[] = $this->gitHubPath;

            // Save plugin in option
            update_option(self::OPTION_NAME, $plugins);
        });

        // When plugin is deactivated, remove plugin from option
        register_deactivation_hook($this->file, function () {

            // Get plugins managed by GitHubUpdater
            $plugins = get_option(self::OPTION_NAME);

            // If none exist (strange), leave it alone
            if (!is_array($plugins)) return;

            // Otherwise find this plugin
            $pos = array_search($this->gitHubPath, $plugins);

            // If plugin doesn't exist (strange), leave it alone
            if ($pos === false) return;

            // Otherwise remove plugin
            unset($plugins[$pos]);

            // If plugin was last plugin managed by GitHubUpdater,
            // delete option from database
            if (count($plugins) === 0) {
                delete_option(self::OPTION_NAME);
                return;
            }

            // Otherwise reindex array
            $plugins = array_values($plugins);

            // Save remaining plugins managed by GitHubUpdater
            update_option(self::OPTION_NAME, $plugins);
        });
    }

    /*------------------------------------------------------------------------*/

    /**
     * Update plugin details URL.
     *
     * If we don't set `slug` in the plugin response within
     * `_checkPluginUpdates()`, a PHP warning appears on Dashboard > Updates,
     * triggered in `wp-admin/update-core.php on line 570`, that the `slug` is
     * missing.
     *
     * Since we're forced to set the `slug`, WordPress thinks the plugin is
     * hosted on wordpress.org, and attempts to show plugin details from
     * wordpress.org in its modal, however this results in an error:
     * `Plugin not found.`
     *
     * We use this filter to replace the WordPress modal URL with the value
     * of the `Update URI` plugin header, which fixes the URL in the following
     * places:
     *
     * Dashboard > Updates
     *
     *   [View version X.Y.Z details]
     *
     * Plugins
     *
     *   [View details]
     *   [View version X.Y.Z details]
     *
     * @return void
     */
    private function updatePluginDetailsUrl(): void
    {
        add_filter(
            'admin_url',
            [$this, '_updatePluginDetailsUrl'],
            10,
            2
        );
    }

    /**
     * Hook to update plugin details URL.
     *
     *   $url      The complete admin area URL including scheme and path.
     *
     *   $path     Path relative to the admin area URL. Blank string if no path
     *             is specified.
     *
     * @param string $url https://example.org/wp-admin/plugin-install.php?tab=plugin-information&plugin=ryansechrest-github-updater-demo&TB_iframe=true&width=600&height=550
     * @param string $path plugin-install.php?tab=plugin-information&plugin=ryansechrest-github-updater-demo&TB_iframe=true&width=600&height=550
     * @return string
     */
    public function _updatePluginDetailsUrl(string $url, string $path): string
    {
        $query = 'plugin=' . $this->pluginSlug;

        // If URL doesn't reference target plugin, exit
        if (!str_contains($path, $query)) return $url;

        return sprintf(
            '%s?TB_iframe=true&width=600&height=550',
            $this->pluginUrl
        );
    }

    /*------------------------------------------------------------------------*/

    /**
     * Check for plugin updates.
     *
     * If plugin has an `Update URI` pointing to `github.com`, then check if
     * plugin was updated on GitHub, and if so, record a pending update so that
     * either WordPress can automatically update it (if enabled), or a user can
     * manually update it much like an officially-hosted plugin.
     *
     * @return void
     */
    private function checkPluginUpdates(): void
    {
        add_filter(
            'update_plugins_github.com',
            [$this, '_checkPluginUpdates'],
            10,
            3
        );
    }

    /**
     * Hook to check for plugin updates.
     *
     *   $update  Plugin update data with the latest details.
     *   $data    Plugin data as defined in plugin header.
     *   $file    Plugin file, e.g. `github-updater-demo/github-updater-demo.php`
     *
     * @param array|false $update false
     * @param array $data ['PluginName' => 'GitHub Updater Demo', ...]
     * @param string $file github-updater-demo/github-updater-demo.php
     * @return array|false
     */
    public function _checkPluginUpdates(
        array|false $update, array $data, string $file
    ): array|false
    {
        // If plugin does not match this plugin, exit
        if ($file !== $this->pluginFile) return $update;

        // Get plugins managed by GitHubUpdater
        $plugins = get_option(self::OPTION_NAME);

        // If none exist, exit
        if (!is_array($plugins)) return $update;

        // If current plugin is not managed by GitHubUpdater, exit
        if (!in_array($this->gitHubPath, $plugins)) return $update;

        // Get remote plugin file contents to read plugin header
        $fileContents = $this->getRemotePluginFileContents();

        // Extract plugin version from remote plugin file contents
        preg_match_all(
            '/\s+\*\s+Version:\s+(\d+(\.\d+){0,2})/',
            $fileContents,
            $matches
        );

        // Save plugin version from remote plugin file, e.g. `1.1.0`
        $newVersion = $matches[1][0] ?? '';

        // If version wasn't found, exit
        if (!$newVersion) return $update;

        // Build plugin response for WordPress
        return [
            'id' => $this->gitHubUrl,
            'slug' => $this->pluginSlug,
            'plugin' => $this->pluginFile,
            'version' => $newVersion,
            'url' => $this->pluginUrl,
            'package' => $this->getRemotePluginZipFile(),
            'icons' => [
                '2x' => $this->pluginUrl . '/icon-256x256.png',
                '1x' => $this->pluginUrl . '/icon-128x128.png',
            ],
            'tested' => $this->testedWpVersion,
        ];
    }

    /**
     * Get remote plugin file contents from GitHub repository.
     *
     * @return string
     */
    private function getRemotePluginFileContents(): string
    {
        return $this->gitHubAccessToken
            ? $this->getPrivateRemotePluginFileContents()
            : $this->getPublicRemotePluginFileContents();
    }

    /**
     * Get remote plugin file contents from public GitHub repository.
     *
     * @return string
     */
    private function getPublicRemotePluginFileContents(): string
    {
        // Get public remote plugin file containing plugin header,
        // e.g. `https://raw.githubusercontent.com/ryansechrest/github-updater-demo/master/github-updater-demo.php`
        $remoteFile = $this->getPublicRemotePluginFile($this->pluginFilename);

        return wp_remote_retrieve_body(
            wp_remote_get($remoteFile)
        );
    }

    /**
     * Get public remote plugin file.
     *
     * @param string $filename github-updater-demo.php
     * @return string https://raw.githubusercontent.com/ryansechrest/github-updater-demo/master/github-updater-demo.php
     */
    private function getPublicRemotePluginFile(string $filename): string
    {
        // Generate URL to public remote plugin file.
        return sprintf(
            'https://raw.githubusercontent.com/%s/%s/%s',
            $this->gitHubPath,
            $this->gitHubBranch,
            $filename
        );
    }

    /**
     * Get remote plugin file contents from private GitHub repository.
     *
     * @return string
     */
    private function getPrivateRemotePluginFileContents(): string
    {
        // Get public remote plugin file containing plugin header,
        // e.g. `https://api.github.com/repos/ryansechrest/github-updater-demo/contents/github-updater-demo.php?ref=master`
        $remoteFile = $this->getPrivateRemotePluginFile($this->pluginFilename);

        return wp_remote_retrieve_body(
            wp_remote_get(
                $remoteFile,
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->gitHubAccessToken,
                        'Accept' => 'application/vnd.github.raw+json',
                    ]
                ]
            )
        );
    }

    /**
     * Get private remote plugin file.
     *
     * @param string $filename github-updater-demo.php
     * @return string https://api.github.com/repos/ryansechrest/github-updater-demo/contents/github-updater-demo.php?ref=master
     */
    private function getPrivateRemotePluginFile(string $filename): string
    {
        // Generate URL to private remote plugin file.
        return sprintf(
            'https://api.github.com/repos/%s/contents/%s?ref=%s',
            $this->gitHubPath,
            $filename,
            $this->gitHubBranch
        );
    }

    /**
     * Get path to remote plugin ZIP file.
     *
     * @return string https://github.com/ryansechrest/github-updater-demo/archive/refs/heads/master.zip
     */
    private function getRemotePluginZipFile(): string
    {
        return $this->gitHubAccessToken
            ? $this->getPrivateRemotePluginZipFile()
            : $this->getPublicRemotePluginZipFile();
    }

    /**
     * Get path to public remote plugin ZIP file.
     *
     * @return string https://github.com/ryansechrest/github-updater-demo/archive/refs/heads/master.zip
     */
    private function getPublicRemotePluginZipFile(): string
    {
        return sprintf(
            'https://github.com/%s/archive/refs/heads/%s.zip',
            $this->gitHubPath,
            $this->gitHubBranch
        );
    }

    /**
     * Get path to private remote plugin ZIP file.
     *
     * @return string https://api.github.com/repos/ryansechrest/github-updater-demo/zipball/master
     */
    private function getPrivateRemotePluginZipFile(): string
    {
        return sprintf(
            'https://api.github.com/repos/%s/zipball/%s',
            $this->gitHubPath,
            $this->gitHubBranch
        );
    }

    /*------------------------------------------------------------------------*/

    /**
     * Prepare HTTP request args.
     *
     * Include GitHub access token in request header when repository is private
     * so that WordPress has access to download the remote plugin ZIP file.
     *
     * @return void
     */
    private function prepareHttpRequestArgs(): void
    {
        add_filter(
            'http_request_args',
            [$this, '_prepareHttpRequestArgs'],
            10,
            2
        );
    }

    /**
     * Hook to prepare HTTP request args.
     *
     *   $args  An array of HTTP request arguments.
     *   $url   The request URL.
     *
     * @param array $args ['method' => 'GET', 'headers' => [], ...]
     * @param string $url https://api.github.com/repos/ryansechrest/github-updater-demo/zipball/master
     * @return array ['headers' => ['Authorization => 'Bearer...'], ...]
     */
    public function _prepareHttpRequestArgs(array $args, string $url): array
    {
        // If URL doesn't match ZIP file to private GitHub repo, exit
        if ($url !== $this->getPrivateRemotePluginZipFile()) return $args;

        // Include GitHub access token and file type
        $args['headers']['Authorization'] = 'Bearer ' . $this->gitHubAccessToken;
        $args['headers']['Accept'] = 'application/vnd.github+json';

        return $args;
    }

    /*------------------------------------------------------------------------*/

    /**
     * Move updated plugin.
     *
     * The updated plugin will be extracted into a directory containing GitHub's
     * branch name (e.g. `github-updater-demo-master`). Since this likely differs from
     * the old plugin (e.g. `github-updater-demo`), it will cause WordPress to
     * deactivate it. In order to prevent this, we move the new plugin to the
     * old plugin's directory.
     *
     * @return void
     */
    private function moveUpdatedPlugin(): void
    {
        add_filter(
            'upgrader_install_package_result',
            [$this, '_moveUpdatedPlugin'],
            10,
            2
        );
    }

    /**
     * Hook to move updated plugin.
     *
     * @param array $result ['destination' => '.../wp-content/plugins/github-updater-demo-master', ...]
     * @param array $options ['plugin' => 'github-updater-demo/github-updater-demo.php', ...]
     * @return array
     */
    public function _moveUpdatedPlugin(array $result, array $options): array
    {
        // Get plugin being updated
        // e.g. `github-updater-demo/github-updater-demo.php`
        $pluginFile = $options['plugin'] ?? '';

        // If plugin does not match this plugin, exit
        if ($pluginFile !== $this->pluginFile) return $result;

        // Save path to new plugin
        // e.g. `.../wp-content/plugins/github-updater-demo-master`
        $newPluginPath = $result['destination'] ?? '';

        // If path to new plugin doesn't exist, exit
        if (!$newPluginPath) return $result;

        // Save root path to all plugins, e.g. `.../wp-content/plugins`
        $pluginRootPath = $result['local_destination'] ?? WP_PLUGIN_DIR;

        // Piece together path to old plugin,
        // e.g. `.../wp-content/plugins/github-updater-demo`
        $oldPluginPath = $pluginRootPath . '/' . $this->pluginDir;

        // Move new plugin to old plugin directory
        move_dir($newPluginPath, $oldPluginPath);

        // Update result based on changes above
        // destination:         `.../wp-content/plugins/github-updater-demo`
        // destination_name:    `github-updater-demo`
        // remote_destination:  `.../wp-content/plugins/github-updater-demo`
        $result['destination'] = $oldPluginPath;
        $result['destination_name'] = $this->pluginDir;
        $result['remote_destination'] = $oldPluginPath;

        return $result;
    }

    /**************************************************************************/

    /**
     * Set GitHub branch of plugin.
     *
     * @param string $branch main
     * @return $this
     */
    public function setBranch(string $branch): self
    {
        $this->gitHubBranch = $branch;

        return $this;
    }

    /**
     * Set GitHub access token.
     *
     * @param string $accessToken github_pat_fU7xGh...
     * @return $this
     */
    public function setAccessToken(string $accessToken): self
    {
        $this->gitHubAccessToken = $accessToken;

        return $this;
    }

    /**
     * Set tested WordPress version.
     *
     * @param string $version 6.5.2
     * @return $this
     */
    public function setTestedWpVersion(string $version): self
    {
        $this->testedWpVersion = $version;

        return $this;
    }
}