<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemCollection;
use App\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index()
    {
        return new ItemCollection(Item::orderBy('position')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'description' => 'required|max:300',
            'image' => 'required|mimes:jpeg,png,gif',
        ]);

        $data['image']->store('images', 'public');

        $max = Item::max('position');

        $item = Item::create([
            'description' => $data['description'],
            'image' => $data['image']->hashName(),
            'position' => is_null($max) ? 0 : $max + 1,
        ]);

        return response()->json($item, 201);
    }

    public function update(Item $item, Request $request)
    {
        $data = $request->validate([
            'description' => 'nullable|max:300',
            'image' => 'nullable|sometimes|image|mimes:jpeg,png,gif',
        ]);

        if (Arr::has($data, 'description')) {
            $item->description = $data['description'];
        }

        if (Arr::has($data, 'image')) {
            $data['image']->store('images', 'public');
            $this->deleteImage($item->image);
            $item->image = $data['image']->hashName();
        }

        $item->save();

        return response()->json($item, 200);
    }

    public function delete(Item $item)
    {
        $item->delete();

        $this->deleteImage($item->image);

        DB::table('items')
            ->where('position', '>', $item->position)
            ->decrement('position', 1);

        return response()->json([], 204);
    }

    public function updatePosition(Item $item, Request $request)
    {
        $data = $request->validate([
            'position' => 'required|gte:0'
        ]);

        $currentPosition = $item->position;
        $newPosition = $data['position'];

        $move = $newPosition > $currentPosition ? 'down' : 'up';

        $item->position = 0;
        $item->save();

        if ($move === 'down') {
            DB::table('items')
                ->where('position', '>', $currentPosition)
                ->where('position', '<=', $newPosition)
                ->decrement('position', 1);
        }

        if ($move === 'up') {
            DB::table('items')
                ->where('position', '<', $currentPosition)
                ->where('position', '>=', $newPosition)
                ->increment('position', 1);
        }

        $item->refresh();
        $item->update([
            'position' => $newPosition,
        ]);

        return response()->json([], 204);
    }

    private function deleteImage(string $imageName)
    {
        Storage::disk('public')->delete("/images/{$imageName}");
    }
}
