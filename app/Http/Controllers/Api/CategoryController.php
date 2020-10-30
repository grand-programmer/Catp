<?php

namespace App\Http\Controllers\Api;

use App\CategoryProduct;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Category;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function getCategorys(Request $request) {
        $categories = Category::all();
        return response(CategoryResource::collection($categories), 200);
    }
    public function createCategory(Request $request) {

        $category = new Category;
        $category->name = $request->name;
        $category->save();

        return response()->json([
            "message" => "Category record created"
        ], 201);
    }
    public function updateCategory(Request $request, $id) {
        if (Category::where(['id'=>$id])->exists()) {
            $category = Category::find($id);
            $category->name = is_null($request->name) ? $category->name : $request->name;
            $category->save();

            return response(new CategoryResource($category));
        } else {
            return response()->json([
                "message" => "Category not found"
            ], 404);
        }
    }
    public function deleteCategory ($id) {
        if(Category::where(['id'=>$id])->exists()) {
            $category = Category::find($id);
            if(!CategoryProduct::where(['category_id'=>$id])->exists()) {
                $category->delete();

                return response()->json([
                    "message" => "records deleted"
                ], 202);
            }
            else return response()->json([
                "message" => "Please change products category from the category"
            ], 404);
        } else {
            return response()->json([
                "message" => "Category not found"
            ], 404);
        }
    }
}
