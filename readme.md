![Lambo](https://raw.githubusercontent.com/tightenco/lambo/master/lambo.jpg)

Super-powered `laravel new` for Laravel and Valet.


![](https://raw.githubusercontent.com/tightenco/lambo/master/lambo.gif)


## Installation

```bash
composer global require tightenco/lambo
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

This will `laravel new superApplication`, change into that directory, make an initial Git commit, and open your web browser to that app.

### What exactly does it do?

- `laravel new $PROJECTNAME`
- `cd $PROJECTNAME`
- Initialize a git repo, add all of the files, and make a commit with the text "Initial commit."
- Replace the `.env` database credentials with the default Mac MySQL credentials: database of `$PROJECTNAME`, user `root`, and empty password
- Replace the `.env` `APP_URL` with `$PROJECTNAME.$YOURVALETTLD`
- Open `$PROJECTNAME.$YOURVALETTLD` in your browser

There are also a few optional behaviors based on the parameters you pass (or define in your config file).

### Optional Arguments

- `-h` or `--help` to get the help dialog

  ```bash
  lambo --help
  ```

- `-e` or `--editor` to define your editor command. Whatever is passed here will be run as `$EDITOR .` after creating the project.

  ```bash
  # runs "subl ." in the project directory after creating the project
  lambo superApplication --editor subl
  ```

- `-m` or `--message` to set the first commit message.

  ```bash
  lambo superApplication --message "This lambo runs fast!"
  ```

- `-p` or `--path` to specify where to install the application.

  ```bash
  lambo superApplication --path ~/Sites
  ```

- `-d` or `--dev` to choose the `develop` branch instead of `master`, getting the beta install

  ```bash
  lambo superApplication --dev
  ```

- `-a` or `--auth` to use Artisan to scaffold all of the routes and views you need for authentication

  ```bash
  lambo superApplication --auth
  ```

- `-n` or `--node` to run `yarn` if installed, otherwise runs `npm install` after creating the project

  ```bash
  lambo superApplication --node
  ```

- `-b` or `--browser` to define which browser you want to open the project in.

  ```bash
  lambo superApplication --browser "/Applications/Google Chrome Canary.app"
  ```

- `-l` or `--link` to create a Valet link to the project directory.

  ```bash
  lambo superApplication --link
  ```

### Commands

- `make-config` creates a config file so you don't have to pass the parameters every time you use Lambo

  ```bash
  lambo make-config
  ```

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

### Config

You can create a config file at `~/.lambo/config` rather than pass the same arguments each time you create a new project.

```bash
lambo make-config
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

- Mac or Ubuntu.
- Requires the [Laravel installer](https://laravel.com/docs/installation#installing-laravel) and [Laravel Valet](https://laravel.com/docs/valet) to be globally installed.

> An Ubuntu fork of Valet can be find [here](https://github.com/cpriego/valet-ubuntu)

## Acknowledgements

Inspired by Taylor Otwell and Adam Wathan's work on Valet.

Name from TJ Miller, inspired by Taylor's love for the lambo.

![](https://i.imgur.com/CrS803Y.gif)
