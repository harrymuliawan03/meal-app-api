<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slug'     => 'required|string|max:255|unique:categories',
            'name'      => 'required|string|max:255',
            'color'      => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $category = Category::create([
            'slug'     => $request->slug,
            'name'      => $request->name,
            'color'      => $request->color,
        ]);

        return response()->json([
            'data'          => $category,
            'message'    => 'Successfully created category',
        ]);
    }

    public function getAll()
    {
        $categories = Category::all();

        return response()->json($categories);
    }

    public function delete($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }

    public function edit($id, Request $request)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $category->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'data' => $category,
            'message' => 'Category updated successfully',
        ]);
    }
}