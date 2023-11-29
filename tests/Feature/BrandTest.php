<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Config;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class BrandTest extends TestCase
{
    public function test_index_is_empty()
    {
        DB::table('brands')->truncate();

        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);
        $baseUrl = Config::get('app.url') . '/api/products/brand';

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
        $brand =  Brand::factory()->create();
        $baseUrl = Config::get('app.url') . '/api/products/brand/';

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
        $brand =  Brand::factory()->create();
        $baseUrl = Config::get('app.url') . '/api/products/brand/?brand=9999999';

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->json('GET', $baseUrl, []);

        $response
                ->assertStatus(200)
                ->assertJsonCount(1, []);  ;   
    }

    public function test_index_filter()
    {
        DB::table('brands')->truncate();
        
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);
        $brand =  Brand::factory()->create();
        $baseUrl = Config::get('app.url') . '/api/products/brand/?type=' . $brand->type;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->json('GET', $baseUrl, []);
        $response
            ->assertStatus(200)
            ->assertJsonCount(1, []);
    }

    public function test_store()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);
        
        $brand = Brand::factory()->make();
        $brandData = [
            'brand' => $brand->brand,
        ];
        $baseUrl = Config::get('app.url') . '/api/products/brand/';
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->json('POST', $baseUrl, $brandData);
    
        $response
            ->assertStatus(201);
    }

    public function test_store_validation()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);
        $brand = [
            'brand' => 0,
        ];
        $baseUrl = Config::get('app.url') . '/api/products/brand/';

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->json('POST', $baseUrl, []);


            $response->assertStatus(422);
    }

    public function test_show()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);
        $brand = Brand::factory()->create();
        $baseUrl = Config::get('app.url') . '/api/products/brand/' . $brand->id;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->json('GET', $baseUrl, []);

         $response->assertStatus(200);
    }

    public function test_show_not_found()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);
        $baseUrl = Config::get('app.url') . '/api/products/brand/999999999';
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->json('GET', $baseUrl, []);

        $response->assertStatus(404);
    }

    public function test_update()
    {
        DB::table('brands')->truncate();

        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);

        $brand = Brand::factory()->make();
        $brands = [
            'brand' => 'Rexona',
        ];

        $brand = Brand::factory()->create();
        $baseUrl = Config::get('app.url') . '/api/products/brand/' . $brand->id ;
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->json('PUT', $baseUrl, $brands);

        $response->assertStatus(200);
    }

    public function test_update_not_found()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);

        $brand = [
            'brand' => 'Sadia',
        ];

        $baseUrl = Config::get('app.url') . '/api/products/brand/0';
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->json('PUT', $baseUrl, $brand);

        $response->assertStatus(404);

    }

    public function test_update_validation()
    {
        
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);

        $brands = [
            'brand' => 0,
        ];

        $brand = Brand::factory()->create();
        $baseUrl = Config::get('app.url') . '/api/products/brand/' . $brand->id;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->json('PUT', $baseUrl, $brands);

        $response->assertStatus(422);

    }

    public function test_destroy()
    {

        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);
        $brand = Brand::factory()->create();
        $baseUrl = Config::get('app.url') . '/api/products/brand/' . $brand->id;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->json('DELETE', $baseUrl, []);

        $response->assertStatus(204);

    }

    public function test_destroy_not_found()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);
        $baseUrl = Config::get('app.url') . 'api/products/brand/0';
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->json('DELETE', $baseUrl, []);
        
        $response->assertStatus(404);

    }

    public function test_restore()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);
        $brand = Brand::factory()->create();
        Brand::find($brand->id)->delete();
        $baseUrl = Config::get('app.url') . '/api/products/brand/' . $brand->id . '/restore';

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
        $baseUrl = Config::get('app.url') . '/api/products/brand/0/restore';
        $response = $this->putJson(
            $baseUrl,
            [],
            ['Authorization' => 'Bearer ' . $token]
        );

        $response->assertStatus(404);
    }
}
