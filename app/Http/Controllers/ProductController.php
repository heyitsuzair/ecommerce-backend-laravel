<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Single_Product_Category;
use App\Models\Single_Product_Color;
use App\Models\Single_Product_Size;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // function to add product
    function addProduct(Request $req)
    {


        $product = new Product;
        $product->name = $req->input('name');
        $product->description = $req->input('description');
        $product->price = $req->input('price');
        $product->save();
        if ($product) {
            // adding all categories into "single_product_category" table using for each
            foreach (json_decode($req->input('categories')) as $key => $value) {
                $single_prod_cat = new Single_Product_Category;
                $single_prod_cat->prod_id = $product->id;
                $single_prod_cat->category = $value;
                $single_prod_cat->save();
            }
            // adding all size into "single_product_sizes" table using for each
            foreach (json_decode($req->input('sizes')) as $key => $value) {
                $single_prod_size = new Single_Product_Size;
                $single_prod_size->prod_id = $product->id;
                $single_prod_size->size = $value;
                $single_prod_size->save();
            }
            // adding all color into "single_product_colors" table using for each
            foreach (json_decode($req->input('colors')) as $key => $value) {
                $single_prod_color = new Single_Product_Color;
                $single_prod_color->prod_id = $product->id;
                $single_prod_color->color = $value;
                // adding all images to cloudinary with the help of key of color available in "colors_"key"_pic" file
                $uploadedFileUrl = $req->file('color_' . $key . '_pic')->storeOnCloudinary('ecommerce-backend-laravel/products/' .  $product->id . '/colors')->getSecurePath();
                $single_prod_color->pic = $uploadedFileUrl;
                $single_prod_color->save();
            }
            return response()->json(['error' => false, 'message' => 'Product Added!'], 200);
        } else {
            return response()->json(['error' => true, 'message' => 'Something Went Wrong!'], 400);
        }
    }

    // function to get all products
    function getProducts()
    {
        return Product::all();
    }
}