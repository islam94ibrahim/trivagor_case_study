<?php

namespace Tests\Unit;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class LocationTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_location_has_items()
    {
        $location = factory(\App\Location::class)->create();

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $location->items);
    }
}
