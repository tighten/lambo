![Lambo logo](https://raw.githubusercontent.com/tightenco/lambo/master/lambo-banner.png)

Super-powered `laravel new` for Laravel and Valet.

> NOTE: If you're working with the alpha, you'll need PHP 7.2, and this readme is currently out of date.The major change is that you'll want to run `lambo new myApplication` instead of `lambo myApplication`.

## Installation

### For Laravel >= 6
```bash
composer global require tightenco/lambo
```

### For Laravel 5.*
```bash
composer global require tightenco/lambo:"^0.4.7"
```

## Upgrading

```bash
composer global update tightenco/lambo
```

If this doesn't get you the latest version, check the file at `~/.composer/composer.json`. If your version spec for Lambo is `^0.1.#`, change it to be `~0.1`.

## Usage

Make sure `~/.composer/vendor/bin` is in your terminal's path.

```bash
cd ~/Sites
lambo superApplication
```

### What exactly does it do?

- `laravel new $PROJECTNAME`
- `cd $PROJECTNAME`
- Initialize a git repo, add all of the files, and make a commit with the text "Initial commit."
- Open the project in your editor
- Replace the `.env` database credentials with the default Mac MySQL credentials: database of `$PROJECTNAME`, user `root`, and empty password
- Replace the `.env` `APP_URL` with `$PROJECTNAME.$YOURVALETTLD`
- Open `$PROJECTNAME.$YOURVALETTLD` in your browser

Note that, in case your `$PROJECTNAME` has dashes (`-`) in it, they will be replaced with underscores (`_`) in the database name.

There are also a few optional behaviors based on the parameters you pass (or define in your config file).

### Optional Arguments

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

```bash
lambo make-config
```

If you wish to edit your config file later on you can always use the edit command:

```bash
lambo edit-config
```

### After File

You can create an after file at `~/.lambo/after` to run additional commands after you create a new project.

```bash
lambo make-after
```

The after file is a bash script so you can include any commands here such as installing additional composer dependencies

```bash
# Install additional composer dependencies as you would from the command line.
echo "Installing Composer Dependencies"
composer require tightenco/mailthief tightenco/quicksand
```

or copying additional files to your new project.

```bash
# To copy standard files to new lambo project place them in ~/.lambo/includes directory.
echo "Copying Include Files"
cp -R ~/.lambo/includes/ $PROJECTPATH
```

You also have access to variables from your config file such as `$PROJECTPATH` and `$CODEEDITOR`.

## Requirements

- Mac or Linux.
- [Git](https://git-scm.com).
- Requires the [Laravel installer](https://laravel.com/docs/installation#installing-laravel) and [Laravel Valet](https://laravel.com/docs/valet) to be globally installed.

> A Linux fork of Valet can be found [here](https://github.com/cpriego/valet-linux)

## Acknowledgements

Inspired by Taylor Otwell and Adam Wathan's work on Valet.

Name from TJ Miller, inspired by Taylor's love for the lambo.

![](https://i.imgur.com/CrS803Y.gif)

## Process for release

If you're working with us and are assigned to push a release, here's the easiest process:

1. Visit the [Lambo Releases page](https://github.com/tightenco/lambo/releases); figure out what your next tag will be (increase the third number if it's a patch or fix; increase the second number if it's adding features)
2. On your local machine, pull down the latest version of `master` (`git checkout master && git pull`)
3. Build for the version you're targeting (`./lambo app:build`)
4. Run the build once to make sure it works (`./builds/lambo`)
5. Commit your build and push it up
6. [Draft a new release](https://github.com/tightenco/lambo/releases/new) with both the tag version and release title of your tag (e.g. `v1.5.1`)
7. Set the body to be a bullet-point list with simple descriptions for each of the PRs merged, as well as the PR link in parentheses at the end. For example:

    `- Add a superpower (#92)`
8. Hit `Publish release`
9. Profit
