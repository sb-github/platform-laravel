<?php

namespace App\Http\Controllers;

use \App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;
use Auth;

class UserController extends Controller
{
	public function authenticate(Request $request)
	{
        $this->validate($request, [
            'login' => 'required|string|max:255',
            'password' => 'required|string|min:6'
        ]);
	
        $user = User::where('login', $request->input('login'))->first();
	
	    if(Hash::check($request->input('password'), $user->password)){
            $apikey = base64_encode(str_random(40));
            User::where('login', $request->input('login'))->update(['token' => "$apikey"]);
            return response()->json(['status' => 'success','token' => $apikey]);
        } else {
	        return response()->json(['status' => 'fail'], 401);
		}
	}

    public function register(Request $request)
    {
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        $this->create($request->all());
        return response()->json(['status' => 'success']);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'login' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'login' => $data['login'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
