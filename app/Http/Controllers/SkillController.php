<?php

namespace App\Http\Controllers;

use App\Skill;
use App\Direction;
use FastRoute\Route;
use Illuminate\Http\Request;
use Mockery\Exception;
use Validator;

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
            return response()->json(Skill::all());
    }

    public function showone($id)
    {
        $val = $this->val_id($id);
        return response()->json($val['body']);
    }

    public function create(Request $request)
    {
        $validator = $this->validator($request);

        if(!$validator->fails()) {
            $new = array(
                'title' => $request->title,
                'image' => null,
            );

            if($request->has('image')) $new['image'] = $request->image;

            $skill = Skill::create($new);
            $new = array_merge($new, array('status' => 'created'));
            return response()->json($new);
        }else {
            $errors = $validator->errors();
            return response()->json($errors->all());
        }
    }

    public function create_array(Request $request)
    {
        foreach ($request->title as $value) {
                $new = array(
                    'title' => $value,
                    'image' => null,
                );
                $skill = Skill::create($new);
        }
        return response()->json($new);
    }
    public function update($id, Request $request)
    {
        $val = $this->val_id($id);
        if(!$val['status']) return response()->json($val['body']);

        $validator = $this->validator($request);

        if(!$validator->fails()) {
            $skill = Skill::find($id);
            $skill->title = $request->input('title');
            $skill->image = $request->input('image');
            $skill->save();
            $status = array_merge($request->all(), array('status' => 'updated'));
            return response()->json($status);
        }else {
            $errors = $validator->errors();
            return response()->json($errors->all());
        }
    }
    public function delete($id)
    {
        $val = $this->val_id($id);
        if(!$val['status']) return response()->json($val['body']);
        $skill = Skill::find($id);
        $skill->delete();
        return response()->json('deleted');
    }
    public function dir($id)
    {
        $val = $this->dir_val_id($id);
        if(!$val['status']) return response()->json($val['body']);

        $dir = Direction::find($id);
        return response()->json($val['body']);
    }
    public function addtodir($id, $skillId)
    {
        $val = $this->dir_val_id($id);
        $val2 = $this->val_id($skillId);
        if(!$val['status'] && !$val2['status']) return response()->json($val , $val2);

        $direction= Direction::find($id);
        $direction->skills()->attach($skillId);
        return response()->json($direction);
    }

    public function validator($request)
    {
        $rules =  array(
            'title' => 'required|max:60',
            'image' => 'nullable|image',
        );

        return \Validator::make(array('title' => $request->input('title'), 'image' => $request->input('image')), $rules);
    }
    public function val_id($id)
    {
        $messages = array(
            'status' => 'skill not found'
        );

        $skill = Skill::find($id);

        return (empty($skill))
            ? array( 'status' => false, 'body' => $messages )
            : array( 'status' => true, 'body' => $skill );
    }
    public function dir_val_id($id)
    {
        $messages = array(
            'status' => 'direction not found'
        );

        $dir = Direction::find($id);

        return (empty($dir))
            ? array( 'status' => false, 'body' => $messages )
            : array( 'status' => true, 'body' => $dir );
    }
}