<?php

namespace App\Http\Controllers;

use App\Skill;
use FastRoute\Route;
use Illuminate\Http\Request;
use Mockery\Exception;

class SkillController extends Controller
{
    /**
     * Retrieve the user for the given ID.
     *
     * @param  int  $id
     * @return Response
     */
    public function all()
    {
        try {
            $skills = Skill::all();
            return response()->json($skills);
        }catch(Exception $e) {
            return response()->json('Somethig going wrong! Sorry');
        }
    }
    public function showone($id)
    {
        try {
            $skills = Skill::find($id);
            return response()->json($skills);
        }catch(Exception $e) {
            return response()->json($e);
        }
    }
    public function create(Request $request)
    {
        try {
            $data = Skill::create($request->all());
            return response()->json($request->all());
        }catch(Exception $e) {
                return response()->json($e);
            }
    }
    public function update($id, Request $request)
    {
        try {
            $data = Skill::find($id);
            $data->title = $request->input('title');
            $data->image = $request->input('image');
            $data->save();
            return response()->json($request->all());
        }catch(Exception $e) {
            return response()->json($e);
        }
    }
    public function delete($id)
    {
        try {
            $skill = Skill::find($id);
            $skill->delete();
            return response()->json('Removed successful');
        }catch(Exception $e) {
            return response()->json($e);
        }
    }
}