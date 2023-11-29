<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (App::environment('production')) {
            DB::listen(function($query) {
                if (strpos($query->sql, 'insert ') !== false ||
                    strpos($query->sql, 'update ') !== false ||
                    strpos($query->sql, 'delete ') !== false
                ) {
                    $match = $lastIndex = 0;

                    while (($index = strpos($query->sql, '?', $lastIndex)) !== false) {
                        if (is_null($query->bindings[$match])) $param = 'NULL';
                        else if (is_numeric($query->bindings[$match])) $param = $query->bindings[$match];
                        else $param = "'" . $query->bindings[$match] . "'";

                        $query->sql = substr_replace($query->sql, $param, $index, strlen('?'));
                        $lastIndex = $lastIndex + strlen('?');
                        $match++;
                    }

                    Storage::disk('local')->append('sqls_' . Carbon::now()->format('Ymd') . '.sql', $query->sql);
                    Log::channel('sqls')->info($query->sql);
                }
            });
        }
    }
}
