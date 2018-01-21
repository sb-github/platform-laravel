<?php

namespace App\Http\Controllers;

use \App\Direction;
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
			return response()->json(Direction::all());
		} catch(Exception $e) {
			return response()->json($e);
		}
	}
	
	public function create(Request $request)
	{
		try {
			$dir = Direction::create($request->all());
			
			return response()->json($request->all());
		} catch(Exception $e) {
			return response()->json($e);
		}
	}
	
	public function getspecific($id)
	{
		try {
			$dir = Direction::find($id);
			
			return response()->json($dir);
		} catch(Exception $e) {
			return response()->json($e);
		}
	}
	
	public function update($id, Request $request)
	{
		try {
			$dir = Direction::find($id);
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
			$dir  = Direction::find($id);
			$dir->delete();
	 
			return response()->json('Removed successfully.');
		} catch(Exception $e) {
			return response()->json($e);
		}
	}
	
	public function subdir($id)
	{
		try {		
			$dir = Direction::find($id);
			return response()->json($dir->subdirections);
		} catch(Exception $e) {
			return response()->json($e);
		}
	}
	
	public function addsubdir($id, Request $request)
	{
		try {
			$dir = Direction::find($id);
			$subdir = Direction::create($request->all());
			$dir->subdirections()->attach($subdir);
			return response()->json($request->all());
		} catch(Exception $e) {
			return response()->json($e);
		}
	}
    //
}
