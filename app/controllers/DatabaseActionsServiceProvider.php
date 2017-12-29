<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class DatabaseActionsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /*DB::listen(function ($query) {
            //die('here');
        echo '<pre>';
        //print_r($query);
        echo $query;
        die();*/
            // $query->time
       // });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}