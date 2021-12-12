<?php

namespace volmaticmw5\IP2Location;

use Illuminate\Support\ServiceProvider;

class IP2LocationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->make('volmaticmw5\ip2location\IP2Location');
    }

    public function boot()
    {
       
    }
}
