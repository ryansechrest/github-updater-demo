# GitHub Updater Demo

WordPress plugin to demonstrate how `GitHubUpdater` can enable WordPress to check for and update a custom plugin that's hosted in either a public or private repository on GitHub.

## Plugin Header Fields

The following [plugin header fields](https://developer.wordpress.org/plugins/plugin-basics/header-requirements/) are being used by `GitHubUpdater`.

### Version (Required)

Specify your current plugin's version. For example:

```
Version: 1.0.0
```

Version is used to compare the installed plugin with the latest one on GitHub to determine if there are updates.

### Plugin URI (Required)

Specify URL to your plugin's changelog. For example:

```
Plugin URI: https://ryansechrest.github.io/github-updater-demo
```

Page will be embedded in a modal when viewing plugin details.

### Update URI (Required)

Specify URL to your plugin's repository on GitHub. For example:

```
Update URI: https://github.com/ryansechrest/github-updater-demo
```

Repository is used as the source for plugin updates.

### Tested up to (Optional)

Specify highest version of WordPress that your plugin was tested on. For example

```
Tested up to: 6.6
```

Will show the following compatibility message on Dashboard > Updates when your plugin has an update:

```
Compatibility with WordPress 6.6: 100% (according to its author)
```

## Getting Started

1. Copy `GitHubUpdater.php` into your plugin
2. Update namespace to match your plugin
3. Require `GitHubUpdater.php` in your plugin
4. Instantiate `GitHubUpdater` in your plugin

## Setup

How to add and configure `GitHubUpdater` for your plugin.

### Instantiate GitHubUpdater (Required)

Instantiate `GitHubUpdater` and pass in the absolute path to your root plugin file.

```php
$gitHubUpdater = new GitHubUpdater(__FILE__);
```

For example, `__FILE__` might resolve to:

```
/var/www/domains/example.org/wp-content/plugins/<pluginDir>/<pluginSlug>.php
```

### Configure: Personal Access Token (Optional)

If your GitHub repository is private, then set your access token:

```php
$gitHubUpdater->setAccessToken('github_pat_XXXXXXXXX');
```

It's not recommended to hardcode a token like you see above.

Either define a constant in `wp-config.php`:

```php
define( 'GITHUB_ACCESS_TOKEN', 'github_pat_XXXXXXXXXX' );
```

And then pass in the constant:

```php
$gitHubUpdater->setAccessToken(GITHUB_ACCESS_TOKEN);
```

Or save your access token in `wp_options` and pass it via `get_option()`:

```php
$gitHubUpdater->setAccessToken(get_option('github_access_token'));
```

### Configure: Production Branch (Optional)

If your production branch is not the default `main`, then specify it:

```php
$gitHubUpdater->setBranch('master');
```

### Configure: Plugin Icon (Optional)

If you want to use an image within your plugin as the plugin icon, set a relative path to the file:

```php
$gitHubUpdater->setPluginIcon('assets/icon.png');
```

The path should be relative from your plugin root. The icon appears on Dashboard > Updates next to your plugin.

### Add GitHubUpdater (Required)

Add all necessary hooks to WordPress to keep your plugin updated moving forward:

```php
$gitHubUpdater->add();
```

This should be the last method call after `GitHubUpdater` has been configured.

## Final Thoughts

If you want a deep dive into how `GitHubUpdater` works, check out this [blog post](https://ryansechrest.com/2024/04/how-to-enable-wordpress-to-update-your-custom-plugin-hosted-on-github/).
