<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Get the authenticated user's information.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllUsers()
    {
        $users = User::select('id', 'name')->get();
        $users->makeHidden(['profile_photo_url']);
        return response()->json($users);
    }
    public function getUser(Request $request)
    {
        return response()->json($request->user());
    }   

}
