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
- Replace the `.env` `APP_URL` with `$PROJECTNAME.$YOURVALETLTD`
- Open `$PROJECTNAME.$YOURVALETTLD` in your browser

### Optional Arguments

- `-h` or `--help` to get the help dialog

  ```bash
  lambo --help
  ```

- `-p` or `--path` to specify where to install the application.

  ```bash
  lambo superApplication -p ~/Sites
  ```

- `-m` or `--message` to set the first commit message.

  ```bash
  lambo superApplication -m "This lambo runs fast!"
  ```

- `-e` or `--editor` to define your editor command. Whatever is passed here will be run as `$EDITOR .` after creating the project.

  ```bash
  # runs "subl ." in the project directory after creating the project
  lambo superApplication -e subl 
  ```

- `-d` or `--dev` to choose the `develop` branch instead of `master`, getting the beta install

  ```bash
  lambo superApplication --dev
  ```

## Requirements

- Mac-only.
- Requires the [Laravel installer](https://laravel.com/docs/installation#installing-laravel) and [Laravel Valet](https://laravel.com/docs/valet) to be globally installed.

## Acknowledgements

Inspired by Taylor Otwell and Adam Wathan's work on Valet.

Name from TJ Miller, inspired by Taylor's love for the lambo.

![](https://i.imgur.com/CrS803Y.gif)
