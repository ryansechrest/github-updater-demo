<?php

namespace RYSE\GitHubUpdaterDemo;

use DateTime;

/**
 * Enable WordPress to check for and update a custom plugin that's hosted in
 * either a public or private repository on GitHub.
 *
 * @author Ryan Sechrest
 * @package RYSE\GitHubUpdaterDemo
 * @version 1.2.0
 */
class GitHubUpdater
{
    /**
     * Absolute path to the plugin file containing the plugin header.
     *
     * @var string .../wp-content/plugins/github-updater-demo/github-updater-demo.php
     */
    private string $file = '';

    // --- GitHub Properties ---------------------------------------------------

    /**
     * GitHub repository URL.
     *
     * @var string https://github.com/ryansechrest/github-updater-demo
     */
    private string $gitHubUrl = '';

    /**
     * GitHub repository path.
     *
     * @var string ryansechrest/github-updater-demo
     */
    private string $gitHubPath = '';

    /**
     * GitHub organization name.
     *
     * @var string ryansechrest
     */
    private string $gitHubOrg = '';

    /**
     * GitHub repository name.
     *
     * @var string github-updater-demo
     */
    private string $gitHubRepo = '';

    /**
     * GitHub branch name.
     *
     * @var string main
     */
    private string $gitHubBranch = 'main';

    /**
     * GitHub access token.
     *
     * @var string github_pat_fU7xGh...
     */
    private string $gitHubAccessToken = '';

    // --- Plugin Properties ---------------------------------------------------

    /**
     * WordPress plugin name.
     *
     * @var string GitHub Updater Demo
     */
    private string $pluginName = '';

    /**
     * WordPress plugin file.
     *
     * @var string github-updater-demo/github-updater-demo.php
     */
    private string $pluginFile = '';

    /**
     * WordPress plugin directory.
     *
     * @var string github-updater-demo
     */
    private string $pluginDir = '';

    /**
     * WordPress plugin filename.
     *
     * @var string github-updater-demo.php
     */
    private string $pluginFilename = '';

    /**
     * WordPress plugin slug.
     *
     * @var string ryansechrest-github-updater-demo
     */
    private string $pluginSlug = '';

    /**
     * WordPress plugin URL.
     *
     * @var string https://github.com/ryansechrest/github-updater-demo
     */
    private string $pluginUrl = '';

    /**
     * WordPress plugin version.
     *
     * @var string 1.0.0
     */
    private string $pluginVersion = '';

    /**
     * Relative path to the plugin icon from the plugin root.
     *
     * @var string assets/icon.png
     */
    private string $pluginIcon = '';

    /**
     * Relative path to the small plugin banner from the plugin root.
     *
     * @var string assets/banner-772x250.jpg
     */
    private string $pluginBannerSmall = '';

    /**
     * Relative path to the large plugin banner from the plugin root.
     *
     * @var string assets/banner-1544x500.jpg
     */
    private string $pluginBannerLarge = '';

    // --- WordPress Properties ------------------------------------------------

    /**
     * Highest WordPress version that's supported by the plugin.
     *
     * @var string 6.6
     */
    private string $testedUpTo = '';

    // --- Custom Properties ---------------------------------------------------

    /**
     * Changelog to use for populating the plugin details modal.
     *
     * @var string CHANGELOG.md
     */
    private string $changelog = '';

    // --- Toggle Properties ---------------------------------------------------

    /**
     * Enable the GitHubUpdate debugger.
     *
     * If this property is set to true, as well as the `WP_DEBUG` and
     * `WP_DEBUG_LOG` constants within `wp-config.php`, then GitHub Updater
     * will log pertinent information to `wp-content/debug.log`.
     *
     * @var bool
     */
    private bool $enableDebugger = false;

    /**
     * Enable the GitHub access token setting on the General Settings page.
     *
     * @var bool
     */
    private bool $enableSetting = false;

    // --- Internal Properties -------------------------------------------------

    /**
     * Automatically generated option name for GitHub access token.
     *
     * @var string github_updater_demo_access_token
     */
    private string $optionName = '';

    // *************************************************************************

    /**
     * Set the absolute path to the plugin file containing the plugin header.
     *
     * @param string $file .../wp-content/plugins/github-updater-demo/github-updater-demo.php
     */
    public function __construct(string $file)
    {
        $this->file = $file;

        $this->load();
    }

