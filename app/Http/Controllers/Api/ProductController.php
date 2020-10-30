<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function getProducts(Request $request) {
        global $data;
        $data=$request->only('name','category_id','category_title','price_from','price_to','published','deleted');
        $validator = Validator::make($data, [
            'price_from' => 'numeric',
            'price_to' => 'numeric',
            'published' => 'boolean',
            'deleted' => 'boolean',
            'category_id'=>'exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => $validator->errors()
            ], 404);
        }

        $products = Product::orderBy('name');
        if(isset($data['name'])) $products=$products->where('name','like','%'.$data['name'].'%');
        if(isset($data['category_title']) or isset($data['category_id'])) $products=$products->whereHas('categories', function ($query)  {
                global $data;
                if(isset($data['category_title'])) $query->where('name', 'like', '%'.$data['category_title'].'%');
                if(isset($data['category_id'])) $query->where('id', '=', $data['category_id']);
            });
        if(isset($data['price_from'])) $products=$products->where('price','>',$data['price_from']);
        if(isset($data['price_to'])) $products=$products->where('price','<',$data['price_to']);
        if(isset($data['published'])) $products=$products->where('published','=',$data['published']);
        if(isset($data['deleted'])) $products=$products->where('deleted','=',$data['deleted']);

        $products=$products->get();
        return response(ProductResource::collection($products), 200);
    }
    public function createProduct(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required|numeric',
            'categories'=>'required|array|min:2|max:10|exists:categories,id',
        ]);


        if ($validator->fails()) {
            return response()->json([
                "message" => $validator->errors()
            ], 404);
        }
        $product = new Product;
        $product->name = $request->name;
        $product->price = $request->price;
        $product->save();
        $product->categories()->attach($request->categories);

        return response()->json([
            "message" => "Product record created"
        ], 201);
    }
    public function updateProduct(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'price' => 'numeric',
            'categories'=>'array|min:2|max:10|exists:categories,id',
        ]);


        if ($validator->fails()) {
            return response()->json([
                "message" => $validator->errors()
            ], 404);
        }
        if (Product::where(['id'=>$id,'deleted'=>false])->exists()) {
            $product = Product::find($id);
            $product->name = is_null($request->name) ? $product->name : $request->name;
            $product->price = is_null($request->price) ? $product->price : $request->price;
            $product->save();
            if($product->categories())
                $product->categories()->detach();
            $product->categories()->attach($request->categories);
            return response(new ProductResource($product));
        } else {
            return response()->json([
                "message" => "Product not found"
            ], 404);
        }
    }
    public function deleteProduct ($id) {
        if(Product::where(['id'=>$id,'deleted'=>false])->exists()) {
            $product = Product::find($id);
            $product->deleted=true;
            $product->categories()->detach();
            $product->save();

            return response()->json([
                "message" => "records deleted"
            ], 202);
        } else {
            return response()->json([
                "message" => "Product not found"
            ], 404);
        }
    }



}
