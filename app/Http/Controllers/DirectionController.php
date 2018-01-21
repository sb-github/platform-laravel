<?php

namespace App\Http\Controllers;

use \App\Group;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DirectionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
	
	public function get()
	{
		try {		
			return response()->json(Group::all());
		} catch(Exception $e) {
			return response()->json($e);
		}
	}
	
	public function create(Request $request)
	{
		try {
			$dir = Group::create($request->all());
			
			return response()->json($request->all());
		} catch(Exception $e) {
			return response()->json($e);
		}
	}
	
	public function getspecific($id)
	{
		try {
			$dir = Group::find($id);
			
			return response()->json($dir);
		} catch(Exception $e) {
			return response()->json($e);
		}
	}
	
	public function update($id, Request $request)
	{
		try {
			$dir = Group::find($id);
			$dir->title = $request->input('title');
			$dir->image = $request->input('image');
			$dir->save();
			
			return response()->json($request->all());
		} catch(Exception $e) {
			return response()->json($e);
		}
	}
	
	public function delete($id)
	{
		try {
			$dir  = Group::find($id);
			$dir->delete();
	 
			return response()->json('Removed successfully.');
		} catch(Exception $e) {
			return response()->json($e);
		}
	}
    //
}