    // --- Public Configuration Methods ----------------------------------------

    /**
     * Set the GitHub access token (if the repository is private).
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
     * Set the GitHub branch name (if it's not `main`).
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
     * Set the relative path to the plugin icon from the plugin root.
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
     * Set the relative path to the small plugin banner from the plugin root.
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
     * Set the relative path to the large plugin banner from the plugin root.
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
     * Set the changelog to render within the plugin details modal.
     *
     * @param string $changelog CHANGELOG.md
     * @return $this
     */
    public function setChangelog(string $changelog): self
    {
        $this->changelog = $changelog;

        return $this;
    }

    // --- Public Feature Methods ----------------------------------------------

    /**
     * Enable the GitHub Updater debugger.
     *
     * If this property is set to true, as well as the `WP_DEBUG` and
     * `WP_DEBUG_LOG` constants within `wp-config.php`, then GitHub Updater
     * will log pertinent information to `wp-content/debug.log`.
     *
     * @return $this
     */
    public function enableDebugger(): self
    {
        $this->enableDebugger = true;

        return $this;
    }

    /**
     * Enable the GitHub access token setting on the General Settings page.
     *
     * When the plugin is hosted in a private GitHub repository, WordPress
     * will not be able to download the plugin ZIP file without a GitHub
     * access token.
     *
     * Instead of manually passing the GitHub access token via an option or a
     * constant using `setAccessToken()`, GitHub Updater can create a
     * corresponding setting on the General Settings page.
     *
     * On the General Settings page you'll find a new section with an input
     * field to save a new access token, the last five characters of the current
     * access token (if one exists), the GitHub username of the person who
     * created it, and the date and time it expires.
     *
     * Last, seven days before the access token expires, an admin notice will
     * alert the user that a new token needs to be created.
     *
     * @return $this
     */
    public function enableSetting(): self
    {
        $this->enableSetting = true;

        $optionName = strtolower($this->pluginName);
        $optionName = str_replace(' ', '_', $optionName);
        $optionName = preg_replace( '/[^a-z0-9_]/', '', $optionName);

        $this->optionName = $optionName . '_access_token';

        return $this;
    }

    // --- Add GitHub Updater --------------------------------------------------

