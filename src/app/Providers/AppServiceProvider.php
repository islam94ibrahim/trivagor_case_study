<?php

namespace App\Providers;

use App\Item;
use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Abilities
        Gate::define('manipulate-items', function (User $user, Item $item) {
            return $item->user_id === $user->id;
        });

        // Custom validation
        Validator::extend('not_contains', function ($attribute, $value, $parameters) {
            foreach ($parameters as $word) {
                if (stripos($value, $word) !== false) {
                    return false;
                }
            }
            return true;
        }, ':attribute should not contain any of :words');

        Validator::replacer('not_contains', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':words', implode(', ', $parameters), $message);
        });
    }
}
