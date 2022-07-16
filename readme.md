![Lambo logo](https://raw.githubusercontent.com/tighten/lambo/main/lambo-banner.png)

[![Run tests](https://github.com/tighten/takeout/workflows/Run%20tests/badge.svg?branch=main)](https://github.com/tighten/lambo/actions?query=workflow%3A%22Run+Tests%22)

**Super-powered `laravel new` for Laravel and Valet**

Lambo is a command-line tool that replaces the Laravel installer and wraps up the most common tasks you might take when creating a Laravel app: opening it in your editor and your browser, initialize a git repository, tweak your `.env` and `.env.example`, and more.


# Requirements

- PHP 7.3+
- (optional, but beneficial) [Laravel Valet](https://laravel.com/docs/valet)

# Installation

```bash
composer global require tightenco/lambo:^2.0
```

# Upgrading

```bash
composer global update tightenco/lambo
```

# Usage

Make sure `~/.composer/vendor/bin` is in your terminal's path.

```bash
cd ~/Sites
lambo new myNextProject
```

# What exactly does it do?

- `laravel new $PROJECTNAME`
- Initialize a git repo, add all the files, and, after some changes below, make a commit with the text "Initial commit."
- Replace the `.env` (and `.env.example`) database credentials with the default macOS MySQL credentials: database of `$PROJECTNAME`, user `root`, and empty password
- Replace the `.env` (and `.env.example`) `APP_URL` with `$PROJECTNAME.$YOURVALETTLD`
- Generate an app key
- Open the project in your favorite editor
- Open `$PROJECTNAME.$YOURVALETTLD` in your browser

> Note: If your `$PROJECTNAME` has dashes (`-`) in it, they will be replaced with underscores (`_`) in the database name.

There are also a few optional behaviors based on the parameters you pass (or define in your config file), including creating a database, migrating, installing Jetstream, running Valet Link and/or Secure, and running a custom bash script of your definition after the fact.

# Customizing Lambo

While the default actions Lambo provides are great, most users will want to customize at least a few of the steps. Thankfully, Lambo is built to be customized!

There are three ways to customize your usage of Lambo: command-line arguments, a config file, and an "after" file.

Most users will want to set their preferred configuration options once and then never think about it again. That's best solved by creating a config file.

But if you find yourself needing to change the way you interact with Lambo on a project-by-project basis, you may also want to use the command-line parameters to customize Lambo when you're using it.

## Creating a config file

You can create a config file at `~/.lambo/config` rather than pass the same arguments each time you create a new project.

The following command creates the file, if it doesn't exist, and edits it:

```bash
lambo edit-config
```

The config file contains the configuration parameters you can customize, and will be read on every usage of Lambo.

## Creating an "after" file

You can also create an after file at `~/.lambo/after` to run additional commands after you create a new project.

The following command creates the file, if it doesn't exist, and edits it:

```bash
lambo edit-after
```

The after file is interpreted as a bash script, so you can include any commands here, such as installing additional composer dependencies...

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

## Using command-line parameters

Any command-line parameters passed in will override Lambo's defaults and your config settings. See a [full list of the parameters you can pass in](#parameters).

# Lambo Commands

- `help` or `help-screen` show the help screen

<a id="config-files"></a>
- `edit-config` edits your config file (and creates one if it doesn't exist)

  ```bash
  lambo edit-config
  ```

- `edit-after` edits your "after" file (and creates one if it doesn't exist)

  ```bash
  lambo edit-after
  ```


<a id="parameters"></a>
# Configurable parameters

You can optionally pass one or more of these parameters every time you use Lambo. If you find yourself wanting to configure any of these settings every time you run Lambo, that's a perfect use for the [config files](#config-files).

- `-e` or `--editor` to define your editor command. Whatever is passed here will be run as `$EDITOR .` after creating the project.

  ```bash
  # runs "subl ." in the project directory after creating the project
  lambo new superApplication --editor=subl
  ```

- `-p` or `--path` to specify where to install the application.

  ```bash
  lambo new superApplication --path=~/Sites
  ```

- `-m` or `--message` to set the first Git commit message.

  ```bash
  lambo new superApplication --message="This lambo runs fast!"
  ```

- `-f` or `--force` to force install even if the directory already exists 

  ```bash
  # Creates a new Laravel application after deleting ~/Sites/superApplication  
  lambo new superApplication --force
  ```
  
- `-d` or `--dev` to choose the `develop` branch instead of `master`, getting the beta install.

  ```bash
  lambo new superApplication --dev
  ```

- `-b` or `--browser` to define which browser you want to open the project in.

  ```bash
  lambo new superApplication --browser="/Applications/Google Chrome Canary.app"
  ```

- `-l` or `--link` to create a Valet link to the project directory.

  ```bash
  lambo new superApplication --link
  ```

- `-s` or `--secure` to secure the Valet site using https.

  ```bash
  lambo new superApplication --secure
  ```

- `--create-db` to create a new MySQL database which has the same name as your project.
  This requires `mysql` command to be available on your system.

  ```bash
  lambo new superApplication --create-db
  ```

- `--migrate-db` to migrate your database.

  ```bash
  lambo new superApplication --migrate-db
  ```

- `--dbuser` to specify the database username.

  ```bash
  lambo new superApplication --dbuser=USER
  ```

- `--dbpassword` specify the database password.

  ```bash
  lambo new superApplication --dbpassword=SECRET
  ```

- `--dbhost` specify the database host.

  ```bash
  lambo new superApplication --dbhost=127.0.0.1
  ```

- `--breeze=STACK` to use the Laravel Breeze starter kit. `STACK` may be either `blade`, `vue` or `react`.

  ```bash
  lambo new superApplication --breeze=blade
  lambo new superApplication --breeze=vue
  lambo new superApplication --breeze=react
  ```

- `--jetstream=STACK[,teams]` to use the Laravel Jetstream starter kit. `STACK` may be either `inertia` or `livewire`.

  ```bash
  lambo new superApplication --jetstream=inertia
  lambo new superApplication --jetstream=inertia,teams
  lambo new superApplication --jetstream=livewire
  lambo new superApplication --jetstream=livewire,teams
  ```
  
- `--full` to use `--create-db`, `--migrate-db`, `--link`, and `-secure`.

  ```bash
  lambo new superApplication --full

**GitHub Repository Creation**

**Important:** To create new repositories Lambo requires one of the following tools to be installed:
- the official [GitHub command line tool](https://github.com/cli/cli#installation)
- the [hub command line tool](https://github.com/github/hub#installation)
 
Lambo will give you the option to continue without GitHub repository creation if neither tool is installed.

- `-g` or `--github` to  Initialize a new private GitHub repository and push your new project to it.

```bash
# Repository created at https://github.com/<your_github_username>/superApplication
lambo new superApplication --github
```

- Use `--gh-public` with `--github` to make the new GitHub repository public.

```bash
lambo new superApplication --github --gh-public
```

- Use `--gh-description` with `--github` to initialize the new GitHub repository with a description.

```bash
lambo new superApplication --github --gh-description='My super application'
```
- Use `--gh-homepage` with `--github` to initialize the new GitHub repository with a homepage url. 

```bash
lambo new superApplication --github --gh-homepage=https://example.com
```
- Use `--gh-org` with `--github` to initialize the new GitHub repository with a specified organization.

```bash
# Repository created at https://github.com/acme/superApplication
lambo new superApplication --github --gh-org=acme
```

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
