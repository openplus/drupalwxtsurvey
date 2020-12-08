# Web Experience Toolkit: Drupal WxT

[![Build Status][githubci-badge]][githubci]

## Important

Drupal WxT for Drupal 9 is under a release candidate phase and will provide an upgrade path for all future releases.

## Important Links

- Documentation Website: [drupalwxt.github.io][docs]
- Installation Profile: [github.com/drupalwxt/wxt][github-wxt]
- Composer Project: [github.com/drupalwxt/site-wxt][github-site-wxt]
- Composer Project Template: [github.com/drupalwxt/wxt-project][project]
- Helm Chart for Kubernetes: [github.com/drupalwxt/helm-drupal][github-helm]
- Docker Containers: [hub.docker.com/r/drupalwxt/site-wxt][docker-hub]
- Drupal Repository: [drupal.org/project/wxt][drupal]
- Issue Queue: [drupal.org/project/wxt/issues][issue-drupal]
- Changelog: [CHANGELOG.md][changelog]
- GitHub Actions: [github.com/drupalwxt/wxt/actions][githubci]
- GitHub Tarball: [github.com/drupalwxt/wxt/releases][release-github]

> Note: For up-to-date documentation please always consult our [README.md][readme] file.

## Overview

The Drupal WxT distribution is a web content management system which assists in building and maintaining innovative Web sites that are accessible, usable, and interoperable. This distribution is open source software led by the Government of Canada and free for use by departments and external Web communities. This distribution relies on and integrates with the [Web Experience Toolkit][wet-boew] for its conformance to the Web Content Accessibility Guideline (WCAG 2.0) and its compliance to the Standards on [Web Accessibility][standard_accessibility], [Web Usability][standard_usability], and [Web Interoperability][standard_interoperability].

## Architecture

The goal of WxT 4.0.x line is to make the installation profile very minimal by default but providing additional extensions that can be enabled as desired.

WxT offers some light enhancements to Drupal Core, mainly around security and performance, and integration with the Web Experience Toolkit. By default, the distribution offers minimal functionality to allow full customizations by users. A set of optional extensions is available that provide additional functionality generally beneficial to Government departments. A community repository of modules and graduation process will be available to enable collaboration between users in the future.

All of the additional contributed modules and Lightning integration have been moved into the WxT Extend `wxt_ext` extensions which can now be installed a la carte for fresh installations via the GUI or passed as flags via Drush.

```
wxt_extension_configure_form.select_all='TRUE'
```

In order to provide a list of the optional enabled extensions during the installation that can be checked, all that any module now has to do is provide a `modulename.wxt_extension.yml` file in their root and they will be picked as installable during the profile install and also respond to the additional drush flag.

For more information please consult the following:

* [WxT Minimal Install][wxt-minimal-install]
* [Upgrade Path from 3.0.x -> 4.0.x][wxt-upgrade-path]
* [Roadmap for Drupal 9][wxt-roadmap]

### Lightning Components

For the optional extensions that Drupal WxT provides we make use of the following Lightning modules:

