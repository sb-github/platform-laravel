<?php

namespace App\Http\Controllers;

use \App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Auth;

class AdminController extends Controller
{
    public function activate($id)
    {
        $user = User::find($id);

        if (empty($user)) return response()->json(['error' => 'User not found'], 403);

        $user->status = 2;
        $user->save();

        return response()->json(['status' => 'success']);
    }
}