    /**
     * Add the update mechanism to the plugin.
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

        $this->registerSetting();
    }

    // *************************************************************************

    /**
     * Load the properties with values based on `$file`.
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
        // Fields from the plugin header
        $pluginData = get_file_data(
            $this->file,
            [
                'PluginName' => 'Plugin Name',
                'PluginURI' => 'Plugin URI',
                'Version' => 'Version',
                'TestedUpTo' => 'Tested up to',
                'UpdateURI' => 'Update URI',
            ]
        );

        // Extract the fields from the plugin header
        $pluginName = $pluginData['PluginName'] ?? '';
        $pluginUri = $pluginData['PluginURI'] ?? '';
        $updateUri = $pluginData['UpdateURI'] ?? '';
        $version = $pluginData['Version'] ?? '';
        $testedUpTo = $pluginData['TestedUpTo'] ?? '';

        // If the required fields were not set, exit
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

        // e.g. `GitHub Updater Demo`
        $this->pluginName = $pluginName;

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

    // -------------------------------------------------------------------------

    /**
     * Build the plugin details result.
     *
     * When WordPress checks for plugin updates, it queries wordpress.org;
     * however, this plugin does not exist there. We use this hook to intercept
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
     * Hook to build the plugin details result.
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
        // If the action is `query_plugins`, `hot_tags`, or `hot_categories`, exit
        if ($action !== 'plugin_information') return $result;

        // If this is not our plugin, exit
        if ($args->slug !== $this->pluginSlug) return $result;

        // Get the remote plugin file contents to read the plugin header
        $fileContents = $this->getRemotePluginFileContents(
            $this->pluginFilename
        );

        // If the remote plugin file could not be retrieved, exit
        if (!$fileContents) return $result;

        // Extract the plugin version from the remote plugin file contents
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

        // Build the plugin details result
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

        // Assume there is no plugin author
        $author = '';

        // If the author name exists, use it
        if ($fields['Author']) {
            $author = $fields['Author'];
        }

        // If both the author name and URL exist, use them
        if ($fields['Author'] && $fields['Author URI']) {
            $author = sprintf(
                '<a href="%s">%s</a>',
                $fields['Author URI'],
                $fields['Author']
            );
        }

        // If the author exists, set it
        if ($author) {
            $result['author'] = $author;
        }

        // If the small plugin banner exists, set it
        if ($pluginBannerSmall = $this->getPluginBannerSmall()) {
            $result['banners']['low'] = $pluginBannerSmall;
        }

        // If the large plugin banner exists, set it
        if ($pluginBannerLarge = $this->getPluginBannerLarge()) {
            $result['banners']['high'] = $pluginBannerLarge;
        }

        // If a changelog exists, set it
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

    // -------------------------------------------------------------------------

    /**
     * Log the plugin details for plugins.
     *
     * Useful for inspecting options on officially hosted WordPress plugins.
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
     * Hook to log the plugin details for plugins.
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

    // -------------------------------------------------------------------------

    /**
     * Check for plugin updates.
     *
     * If the plugin has an `Update URI` pointing to `github.com`, then check
     * if the plugin was updated on GitHub. If so, record a pending update so
     * that either WordPress can automatically update it (if enabled), or a
     * user can manually update it much like an officially hosted plugin.
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
     *   $data    Plugin data as defined in the plugin header.
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
        // If the plugin does not match this plugin, exit
        if ($file !== $this->pluginFile) return $update;

        $this->logStart(
            '_checkPluginUpdates', 'update_plugins_github.com'
        );

        // Get the remote plugin file contents to read the plugin header
        $fileContents = $this->getRemotePluginFileContents(
            $this->pluginFilename
        );

        // Extract the plugin version from the remote plugin file contents
        $fields = $this->extractPluginHeaderFields(
            ['Version' => 'version'], $fileContents
        );

        $this->log('Does $newVersion (' . $fields['Version'] . ') exist...');

        // If the version was not found, exit
        if (!$fields['Version']) {
            $this->log('No');
            $this->logValue('Return early', $update);
            $this->logFinish('_checkPluginUpdates');

            return $update;
        }

        $this->log('Yes');

        // Build the plugin data response for WordPress
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

        // If there was no icon was defined, exit with plugin data
        if (!$pluginIcon) {
            $this->log('No');
            $this->logValue('Return early', $pluginData);
            $this->logFinish('_checkPluginUpdates');

            return $pluginData;
        }

        $this->log('Yes');

        // Otherwise add the icon to the plugin data
        $pluginData['icons'] = ['default' => $pluginIcon];

        $this->logValue('Return', $pluginData);
        $this->logFinish('_checkPluginUpdates');

        return $pluginData;
    }

    /**
     * Get the remote plugin file contents from the GitHub repository.
     *
     * @param string $filename github-updater-demo.php
     * @return string
     */
    private function getRemotePluginFileContents(string $filename): string
    {
        return $this->getAccessToken()
            ? $this->getPrivateRemotePluginFileContents($filename)
            : $this->getPublicRemotePluginFileContents($filename);
    }

    /**
     * Get the remote plugin file contents from the public GitHub repository.
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
     * Get the public remote plugin file.
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
     * Get the remote plugin file contents from the private GitHub repository.
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
                        'Authorization' => 'Bearer ' . $this->getAccessToken(),
                        'Accept' => 'application/vnd.github.raw+json',
                    ]
                ]
            )
        );
    }

    /**
     * Get the private remote plugin file.
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
     * Get the path to the remote plugin ZIP file.
     *
     * @return string https://github.com/ryansechrest/github-updater-demo/archive/refs/heads/main.zip
     */
    private function getRemotePluginZipFile(): string
    {
        return $this->getAccessToken()
            ? $this->getPrivateRemotePluginZipFile()
            : $this->getPublicRemotePluginZipFile();
    }

