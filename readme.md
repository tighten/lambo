# Lambo

For Laravel and Valet.

## Installation

```bash
composer global require mattstauffer/lambo
```

## Usage

Make sure ~/.composer/vendor/bin is in your terminal's path.

```bash
cd ~/Sites
lambo superApplication
```

This will `laravel new superApplication`, change into that directory, make an initial Git commit, and open your web browser to that app.

## Requirements

Mac-only. Requires the [Laravel installer](https://laravel.com/docs/installation#installing-laravel) and [Laravel Valet](https://laravel.com/docs/valet) to be globally installed.
