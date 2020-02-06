<?php

namespace Tests\Unit;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_has_items()
    {
        $user = factory(\App\User::class)->create();

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->items);
    }
}
