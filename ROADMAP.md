# Lambo

## Basic workflow to run with project in a folder

While not in global composer bin. 
Project in `~/code/lambo/**project**`

Being in shell say in `~/code` ...

Running:
```
~/code:$  php lambo/lambo new application
```

Would make a new laravel application in `~/code/application`

### Application workflow

New Command: future "lambo new application"

- Merges inline config, with passed arguments (most cases, will be none, as we can configure and preset)

- Runs all (Pre) verifications App\Verifications

- Initial screen: We are presented with the Logo, and decide to run or to customize.
    Maybe here it should be showing current in-memory configs?
    Show the available options
    Perform an option, or go back.

- Run the application, on superpowers! :)


### To figure out

- Verification destination folder does not exist! (how did this one went through until now??)

- Once the application is in global composer (in Phar or not), there could be a place where user change presets
like other apps do (Valet,Yarn) `~/.config/lambo/config`

- Config file in php (return array), or json or .env or .ini?? :)

- Command to export config files

- Having or not an After file.. For example, I have an alias in my shell `barry` that installs barryvdh -> laravel-ide-helper and laravel-debugbar :)
    And as such, there are some stuff, like user recipes that could be handy
    Well, this is a feature that we can think after a first release, right??
        

- Maybe there should be Verifications (PreRunning), and Verifications (On running)
    This goes for example, when i decide yarn/npm... there was no verification for it
    Also cant be pre verification, because we don't know which will choose.
    Hmmm. Maybe we could do the verification as ending process of OptionSettting
    
    
