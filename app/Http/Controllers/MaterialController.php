<?php

namespace App\Http\Controllers;

use \App\Material;
use \App\Skill;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        try {       
            return response()->json(Material::all());
        } catch(Exception $e) {
            return response()->json($e);
        }
    }
    
    public function create(Request $request)
    {
        try {
            Material::create($request->all());
            
            return response()->json($request->all());
        } catch(Exception $e) {
            return response()->json($e);
        }
    }
    
    public function getspecific($id)
    {
        try {
            $item = Material::find($id);
            
            return response()->json($item);
        } catch(Exception $e) {
            return response()->json($e);
        }
    }
    
    public function update($id, Request $request)
    {
        try {
            $item = Material::find($id);
            $item->skill_id = $request->input('skill_id');
            $item->text = $request->input('text');
            $item->title = $request->input('title');
            $item->save();
            
            return response()->json($request->all());
        } catch(Exception $e) {
            return response()->json($e);
        }
    }
    
    public function delete($id)
    {
        try {
            $item = Material::find($id);
            $item->delete();
     
            return response()->json('Removed successfully.');
        } catch(Exception $e) {
            return response()->json($e);
        }
    }

    public function getBySkill($id)
    {
        try {       
            $item = Skill::find($id);
            return response()->json($item->materials);
        } catch(Exception $e) {
            return response()->json($e);
        }
    }
    
}
