<?php

namespace App\Http\Controllers;

use App\Models\Category;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    function addCategory(Request $req)
    {
        $category = new Category;
        $category->name = $req->input('name');
        // Upload an Image File to Cloudinary with One line of Code
        $uploadedFileUrl = Cloudinary::upload($req->file('pic')->getRealPath())->getSecurePath();
        $category->pic = $uploadedFileUrl;
        $category->save();
        if ($category) {
            return response()->json(['error' => false, 'message' => 'Category Added!'], 200);
        } else {
            return response()->json(['error' => true, 'message' => 'Internal Server Error!'], 500);
        }
    }
}