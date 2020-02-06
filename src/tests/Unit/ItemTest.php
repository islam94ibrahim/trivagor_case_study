<?php

namespace Tests\Unit;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function an_item_has_owner()
    {
        $item = factory(\App\Item::class)->create();

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $item->owner()->get());
    }

    /** @test */
    public function an_item_has_location()
    {
        $item = factory(\App\Item::class)->create();

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $item->location()->get());
    }

    /** @test */
    public function an_item_has_reputation_badge_attribute()
    {
        $item = factory(\App\Item::class)->create();

        $this->assertArrayHasKey('reputationBadge', $item);
    }

    /** @test */
    public function an_item_has_scope_owned()
    {
        $user = factory(\App\User::class)->create();

        $item = factory(\App\Item::class)->create([
            'user_id' => $user->id
        ]);

        $this->assertEquals(\App\Item::owned($user->id)->first()->id, $item->id);
    }

    /** @test */
    public function an_item_has_scope_min_availability()
    {
        factory(\App\Item::class)->create([
            'availability' => 0
        ]);

        $item = factory(\App\Item::class)->create([
            'availability' => 10
        ]);


        $this->assertEquals(\App\Item::minAvailability(5)->first()->id, $item->id);
    }

    /** @test */
    public function an_item_has_scope_max_availability()
    {
        $item = factory(\App\Item::class)->create([
            'availability' => 5
        ]);

        factory(\App\Item::class)->create([
            'availability' => 10
        ]);


        $this->assertEquals(\App\Item::maxAvailability(5)->first()->id, $item->id);
    }
}
