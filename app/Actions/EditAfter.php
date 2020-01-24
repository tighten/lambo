<?php

namespace App\Actions;

use App\InteractsWithLamboConfig;

class EditAfter
{
    use InteractsWithLamboConfig;

    public function __invoke()
    {
        $this->createOrEditConfigFile("after", $this->afterFileTemplate());
    }

    private function afterFileTemplate()
    {
        return <<<'TEMPLATE'
#!/usr/bin/env bash

# Install additional composer dependencies as you would from the command line.
# echo "
# Installing Composer Dependencies
# "
# composer require tightenco/mailthief tightenco/quicksand

# To copy standard files to new lambo project place them in ~/.lambo/includes directory.
# echo "
# Copying Include Files
# "
# cp -R ~/.lambo/includes/ $PROJECTPATH

# To add a git commit after given modifications
# echo "
# Committing after modifications to Git
# "
# git add .
# git commit -am "Initialize Composer dependencies and additional files."
TEMPLATE;
    }
}
