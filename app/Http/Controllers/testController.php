<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class testController extends Controller
{
    public function index()
    {
        $users = User::all();
        if($users->isEmpty()) {
            return response()->json(['message' => 'No users found'], 404);
        }
        return response()->json($users);        
    }
}