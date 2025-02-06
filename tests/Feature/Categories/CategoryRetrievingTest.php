<?php

namespace Tests\Feature\Categories;

use App\Models\Category;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryRetrievingTest extends TestCase
{
    use RefreshDatabase;
    // protected User $user;
    protected function setUp(): void
    {
        parent::setUp();
        // $this->user = User::factory()->create();
        $this->actingAs(User::factory()->create());
    }

    public function test_if_categories_page_can_be_retrieved_successfully(): void
    {

        // $response = $this->actingAs($this->user)->get(route('categories.index'));
        $response = $this->get(route('categories.index'));

        $response
            ->assertStatus(200)
            ->assertViewIs('categories.index')
            ->assertSeeText('Categories');

    }

    function test_check_if_categories_page_contain_categories()
    {
        Category::factory()->count(4)->create();

        $response = $this->get(route('categories.index'));

        $response->assertViewHas('categories', function ($categories) {
            return $categories->count() == 4;

        });



    }

    function test_check_if_pagination_works()
    {
        Category::factory()->count(15)->create();


        $response = $this->get(route('categories.index'));
        $response->assertViewHas('categories', function ($categories) {
            return $categories->count() == 10;

        });


        $response = $this->get(route('categories.index', ['page' => 2]));
        $response->assertViewHas('categories', function ($categories) {
            return $categories->count() == 5;

        });
    }


    function test_check_if_categories_page_show_contain_right_content()
    {
        $category = Category::factory()->create();

        $response = $this->get(route('categories.show', $category));

        $response
        ->assertStatus(200)
        ->assertViewIs('categories.show')
        ->assertViewHas('categories', $category)
        ->assertSeeText($category->name)
        ->assertSeeText($category->description);
    }
}
