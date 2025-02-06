<?php

namespace Tests\Feature\Categories;

use Tests\TestCase;
use App\Models\User;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryCreatingTest extends TestCase
{
    use RefreshDatabase;
    protected User $user;
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

    }
    public function test_check_if_categories_page_retrieved_successfully(): void
    {
        $response = $this->actingAs($this->user)->get(route('categories.create'));

        $response->assertStatus(200)
            ->assertViewIs('categories.create')
            ->assertSeeText('Name')
            ->assertSeeText('Description');

    }

    public function test_check_if_create_category(): void
    {
        //arrange
        $category = Category::factory()->make()->toArray();

        //act
        $response = $this->actingAs($this->user)->post(route('categories.store'), $category);

        //assert
        $response->assertStatus(302)
            ->assertRedirect(route('categories.index'))
            ->assertSessionHas('success', 'Category created successfully');

        $this->assertDatabaseHas('categories', $category);


    }

    public function test_check_category_name_is_required()
    {
        //arrange
        $category = [
            "description" => "test"
        ];

        //act
        $response = $this->actingAs($this->user)->post(route('categories.store'), $category);

        //assert
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('name', 'The name field is required.');


        $this->assertDatabaseMissing('categories', $category);


    }


    public function test_check_category_name_min_length_must_be_3()
    {
        //arrange
        $category = [
            "description" => "test",
            "name"=>"ab"
        ];

        //act
        $response = $this->actingAs($this->user)->post(route('categories.store'), $category);

        //assert
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('name', 'The name field must be at least 3 characters.');


        $this->assertDatabaseMissing('categories', $category);


    }



    public function test_check_category_name_max_length_must_be_255()
    {
        //arrange
        $category = [
            "description" => "test",
            "name"=>str_repeat('a', 256)
        ];

        //act
        $response = $this->actingAs($this->user)->post(route('categories.store'), $category);

        //assert
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('name', 'The name field must be at most 255 characters.');


        $this->assertDatabaseMissing('categories', $category);


    }




    public function test_check_category_description_is_optional()
    {
        //arrange
        $category = [
            "name" => "test"
        ];

        //act
        $response = $this->actingAs($this->user)->post(route('categories.store'), $category);

        //assert
        $response
            ->assertStatus(302)
            ->assertRedirect(route('categories.index'))
            ->assertSessionHas('success', 'Category created successfully');

        $this->assertDatabaseHas('categories', $category);


    }







    public function test_check_category_description_max_length_must_be_1000()
    {
        //arrange
        $category = [
            "description" => str_repeat('a', 1004),
            "name"=>"test"
        ];

        //act
        $response = $this->actingAs($this->user)->post(route('categories.store'), $category);

        //assert
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('description', 'The description field must be at most 1000 characters.');


        $this->assertDatabaseMissing('categories', $category);


    }
}
