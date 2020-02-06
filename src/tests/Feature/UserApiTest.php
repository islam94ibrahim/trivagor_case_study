<?php

namespace Tests\Feature;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_creates_a_user()
    {
        $this->json('POST', '/users', ['username' => 'Hotelier'])
            ->seeJson(['id' => \App\User::first()->id])
            ->assertResponseStatus(200);
    }

    /** @test */
    public function it_throws_an_error_when_creating_a_user_with_same_username()
    {
        $this->json('POST', '/users', ['username' => 'Hotelier'])
            ->seeJson(['id' => \App\User::first()->id]);

        $this->json('POST', '/users', ['username' => 'Hotelier'])
            ->seeJson(['title' => 'QueryException'])
            ->assertResponseStatus(422);
    }
}
