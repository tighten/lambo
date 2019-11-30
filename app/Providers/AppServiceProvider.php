<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        config()->set('lambo.store.install_path', getcwd());

        // @todo get tld

        //if [[ -f ~/.config/valet/config.json ]]; then
//     TLD=$(php -r "echo json_decode(file_get_contents('$HOME/.config/valet/config.json'))->tld;")
// else
//     TLD=$(php -r "echo json_decode(file_get_contents('$HOME/.valet/config.json'))->domain;")
// fi
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
