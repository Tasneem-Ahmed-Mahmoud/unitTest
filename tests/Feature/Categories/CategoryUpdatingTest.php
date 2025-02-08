<?php

namespace Tests\Feature\Categories;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryUpdatingTest extends TestCase
{
    use RefreshDatabase;
   
    protected User $user;
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

    }
    

    function test_check_if_update_category_page_can_be_retrieved_successfully_and_contain_right_content(): void{
        $category = Category::factory()->create();
        $response = $this->actingAs($this->user)->get(route('categories.edit', $category));

        $response
            ->assertStatus(200)
            ->assertViewIs('categories.edit')
            ->assertViewHas('category', $category)
            ->assertSee($category->name)
            ->assertSee($category->description);
    }





    function test_update_category(): void
    {
        $category = Category::factory()->create()->toArray();
        $newCategory = Category::factory()->make()->toArray();

        $response = $this->actingAs($this->user)->put(route('categories.update', $category['id']), $newCategory );

        $response
            ->assertStatus(302)
            ->assertRedirect(route('categories.index'))
            ->assertSessionHas('success', 'Category updated successfully');

        $this->assertDatabaseHas('categories', $newCategory);
        $this->assertDatabaseMissing('categories', $category);

    }

    function test_check_if_category_name_is_required()
    {
        $category= Category::factory()->create()->toArray();
        $updatedCategory = [
            "description" => "test"
        ];

        $response = $this->actingAs($this->user)->put(route('categories.update', $category['id']), $updatedCategory);

        $response   
            ->assertStatus(302)
            ->assertSessionHasErrors('name', 'The name field is required.');

        $this->assertDatabaseMissing('categories', $updatedCategory);

    }

    function test_check_if_category_name_min_length_must_be_3()
    {
        $category= Category::factory()->create()->toArray();
        $updatedCategory = [
            "description" => "test",
            "name"=>"ab"
        ];

        $response = $this->actingAs($this->user)->put(route('categories.update', $category['id']), $updatedCategory);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('name', 'The name field must be at least 3 characters.');

        $this->assertDatabaseMissing('categories', $updatedCategory);
    }

    function test_check_if_category_name_max_length_must_be_255()
    {
        $category= Category::factory()->create()->toArray();
        $updatedCategory = [
            "description" => "test",
            "name"=>str_repeat('a', 256)
        ];

        $response = $this->actingAs($this->user)->put(route('categories.update', $category['id']), $updatedCategory);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('name', 'The name field must be at most 255 characters.'); 

        $this->assertDatabaseMissing('categories', $updatedCategory);


    }

    function test_check_if_category_description_is_optional()
    {
        $category= Category::factory()->create()->toArray();
        $updatedCategory = [
            "name" => "test"
        ];
        $response = $this->actingAs($this->user)->put(route('categories.update', $category['id']), $updatedCategory);

        $response
            ->assertStatus(302)
            ->assertRedirect(route('categories.index'))
            ->assertSessionHas('success', 'Category updated successfully');

        $this->assertDatabaseHas('categories', $updatedCategory);
        $this->assertDatabaseMissing('categories', $category);

    }


    function test_check_if_category_description_max_length_must_be_1000()
    {
        $category= Category::factory()->create()->toArray();
        $updatedCategory = [
            "description" => str_repeat('a', 1004),
            "name"=>"test"
        ];

        $response = $this->actingAs($this->user)->put(route('categories.update', $category['id']), $updatedCategory);

        $response
            ->assertStatus(302)
            ->assertSessionHasErrors('description', 'The description field must be at most 1000 characters.');

        $this->assertDatabaseMissing('categories', $updatedCategory);

    }




















}
