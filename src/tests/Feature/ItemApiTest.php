<?php

namespace Tests\Feature;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class ItemApiTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_throws_an_error_if_not_logged_in()
    {
        $response = $this->call('get', '/items');
        $this->assertEquals(401, $response->status());
    }

    /** @test */
    public function it_gets_all_items()
    {
        $user = factory(\App\User::class)->create();

        $item = factory(\App\Item::class)->create([
            'user_id' => $user->id
        ]);

        $this->actingAs($user)
            ->json('get', '/items')
            ->seeJson(['id' => $item->id])
            ->assertResponseStatus(200);
    }

    /** @test */
    public function it_gets_an_item()
    {
        $user = factory(\App\User::class)->create();

        $item = factory(\App\Item::class)->create([
            'user_id' => $user->id
        ]);

        $this->actingAs($user)
            ->json('get', '/items/' . $item->id)
            ->seeJson(['id' => $item->id])
            ->assertResponseStatus(200);
    }

    /** @test */
    public function it_filters_an_item_by_location_city()
    {
        $user = factory(\App\User::class)->create();

        $locationPrague = factory(\App\Location::class)->create([
            'city' => 'Prague'
        ]);

        $locationBerlin = factory(\App\Location::class)->create([
            'city' => 'Berlin'
        ]);

        $itemInPrague = factory(\App\Item::class)->create([
            'location_id' => $locationPrague->id,
            'user_id' => $user->id
        ]);

        $itemInBerlin = factory(\App\Item::class)->create([
            'location_id' => $locationBerlin->id,
            'user_id' => $user->id
        ]);

        $this->actingAs($user)
            ->json('get', '/items?filter[city]=prague')
            ->seeJson(['id' => $itemInPrague->id])
            ->dontSeeJson(['id' => $itemInBerlin->id])
            ->assertResponseStatus(200);
    }

    /** @test */
    public function it_throws_an_error_when_item_is_not_found()
    {
        $user = factory(\App\User::class)->create();

        $this->actingAs($user)
            ->json('get', '/items/1')
            ->seeJson(['title' => 'Item not found.'])
            ->assertResponseStatus(404);
    }

    /** @test */
    public function it_creates_an_item()
    {
        $user = factory(\App\User::class)->create();

        $location = factory(\App\Location::class)->create();

        $this->actingAs($user)
            ->json('post', '/items', [
                'name' => 'Tests Hotel',
                'rating' => 5,
                'category' => 'hotel',
                'image' => 'http://image-url.com',
                'reputation' => 500,
                'price' => 1000,
                'availability' => 10,
                'location_id' => $location->id
            ])
            ->seeJson(['id' => \App\Item::first()->id])
            ->assertResponseStatus(200);
    }

    /** @test */
    public function it_throws_validation_error_if_name_contains_illegal_word()
    {
        $user = factory(\App\User::class)->create();

        $this->actingAs($user)
            ->json('post', '/items', [
                'name' => 'Tests free Hotel',
                'rating' => 5,
                'category' => 'hotel',
                'image' => 'http://image-url.com',
                'reputation' => 500,
                'price' => 1000,
                'availability' => 10
            ])
            ->seeJson(['title' => 'Validation error.'])
            ->assertResponseStatus(422);
    }

    /** @test */
    public function it_updates_an_item()
    {
        $user = factory(\App\User::class)->create();

        $item = factory(\App\Item::class)->create([
            'user_id' => $user->id
        ]);

        $this->actingAs($user)
            ->json('patch', '/items/' . $item->id, [
                'name' => 'Random Test name',
                'rating' => 5,
                'category' => 'hotel',
                'image' => 'http://image-url.com',
                'reputation' => 500,
                'price' => 1000,
                'availability' => 10,
                'location_id' => 1
            ])
            ->seeJson(['name' => 'Random Test name'])
            ->assertResponseStatus(200);
    }

    /** @test */
    public function it_throws_validation_error_while_updating_an_item()
    {
        $user = factory(\App\User::class)->create();

        $item = factory(\App\Item::class)->create([
            'user_id' => $user->id
        ]);

        $this->actingAs($user)
            ->json('patch', '/items/' . $item->id, [
                'name' => 'Random Test name',
                'rating' => 5,
                'category' => 'wrong category',
                'image' => 'http://image-url.com',
                'reputation' => 500,
                'price' => 1000,
                'availability' => 10,
                'location_id' => 1
            ])
            ->seeJson(['title' => 'Validation error.'])
            ->assertResponseStatus(422);
    }

    /** @test */
    public function it_throws_not_found_error_if_updating_non_existing_item()
    {
        $user = factory(\App\User::class)->create();

        $this->actingAs($user)
            ->json('patch', '/items/10', [
                'name' => 'Random Test name',
                'rating' => 5,
                'category' => 'wrong category',
                'image' => 'http://image-url.com',
                'reputation' => 500,
                'price' => 1000,
                'availability' => 10,
                'location_id' => 1
            ])
            ->seeJson(['title' => 'Item not found.'])
            ->assertResponseStatus(404);
    }

    /** @test */
    public function it_throws_permission_error_if_updating_item_not_owned_by_logged_user()
    {
        $user = factory(\App\User::class)->create();

        $item = factory(\App\Item::class)->create();

        $this->actingAs($user)
            ->json('patch', '/items/' . $item->id, [
                'name' => 'Random Test name',
                'rating' => 5,
                'category' => 'wrong category',
                'image' => 'http://image-url.com',
                'reputation' => 500,
                'price' => 1000,
                'availability' => 10,
                'location_id' => 1
            ])
            ->seeJson(['title' => 'Permission error.'])
            ->assertResponseStatus(401);
    }

    /** @test */
    public function it_deletes_an_item()
    {
        $user = factory(\App\User::class)->create();

        $item = factory(\App\Item::class)->create([
            'user_id' => $user->id
        ]);

        $this->actingAs($user)
            ->json('delete', '/items/' . $item->id)
            ->seeJson(['Item deleted successfully.'])
            ->assertResponseStatus(200);
    }

    /** @test */
    public function it_throws_not_found_error_if_deleting_non_existing_item()
    {
        $user = factory(\App\User::class)->create();

        $this->actingAs($user)
            ->json('delete', '/items/10')
            ->seeJson(['title' => 'Item not found.'])
            ->assertResponseStatus(404);
    }

    /** @test */
    public function it_throws_permission_error_if_deleting_non_item_not_owned_by_logged_user()
    {
        $user = factory(\App\User::class)->create();

        $item = factory(\App\Item::class)->create();

        $this->actingAs($user)
            ->json('delete', '/items/' . $item->id)
            ->seeJson(['title' => 'Permission error.'])
            ->assertResponseStatus(401);
    }

    /** @test */
    public function it_can_book_an_item()
    {
        $user = factory(\App\User::class)->create();

        $item = factory(\App\Item::class)->create([
            'availability' => 1
        ]);

        $this->actingAs($user)
            ->json('post', '/items/' . $item->id . '/book')
            ->seeJson(['Item booked successfully.'])
            ->assertResponseStatus(200);
    }

    /** @test */
    public function it_throws_not_found_error_if_booking_non_existing_item()
    {
        $user = factory(\App\User::class)->create();

        $this->actingAs($user)
            ->json('post', '/items/10/book')
            ->seeJson(['title' => 'Item not found.'])
            ->assertResponseStatus(404);
    }

    /** @test */
    public function it_throws_availability_error_if_booking_item_with_availability_zero()
    {
        $user = factory(\App\User::class)->create();

        $item = factory(\App\Item::class)->create([
            'availability' => 0
        ]);

        $this->actingAs($user)
            ->json('post', '/items/' . $item->id . '/book')
            ->seeJson(['title' => 'Item availability error.'])
            ->assertResponseStatus(400);
    }
}
