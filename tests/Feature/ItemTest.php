<?php

namespace Tests\Feature;

use App\Item;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Testing\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ItemTest extends TestCase
{
    Use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        // Workaround becase laravel-mongodb not support "RefreshDatabase"
        Item::truncate();

        Storage::fake('public');
    }

    public function testGetItems()
    {
        $createItemResponse = $this->createItem();

        $response = $this->json('GET', "/api/items");

        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'id' => $createItemResponse->json('_id'),
                        'description' => $createItemResponse->json('description'),
                        'image' => "http://localhost/storage/public/" . $createItemResponse->json('image'),
                        'position' => $createItemResponse->json('position'),
                    ],
                ],
            ]);
    }

    public function testCreateItemFailsWhenDescriptionOrFileIsMissing()
    {
        Storage::fake('public');

        $description = Str::random(100);
        $image = UploadedFile::fake()->image('image01.png');

        $response = $this->json('POST', '/api/items', []);

        $response
            ->assertStatus(422)
            ->assertJson([
                "message" => "The given data was invalid.",
                "errors" => [
                    "description" => ["The description field is required."],
                    "image" => ["The image field is required."]
                ],
            ]);

        $this->assertDatabaseMissing('items', [
            'description' => $description,
        ]);

        Storage::disk('public')->assertMissing('images/' . $image->hashName());
    }

    public function testCreateItemFailsWhenDescriptionIsGreaterThan300()
    {
        Storage::fake('public');

        $description = Str::random(400);
        $image = UploadedFile::fake()->image('image01.png');

        $response = $this->json('POST', '/api/items', [
            'description' => $description,
            'image' => $image,
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                "message" => "The given data was invalid.",
                "errors" => [
                    "description" => ["The description may not be greater than 300 characters."],
                ],
            ]);

        $this->assertDatabaseMissing('items', [
            'description' => $description,
        ]);

        Storage::disk('public')->assertMissing('images/' . $image->hashName());
    }

    public function testCreateItemFailsWhenImageFileHasAnUnsupportedType()
    {
        Storage::fake('public');

        $description = Str::random(300);
        $image = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->json('POST', '/api/items', [
            'description' => $description,
            'image' => $image,
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                "message" => "The given data was invalid.",
                "errors" => [
                    "image" => ["The image must be a file of type: jpeg, png, gif."],
                ],
            ]);

        $this->assertDatabaseMissing('items', [
            'description' => $description,
        ]);

        Storage::disk('public')->assertMissing('images/' . $image->hashName());
    }

    public function testCreateItem()
    {
        Storage::fake('public');

        $description = Str::random(100);
        $image = UploadedFile::fake()->image('image01.png');

        $response = $this->json('POST', '/api/items', [
            'description' => $description,
            'image' => $image,
        ]);

        $id = $response->json('_id');

        $response
            ->assertStatus(201)
            ->assertJson([
                'description' => $description,
                'image' => $image->hashName(),
            ]);

        $this->assertDatabaseHas('items', [
            '_id' => $id,
        ]);

        Storage::disk('public')->assertExists('images/' . $image->hashName());
    }

    public function testEditOnlyDescriptionOfAnItem()
    {
        Storage::fake('public');

        $createItemResponse = $this->createItem();

        $id = $createItemResponse->json('_id');
        $oldDescription = $createItemResponse->json('description');
        $oldImage = $createItemResponse->json('image');

        $newDescription = Str::random(100);

        $response = $this->json('PATCH', "/api/items/{$id}", [
            'description' => $newDescription,
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'description' => $newDescription,
                'image' => $oldImage,
            ]);

        $this->assertDatabaseHas('items', [
            '_id' => $id,
            'description' => $newDescription,
        ]);

        $this->assertDatabaseMissing('items', [
            '_id' => $id,
            'description' => $oldDescription,
        ]);

        Storage::disk('public')->assertExists('images/' . $oldImage);
    }

    public function testEditItem()
    {
        Storage::fake('public');

        $createItemResponse = $this->createItem();

        $id = $createItemResponse->json('_id');
        $oldDescription = $createItemResponse->json('description');
        $oldImage = $createItemResponse->json('image');

        $newDescription = Str::random(100);
        $newImage = UploadedFile::fake()->image('image02.png');

        $response = $this->json('PATCH', "/api/items/{$id}", [
            'description' => $newDescription,
            'image' => $newImage,
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'description' => $newDescription,
                'image' => $newImage->hashName(),
            ]);

        $this->assertDatabaseHas('items', [
            '_id' => $id,
            'description' => $newDescription,
        ]);

        $this->assertDatabaseMissing('items', [
            '_id' => $id,
            'description' => $oldDescription,
        ]);

        Storage::disk('public')->assertExists('images/' . $newImage->hashName());
        Storage::disk('public')->assertMissing('images/' . $oldImage);
    }

    public function testDeleteItem()
    {
        Storage::fake('public');

        $createItemResponse = $this->createItem();

        $id = $createItemResponse->json('_id');
        $image = $createItemResponse->json('image');

        $response = $this->json('DELETE', "/api/items/{$id}");

        $response
            ->assertStatus(204)
            ->assertNoContent();

        $this->assertDatabaseMissing('items', [
            '_id' => $id,
        ]);

        Storage::disk('public')->assertMissing('images/' . $image);
    }

    public function testUpdatePositionOfItemFailsWhenDontSendPosition()
    {
        $createItem1Response = $this->createItem("item1", UploadedFile::fake()->image('item01.png'));
        $item1Id = $createItem1Response->json('_id');
        $item1OrderPosition = $createItem1Response->json('position');

        $this->createItem("item2", UploadedFile::fake()->image('item02.png'));

        $response = $this->json('PATCH', "/api/items/{$item1Id}/position", [
            'position' => null,
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'position' => ['The position field is required.']
                ],
            ]);

        $this->assertDatabaseHas('items', [
            '_id' => $item1Id,
            'position' => $item1OrderPosition,
        ]);
    }

    public function testUpdatePositionItem()
    {
        $createItem1Response = $this->createItem("item1", UploadedFile::fake()->image('item01.png'));
        $item1Id = $createItem1Response->json('_id');
        $item1OrderPosition = $createItem1Response->json('position');
        $this->assertDatabaseHas('items', [
            '_id' => $item1Id,
            'position' => $item1OrderPosition,
        ]);

        $createItem2Response = $this->createItem("item2", UploadedFile::fake()->image('item02.png'));
        $item2Id = $createItem2Response->json('_id');
        $item2OrderPosition = $createItem2Response->json('position');
        $this->assertDatabaseHas('items', [
            '_id' => $item2Id,
            'position' => $item2OrderPosition,
        ]);

        $response = $this->json('PATCH', "/api/items/{$item1Id}/position", [
            'position' => $item2OrderPosition,
        ]);

        $response
            ->assertStatus(204)
            ->assertNoContent();

        $this->assertDatabaseHas('items', [
            '_id' => $item1Id,
            'position' => $item2OrderPosition,
        ]);

        $this->assertDatabaseHas('items', [
            '_id' => $item2Id,
            'position' => $item1OrderPosition,
        ]);
    }

    private function createItem(string $description = null, File $imageFile = null)
    {
        $description = $description ?? Str::random(100);
        $image = $image ?? UploadedFile::fake()->image('image01.png');

        return $this->json('POST', '/api/items', [
            'description' => $description,
            'image' => $image,
        ]);
    }
}
