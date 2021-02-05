![Lambo logo](https://raw.githubusercontent.com/tighten/lambo/main/lambo-banner.png)

[![Run tests](https://github.com/tighten/takeout/workflows/Run%20tests/badge.svg?branch=main)](https://github.com/tighten/lambo/actions?query=workflow%3A%22Run+Tests%22)

**Super-powered `laravel new` for Laravel and Valet**

Lambo is a command-line tool that replaces the Laravel installer and wraps up the most common tasks you might take when creating a Laravel app: opening it in your editor and your browser, initialize a git repository, tweak your `.env` and `.env.example`, and more.


## Requirements

- PHP 7.3+
- (optional, but beneficial) [Laravel Valet](https://laravel.com/docs/valet)

## Installation

```bash
composer global require tightenco/lambo:^1.0
```

## Upgrading

```bash
composer global update tightenco/lambo
```

## Usage

Make sure `~/.composer/vendor/bin` is in your terminal's path.

```bash
cd ~/Sites
lambo new myNextProject
```

### What exactly does it do?

- `laravel new $PROJECTNAME`
- Initialize a git repo, add all of the files, and, after some changes below, make a commit with the text "Initial commit."
- Replace the `.env` (and `.env.example`) database credentials with the default macOS MySQL credentials: database of `$PROJECTNAME`, user `root`, and empty password
- Replace the `.env` (and `.env.example`) `APP_URL` with `$PROJECTNAME.$YOURVALETTLD`
- Generate an app key
- Open the project in your favorite editor
- Open `$PROJECTNAME.$YOURVALETTLD` in your browser

> Note: If your `$PROJECTNAME` has dashes (`-`) in it, they will be replaced with underscores (`_`) in the database name.

There are also a few optional behaviors based on the parameters you pass (or define in your config file), including creating a database, migrating, installing Jetstream, running Valet Link and/or Secure, and running a custom bash script of your definition after the fact.

## Customizing Lambo

While the default actions Lambo provides are great, most users will want to customize at least a few of the steps. Thankfully, Lambo is built to be customized!

There are two ways to customize your usage of Lambo: command-line arguments and a config file.

Most users will want to set their preferred configuration options once and then never think about it again. That's best solved by creating a config file.

But if you find yourself needing to change the way you interact with Lambo on a project-by-project basis, you may also want to use the command-line parameters to customize Lambo when you're using it.

### Creating a config file

You can create a config file at `~/.lambo/config` rather than pass the same arguments each time you create a new project.

The following command creates the file, if it doesn't exist, and edits it:

```bash
lambo edit-config
```

The config file contains the configuration parameters you can customize, and will be read on every usage of Lambo.

### Using command-line parameters

Any command-line parameters passed in will override Lambo's defaults and your config settings. See a [full list of the parameters you can pass in](#parameters).

### Commands

- `edit-config` edits your config file (and creates one if it doesn't exist)

  ```bash
  lambo edit-config
  ```

- `edit-after` edits your "after" file (and creates one if it doesn't exist)

  ```bash
  lambo edit-after
  ```

### After File

You can create an after file at `~/.lambo/after` to run additional commands after you create a new project.

The following command creates the file, if it doesn't exist, and edits it:

```bash
lambo edit-after
```

The after file is interpreted as a bash script, so you can include any commands here such as installing additional composer dependencies...

```bash
# Install additional composer dependencies as you would from the command line.
echo "Installing Composer Dependencies"
composer require tightenco/mailthief tightenco/quicksand
```

...or copying additional files to your new project.

```bash
# To copy standard files to new lambo project place them in ~/.lambo/includes directory.
echo "Copying Include Files"
cp -R ~/.lambo/includes/ $PROJECTPATH
```

You also have access to variables from your config file such as `$PROJECTPATH` and `$CODEEDITOR`.

<a id="parameters"></a>
### Configurable parameters

List coming soon. For now, just run `lambo help` and it'll give you the full list.

-----

# For contributors:

## Process for release

If you're working with us and are assigned to push a release, here's the easiest process:

1. Visit the [Lambo Releases page](https://github.com/tighten/lambo/releases); figure out what your next tag will be (increase the third number if it's a patch or fix; increase the second number if it's adding features)
2. On your local machine, pull down the latest version of `main` (`git checkout main && git pull`)
3. Build for the version you're targeting (`./lambo app:build`)
4. Run the build once to make sure it works (`./builds/lambo`)
5. Commit your build and push it up
6. [Draft a new release](https://github.com/tighten/lambo/releases/new) with both the tag version and release title of your tag (e.g. `v1.5.1`)
7. Set the body to be a bullet-point list with simple descriptions for each of the PRs merged, as well as the PR link in parentheses at the end. For example:

    `- Add a superpower (#92)`
8. Hit `Publish release`
9. Profit

## Notes for future development

- All new configuration keys must be added to the `$newConfiguration` property in `UpgradeSavedConfiguration`
- All removed or deprecated configuration keys must be added to the `$removedConfigurationKeys` property in `UpgradeSavedConfiguration`
- Any time configuration keys are changed, the `$configurationVersion` property in `UpgradeSavedConfiguration` needs to be incremented
