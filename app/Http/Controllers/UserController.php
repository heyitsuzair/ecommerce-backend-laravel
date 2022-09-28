<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    // function to add user
    function addUser(Request $req)
    {
        // check if user already exists against email, if true than prevent adding it again
        $check_if_exists = User::where('user_email', $req->email)->exists();
        if ($check_if_exists) {
            return ['error' => true, 'message' => 'User Already Exists!'];
        } else {

            $user = new User;
            $user->user_email = $req->input('email');
            $user->user_name = $req->input('name');
            $user->user_password = Hash::make($req->input('password'));
            $user->save();
            if ($user) {
                return ['error' => false, 'message' => 'User Added!'];
            } else {
                return ['error' => true, 'message' => 'Internal Server Error!'];
            }
        }
    }
    // function to delete user
    function delUser($id)
    {
        $delete  = User::where('id', $id)->delete();
        if ($delete == 1) {
            return ['error' => false, 'message' => 'User Deleted!'];
        } else {
            return ['error' => true, 'message' => 'Something Went Wrong!'];
        }
    }
    // function to login user
    function loginUser(Request $req)
    {
        // check if user already exists against email, if true than prevent adding it again
        $check_if_exists = User::where('user_email', $req->email)->first();
        if (!$check_if_exists || !Hash::check($req->password, $check_if_exists->user_password)) {
            return response()->json(['error' => true, 'message' => 'Invalid Credentials!'], 401);
        } else {
            return response()->json(['error' => false, 'user_id' => $check_if_exists->id], 200);
        }
    }

    // function to get all users in db
    function getUsers()
    {
        return User::all('id', 'user_email', 'user_name');
    }

    // function to update user
    function updateUser($user_id, Request $req)
    {
        $user = User::find($user_id);
        $user->user_name = $req->input('name');
        $user->user_email = $req->input('email');
        $user->user_password = Hash::make($req->input('password'));
        $user->save();
        if ($user) {
            return response()->json(['error' => false, 'message' => 'User Updated!'], 200);
        } else {
            return response()->json(['error' => true, 'message' => 'Internal Server Error!'], 500);
        }
    }
}