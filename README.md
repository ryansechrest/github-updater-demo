# GitHub Updater Demo

WordPress plugin to demonstrate how `GitHubUpdater` can enable WordPress to check for and update a custom plugin that's hosted in either a public or private repository on GitHub.

## Getting Started

1. Copy `GitHubUpdater.php` into your plugin
2. Update namespace (if needed) to match your plugin
3. Require `GitHubUpdater.php` if not auto-loaded
4. Add `GitHubUpdater` to your plugin

## Add GitHubUpdater

How to add GitHubUpdater to your plugin.

### Instantiate (Required)

```php
$gitHubUpdater = new GitHubUpdater(__FILE__);
```

If you don't do this in your root plugin file, the one which contains your plugin header, then make sure that you pass the absolute file path of your root plugin file.

For example:

```php
$file = '/var/www/domains/example.org/wp-content/plugins/<pluginDir>/<pluginFilename>.php';

$gitHubUpdater = new GitHubUpdater($file);
```

The path above is just an example. It's not a good idea to hardcode any part of that path, because directories can change.

### Configure: Production Branch (Optional)

If your production branch is called `main`, you don't need to set the branch, but if your production branch has a different name, specify it:

```php
$gitHubUpdater->setBranch('master');
```

### Configure: Personal Access Token (Optional)

If your GitHub repository is public, you don't need to set an access token, but if your repository is private, you can pass it in here:

```php
$gitHubUpdater->setAccessToken('github_pat_XXXXXXXXX');
```

Note that it's not recommended that you hardcode the token like this.

Either set a constant in `wp-config.php` and pass in the constant:

```php
# wp-config.php
define( 'GITHUB_ACCESS_TOKEN', 'github_pat_XXXXXXXXXX' );
```

```php
$gitHubUpdater->setAccessToken(GITHUB_ACCESS_TOKEN);
```

Or save the token in `wp_options` (manually or via a settings field on an options page) and then pass it via `get_option()`:

```php
$gitHubUpdater->setAccessToken(get_option('github_access_token'));
```

### Configure: Tested WordPress Version (Optional)

If you want WordPress to show your plugin is compatible with the latest version on Dashboard > Updates, then provided the highest version of WordPress you've tested your plugin on:

```php
$gitHubUpdater->setTestedWpVersion('6.5.2');
```

### Add GitHubUpdater to WordPress (Required)

```php
$gitHubUpdater->add();
```

If you don't call `add()`, nothing will happen. This should be the very last method call after everything has been configured.

Last, if you want an even deeper dive into how it works, check out this blog post here: https://ryansechrest.com/2024/04/how-to-enable-wordpress-to-update-your-custom-plugin-hosted-on-github/