* [Lightning API](https://www.drupal.org/project/lightning_api)
* [Lightning Core](https://www.drupal.org/project/lightning_core)
* [Lightning Layout](https://www.drupal.org/project/lightning_layout)
* [Lightning Media](https://www.drupal.org/project/lightning_media)
* [Lightning Workflow](https://www.drupal.org/project/lightning_workflow)

> Note: Originally we were leveraging the [Lightning](https://www.drupal.org/project/lightning) installation profile but since Lightning now provides support for the [individual components outside of the profile][lightning_split] we now leverage them directly.

For more information about Lightning:

* https://www.acquia.com/products-services/acquia-lightning
* https://www.acquia.com/blog/building-drupal-8-sites-acquia-lightning-cuts-costs-100000
* https://www.drupal.org/docs/8/distributions/degov/about-degov
* https://github.com/govcms/govcms8

## Installing WxT

We highly recommend using our [Composer-based project template][project-new] to build and maintain your WxT derived project’s codebase.

```sh
composer self-update
composer create-project drupalwxt/wxt-project:4.0.x-dev MYPROJECT
```

> Note: Normally you will pass a stable tag to the above command rather then just pulling from the development branch.

If you don't want to use Composer, you can install WxT the traditional way by downloading a tarball from [WxT's GitHub releases][release-github] page.

> Note: That the tarball generated by the Drupal.org packager does not include the required Composer dependencies and should not be used without following the specialized instructions.

## Site Installation

Install the site using drush which should take approximately 4-5 minutes depending on your system.

```sh
drush si wxt
  --sites-subdir=default \
  --db-url=mysql://root:WxT@mysql:3306/wxt \
  --account-name=admin \
  --account-pass=WxT \
  --site-mail=admin@example.com \
  --site-name="Drupal Install Profile (WxT)" \
  install_configure_form.update_status_module='array(FALSE,FALSE)' \
  --yes
```

> Note: If you work for the Government of Canada you might want to enable the `canada.ca` theme. You can navigate to the `WxT Library` settings page or run the Drush command below.

```sh
drush config-set wxt_library.settings wxt.theme theme-gcweb -y
```

### Generating a Sub Profile

You can customize your installation by creating a sub-profile which uses WxT as its base profile.

WxT includes a Drush command which will generate a sub-profile for you:

```sh
drush --root=/var/www/html/sites/default generate wxt-subprofile
```

### Installing from exported config

WxT can be installed from a set of exported configuration (e.g., using the `--existing-config` option with drush site:install).

### Installation of Default Content via [Migrate][migrate]

```sh
drush migrate:import --group wxt --tag 'Core'
drush migrate:import --group gcweb --tag 'Core'
drush migrate:import --group gcweb --tag 'Menu'
drush config-set wxt_library.settings wxt.theme theme-gcweb -y
drush cr
```

### WxT

Imports examples of common design patterns for WxT branded sites.

```sh
drush migrate:import --group wxt --tag 'Core'
```

> Note: There is a group wxt_translation for importing the corresponding french content.

### Canada

Imports examples of common design patterns for Canada.ca aligning to C&IA specification.

```sh
drush migrate:import --group wxt --tag 'Core'
drush migrate:import --group gcweb --tag 'Core'
drush migrate:import --group gcweb --tag 'Menu'
```

> Note: There is a group gcweb_translation for importing the corresponding french content.

### Groups

We also provide an example of importing groups via a json feed from open.canada.ca that will create a group for every government department where you can isolate content acess.

```sh
drush en wxt_ext_group -y
drush migrate:import --group gcweb --tag 'Group'
```

> Note: Make sure to only have one set of menu's imported for each of the supported themes. Leverage migrate:rollback to assist with this requirement.

## Configuration Management

Drupal WxT thanks to the work done by the Acquia Team is able to use advanced
configuration management strategies.

At the moment this remains an opt-in process and you will have to add the
following modules to your `composer.json` before you add the code snippet
below to your `settings.php` file.

* [Configuration Split](https://www.drupal.org/project/config_split)
* [Configuration Ignore](https://www.drupal.org/project/config_ignore)

Once enabled all default configuration will be stored in `/sites/default/files/config/default/`
and then depending on your environment additionally configuration splits can
be leveraged depending on your `SDLC`.

```php
/**
 * Configuration Split for Configuration Management
 *
 * WxT is following the best practices given by Acquia for configuration
 * management. The "default" configuration directory should be shared between
 * all multi-sites, and each multisite will override this selectively using
 * configuration splits.
 *
 * To disable this functionality simply set the following parameters:
 * $wxt_override_config_dirs = FALSE;
 * $settings['config_sync_directory'] = $dir . "/config/$site_dir";
 *
 * See https://github.com/acquia/blt/blob/12.x/settings/config.settings.php
 * for more information.
 */

use Drupal\wxt\Robo\Common\EnvironmentDetector;

if (!isset($wxt_override_config_dirs)) {
  $wxt_override_config_dirs = TRUE;
}
if ($wxt_override_config_dirs) {
  $config_directories['sync'] = $repo_root . "/var/www/html/sites/default/files/config/default";
  $settings['config_sync_directory'] = $repo_root . "/var/www/html/sites/default/files/config/default";
}
$split_filename_prefix = 'config_split.config_split';
if (isset($config_directories['sync'])) {
  $split_filepath_prefix = $config_directories['sync'] . '/' . $split_filename_prefix;
}
else {
  $split_filepath_prefix = $settings['config_sync_directory'] . '/' . $split_filename_prefix;
}

/**
 * Set environment splits.
 */
$split_envs = [
  'local',
  'dev',
  'test',
  'qa',
  'prod',
  'ci',
];
foreach ($split_envs as $split_env) {
  $config["$split_filename_prefix.$split_env"]['status'] = FALSE;
}
if (!isset($split)) {
  $split = 'none';
  if (EnvironmentDetector::isLocalEnv()) {
    $split = 'local';
  }
  if (EnvironmentDetector::isCiEnv()) {
    $split = 'ci';
  }
  if (EnvironmentDetector::isDevEnv()) {
    $split = 'dev';
  }
  elseif (EnvironmentDetector::isTestEnv()) {
    $split = 'test';
  }
  elseif (EnvironmentDetector::isQaEnv()) {
    $split = 'qa';
  }
  elseif (EnvironmentDetector::isProdEnv()) {
    $split = 'prod';
  }
}
if ($split != 'none') {
  $config["$split_filename_prefix.$split"]['status'] = TRUE;
}

/**
 * Set multisite split.
 */
// $config["$split_filename_prefix.SITENAME"]['status'] = TRUE;
```

## Docker Containers (Optional)

For the (optional) container based development workflow this is roughly the steps that are followed.

> Note: The [docker-scaffold][docker-scaffold] has now been moved to its own repository. Should you wish to use the docker workflow you simply need to run the following command in the site-wxt repository's working directory.

```sh
# Git clone docker scaffold
git clone https://github.com/drupalwxt/docker-scaffold.git docker

# Create symlinks
ln -s docker/docker-compose.yml docker-compose.yml
ln -s docker/docker-compose-ci.yml docker-compose-ci.yml

# Composer install
export COMPOSER_MEMORY_LIMIT=-1 && composer install

# Make our base docker image
make build

# Bring up the dev stack
docker-compose -f docker-compose.yml build --no-cache

# Install Drupal
make drupal_install

# Development configuration
./docker/bin/drush config-set system.performance js.preprocess 0 -y && \
./docker/bin/drush config-set system.performance css.preprocess 0 -y && \
./docker/bin/drush php-eval 'node_access_rebuild();' && \
./docker/bin/drush config-set wxt_library.settings wxt.theme theme-gcweb -y && \
./docker/bin/drush cr

# Migrate default content
./docker/bin/drush migrate:import --group wxt --tag 'Core' && \
./docker/bin/drush migrate:import --group gcweb --tag 'Core' && \
./docker/bin/drush migrate:import --group gcweb --tag 'Menu'
```

## Version History

### Changelog

- [CHANGELOG.md][changelog]

### Releases

- [GitHub Releases][release-github]

## Contributor(s)

Contributors: https://github.com/drupalwxt/wxt/graphs/contributors

## Acknowledgements

Extended with code and lessons learned by the [Acquia Team](https://acquia.com) over at [Lightning](https://github.com/acquia/lightning) and [BLT](https://github.com/acquia/blt).

<!-- Links Referenced -->

[acquia]:                       https://acquia.com
[changelog]:                    https://github.com/drupalwxt/wxt/blob/4.0.x/CHANGELOG.md
[demo]:                         https://drupalwxt.govcloud.ca
[docs]:                         http://drupalwxt.github.io
[docker-hub]:                   https://hub.docker.com/r/drupalwxt/site-wxt
[docker-scaffold]:              https://github.com/drupalwxt/docker-scaffold.git
[drupal]:                       http://drupal.org/project/wxt
[drupal7]:                      http://drupal.org/project/wetkit
[githubci]:                     https://github.com/drupalwxt/wxt/actions
[githubci-badge]:               https://github.com/drupalwxt/wxt/workflows/build/badge.svg
[github-helm]:                  https://github.com/drupalwxt/helm-drupal
[github-wxt]:                   https://github.com/drupalwxt/wxt
[github-site-wxt]:              https://github.com/drupalwxt/site-wxt
[issue-drupal]:                 https://drupal.org/project/issues/wxt
[issue-github]:                 https://github.com/drupalwxt/wxt/issues
[lightning]:                    https://github.com/acquia/lightning
[lightning_split]:              https://www.drupal.org/project/lightning/issues/2933252
[migrate]:                      https://www.drupal.org/node/2127611
[project]:                      https://github.com/drupalwxt/wxt-project#user-content-new-project
[project-new]:                  https://github.com/drupalwxt/wxt-project#user-content-new-project
[readme]:                       https://github.com/drupalwxt/wxt/blob/4.0.x/README.md
[release-github]:               https://github.com/drupalwxt/wxt/releases
[simplytest]:                   http://simplytest.me/project/wxt/8.x-3.x
[standard_accessibility]:       https://www.tbs-sct.gc.ca/pol/doc-eng.aspx?id=23601
[standard_usability]:           http://www.tbs-sct.gc.ca/pol/doc-eng.aspx?id=24227
[standard_interoperability]:    http://www.tbs-sct.gc.ca/pol/doc-eng.aspx?id=25875
[wet-boew]:                     https://github.com/wet-boew/wet-boew
[wet-boew-page]:                https://www.canada.ca/en/treasury-board-secretariat/services/government-communications/web-experience-toolkit.html
[wxt-minimal-install]:          https://www.drupal.org/project/wxt/issues/3182208
[wxt-upgrade-path]:             https://www.drupal.org/project/wxt/issues/3182648
[wxt-roadmap]:                  https://www.drupal.org/project/wxt/issues/3182977