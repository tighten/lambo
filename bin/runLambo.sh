#!/usr/bin/env bash

# Quick and dirty manual testing script that speeds things up by copying a
# "template" installation of Laravel rather than running the Laravel Installer.
# Useful when running lambo over and over during testing.
#
# 1. Comment out the following in app/Commands/NewCommand:
#    app(VerifyPathAvailable::class)()
#    app(RunLaravelInstaller::class)()
#
# 2. run the laravel installer to create a 'template' installation in this dir.
#    cd <project_root>/bin
#    laravel new template
#
# 3. run sh bin/runLambo.sh from the project root dir (without new) passing any
#    regular lambo flags and options. E.g.
#    ./bin/runLambo.sh my-project --create-db --link etc...

[ ! -d "./template" ] && echo "Laravel template does not exist, creating it now…" && laravel new template --quiet

NAME=$1
shift

# remove previous run before execution.
# We may need to examine the created project after execution.
rm -rf _TMP_
mkdir _TMP_
cd _TMP_
cp -r ../bin/template ./$NAME
../lambo new $NAME --path . $*
