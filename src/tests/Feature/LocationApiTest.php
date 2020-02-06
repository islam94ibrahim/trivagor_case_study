<?php

namespace Tests\Feature;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class LocationApiTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_throws_an_error_if_not_logged_in()
    {
        $response = $this->call('get', '/locations');
        $this->assertEquals(401, $response->status());
    }

    /** @test */
    public function it_gets_all_locations()
    {
        $user = factory(\App\User::class)->create();

        $location = factory(\App\Location::class)->create();

        $this->actingAs($user)
            ->json('get', '/locations')
            ->seeJson(['id' => $location->id])
            ->assertResponseStatus(200);
    }

    /** @test */
    public function it_gets_a_location()
    {
        $user = factory(\App\User::class)->create();

        $location = factory(\App\Location::class)->create();

        $this->actingAs($user)
            ->json('get', '/locations/' . $location->id)
            ->seeJson(['id' => $location->id])
            ->assertResponseStatus(200);
    }

    /** @test */
    public function it_throws_an_error_when_location_is_not_found()
    {
        $user = factory(\App\User::class)->create();

        $this->actingAs($user)
            ->json('get', '/locations/1')
            ->seeJson(['title' => 'Location not found.'])
            ->assertResponseStatus(404);
    }

    /** @test */
    public function it_creates_a_location()
    {
        $user = factory(\App\User::class)->create();

        $this->actingAs($user)
            ->json('post', '/locations', [
                'city' => 'Prague',
                'state' => 'praha 4',
                'country' => 'Czech Republic',
                'zip_code' => '11111',
                'address' => 'street 4'
            ])
            ->seeJson(['id' => \App\Location::first()->id])
            ->assertResponseStatus(200);
    }

    /** @test */
    public function it_throws_validation_error_if_zip_not_five_digits()
    {
        $user = factory(\App\User::class)->create();

        $this->actingAs($user)
            ->json('post', '/locations', [
                'city' => 'Prague',
                'state' => 'praha 4',
                'country' => 'Czech Republic',
                'zip_code' => '1',
                'address' => 'street 4'
            ])
            ->seeJson(['title' => 'Validation error.'])
            ->assertResponseStatus(422);
    }

    /** @test */
    public function it_updates_a_location()
    {
        $user = factory(\App\User::class)->create();

        $location = factory(\App\Location::class)->create();

        $this->actingAs($user)
            ->json('patch', '/locations/' . $location->id, [
                'city' => 'Prague',
                'state' => 'praha 4',
                'country' => 'Czech Republic',
                'zip_code' => '11111',
                'address' => 'street 4'
            ])
            ->seeJson(['zip_code' => '11111'])
            ->assertResponseStatus(200);
    }

    /** @test */
    public function it_throws_validation_error_while_updating_a_location()
    {
        $user = factory(\App\User::class)->create();

        $location = factory(\App\Location::class)->create();

        $this->actingAs($user)
            ->json('patch', '/locations/' . $location->id, [
                'city' => 'Prague',
                'state' => 'praha 4',
                'country' => 'Czech Republic',
                'zip_code' => '1',
                'address' => 'street 4'
            ])
            ->seeJson(['title' => 'Validation error.'])
            ->assertResponseStatus(422);
    }

    /** @test */
    public function it_throws_not_found_error_if_updating_non_existing_location()
    {
        $user = factory(\App\User::class)->create();

        $this->actingAs($user)
            ->json('patch', '/locations/10', [
                'city' => 'Prague',
                'state' => 'praha 4',
                'country' => 'Czech Republic',
                'zip_code' => '11111',
                'address' => 'street 4'
            ])
            ->seeJson(['title' => 'Location not found.'])
            ->assertResponseStatus(404);
    }

    /** @test */
    public function it_deletes_a_location()
    {
        $user = factory(\App\User::class)->create();

        $location = factory(\App\Location::class)->create();

        $this->actingAs($user)
            ->json('delete', '/locations/' . $location->id)
            ->seeJson(['Location deleted successfully'])
            ->assertResponseStatus(200);
    }

    /** @test */
    public function it_throws_not_found_error_if_deleting_non_existing_location()
    {
        $user = factory(\App\User::class)->create();

        $this->actingAs($user)
            ->json('delete', '/locations/10')
            ->seeJson(['title' => 'Location not found.'])
            ->assertResponseStatus(404);
    }
}
