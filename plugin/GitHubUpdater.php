<?php

namespace RYSE\GitHubUpdaterDemo;

/**
 * Enable WordPress to check for and update a custom plugin that's hosted in
 * either a public or private repository on GitHub.
 *
 * @author Ryan Sechrest
 * @package RYSE\GitHubUpdaterDemo
 * @version 1.1.0
 */
class GitHubUpdater
{
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
     * @var string https://github.com/ryansechrest/github-updater-demo
     */
    private string $pluginUrl = '';

    /**
     * Plugin version
     *
     * @var string 1.0.0
     */
    private string $pluginVersion = '';

    /**
     * Relative path to plugin icon from plugin root.
     *
     * @var string assets/icon.png
     */
    private string $pluginIcon = '';

    /**
     * Relative path to small plugin banner from plugin root.
     *
     * @var string assets/banner-772x250.jpg
     */
    private string $pluginBannerSmall = '';

    /**
     * Relative path to large plugin banner from plugin root.
     *
     * @var string assets/banner-1544x500.jpg
     */
    private string $pluginBannerLarge = '';

    /**
     * Changelog to use for populating plugin detail modal.
     *
     * @var string CHANGELOG.md
     */
    private string $changelog = '';

    /*------------------------------------------------------------------------*/

    /**
     * Tested up to specified WordPress version.
     *
     * @var string 6.6
     */
    private string $testedUpTo = '';

    /*------------------------------------------------------------------------*/

    /**
     * Enable GitHubUpdate debugger.
     *
     * @var bool
     */
    private bool $enableDebugger = false;

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
     * Set relative path to plugin icon from plugin root.
     *
     * @param string $file assets/icon.png
     * @return $this
     */
    public function setPluginIcon(string $file): self
    {
        $this->pluginIcon = ltrim($file, '/');

        return $this;
    }

    /**
     * Set relative path to small plugin banner from plugin root.
     *
     * @param string $file assets/banner-772x250.jpg
     * @return $this
     */
    public function setPluginBannerSmall(string $file): self
    {
        $this->pluginBannerSmall = ltrim($file, '/');

        return $this;
    }

    /**
     * Set relative path to large plugin banner from plugin root.
     *
     * @param string $file assets/banner-1544x500.jpg
     * @return $this
     */
    public function setPluginBannerLarge(string $file): self
    {
        $this->pluginBannerLarge = ltrim($file, '/');

        return $this;
    }

    /**
     * Set changelog to use for plugin detail modal.
     *
     * @param string $changelog CHANGELOG.md
     * @return $this
     */
    public function setChangelog(string $changelog): self
    {
        $this->changelog = $changelog;

        return $this;
    }

    /**
     * Enable GitHubUpdater debugger.
     *
     * If this property is set to true, as well as the WP_DEBUG and WP_DEBUG_LOG
     * constants within wp-config.php, then GitHubUpdater will log pertinent
     * information to wp-content/debug.log.
     *
     * @return $this
     */
    public function enableDebugger(): self
    {
        $this->enableDebugger = true;

        return $this;
    }

