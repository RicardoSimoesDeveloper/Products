<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Config;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProductTest extends TestCase
{
    public function test_index_is_empty()
    {
        DB::table('products')->truncate();

        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);
        $baseUrl = Config::get('app.url') . '/api/products/product';

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
        $product =  Product::factory()->create();
        $baseUrl = Config::get('app.url') . '/api/products/product/';

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
        $product =  Product::factory()->create();
        $baseUrl = Config::get('app.url') . '/api/products/product/?type=9999999';

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->json('GET', $baseUrl, []);

        $response
                ->assertStatus(200)
                ->assertJsonCount(1, []);  ;   
    }

    public function test_index_filter()
    {
    DB::table('products')->truncate();
        
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);
        $product =  Product::factory()->create();
        $baseUrl = Config::get('app.url') . '/api/products/product/?type=' . $product->type;

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
        
        $product = Product::factory()->make();
        $productData = [
            'type' => $product->type,
            'brand' => $product->brand,
            'description' => fake()->name(),
            'price' => rand(10, 1000),
            'stock' => rand(1, 2000)
        ];
        $baseUrl = Config::get('app.url') . '/api/products/product/';
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->json('POST', $baseUrl, $productData);
    
        $response
            ->assertStatus(201);
    }

    public function test_store_validation()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);
        $product = [
            'type' => 0,
            'brand' => 0,
            'description' => fake()->name(),
            'price' => rand(100000, 1000000),
            'stock' => rand(10000, 2000000)
        ];
        $baseUrl = Config::get('app.url') . '/api/products/product/';

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->json('POST', $baseUrl, []);


            $response->assertStatus(422);
    }

    public function test_show()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);
        $product = Product::factory()->create();
        $baseUrl = Config::get('app.url') . '/api/products/product/' . $product->id;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->json('GET', $baseUrl, []);

         $response->assertStatus(200);
    }

    public function test_show_not_found()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);
        $baseUrl = Config::get('app.url') . '/api/products/product/999999999';
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->json('GET', $baseUrl, []);

        $response->assertStatus(404);
    }

    public function test_update()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);

        $product = Product::factory()->make();
        $products = [
            'description' => fake()->name(),
        ];

        $product = Product::factory()->create();
        $baseUrl = Config::get('app.url') . '/api/products/product/' . $product->id ;
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->json('PUT', $baseUrl, $products);

        $response->assertStatus(200);
    }

    public function test_update_not_found()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);

        $product = [
            'description' => fake()->name(),
        ];

        $baseUrl = Config::get('app.url') . '/api/products/product/0';
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->json('PUT', $baseUrl, $product);

        $response->assertStatus(404);

    }

    public function test_update_validation()
    {
        
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);

        $products = [
            'type' => 0,
        ];

        $product = Product::factory()->create();
        $baseUrl = Config::get('app.url') . '/api/products/product/' . $product->id;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->json('PUT', $baseUrl, $products);

        $response->assertStatus(422);

    }

    public function test_destroy()
    {

        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);
        $product = Product::factory()->create();
        $baseUrl = Config::get('app.url') . '/api/products/product/' . $product->id;
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->json('DELETE', $baseUrl, []);

        $response->assertStatus(204);

    }

    public function test_destroy_not_found()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);
        $baseUrl = Config::get('app.url') . 'api/products/product/0';
        
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->json('DELETE', $baseUrl, []);
        
        $response->assertStatus(404);

    }

    public function test_restore()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $token = JWTAuth::fromUser($user);
        $product = Product::factory()->create();
        Product::find($product->id)->delete();
        $baseUrl = Config::get('app.url') . '/api/products/product/' . $product->id . '/restore';

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
        $baseUrl = Config::get('app.url') . '/api/products/product/0/restore';
        $response = $this->putJson(
            $baseUrl,
            [],
            ['Authorization' => 'Bearer ' . $token]
        );

        $response->assertStatus(404);

    }
}