    /**
     * Get the path to the public remote plugin ZIP file.
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
     * Get the path to the private remote plugin ZIP file.
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

    // -------------------------------------------------------------------------

    /**
     * Prepare the HTTP request args.
     *
     * Include the GitHub access token in the request header when the
     * repository is private so that WordPress has access to download the
     * remote plugin ZIP file.
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
     * Hook to prepare the HTTP request args.
     *
     *   $args  An array of the HTTP request arguments.
     *   $url   The request URL.
     *
     * @param array $args ['method' => 'GET', 'headers' => [], ...]
     * @param string $url https://api.github.com/repos/ryansechrest/github-updater-demo/zipball/main
     * @return array ['headers' => ['Authorization => 'Bearer...'], ...]
     */
    public function _prepareHttpRequestArgs(array $args, string $url): array
    {
        // If the URL doesn't match the ZIP file to the private GitHub repo, exit
        if ($url !== $this->getPrivateRemotePluginZipFile()) return $args;

        // Include GitHub access token and file type
        $args['headers']['Authorization'] = 'Bearer ' . $this->getAccessToken();
        $args['headers']['Accept'] = 'application/vnd.github+json';

        $this->logStart('_prepareHttpRequestArgs', 'http_request_args');
        $this->logValue('Return', $args);
        $this->logFinish('_prepareHttpRequestArgs');

        return $args;
    }

    // -------------------------------------------------------------------------

