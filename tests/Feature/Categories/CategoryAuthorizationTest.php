<?php

namespace Tests\Feature\Categories;

use Tests\TestCase;
use App\Models\Category;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryAuthorizationTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_check_guest_can_not_see_categories_page()
    {
        $response = $this->get('/categories');

        $response
        ->assertStatus(302)
        ->assertRedirect(route('login'))
        ;
    }

    public function test_check_guest_can_not_see_categories__create_page()
    {
        $response = $this->get('/categories/create');

        $response
        ->assertStatus(302)
        ->assertRedirect(route('login'))
        ;
    }

    public function test_check_guest_can_not_see_categories__edit_page()
    {
        $category = Category::factory()->create();

        $response = $this->get(route('categories.edit', $category));

        $response
        ->assertStatus(302)
        ->assertRedirect(route('login'))
        ;
    }


    public function test_check_guest_can_not_see_categories__show_page()
    {
        $category = Category::factory()->create();

        $response = $this->get(route('categories.show', $category));

        $response
        ->assertStatus(302)
        ->assertRedirect(route('login'))
        ;
    }

    public function test_check_guest_can_not_store_category()
    {
        $category = Category::factory()->make()->toArray();

        $response = $this->post(route('categories.store', $category));

        $response
        ->assertStatus(302)
        ->assertRedirect(route('login'))
        ;
    }

    public function test_check_guest_can_not_update_category()
    {

        $category = Category::factory()->create();
        $newCategory = Category::factory()->make()->toArray();

        $response = $this->put(route('categories.update', $category), $newCategory );

        $response
        ->assertStatus(302)
        ->assertRedirect(route('login'))
        ;

        $this->assertDatabaseMissing('categories', $newCategory);
    }

    public function test_check_guest_can_not_delete_category()
    {
        $category = Category::factory()->create()
;

        $response = $this->delete(route('categories.destroy', $category));

        $response
        ->assertStatus(302)
        ->assertRedirect(route('login'));
      

    }
}
