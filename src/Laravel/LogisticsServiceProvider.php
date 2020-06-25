<?php

/*
 * This file is part of the uuk020/logistics.
 *
 * (c) daley <poicue@163.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Daley\Logistics\Laravel;

use Daley\Logistics\Logistics;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

class LogisticsServiceProvider extends ServiceProvider
{
    /**
     * If is defer.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Boot the service.
     */
    public function boot()
    {
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes(
                [
                dirname(dirname(__DIR__)).'/config/logistics.php' => config_path('logistics.php'), ],
                'logistics'
            );
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('logistics');
        }
    }

    /**
     * Register the service.
     */
    public function register()
    {
        $this->mergeConfigFrom(dirname(dirname(__DIR__)).'/config/logistics.php', 'logistics');

        $this->app->singleton(Logistics::class, function () {
            return new Logistics(config('logistics'));
        });
        $this->app->alias(Logistics::class, 'logistics');
    }

    /**
     * Get services.
     *
     * @return array
     */
    public function provides()
    {
        return ['logistics'];
    }
}
