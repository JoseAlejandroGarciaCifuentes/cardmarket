<?php

namespace Tests\Feature;

use Illuminate\Http\Response;

use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

//php artisan test --testsuite Feature Tests --coverage-html tests/coverage         --->for running tests in the command line
//endor/bin/phpunit --coverage-html tests/reports      
class UserTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseMigrations;

    public function testUserIsCreatedSuccessfully()
    {
        $payload = [
        'email' => $this->faker->email,
        'username'  => $this->faker->username,
        'password' => $this->faker->password,
        'role' => 'Individual'
    ];

        $this->json('post', 'api/users/signup', $payload)
         ->assertStatus(200)
         ->assertJsonStructure(
             [
                  
                'id',
                'email',
                'username',
                'password',
                'role', 
                'created_at',
                'updated_at'
                 
             ]
         );
        //$this->assertDatabaseHas('users', $payload);
    }
}
