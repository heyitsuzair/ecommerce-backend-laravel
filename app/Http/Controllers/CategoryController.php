<?php

namespace App\Http\Controllers;

use App\Models\Category;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // function to add category in db
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
    // function to delete category from db
    function deleteCategory($id)
    {
        // check whether the id exists or not
        $select = Category::where('id', $id)->exists();
        if ($select) {

            // selecting the row and deleting pic from cloudinary
            $getRow = Category::where('id', $id)->get();
            $token = explode('/', $getRow[0]->pic);
            $file_name = explode('.', $token[sizeof($token) - 1]);
            Cloudinary::destroy('ecommerce-backend-laravel/categories/' . $file_name[0]);
            // selecting the row and deleting pic from cloudinary
            $deleteRow = Category::where('id', $id)->delete();

            if ($deleteRow) {
                return response()->json(['error' => false, 'message' => 'Category Deleted!'], 200);
            } else {
                return response()->json(['error' => true, 'message' => 'Something Went Wrong!'], 500);
            }
        } else {
            return response()->json(['error' => true, 'message' => 'Category Not Found!'], 400);
        }
    }

    // function to select all categories
    function selectCategories()
    {
        return Category::all();
    }
}