<?php

namespace App\Http\Controllers;

use \App\Direction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

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
		return response()->json(Direction::all());
	}
	
	public function create(Request $request)
	{
		$validator = $this->validator($request);
		
		if(!$validator->fails()) {
			$new = array(
				'title' => $request->title,
				'image' => null,
				'parent' => null
			);
			
			if($request->has('image')) $new['image'] = $request->image;
			
			if($request->has('parent')) {
				$new['parent'] = $request->parent;
				$dirparent = Direction::find($new['parent']);
				
				if(empty($dirparent)) {
					$messages = array(
						'status' => 'parent not found.'
					);
					return response()->json($messages);
				}
			}
			
			$dir = Direction::create($new);
			$new = array_merge($new, array('status' => 'created'));
			return response()->json($new);
		} else {
			$errors = $validator->errors();
            return response()->json($errors->all());
		}
	}
	
	public function getspecific($id)
	{   
        $val = $this->val_id($id);
		if(!$val['status']) return response()->json($val['body']);
		else return response()->json($val['body']);
	}
	
	public function update($id, Request $request)
	{
		$val = $this->val_id($id);
		if(!$val['status']) return response()->json($val['body']);
		
		$validator = $this->validator($request);
		
		if(!$validator->fails()) {
			
			$dir->title = $request->input('title');
			$dir->image = $request->input('image');
			
			if($request->has('parent')) {
				$dirparent = Direction::find($request->parent);
					
				if(empty($dirparent)) {
					$messages = array(
						'status' => 'parent not found.'
					);
					return response()->json($messages);
				}
				
				$dir->parent = $request->parent;
			}

			$dir->save();
			$status = array_merge($request->all(), array('status' => 'updated'));
			return response()->json($status);
			
		} else {
			$errors = $validator->errors();
            return response()->json($errors->all());
		}
	}
	
	public function delete($id)
	{
		$val = $this->val_id($id);
		if(!$val['status']) return response()->json($val['body']);
		
		$dir = Direction::find($id);
		
		$status = array_merge($dir->get(), array('status' => 'deleted'));
		$dir->delete();
		
		return response()->json($status);
	}
	
	public function subdir($id)
	{
		$val = $this->val_id($id);
		if(!$val['status']) return response()->json($val['body']);
		
		$sub = Direction::where('parent', $id)->get();
		return response()->json($sub);
	}
	
	public function addsubdir($id, Request $request)
	{
		$validator = $this->validator($request);
		
		if(!$validator->fails()) {
			
			$val = $this->val_id($id);
			if(!$val['status']) return response()->json($val);
			
			$sub = array(
				'title' => $request->input('title'),
				'image' => $request->input('image'),
				'parent' => intval($id)
			);
			
			$subdir = Direction::create($sub);
			
			$status = array_merge($sub, array('status' => 'subdir created'));
			return response()->json($status);
		
		} else {
			$errors = $validator->errors();
            return response()->json($errors->all());
		}
	}
	
	public function validator($request) 
	{
		$rules =  array(
            'title' => 'required|max:60',
			'image' => 'nullable|image',
			'parent' => 'integer'
        );
        
        $messages = array(
            'title.required' => 'title is required.',
			'title.max' => 'title - max:60.',
			'image.image' => 'image has no image format.',
			'parent.integer' => 'parent must be integer.'
        );
		
		return \Validator::make(array('title' => $request->input('title'), 'image' => $request->input('image'), 'parent' => $id), $rules, $messages);
	}
	
	public function val_id($id)
	{
		$messages = array(
			'status' => 'not found'
		);
			
		$dir = Direction::find($id);
			
		if(empty($dir)) return array( 'status' => false, 'body' => $messages );
		else return array( 'status' => true, 'body' => $dir );
	}

}
