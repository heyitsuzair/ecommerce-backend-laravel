<?php

namespace App\Http\Controllers;

use App\Models\Category;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    function addCategory(Request $req)
    {
        // check if the category with this name already exists, if yes than return a response with error and message
        $check_exits = Category::where('name', $req->name)->exists();
        if ($check_exits) {
            return response()->json(['error' => true, 'message' => 'A Category With This Name Already Exists!'], 400);
        } else {
            $category = new Category;
            $category->name = $req->input('name');
            // Upload an Image File to Cloudinary with One line of Code
            $uploadedFileUrl = $req->file('pic')->storeOnCloudinary('ecommerce-backend-laravel/categories')->getSecurePath();
            $category->pic = $uploadedFileUrl;
            $category->save();
            if ($category) {
                return response()->json(['error' => false, 'message' => 'Category Added!'], 200);
            } else {
                return response()->json(['error' => true, 'message' => 'Internal Server Error!'], 500);
            }
        }
    }
}