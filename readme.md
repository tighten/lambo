![Lambo logo](https://raw.githubusercontent.com/tighten/lambo/main/lambo-banner.png)

[![Run tests](https://github.com/tighten/takeout/workflows/Run%20tests/badge.svg?branch=main)](https://github.com/tighten/lambo/actions?query=workflow%3A%22Run+Tests%22)

Super-powered `laravel new` for Laravel and Valet.

<strong>A new version of Lambo is coming that will introduce new features and provide a better platform for future enhancements... Oh, and it's being re-written from the ground-up in PHP! :fire:

There's a good chance the non-beta no longer works usefully for creating Laravel 8+ apps. If you are interested in using the old version, check out the [pre-php](https://github.com/tighten/lambo/tree/pre-php) branch and the [v0.5.5](https://github.com/tighten/lambo/releases/tag/v0.5.5) release.</strong>

# For beta testers

To try the beta without installing it globally:

```bash
git clone https://github.com/tightenco/lambo.git
cd lambo
./lambo
```

To try the beta globally:

1. Open `~/.composer/composer.json`
2. Make sure you have the Laravel installer required at `^4.0`
3. Modify your `tightenco/lambo` line to have the version constraint of `^1.0@dev`
4. Save that, and exit
5. Run `composer global update tightenco/lambo`
6. Cross your fingers

The below readme is still being cleaned up from references to the current Lambo version, so please note the following major changes:

- You'll need PHP 7.2+ to run Lambo.
- You'll want to run `lambo new myApplication` instead of `lambo myApplication`.
- Lambo now targets Laravel 8+ and [Laravel Jetstream](https://jetstream.laravel.com/1.x/introduction.html) which means the current options, `--vue`, `--bootstrap` and `--react` have been replaced by `--inertia` and `--livewire`. You may add the `--teams` option to enable team support with inertia and livewire.
- The `--quiet` option has been removed. Lambo now uses quiet/silent mode for git, npm and the laravel installer by default. You may use `--with-output` if terminal output is required.


-----

# Current non-beta version

## Installation

### For Laravel 8+ once we release this from beta
```bash
composer global require tightenco/lambo
```

## Upgrading

```bash
composer global update tightenco/lambo
```

## Usage

Make sure `~/.composer/vendor/bin` is in your terminal's path.

```bash
cd ~/Sites
lambo new superApplication
```

### What exactly does it do?

- `laravel new $PROJECTNAME`
- Initialize a git repo, add all of the files, and make a commit with the text "Initial commit."
- Open the project in your editor
- Replace the `.env` database credentials with the default macOS MySQL credentials: database of `$PROJECTNAME`, user `root`, and empty password
- Replace the `.env` `APP_URL` with `$PROJECTNAME.$YOURVALETTLD`
- Open `$PROJECTNAME.$YOURVALETTLD` in your browser
- TODO: What else does it do in the new version? Anything else?

> Note: If your `$PROJECTNAME` has dashes (`-`) in it, they will be replaced with underscores (`_`) in the database name.

There are also a few optional behaviors based on the parameters you pass (or define in your config file).

### Optional Arguments

TODO: Update all of these for the new version

- `-h` or `--help` to get the help dialog

  ```bash
  lambo --help
  ```

- `-e` or `--editor` to define your editor command. Whatever is passed here will be run as `$EDITOR .` after creating the project.

  ```bash
  # runs "subl ." in the project directory after creating the project
  lambo new superApplication --editor=subl
  ```

- `-m` or `--message` to set the first commit message.

  ```bash
  lambo new superApplication --message="This lambo runs fast!"
  ```

- `-p` or `--path` to specify where to install the application.

  ```bash
  lambo new superApplication --path=~/Sites
  ```

- `-q` or `--quiet` use quiet/silent mode for `git`, `yarn`/`npm` and laravel installer.

- `-d` or `--dev` to choose the `develop` branch instead of `master`, getting the beta install

  ```bash
  lambo new superApplication --dev
  ```

- `-a` or `--auth` to use Artisan to scaffold all of the routes and views you need for authentication

  ```bash
  lambo new superApplication --auth
  ```

- `--node` to run `yarn` if installed, otherwise runs `npm install` after creating the project

  ```bash
  lambo new superApplication --node
  ```

- `-l` or `--link` to create a Valet link to the project directory.

  ```bash
  lambo new superApplication --link
  ```

- `-s` or `--secure` to secure the Valet site using https.

  ```bash
  lambo new superApplication --secure
  ```

- `--create-db` create a new MySql database which has the same name as your project.
  This requires `mysql` command to be available on your system.

  ```bash
  lambo new superApplication --create-db
  ```

- `--dbuser` specify the database username.

  ```bash
  lambo new superApplication --dbuser=USER
  ```

- `--dbpassword` specify the database password.

  ```bash
  lambo new superApplication --dbpassword=SECRET
  ```

- `--vue` to set the frontend to the default Laravel 5.* scaffolding (set by default)

  ```bash
  lambo new superApplication --vue
  ```

- `--bootstrap` to set the frontend to Bootstrap

  ```bash
  lambo new superApplication --bootstrap
  ```

- `--react` to set the frontend to React

  ```bash
  lambo new superApplication --react
  ```

### Commands

- `edit-config` edits your config file

  ```bash
  lambo edit-config
  ```

- `make-after` creates an "after" file so you can run additional commands after Lambo finishes

  ```bash
  lambo make-after
  ```

- `edit-after` edits your after file

  ```bash
  lambo edit-after
  ```

### Config File

You can create a config file at `~/.lambo/config` rather than pass the same arguments each time you create a new project.

The following command creates the file, if it doesn't exist, and edits it:

```bash
lambo edit-config
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

## Requirements

- macOS or Linux.
- [Git](https://git-scm.com).
- The [Laravel installer](https://laravel.com/docs/installation#installing-laravel) and [Laravel Valet](https://laravel.com/docs/valet) installed globally.

> A Linux fork of Valet can be found [here](https://github.com/cpriego/valet-linux)

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
