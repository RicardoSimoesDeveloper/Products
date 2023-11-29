<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Type;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Config;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TypeTest extends TestCase
{
    public function test_index_is_empty()
    {
        DB::table('types')->truncate();

        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);
        $baseUrl = Config::get('app.url') . '/api/products/type';

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->json('GET', $baseUrl, []);

        $response
            ->assertOk()
            ->assertJsonCount(1, []);
    }
    

    public function test_index()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);
        $type =  Type::factory()->create();
        $baseUrl = Config::get('app.url') . '/api/products/type/';

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->json('GET', $baseUrl, []);

        $response
            ->assertOk()
            ->assertJsonCount(1, []);
    }

    public function test_index_filter_not_found()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);
        $type =  Type::factory()->create();
        $baseUrl = Config::get('app.url') . '/api/products/type/?type=9999999';

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->json('GET', $baseUrl, []);

        $response
                ->assertStatus(200)
                ->assertJsonCount(1, []);  ;   
    }

    public function test_index_filter()
    {
        DB::table('types')->truncate();
        
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);
        $type =  Type::factory()->create();
        $baseUrl = Config::get('app.url') . '/api/products/type/?type=' . $type->type;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->json('GET', $baseUrl, []);
        $response
            ->assertStatus(200)
            ->assertJsonCount(1, []);
    }

    public function test_store()
    {
        DB::table('types')->truncate();

        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);
        
        $type = Type::factory()->make();
        $typeData = [
            'type' => $type->type,
        ];
        $baseUrl = Config::get('app.url') . '/api/products/type/';
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->json('POST', $baseUrl, $typeData);
    
        $response
            ->assertStatus(201);
    }

    public function test_store_validation()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);
        $type = [
            'type' => 0,
        ];
        $baseUrl = Config::get('app.url') . '/api/products/type/';

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->json('POST', $baseUrl, []);


            $response->assertStatus(422);
    }

    public function test_show()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);
        $type = Type::factory()->create();
        $baseUrl = Config::get('app.url') . '/api/products/type/' . $type->id;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->json('GET', $baseUrl, []);

         $response->assertStatus(200);
    }

    public function test_show_not_found()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);
        $baseUrl = Config::get('app.url') . '/api/products/type/999999999';
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->json('GET', $baseUrl, []);

        $response->assertStatus(404);
    }

    public function test_update()
    {
        DB::table('types')->truncate();

        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);

        $type = Type::factory()->make();
        $types = [
            'type' => 'mobile',
        ];

        $type = Type::factory()->create();
        $baseUrl = Config::get('app.url') . '/api/products/type/' . $type->id ;
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->json('PUT', $baseUrl, $types);

        $response->assertStatus(200);
    }

    public function test_update_not_found()
    {
        
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);

        $type = [
            'type' => 'clothes',
        ];

        $baseUrl = Config::get('app.url') . '/api/products/type/0';
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->json('PUT', $baseUrl, $type);

        $response->assertStatus(404);

    }

    public function test_update_validation()
    {
        
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);

        $types = [
            'type' => 0,
        ];

        $type = Type::factory()->create();
        $baseUrl = Config::get('app.url') . '/api/products/type/' . $type->id;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->json('PUT', $baseUrl, $types);

        $response->assertStatus(422);

    }

    public function test_destroy()
    {

        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);
        $type = Type::factory()->create();
        $baseUrl = Config::get('app.url') . '/api/products/type/' . $type->id;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->json('DELETE', $baseUrl, []);

        $response->assertStatus(204);

    }

    public function test_destroy_not_found()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);
        $baseUrl = Config::get('app.url') . 'api/products/type/0';
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->json('DELETE', $baseUrl, []);
        
        $response->assertStatus(404);

    }

    public function test_restore()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);
        $type = Type::factory()->create();
        Type::find($type->id)->delete();
        $baseUrl = Config::get('app.url') . '/api/products/type/' . $type->id . '/restore';

        $response = $this->putJson(
            $baseUrl,
            [],
            ['Authorization' => 'Bearer ' . $token]
        );

        $response->assertStatus(204);
    }

    public function test_restore_not_found()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);
        $baseUrl = Config::get('app.url') . '/api/products/type/0/restore';
        $response = $this->putJson(
            $baseUrl,
            [],
            ['Authorization' => 'Bearer ' . $token]
        );

        $response->assertStatus(404);
    }
}
