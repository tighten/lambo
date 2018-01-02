## Braindump and TODO

### Missing Lambo functionality:
- ~/.lambo/config and related commands:
  - make-config
  - edit-config
  - make-after
  - edit-after
- Use default configuration values from `~/.lambo/config`, most code today requires options
- Bash lambo does not have `new`, can we make it optional?

### How PHP Lambo differs from Bash Lambo

- `new` action is required now e.g., `lambo coolApp $args` vs `lambo new coolApp $args`
- Now browser only opens if valet is installed
- -c,--createdb is new
- text editor selection is different, and now includes GUI editors (document me)

### Open Browser

- macOS: Add chrome alias to "Google Chrome" to allow: `--browser safari|firefox|chrome|opera`
- Research topic for Linux and Windows

### Configuration

- Consider implementing native Laravel configuration methods instead of `~/.lambo/config`

### Other desires and thoughts

- Allow composer install instead of requiring laravel installer
- Add full interactive mode e.g., "Which browser?" "Create a valet link?" and so on
- Offer non-valet solutions e.g., docker, homestead, and so on

### TODO

- Add tests
- Test on Windows and Linux
- Refactor most code logic from `NewCommand.php` into separate classes

### Scratchpad

#### Default browser locations

##### macOS

- `/Applications/Firefox.app/Contents/MacOS/firefox`
- `/Applications/Google\ Chrome.app/Contents/MacOS/Google\ Chrome`
- `/Applications/Safari.app/Contents/MacOS/Safari`
- `/Applications/Opera.app/Contents/MacOS/Opera`