    /**
     * Add update mechanism to plugin.
     *
     * @return void
     */
    public function add(): void
    {
        $this->buildPluginDetailsResult();
        //$this->logPluginDetailsResult();;
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
     *   $pluginUrl       Plugin URL           https://github.com/ryansechrest/github-updater-demo
     *   $pluginVersion   Plugin version       1.0.0
     *   $testedUpTo      Tested up to         6.6
     */
    private function load(): void
    {
        // Fields from plugin header
        $pluginData = get_file_data(
            $this->file,
            [
                'PluginURI' => 'Plugin URI',
                'Version' => 'Version',
                'TestedUpTo' => 'Tested up to',
                'UpdateURI' => 'Update URI',
            ]
        );

        // Extract fields from plugin header
        $pluginUri = $pluginData['PluginURI'] ?? '';
        $updateUri = $pluginData['UpdateURI'] ?? '';
        $version = $pluginData['Version'] ?? '';
        $testedUpTo = $pluginData['TestedUpTo'] ?? '';

        // If required fields were not set, exit
        if (!$updateUri || !$version) {
            $this->addAdminNotice('Plugin <b>%s</b> is missing one or more required header fields: <b>Version</b> and/or <b>Update URI</b>.');
            return;
        }

        // e.g. `https://github.com/ryansechrest/github-updater-demo`
        $this->gitHubUrl = $updateUri;

        // e.g. `ryansechrest/github-updater-demo`
        $this->gitHubPath = trim(
            wp_parse_url($updateUri, PHP_URL_PATH),
            '/'
        );

        // e.g. `ryansechrest` and `github-updater-demo`
        [$this->gitHubOrg, $this->gitHubRepo] = explode(
            '/', $this->gitHubPath
        );

        // e.g. `github-updater-demo/github-updater-demo.php`
        $this->pluginFile = str_replace(
            WP_PLUGIN_DIR . '/', '', $this->file
        );

        // e.g. `github-updater-demo` and `github-updater-demo.php`
        [$this->pluginDir, $this->pluginFilename] = explode(
            '/', $this->pluginFile
        );

        // e.g. `ryansechrest-github-updater-demo`
        $this->pluginSlug = sprintf(
            '%s-%s', $this->gitHubOrg, $this->gitHubRepo
        );

        // e.g. `https://github.com/ryansechrest/github-updater-demo`
        $this->pluginUrl = $pluginUri;

        // e.g. `1.0.0`
        $this->pluginVersion = $version;

        // e.g. `6.6`
        $this->testedUpTo = $testedUpTo;
    }

    /*------------------------------------------------------------------------*/

    /**
     * Build plugin details result.
     *
     * When WordPress checks for plugin updates, it queries wordpress.org,
     * however this plugin does not exist there. We use this hook to intercept
     * the request and manually populate the desired fields so that WordPress
     * can render the plugin details modal.
     *
     * @return void
     */
    private function buildPluginDetailsResult(): void
    {
        add_filter(
            'plugins_api',
            [$this, '_buildPluginDetailsResult'],
            10,
            3
        );
    }

    /**
     * Hook to build plugin details result.
     *
     * @param array|false|object $result ['name' => 'GitHub Updater Demo', ...]
     * @param string $action plugin_information
     * @param object $args ['slug' => 'ryansechrest-github-updater-demo', ...]
     * @return array|false|object ['name' => 'GitHub Updater Demo', ...]
     */
    public function _buildPluginDetailsResult(
        array|false|object $result, string $action, object $args
    ): array|false|object
    {
        // If action is query_plugins, hot_tags, or hot_categories, exit
        if ($action !== 'plugin_information') return $result;

        // If not our plugin, exit
        if ($args->slug !== $this->pluginSlug) return $result;

        // Get remote plugin file contents to read plugin header
        $fileContents = $this->getRemotePluginFileContents(
            $this->pluginFilename
        );

        // If remote plugin file could not be retrieved, exit
        if (!$fileContents) return $result;

        // Extract plugin version from remote plugin file contents
        $fields = $this->extractPluginHeaderFields(
            [
                'Plugin Name' => '',
                'Plugin URI' => '',
                'Version' => 'version',
                'Author' => '',
                'Author URI' => '',
                'Tested up to' => 'version',
                'Requires at least' => 'version',
                'Requires PHP' => 'version',
            ],
            $fileContents
        );

        // Build plugin detail result
        $result = [
            'name' => $fields['Plugin Name'],
            'slug' => $this->pluginSlug,
            'version' => $fields['Version'],
            'requires' => $fields['Requires at least'],
            'tested' => $fields['Tested up to'],
            'requires_php' => $fields['Requires PHP'],
            'homepage' => $fields['Plugin URI'],
            'sections' => [],
        ];

        // Assume no author
        $author = '';

        // If author name exists, use it
        if ($fields['Author']) {
            $author = $fields['Author'];
        }

        // If author name and URL exist, use them both
        if ($fields['Author'] && $fields['Author URI']) {
            $author = sprintf(
                '<a href="%s">%s</a>',
                $fields['Author URI'],
                $fields['Author']
            );
        }

        // If author exists, set it
        if ($author) {
            $result['author'] = $author;
        }

        // If small plugin banner exists, set it
        if ($pluginBannerSmall = $this->getPluginBannerSmall()) {
            $result['banners']['low'] = $pluginBannerSmall;
        }

        // If large plugin banner exists, set it
        if ($pluginBannerLarge = $this->getPluginBannerLarge()) {
            $result['banners']['high'] = $pluginBannerLarge;
        }

        // If changelog exists, set it
        if ($changelog = $this->getChangelog()) {
            $result['sections']['changelog'] = $changelog;
        }

        $this->logStart('_buildPluginDetailsResult', 'plugins_api');
        $this->logValue('Return $result', $result);
        $this->logValue('$action', $action);
        $this->logValue('$args', $args);
        $this->logFinish('_buildPluginDetailsResult');

        return (object) $result;
    }

    /*------------------------------------------------------------------------*/

    /**
     * Log plugin details for plugins.
     *
     * Useful for inspecting options on officially-hosted WordPress plugins.
     *
     * @return void
     */
    private function logPluginDetailsResult(): void
    {
        add_filter(
            'plugins_api_result',
            [$this, '_logPluginDetailsResult'],
            10,
            3
        );
    }

    /**
     * Hook to log plugin details for plugins.
     *
     * @param object $res ['name' => 'GitHub Updater Demo', ...]
     * @param string $action plugin_information
     * @param object $args ['slug' => 'ryansechrest-github-updater-demo', ...]
     * @return object ['name' => 'GitHub Updater Demo', ...]
     */
    public function _logPluginDetailsResult(
        object $res, string $action, object $args
    ): object
    {
        if ($action !== 'plugin_information') return $res;

        $this->logStart('_logPluginDetailsResult', 'plugins_api_result');
        $this->logValue('Return $res', $res);
        $this->logValue('$action', $action);
        $this->logValue('$args', $args);
        $this->logFinish('_logPluginDetailsResult');

        return $res;
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

        $this->logStart(
            '_checkPluginUpdates', 'update_plugins_github.com'
        );

        // Get remote plugin file contents to read plugin header
        $fileContents = $this->getRemotePluginFileContents(
            $this->pluginFilename
        );

        // Extract plugin version from remote plugin file contents
        $fields = $this->extractPluginHeaderFields(
            ['Version' => 'version'], $fileContents
        );

        $this->log('Does $newVersion (' . $fields['Version'] . ') exist...');

        // If version wasn't found, exit
        if (!$fields['Version']) {
            $this->log('No');
            $this->logValue('Return early', $update);
            $this->logFinish('_checkPluginUpdates');

            return $update;
        }

        $this->log('Yes');

        // Build plugin data response for WordPress
        $pluginData = [
            'id' => $this->gitHubUrl,
            'slug' => $this->pluginSlug,
            'plugin' => $this->pluginFile,
            'version' => $fields['Version'],
            'url' => $this->pluginUrl,
            'package' => $this->getRemotePluginZipFile(),
            'tested' => $this->testedUpTo,
        ];

        $pluginIcon = $this->getPluginIcon();

        $this->log('Does $pluginIcon (' . $pluginIcon . ') exist...');

        // If no icon was defined, exit with plugin data
        if (!$pluginIcon) {
            $this->log('No');
            $this->logValue('Return early', $pluginData);
            $this->logFinish('_checkPluginUpdates');

            return $pluginData;
        }

        $this->log('Yes');

        // Otherwise add icon to plugin data
        $pluginData['icons'] = ['default' => $pluginIcon];

        $this->logValue('Return', $pluginData);
        $this->logFinish('_checkPluginUpdates');

        return $pluginData;
    }

    /**
     * Get remote plugin file contents from GitHub repository.
     *
     * @param string $filename github-updater-demo.php
     * @return string
     */
    private function getRemotePluginFileContents(string $filename): string
    {
        return $this->gitHubAccessToken
            ? $this->getPrivateRemotePluginFileContents($filename)
            : $this->getPublicRemotePluginFileContents($filename);
    }

    /**
     * Get remote plugin file contents from public GitHub repository.
     *
     * @param string $filename github-updater-demo.php
     * @return string
     */
    private function getPublicRemotePluginFileContents(string $filename): string
    {
        // Get public remote plugin file containing plugin header,
        // e.g. `https://raw.githubusercontent.com/ryansechrest/github-updater-demo/main/github-updater-demo.php`
        $remoteFile = $this->getPublicRemotePluginFile($filename);

        return wp_remote_retrieve_body(wp_remote_get($remoteFile));
    }

    /**
     * Get public remote plugin file.
     *
     * @param string $filename github-updater-demo.php
     * @return string https://raw.githubusercontent.com/ryansechrest/github-updater-demo/main/github-updater-demo.php
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
     * @param string $filename github-updater-demo.php
     * @return string
     */
    private function getPrivateRemotePluginFileContents(
        string $filename
    ): string
    {
        // Get public remote plugin file containing plugin header,
        // e.g. `https://api.github.com/repos/ryansechrest/github-updater-demo/contents/github-updater-demo.php?ref=main`
        $remoteFile = $this->getPrivateRemotePluginFile($filename);

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
     * @return string https://api.github.com/repos/ryansechrest/github-updater-demo/contents/github-updater-demo.php?ref=main
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
     * @return string https://github.com/ryansechrest/github-updater-demo/archive/refs/heads/main.zip
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
     * @return string https://github.com/ryansechrest/github-updater-demo/archive/refs/heads/main.zip
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
     * @return string https://api.github.com/repos/ryansechrest/github-updater-demo/zipball/main
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
     * @param string $url https://api.github.com/repos/ryansechrest/github-updater-demo/zipball/main
     * @return array ['headers' => ['Authorization => 'Bearer...'], ...]
     */
    public function _prepareHttpRequestArgs(array $args, string $url): array
    {
        // If URL doesn't match ZIP file to private GitHub repo, exit
        if ($url !== $this->getPrivateRemotePluginZipFile()) return $args;

        // Include GitHub access token and file type
        $args['headers']['Authorization'] = 'Bearer ' . $this->gitHubAccessToken;
        $args['headers']['Accept'] = 'application/vnd.github+json';

        $this->logStart('_prepareHttpRequestArgs', 'http_request_args');
        $this->logValue('Return', $args);
        $this->logFinish('_prepareHttpRequestArgs');

        return $args;
    }

    /*------------------------------------------------------------------------*/

    /**
     * Move updated plugin.
     *
     * The updated plugin will be extracted into a directory containing GitHub's
     * branch name (e.g. `github-updater-demo-main`). Since this likely differs from
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
     * @param array $result ['destination' => '.../wp-content/plugins/github-updater-demo-main', ...]
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

        $this->logStart(
            '_moveUpdatedPlugin', 'upgrader_install_package_result'
        );

        // Save path to new plugin
        // e.g. `.../wp-content/plugins/github-updater-demo-main`
        $newPluginPath = $result['destination'] ?? '';

        $this->log(
            'Does $newPluginPath (' . $newPluginPath . ') exist...'
        );

        // If path to new plugin doesn't exist, exit
        if (!$newPluginPath) {
            $this->log('No');
            $this->logValue('Return early', $result);
            $this->logFinish('_moveUpdatedPlugin');

            return $result;
        }

        $this->log('Yes');

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

        $this->logValue('Return', $result);
        $this->logFinish('_moveUpdatedPlugin');

        return $result;
    }

    /**************************************************************************/

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
     * Get plugin icon if defined and valid.
     *
     * @return string https://example.org/wp-content/plugins/consent-manager/assets/icon.png
     */
    private function getPluginIcon(): string
    {
        if (!$this->pluginIcon) return '';

        $pluginIconPath = $this->pluginDir . '/' . $this->pluginIcon;

        return plugins_url($pluginIconPath);
    }

    /**
     * Get small plugin banner (772x250).
     *
     * @return string https://example.org/wp-content/plugins/consent-manager/assets/banner-772x250.jpg
     */
    private function getPluginBannerSmall(): string
    {
        if (!$this->pluginBannerSmall) return '';

        return $this->getPluginFile($this->pluginBannerSmall);
    }

    /**
     * Get large plugin banner (1544x500).
     *
     * @return string https://example.org/wp-content/plugins/consent-manager/assets/banner-1544x500.jpg
     */
    private function getPluginBannerLarge(): string
    {
        if (!$this->pluginBannerLarge) return '';

        return $this->getPluginFile($this->pluginBannerLarge);
    }

    /**
     * Get plugin file if exists.
     *
     * @param string $file assets/icon.png
     * @return string https://example.org/wp-content/plugins/consent-manager/assets/icon.png
     */
    private function getPluginFile(string $file): string
    {
        $file = sprintf('%s/%s', $this->pluginDir, $file);

        if (!file_exists(WP_PLUGIN_DIR . '/' . $file)) return '';

        return plugins_url($file);
    }

    /**
     * Get changelog from GitHub.
     *
     * @return string
     */
    private function getChangelog(): string
    {
        // If no changelog specified, exit
        if (!$this->changelog) return '';

        // Get changelog contents from GitHub
        $changelogContents = $this->getRemotePluginFileContents(
            $this->changelog
        );

        // If changelog contents are blank, exit with error
        if (!$changelogContents) {
            return '<div class="notice notice-error notice-alt">'
                   . '<p><strong>ERROR:</strong> Changelog could not be retrieved from GitHub repository.</p>'
                   . '</div>';
        }

        // If changelog contents contains 404, exit with error
        if (str_contains($changelogContents, '404')) {
            return '<div class="notice notice-error notice-alt">'
                   . '<p><strong>ERROR:</strong> Changelog not found within GitHub repository.</p>'
                   . '</div>';
        }

        return $this->convertMarkdownToHtml($changelogContents);
    }

    /*------------------------------------------------------------------------*/

    /**
     * Extract plugin header fields from file contents.
     *
     * @param array $fields ['Version' => 'version', ...]
     * @param string $contents
     * @return array ['Version' => '1.0.0.', ...]
     */
    private function extractPluginHeaderFields(
        array $fields, string $contents
    ): array
    {
        $values = [];

        foreach ($fields as $field => $type) {

            // Select regex based on specified field type
            $regex = match ($type) {
                'version' => '\d+(\.\d+){0,2}',
                default => '.+',
            };

            // Extract field value using selected regex
            preg_match(
                '/\s+\*\s+' . $field . ':\s+(' . $regex . ')/',
                $contents,
                $matches
            );

            // Always return field with a value
            $values[$field] = $matches[1] ?? '';

            // Remove possible leading or trailing whitespace
            $values[$field] = trim($values[$field]);
        }

        return $values;
    }

    /*------------------------------------------------------------------------*/

    /**
     * Convert markdown to HTML.
     *
     * @param string $markdown # Changelog
     * @return string <h1>Changelog</h1>
     */
    private function convertMarkdownToHtml(string $markdown): string
    {
        $html = [];
        $lines = explode(PHP_EOL, $markdown);
        $index = 0;

        while (isset($lines[$index])) {
            $line = trim($lines[$index]);
            $element = match ($this->getMarkdownBlockType($line)) {
                'header' => $this->convertMarkdownHeader($line),
                'list' => $this->convertMarkdownList($index, $lines),
                'blockquote' => $this->convertMarkdownBlockquote($line),
                'code' => $this->convertMarkdownCode($index, $lines),
                'paragraph' => $this->convertMarkdownParagraph($line),
                default => [$line],
            };
            $html = array_merge($html, $element);
            $index++;
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * Get Markdown block type from line.
     *
     * @param string $line # Foobar
     * @return string header
     */
    private function getMarkdownBlockType(string $line): string
    {
        if ($this->isMarkdownHeader($line)) {
            return 'header';
        } elseif ($this->isMarkdownList($line)) {
            return 'list';
        } elseif ($this->isMarkdownBlockquote($line)) {
            return 'blockquote';
        } elseif ($this->isMarkdownCode($line)) {
            return 'code';
        } elseif ($this->isMarkdownParagraph($line)) {
            return 'paragraph';
        }

        return '';
    }

    /**
     * Whether line contains Markdown header.
     *
     * @param string $line # Foobar
     * @return bool true
     */
    private function isMarkdownHeader(string $line): bool
    {
        return str_starts_with($line, '#');
    }

    /**
     * Convert Markdown header to HTML.
     *
     * # Foo -> <h1>Foo</h1>
     * ## Foo -> <h2>Foo</h2>
     * ### Foo -> <h3>Foo</h3>
     * #### Foo -> <h4>Foo</h4>
     * ##### Foo -> <h5>Foo</h5>
     * ###### Foo -> <h6>Foo</h6>
     *
     * @param string $line # Foobar
     * @return string[] ['<h1>Foobar</h1>']
     */
    private function convertMarkdownHeader(string $line): array
    {
        $html = preg_replace_callback(
            '/(#{1,6}) (.+)/',
            function($match) {
                $size = strlen($match[1]);
                return '<h' . $size . '>' . $match[2] . '</h' . $size . '>';
            },
            $line
        );

        return [$html];
    }

    /**
     * Whether line contains Markdown list.
     *
     * @param string $line - Foobar
     * @return bool true
     */
    private function isMarkdownList(string $line): bool
    {
        return str_starts_with($line, '-');
    }

    /**
     * Convert unordered lists.
     *
     * - Foo
     * - Bar
     *
     * <ul>
     *   <li>Foo</li>
     *   <li>Bar</li>
     * </ul>
     *
     * @param int $index 0
     * @param array $lines ['- Foo', '- Bar']
     * @return string[] ['<ul>', '<li>Foo</li>', '<li>Bar</li>', '</ul>']
     */
    private function convertMarkdownList(int &$index, array $lines): array
    {
        $html[] = '<ul>';

        do {

            $html[] = preg_replace(
                '/- (.+)/',
                '<li>$1</li>',
                $this->convertInlineMarkdown(trim($lines[$index]))
            );
            $index++;

        } while (isset($lines[$index])
                 && $this->isMarkdownList(trim($lines[$index])));

        $index--;
        $html[] = '</ul>';

        return $html;
    }

    /**
     * Whether line contains Markdown blockquote.
     *
     * @param string $line > Foobar
     * @return bool true
     */
    private function isMarkdownBlockquote(string $line): bool
    {
        return str_starts_with($line, '>');
    }

    /**
     * Convert Markdown blockquote.
     *
     * > Foobar
     *
     * <blockquote>Foobar</blockquote>
     *
     * @param string $line > Foobar
     * @return string[] ['<blockquote>Foobar</blockquote>']
     */
    private function convertMarkdownBlockquote(string $line): array
    {
        $html = preg_replace(
            '/> (.+)/',
            '<blockquote>$1</blockquote>',
            $this->convertInlineMarkdown($line)
        );

        return [$html];
    }

    /**
     * Whether line contains Markdown code block.
     *
     * @param string $line ```
     * @return bool true
     */
    private function isMarkdownCode(string $line): bool
    {
        return str_starts_with($line, '```');
    }

    /**
     * Convert Markdown code block.
     *
     * ```
     * <?php
     * function foo() {
     *   echo 'bar';
     * }
     * ```
     *
     * <pre>
     * function foo() {
     *   echo 'bar';
     * }
     * </pre>
     *
     * @param int $index 0
     * @param array $lines ['```', 'Foobar', '```']
     * @return array ['<pre>', 'Foobar', '</pre>']
     */
    private function convertMarkdownCode(int &$index, array $lines): array
    {
        $html[] = preg_replace_callback(
            '/```(.*)/',
            function($match) {
                $lang = trim($match[1]);
                return $lang === ''
                    ? '<pre>'
                    : '<pre class="lang-' . $lang . '">';
            },
            trim($lines[$index])
        );

        $index++;

        while (isset($lines[$index]) && !$this->isMarkdownCode($lines[$index])) {
            $html[] = $lines[$index];
            $index++;
        }

        $html[] = '</pre>';

        return $html;
    }

    /**
     * Whether line contains Markdown paragraph.
     *
     * @param string $line Foobar
     * @return bool true
     */
    private function isMarkdownParagraph(string $line): bool
    {
        return $line !== '';
    }

    /**
     * Convert Markdown paragraph.
     *
     * @param string $line Foobar
     * @return string[] ['<p>Foobar</p>']
     */
    private function convertMarkdownParagraph(string $line): array
    {
        return ['<p>' . $this->convertInlineMarkdown($line) . '</p>'];
    }

    /**
     * Convert inline Markdown.
     *
     * @param string $line Convert `code`, **bold**, and *italic* text.
     * @return string Convert <code>code</code>, <strong>bold</strong>, and <em>italic</em> text.
     */
    private function convertInlineMarkdown(string $line): string
    {
        /**
         * Convert code text.
         *
         * `Foo` -> <code>Foo</code>
         */
        $line = preg_replace(
            '/`(.+)`/U',
            '<code>$1</code>',
            $line
        );

        /**
         * Convert bold text.
         *
         * **Foo** -> <strong>Foo</strong>
         */
        $line = preg_replace(
            '/\*\*(.+)\*\*/U',
            '<strong>$1</strong>',
            $line
        );

        /**
         * Convert italic text.
         *
         * *Foo* -> <em>Foo</em>
         */
        $line = preg_replace(
            '/\*(.+)\*/U',
            '<em>$1</em>',
            $line
        );

        return $line;
    }

    /*------------------------------------------------------------------------*/

    /**
     * Log message with optional value.
     *
     * @param string $message Plugins data
     * @return void
     */
    private function log(string $message,): void
    {
        if (!$this->enableDebugger || !WP_DEBUG || !WP_DEBUG_LOG) return;

        error_log('[GitHubUpdater] ' . $message);
    }

    /**
     * Log when method starts running.
     *
     * @param string $method _checkPluginUpdates
     * @param string $hook update_plugins_github.com
     * @return void
     */
    private function logStart(string $method, string $hook = ''): void
    {
        $message = $method . '() ';

        if ($hook) $message = $hook . ' → ' . $message;

        $this->log($message);
        $this->log(str_repeat('-', 50));
    }

    /**
     * Log label and value through print_r().
     *
     * @param string $label $pluginData
     * @param mixed $value ['version' => '1.0.0', ...]
     * @return void
     */
    private function logValue(string $label, mixed $value): void
    {
        if (!is_string($value)) {
            $value = var_export($value, true);
        }

        $this->log($label . ': ' . $value);
    }

    /**
     * Log when method finishes running.
     *
     * @param string $method _checkPluginUpdates
     * @return void
     */
    private function logFinish(string $method): void
    {
        $this->log('/ ' . $method . '()');
        $this->log('');
    }
}