    /**
     * Move the updated plugin.
     *
     * The updated plugin will be extracted into a directory containing GitHub's
     * branch name (e.g. `github-updater-demo-main`). Since this likely differs
     * from the old plugin (e.g. `github-updater-demo`), it will cause WordPress
     * to deactivate it. To prevent this, we move the new plugin to the old
     * plugin's directory.
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
     * Hook to move the updated plugin.
     *
     * @param array $result ['destination' => '.../wp-content/plugins/github-updater-demo-main', ...]
     * @param array $options ['plugin' => 'github-updater-demo/github-updater-demo.php', ...]
     * @return array
     */
    public function _moveUpdatedPlugin(array $result, array $options): array
    {
        // Get the plugin being updated
        // e.g. `github-updater-demo/github-updater-demo.php`
        $pluginFile = $options['plugin'] ?? '';

        // If the plugin does not match this plugin, exit
        if ($pluginFile !== $this->pluginFile) return $result;

        $this->logStart(
            '_moveUpdatedPlugin', 'upgrader_install_package_result'
        );

        // Save the path to the new plugin
        // e.g. `.../wp-content/plugins/github-updater-demo-main`
        $newPluginPath = $result['destination'] ?? '';

        $this->log(
            'Does $newPluginPath (' . $newPluginPath . ') exist...'
        );

        // If the path to the new plugin doesn't exist, exit
        if (!$newPluginPath) {
            $this->log('No');
            $this->logValue('Return early', $result);
            $this->logFinish('_moveUpdatedPlugin');

            return $result;
        }

        $this->log('Yes');

        // Save the root path to all plugins, e.g. `.../wp-content/plugins`
        $pluginRootPath = $result['local_destination'] ?? WP_PLUGIN_DIR;

        // Piece together the path to the old plugin,
        // e.g. `.../wp-content/plugins/github-updater-demo`
        $oldPluginPath = $pluginRootPath . '/' . $this->pluginDir;

        // Move the new plugin to the old plugin directory
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

    // *************************************************************************

    /**
     * Register the GitHub access token setting in WordPress.
     *
     * @return void
     */
    private function registerSetting(): void
    {
        if (!$this->enableSetting) {
            return;
        }

        add_action('admin_init', function() {
            register_setting(
                option_group: 'general',
                option_name: $this->optionName,
                args: [
                    'type' => 'string',
                    'label' => $this->pluginName,
                    'description' => 'GitHub access token for "' . $this->pluginName . '" plugin.',
                    'sanitize_callback' => [$this, '_sanitizeSetting'],
                    'show_in_rest' => false,
                    'default' => '',
                ]
            );

            add_settings_section(
                id: 'ryse-github-updater-section',
                title: 'GitHub Access Tokens',
                callback: [$this, '_addSettingsSection'],
                page: 'general',
                args: []
            );

            add_settings_field(
                id: 'ryse-github-updater-field',
                title: $this->pluginName,
                callback: [$this, '_addSettingsField'],
                page: 'general',
                section: 'ryse-github-updater-section',
                args: ['label_for' => $this->optionName]
            );
        });

        if (!$this->accessTokenExpiresInDays(30)) {
            return;
        }

        add_action('admin_notices', function() {
            $tokenExpiration = $this->getAccessTokenExpiration();
            echo '<div class="notice notice-warning">';
            echo '<p>';
            echo wp_kses(
                sprintf(
                    'GitHub access token for the <b>%s</b> plugin expires on <b>%s</b> at <b>%s</b>, after which the plugin can no longer be automatically updated. Create a <a href="https://github.com/settings/personal-access-tokens" target="_blank">new access token</a> and save it on the <a href="%s">General Settings</a> page.',
                    $this->pluginName,
                    $this->formatAccessTokenExpirationDate($tokenExpiration),
                    $this->formatAccessTokenExpirationTime($tokenExpiration),
                    admin_url('options-general.php') . '#' . $this->optionName
                ),
                ['a' => ['href' => [], 'target' => []], 'b' => []]
            );
            echo '</p>';
            echo '</div>';
        });
    }

    // --- Access Token Hooks --------------------------------------------------

    /**
     * Hook to sanitize the new GitHub access token value.
     *
     * @param string $value github_pat_*****
     * @return string
     */
    public function _sanitizeSetting(string $value): string
    {
        // Get the current access token if there is one
        $oldValue = get_option($this->optionName, '');

        if ($value === '') return $oldValue;

        // If the value doesn't start with the proper prefix, exit w/ error
        if (!str_starts_with($value, 'github_pat_')) {
            add_settings_error(
                setting: $this->pluginName,
                code: $this->optionName . '_error',
                message: sprintf(
                    __(
                        'GitHub access token format for the "%s" plugin was not recognized. Access token should start with "github_pat_".',
                        'ryse-github-updater'
                    ),
                    esc_html($this->pluginName)
                ),
            );
            return $oldValue;
        }

        $tokenLength = strlen($value);

        // If the value is too short, exit w/ error
        if ($tokenLength < 80) {
            add_settings_error(
                setting: $this->pluginName,
                code: $this->optionName . '_error',
                message: sprintf(
                    __(
                        'GitHub access token for the "%s" plugin is too short to be valid. Access token should be at least 80 characters.',
                        'ryse-github-updater'
                    ),
                    esc_html($this->pluginName)
                )
            );
            return $oldValue;
        }

        // If the new value is the same as the old value,
        // then nothing changed, so exit
        if ($value === $oldValue) return $oldValue;

        // Make test request to ensure access token is valid
        $response = wp_remote_get(
            'https://api.github.com/user',
            ['headers' => ['Authorization' => 'Bearer ' . $value]]
        );

        // Check the response code...
        // `200` = OK, `401` = NOT AUTHORIZED
        $responseCode = wp_remote_retrieve_response_code($response);

        // If response isn't OK, exit w/ error
        if ($responseCode !== 200) {
            add_settings_error(
                setting: $this->pluginName,
                code: $this->optionName . '_error',
                message: sprintf(
                    __(
                        'GitHub access token for the "%s" plugin is invalid. If there is currently a valid access token saved, it was retained to ensure connectivity.',
                        'ryse-github-updater'
                    ),
                    esc_html($this->pluginName)
                ),
            );
            return $oldValue;
        }

        // JSON decode API response body
        $body = json_decode(wp_remote_retrieve_body($response));

        // If the GitHub account name exists, save it
        // e.g. `ryansechrest`
        if (property_exists($body, 'login')) {
            $this->setAccessTokenCreator($body->login);
        }

        // e.g. `2025-04-21 23:00:00 -0500`
        $tokenExpiration = wp_remote_retrieve_header(
            $response, 'github-authentication-token-expiration'
        );

        // If the token expiration header exists, save it
        if ($tokenExpiration) {
            $this->setAccessTokenExpiration($tokenExpiration);
        }

        return $value;
    }

    /**
     * Hook to add a section to the WordPress General Settings page.
     *
     * @return void
     */
    public function _addSettingsSection(): void
    {
        echo __(
            'Manage <a href="https://github.com/settings/personal-access-tokens">personal access tokens</a> for plugins hosted in a private repository on GitHub.',
            'ryse-github-updater'
        );
    }

    /**
     * Hook to add a field to the WordPress General Settings page.
     *
     * @return void
     */
    public function _addSettingsField(): void
    {
        echo '<input ';
        echo 'name="' . esc_attr($this->optionName) . '" ';
        echo 'type="text" ';
        echo 'id="' . esc_attr($this->optionName) . '" ';
        echo 'placeholder="' . __('Enter a new access token', 'ryse-github-updater') . '" ';
        echo 'class="regular-text"';
        echo '>';

        $accessToken = get_option($this->optionName, '');

        if (!$accessToken) {
            echo '<p class="description" style="margin-top:.5em;"><i>';
            echo __(
                'There is currently no access token saved.',
                'ryse-github-updater'
            );
            echo '</i></p>';
            return;
        }

        $tokenTail = substr(
            $accessToken, strlen($accessToken) - 5
        );

        $tokenExpiration = $this->getAccessTokenExpiration();
        $gitHubAccount = $this->getAccessTokenCreator();

        echo '<p class="description" style="margin-top:.5em;">';
        echo sprintf(
            __(
                'Current access token <code>github_pat_***************%s</code>, generated by <b><a href="%s" target="_blank">%s</a></b>, expires on <b>%s</b> at <b>%s</b>.',
                'ryse-github-updater'
            ),
            esc_html($tokenTail),
            esc_html('https://github.com/' . $gitHubAccount),
            esc_html($gitHubAccount),
            esc_html($this->formatAccessTokenExpirationDate($tokenExpiration)),
            esc_html($this->formatAccessTokenExpirationTime($tokenExpiration)),
        );
        echo '</p>';
    }

    // --- Access Token Methods ------------------------------------------------

    /**
     * Get the manually configured GitHub access token (if set) or check if
     * one exists in the database via the automatic access token setting
     * created using `enableSetting()` and `registerSetting()`.
     *
     * @return string github_pat_*****
     */
    private function getAccessToken(): string
    {
        // If the GitHub access token was manually set, return it
        if ($this->gitHubAccessToken) {
            return $this->gitHubAccessToken;
        }

        // If the option name is blank, meaning `enableSetting()` is `false`,
        // then there cannot be an access token, so return a blank string
        if (!$this->optionName) {
            return '';
        }

        // Otherwise, return the access token from the database
        return get_option($this->optionName, '');
    }

    /**
     * Get the expiration date and time of the GitHub access token.
     *
     * @return string 2025-01-01 09:00:00
     */
    private function getAccessTokenExpiration(): string
    {
        return get_option(
            $this->optionName . '_expiration',
            '0000-00-00 00:00:00'
        );
    }

    /**
     * Format the access token expiration as a date.
     *
     * @param string $tokenExpiration 2025-01-01 09:00:00
     * @return string January 1, 2025
     */
    private function formatAccessTokenExpirationDate(string $tokenExpiration): string
    {
        $dateTime = new DateTime($tokenExpiration);

        return $dateTime->format(get_option('date_format', 'F j, Y'));
    }

    /**
     * Format the access token expiration as a time.
     *
     * @param string $tokenExpiration 2025-01-01 09:00:00
     * @return string 9:00am
     */
    private function formatAccessTokenExpirationTime(string $tokenExpiration): string
    {
        $dateTime = new DateTime($tokenExpiration);

        return $dateTime->format(get_option('time_format', 'g:i a'));
    }

    /**
     * Set the expiration date and time of the GitHub access token.
     *
     * @param string $expiration 0000-00-00 00:00:00
     * @return void
     */
    private function setAccessTokenExpiration(string $expiration): void
    {
        update_option(
            $this->optionName . '_expiration',
            $expiration
        );
    }

    /**
     * Get the GitHub account name that created the GitHub access token.
     *
     * @return string ryansechrest
     */
    private function getAccessTokenCreator(): string
    {
        return get_option(
            $this->optionName . '_account',
            'unknown'
        );
    }

    /**
     * Set the GitHub account name that created the GitHub access token.
     *
     * @param string $account ryansechrest
     * @return void
     */
    private function setAccessTokenCreator(string $account): void
    {
        update_option(
            $this->optionName . '_account',
            $account
        );
    }

    /**
     * Check whether the GitHub access token expires in `$days` or less.
     *
     * @param int $days 7
     * @return bool
     */
    private function accessTokenExpiresInDays(int $days): bool
    {
        $currentDateTime = new DateTime();
        $tokenDateTime = new DateTime($this->getAccessTokenExpiration());

        // If the access token is already expired
        if ($tokenDateTime <= $currentDateTime) {
            return true;
        }

        $interval = $currentDateTime->diff($tokenDateTime);

        return $interval->days <= $days;
    }

    // *************************************************************************

    /**
     * Add an admin notice that required plugin header fields are missing.
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

    // --- Private Retrieval Methods -------------------------------------------

    /**
     * Get the plugin icon if defined and valid.
     *
     * @return string https://example.org/wp-content/plugins/github-updater-demo/assets/icon.png
     */
    private function getPluginIcon(): string
    {
        if (!$this->pluginIcon) return '';

        $pluginIconPath = $this->pluginDir . '/' . $this->pluginIcon;

        return plugins_url($pluginIconPath);
    }

    /**
     * Get the small plugin banner (772x250).
     *
     * @return string https://example.org/wp-content/plugins/github-updater-demo/assets/banner-772x250.jpg
     */
    private function getPluginBannerSmall(): string
    {
        if (!$this->pluginBannerSmall) return '';

        return $this->getPluginFile($this->pluginBannerSmall);
    }

    /**
     * Get the large plugin banner (1544x500).
     *
     * @return string https://example.org/wp-content/plugins/github-updater-demo/assets/banner-1544x500.jpg
     */
    private function getPluginBannerLarge(): string
    {
        if (!$this->pluginBannerLarge) return '';

        return $this->getPluginFile($this->pluginBannerLarge);
    }

    /**
     * Get the specified plugin file if it exists.
     *
     * @param string $file assets/icon.png
     * @return string https://example.org/wp-content/plugins/github-updater-demo/assets/icon.png
     */
    private function getPluginFile(string $file): string
    {
        $file = sprintf('%s/%s', $this->pluginDir, $file);

        if (!file_exists(WP_PLUGIN_DIR . '/' . $file)) return '';

        return plugins_url($file);
    }

    /**
     * Get the changelog from GitHub.
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

    // --- Private Helper Methods ----------------------------------------------

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

    // --- Private Markdown Methods --------------------------------------------

    /**
     * Convert the Markdown to HTML.
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
     * Get the Markdown block type from the line.
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
     * Whether the line contains a Markdown header.
     *
     * @param string $line # Foobar
     * @return bool true
     */
    private function isMarkdownHeader(string $line): bool
    {
        return str_starts_with($line, '#');
    }

    /**
     * Convert the Markdown header to HTML.
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
     * Whether the line contains a Markdown list.
     *
     * @param string $line - Foobar
     * @return bool true
     */
    private function isMarkdownList(string $line): bool
    {
        return str_starts_with($line, '-');
    }

    /**
     * Convert the unordered list.
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
     * Whether the line contains a Markdown blockquote.
     *
     * @param string $line > Foobar
     * @return bool true
     */
    private function isMarkdownBlockquote(string $line): bool
    {
        return str_starts_with($line, '>');
    }

    /**
     * Convert the Markdown blockquote.
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
     * Whether the line contains a Markdown code block.
     *
     * @param string $line ```
     * @return bool true
     */
    private function isMarkdownCode(string $line): bool
    {
        return str_starts_with($line, '```');
    }

    /**
     * Convert the Markdown code block.
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
     * Whether the line contains a Markdown paragraph.
     *
     * @param string $line Foobar
     * @return bool true
     */
    private function isMarkdownParagraph(string $line): bool
    {
        return $line !== '';
    }

    /**
     * Convert the Markdown paragraph.
     *
     * @param string $line Foobar
     * @return string[] ['<p>Foobar</p>']
     */
    private function convertMarkdownParagraph(string $line): array
    {
        return ['<p>' . $this->convertInlineMarkdown($line) . '</p>'];
    }

    /**
     * Convert all inline Markdown.
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

    // --- Private Log Methods -------------------------------------------------

    /**
     * Log a message with an optional value.
     *
     * @param string $message Plugins data
     * @return void
     */
    private function log(string $message,): void
    {
        if (!$this->enableDebugger || !WP_DEBUG || !WP_DEBUG_LOG) return;

        error_log('[GitHub Updater] ' . $message);
    }

    /**
     * Log when a method starts running.
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
     * Log the label and value through `print_r()`.
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
     * Log when the method finishes running.
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
