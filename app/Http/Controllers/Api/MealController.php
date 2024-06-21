<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Meal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MealController extends Controller
{
    public function index()
    {
        $meals = Meal::with('categories')->get()->map(function ($meal) {
            return $this->formatMeal($meal);
        });

        return response()->json($meals);
    }

    public function show($id)
    {
        $meal = Meal::with('categories')->find($id);

        if (!$meal) {
            return response()->json(['error' => 'Meal not found'], 404);
        }

        return response()->json($this->formatMeal($meal));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'image' => 'sometimes|required|string', // Accept Base64 image
            'ingredients' => 'required|array',
            'steps' => 'required|array',
            'duration' => 'required|integer',
            'complexity' => 'required|string|in:simple,medium,complex',
            'affordability' => 'required|string|in:affordable,pricey,luxurious',
            'isGlutenFree' => 'required|boolean',
            'isLactoseFree' => 'required|boolean',
            'isVegan' => 'required|boolean',
            'isVegetarian' => 'required|boolean',
            'categories' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $categoryIds = Category::whereIn('slug', $request->categories)->pluck('id')->toArray();
        $imagePath = $request->has('image') ? $this->saveBase64Image($request->image) : null;

        $meal = Meal::create(array_merge(
            $request->only([
                'title', 'ingredients', 'steps', 'duration', 'complexity', 'affordability',
                'isGlutenFree', 'isLactoseFree', 'isVegan', 'isVegetarian'
            ]),
            ['image' => $imagePath]
        ));

        $meal->categories()->attach($categoryIds);

        return response()->json($this->formatMeal($meal->load('categories')), 201);
    }

    public function update(Request $request, $id)
    {
        $meal = Meal::find($id);

        if (!$meal) {
            return response()->json(['error' => 'Meal not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'image' => 'nullable|string', // Accept Base64 image
            'ingredients' => 'sometimes|required|array',
            'steps' => 'sometimes|required|array',
            'duration' => 'sometimes|required|integer',
            'complexity' => 'sometimes|required|string|in:simple,medium,complex',
            'affordability' => 'sometimes|required|string|in:affordable,pricey,luxurious',
            'isGlutenFree' => 'sometimes|required|boolean',
            'isLactoseFree' => 'sometimes|required|boolean',
            'isVegan' => 'sometimes|required|boolean',
            'isVegetarian' => 'sometimes|required|boolean',
            'categories' => 'sometimes|required|array'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        if ($request->has('image')) {
            $imagePath = $this->saveBase64Image($request->image);
            $meal->image = $imagePath;
        }

        $meal->update($request->only([
            'title', 'ingredients', 'steps', 'duration', 'complexity', 'affordability',
            'isGlutenFree', 'isLactoseFree', 'isVegan', 'isVegetarian'
        ]));

        if ($request->has('categories')) {
            $categoryIds = Category::whereIn('slug', $request->categories)->pluck('id')->toArray();
            $meal->categories()->sync($categoryIds);
        }

        return response()->json($this->formatMeal($meal->load('categories')));
    }

    public function destroy($id)
    {
        $meal = Meal::find($id);

        if (!$meal) {
            return response()->json(['error' => 'Meal not found'], 404);
        }

        $meal->categories()->detach();
        if ($meal->image) {
            Storage::disk('public')->delete($meal->image);
        }
        $meal->delete();

        return response()->json(['message' => 'Meal deleted successfully']);
    }

    private function saveBase64Image($base64Image)
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
            $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif

            if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
                throw new \Exception('Invalid image type');
            }

            $base64Image = str_replace(' ', '+', $base64Image);
            $imageName = Str::random(10) . '.' . $type;
            Storage::disk('public')->put($imageName, base64_decode($base64Image));

            return $imageName;
        } else {
            throw new \Exception('Invalid base64 string');
        }
    }

    private function formatMeal($meal)
    {
        return [
            'id' => $meal->id,
            'title' => $meal->title,
            'image' => $meal->image ? Storage::disk('public')->url($meal->image) : null, // Return the image URL if exists
            'ingredients' => $meal->ingredients,
            'steps' => $meal->steps,
            'duration' => $meal->duration,
            'complexity' => $meal->complexity,
            'affordability' => $meal->affordability,
            'isGlutenFree' => $meal->isGlutenFree,
            'isLactoseFree' => $meal->isLactoseFree,
            'isVegan' => $meal->isVegan,
            'isVegetarian' => $meal->isVegetarian,
            'categories' => $meal->categories->pluck('slug')->toArray(),
        ];
    }
}
