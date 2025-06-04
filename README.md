# GitHub Updater Demo

WordPress plugin to demonstrate how `GitHubUpdater` can enable WordPress to check for and update a custom plugin that's hosted in either a public or private repository on GitHub.

## Plugin Header Fields

The following [plugin header fields](https://developer.wordpress.org/plugins/plugin-basics/header-requirements/) are being used by `GitHubUpdater`.

### 🔴 Plugin Name (Required)

Set the name of your plugin:

```
Plugin Name: GitHub Updater Demo
```

Name is displayed on the **Plugins > Installed Plugins** page, on the
**Dashboard > Updates** page, and within the plugin details modal when clicking
**View details**.

### 🔵 Plugin URI (Optional)

Set the URL of your plugin's website:

```
Plugin URI: https://github.com/ryansechrest/github-updater-demo
```

URL is displayed on the within the plugin details modal when clicking
**View details** as **Plugin Homepage**.

### 🔴 Version (Required)

Set the current version of your plugin:

```
Version: 1.0.0
```

Version is used to compare the installed plugin with the latest one on GitHub to
determine if there are updates. Version is displayed on
**Plugins > Installed Plugins** page, on the **Dashboard > Updates** page, and
within the plugin details modal when clicking **View details**. 

### 🔴 Update URI (Required)

Set the URL to your plugin's GitHub repository:

```
Update URI: https://github.com/ryansechrest/github-updater-demo
```

Repository is used as the source for plugin updates.

### 🔵 Author (Optional)

Set the name of your plugin's author:

```
Author: Ryan Sechrest
```

Name is displayed on the **Plugins > Installed Plugins** page, on the
**Dashboard > Updates** page, and within the plugin details modal when clicking
**View details**.

### 🔵 Author URI (Optional)

Set the URL of your plugin author's website:

```
Author URI: https://ryansechrest.com
```

Will hyperlink author's name from up above.

### 🔵 Tested up to (Optional)

Set the highest version of WordPress that your plugin was tested on:

```
Tested up to: 6.6.1
```

Will display the following compatibility message on **Dashboard > Updates** when
your plugin has an update:

```
Compatibility with WordPress 6.6.1: 100% (according to its author)
```

### 🔵 Requires at least (Optional)

Set the lowest version of WordPress that your plugin works on:

```
Requires at least: 6.5
```

Version is displayed within the plugin details modal when clicking
**View details**.

### 🔵 Requires PHP (Optional)

Set the lowest version of PHP that your plugin works on:

```
Requires PHP: 8.2
```

Version is displayed within the plugin details modal when clicking
**View details**.

## Getting Started

1. Copy `GitHubUpdater.php` into your plugin
2. Update namespace to match your plugin
3. Require `GitHubUpdater.php` in your plugin
4. Instantiate `GitHubUpdater` in your plugin

## Setup

How to add and configure `GitHubUpdater` for your plugin.

### 🔴 Instantiate GitHubUpdater (Required)

Instantiate `GitHubUpdater` and pass in the absolute path to your root plugin file.

```php
$gitHubUpdater = new GitHubUpdater(__FILE__);
```

For example, `__FILE__` might resolve to:

```
/var/www/domains/example.org/wp-content/plugins/<pluginDir>/<pluginSlug>.php
```

### 🔵 Configure: Personal Access Token (Optional)

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

### 🔵 Configure: Production Branch (Optional)

If your production branch is not the default `main`, then specify it:

```php
$gitHubUpdater->setBranch('master');
```

### 🔵 Configure: Plugin Icon (Optional)

Specify a relative path from the plugin root to configure a plugin icon:

```php
$gitHubUpdater->setPluginIcon('assets/icon.png');
```

The icon appears on **Dashboard > Updates** next to your plugin.

### 🔵 Configure: Small Plugin Banner (Optional)

Specify a relative path from the plugin root to configure a small plugin banner:

```php
$gitHubUpdater->setPluginBannerSmall('assets/banner-772x250.jpg');
```

The banner will appear in the modal when clicking **View details** on your
plugin.

### 🔵 Configure: Large Plugin Banner (Optional)

Specify a relative path from the plugin root to configure a large plugin banner:

```php
$gitHubUpdater->setPluginBannerLarge('assets/banner-1544x500.jpg');
```

The banner will appear in the modal when clicking **View details** on your
plugin.

### 🔵 Configure: Changelog (Optional)

Specify a relative path from the plugin root to your changelog:

```php
$gitHubUpdater->setChangelog('CHANGELOG.md');
```

This should be a Markdown file and will populate the Changelog tab when clicking
**View details** on your plugin. `GitHubUpdater` will use the most recent file
from  GitHub so that a user can review changes before updating.

The Markdown to HTML converter currently only supports:

- Headers (`#`, `##`, `###`, etc.)
- Unordered lists (`-`)
- Blockquotes (`>`)
- Code blocks (using three backticks to start and end block)
- Paragraphs

And only formats:

- Bold text (`**Foo**`)
- Italic text (`*Foo*`)
- Code (using single backtick to wrap text)

### 🔵 Enable: Access Token Setting

Enable this setting to add a section called **GitHub Access Tokens** to the bottom of WordPress' **General Settings** page. Within this section, there will be a field to store the access token for the plugin.

```php
$gitHubUpdater->enableSetting();
```

When an access token is entered, GitHub Updater will ensure it's valid before saving it, keeping any previous access token as a precaution.

Once an access token is validated and saved, GitHub Updater will display the last five characters of the token, the GitHub account name that created it, and the expiration date below the field.

Should the access token expire in seven days or less, an admin notice will display on all admin pages in WordPress to let users know that a new token must be generated.

Should both `enableSetting()` and `setAccessToken()` be called, the access token passed into `setAccessToken()` will take precedence, so remove `setAccessToken()` to use the settings field.

Last, if the plugin is called **GitHub Updater Demo**, the following three options will be created in the database (`wp_options`) based on the plugin name:

| Option Name                                   | Sample Value | Description                                          |
|-----------------------------------------------|--------------|------------------------------------------------------|
| `github_updater_demo_access_token`            | `github_pat_*****` | Actual GitHub access token                           |
| `github_updater_demo_access_token_account`    | `ryansechrest` | GitHub username of person who generated access token |
| `github_updater_demo_access_token_expiration` | `0000-00-00 00:00:00` | Date and time of when GitHub access token expires    |

This model will allow multiple plugins to use GitHub Updater and keep all access tokens managed in one place.

### 🔴 Add GitHubUpdater (Required)

Add all necessary hooks to WordPress to keep your plugin updated moving forward:

```php
$gitHubUpdater->add();
```

This should be the last method call after `GitHubUpdater` has been configured.

## Final Thoughts

If you want a deep dive into how `GitHubUpdater` works, check out this 
[blog post](https://ryansechrest.com/2024/04/how-to-enable-wordpress-to-update-your-custom-plugin-hosted-on-github/).
That said, while the fundamentals in the blog post are still being used,
`GitHubUpdater` has changed quite a bit since it was written.
