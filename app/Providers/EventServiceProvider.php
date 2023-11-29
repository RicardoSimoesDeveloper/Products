<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        if (App::environment('production')) {
            Event::listen([
                'eloquent.created: *',
                'eloquent.updated: *',
                'eloquent.deleted: *'
            ], function($event, $data) {
                list($acao, $model) = explode(': ', $event);

                if ($acao === 'eloquent.created' && $model === 'App\Models\User') {
                    $login = $data[0]->email;
                } else {
                    $user = JWTAuth::parseToken()->authenticate();
                    $login = $user->email;
                }
                
                $primaryKey = app($model)->getKeyName();
                $objectId = $data[0]->$primaryKey;

                if ($acao === 'eloquent.updated' &&
                    count($data[0]->getDirty()) === 2 &&
                    $data[0]->isDirty('atualizado_em') &&
                    $data[0]->isDirty('excluido_em')
                ) {
                    $acao = 'eloquent.restored';
                }

                DB::beginTransaction();

                try {
                    $id = DB::table('logs')->insertGetId([
                        'criado_em' => Carbon::now(),
                        'acao' => $acao,
                        'login' => $login,
                        'tabela' => $model,
                        'chave' => $objectId
                    ]);

                    if ($acao !== 'eloquent.deleted') {
                        foreach ($data[0]->getDirty() as $key => $value) {
                            DB::table('log_details')->insert([
                                'log_id' => $id,
                                'campo' => $key,
                                'valor_antigo' => $data[0]->getOriginal($key),
                                'valor_novo' => $value
                            ]);
                        }
                    } else {
                        DB::table('log_details')->insert([
                            'log_id' => $id,
                            'campo' => 'excluido_em',
                            'valor_antigo' => null,
                            'valor_novo' => $data[0]->getOriginal('excluido_em')
                        ]);
                    }

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::channel('sqls')->critical($e->getMessage());
                }
            });
        }
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
