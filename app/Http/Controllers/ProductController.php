<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Single_Product_Category;
use App\Models\Single_Product_Color;
use App\Models\Single_Product_Size;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
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
        $product->available_quantity = $req->input('available_quantity');
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
        $products = Product::all();

        // looping on all incoming products and getting product sizes and appending it according to index of all products
        foreach ($products as $key => $value) {
            $sizes = Single_Product_Size::where('prod_id', $value->id)->get();
            $products[$key]->sizes = (object)$sizes;
        }
        // looping on all incoming products and getting product colors and appending it according to index of all products
        foreach ($products as $key => $value) {
            $colors = Single_Product_Color::where('prod_id', $value->id)->get();
            $products[$key]->colors = (object)$colors;
        }
        // looping on all incoming products and getting product categories and appending it according to index of all products
        foreach ($products as $key => $value) {
            $categories = Single_Product_Category::where('prod_id', $value->id)->get();
            $products[$key]->categories = (object)$categories;
        }
        // return all products found, after adding sizes,colors and categories respectively
        return $products;
    }

    // function to delete a product
    function delProduct($id)
    {
        // check whether the product id exists or not
        $select = Product::where('id', $id)->exists();
        if ($select) {
            // deleting product
            $delProduct = Product::where('id', $id)->delete();
            // deleting product categories
            $delProductCategories = Single_Product_Category::where('prod_id', $id)->delete();

            // selecting product colors
            $selectProductColors = Single_Product_Color::where('prod_id', $id)->get();
            // looping on product colors and deleting images from cloudinary one by one as each color contains its picture
            foreach ($selectProductColors as $key => $value) {
                $token = explode('/', $value->pic);
                $file_name = explode('.', $token[sizeof($token) - 1]);
                Cloudinary::destroy('ecommerce-backend-laravel/products/' . $id . '/colors' . '/' . $file_name[0]);
            }
            // deleting product colors
            $delProductColors = Single_Product_Color::where('prod_id', $id)->delete();
            // deleting product sizes
            $delProductSizes = Single_Product_Size::where('prod_id', $id)->delete();

            return response()->json(['error' => false, 'message' => 'Product Deleted!'], 200);
        } else {
            return response()->json(['error' => true, 'message' => 'Product Not Found'], 500);
        }
    }

    // function to update the product in database against id
    function updateProduct($id, Request $req)
    {
        // check if exists
        $exists = Product::where('id', $id)->exists();
        if ($exists) {

            $product = Product::find($id);
            $product->name = $req->input('name');
            $product->description = $req->input('description');
            $product->price = $req->input('price');
            $product->save();
            if ($product) {
                // first delete the categories,sizes and than add it again
                $single_prod_cat = Single_Product_Category::where('prod_id', $id)->delete();
                $single_prod_size = Single_Product_Size::where('prod_id', $id)->delete();

                foreach (json_decode($req->input('colors')) as $key => $value) {
                    // select the row and get picture and then explode it to delete it from cloudinary
                    $getRow = Single_Product_Color::where('prod_id', $id)->where('color', $value)->get();
                    $token = explode('/', $getRow[0]->pic);
                    $file_name = explode('.', $token[sizeof($token) - 1]);
                    Cloudinary::destroy('ecommerce-backend-laravel/products/' . $id . '/colors' . '/' . $file_name[0]);
                }
                $single_prod_color = Single_Product_Color::where('prod_id', $id)->delete();
                // adding all categories into "single_product_category" table using for each
                foreach (json_decode($req->input('categories')) as $key => $value) {
                    $single_prod_cat =  new Single_Product_Category;
                    $single_prod_cat->prod_id = $product->id;
                    $single_prod_cat->category = $value;
                    $single_prod_cat->save();
                }
                // adding all size into "single_product_sizes" table using for each
                foreach (json_decode($req->input('sizes')) as $key => $value) {
                    $single_prod_size = new Single_Product_Size();
                    $single_prod_size->prod_id = $product->id;
                    $single_prod_size->size = $value;
                    $single_prod_size->save();
                }
                if ($req->input('colors')) {
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
                }
                return response()->json(['error' => false, 'message' => 'Product Updated!'], 200);
            } else {
                return response()->json(['error' => true, 'message' => 'Something Went Wrong!'], 400);
            }
        } else {
            return response()->json(['error' => true, 'message' => 'Product Not Found!'], 400);
        }
    }
    // function to get single product
    function getSingleProduct($id)
    {
        // find product by id
        $product = Product::find($id);

        // get product categories
        $productCategories = Single_Product_Category::where('prod_id', $id)->get();

        // get product sizes
        $productSizes = Single_Product_Size::where('prod_id', $id)->get();

        // get product colors
        $productColors = Single_Product_Color::where('prod_id', $id)->get();

        // appending all info
        $product->categories = (object)$productCategories;
        $product->sizes = (object)$productSizes;
        $product->colors = (object)$productColors;

        // return the product
        return $product;
    }
}