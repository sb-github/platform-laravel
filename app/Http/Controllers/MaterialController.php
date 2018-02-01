<?php

namespace App\Http\Controllers;

use \App\Material;
use \App\Skill;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class MaterialController extends Controller
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
            return response()->json(Material::all());
    }
    
    public function create(Request $request)
    {
        $validator = $this->validator($request);
        
        if(!$validator->fails()) {
            $new = array(
                'skill_id' => $request->skill_id,
                'text' => null,
                'title' => $request->title
            );
            
            if($request->has('text')) {
                $new['text'] = $request->text;
            }
                
            $skill = Skill::find($new['skill_id']);          
            if(empty($skill)) {
                $messages = array(
                    'status' => 'Skill not found.'
                );
                return response()->json($messages);
            }
            
            
            $item = Material::create($new);
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
        return response()->json($val['body']);
    }
    
    public function update($id, Request $request)
    {

        $val = $this->val_id($id);
        if(!$val['status']) return response()->json($val['body']);
        
        $validator = $this->validator($request);
        
        if(!$validator->fails()) {
            
            $item = Material::find($id);
            $item->text = $request->input('text');
            $item->title = $request->input('title');
            
            if($request->has('skill_id')) {
                $skill = Skill::find($request->skill_id);
                    
                if(empty($skill)) {
                    $messages = array(
                        'status' => 'Skill not found.'
                    );
                    return response()->json($messages);
                }
                
                $item->skill_id = $request->skill_id;
            }

            $item->save();
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
        
        $item = Material::find($id);
        $item_id = $item->id;
        $item->delete();

        return response()->json(['status' => "Item deleted.", 'item_id' => $item_id]);
        
    }

    public function getBySkill($id)
    {
        $messages = array(
            'status' => 'not found'
        );

        $item = Skill::find($id);
        if(empty($item))
        {
            return response()->json(['status' => false, 'body' => $messages]);
        }
        else
            return response()->json($item->materials);

    }


    public function validator($request) 
    {
        $rules =  array(
            'skill_id' => 'required|integer',
            'text' => 'nullable',
            'title' => 'required|max:40'
        );
        
        return \Validator::make(array('skill_id' => $request->input('skill_id'), 'text' => $request->input('text'), 'title' => $request->input('title')), $rules);
    }    

    public function val_id($id)
    {
        $messages = array(
            'status' => 'not found'
        );
            
        $item = Material::find($id);

        return empty($item) 
                ? array( 'status' => false, 'body' => $messages )
                : array( 'status' => true, 'body' => $item );
    }
}
