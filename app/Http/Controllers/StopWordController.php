<?php

namespace App\Http\Controllers;

use App\StopWord;
use FastRoute\Route;
use Illuminate\Http\Request;
use Mockery\Exception;
use Validator;

class StopWordController extends Controller
{
    /**
     * Retrieve the user for the given ID.
     *
     * @param  int  $id
     * @return Response
     */
    public function all()
    {
        return response()->json(StopWord::all());
    }

    public function showone($id)
    {
        $val = $this->val_id($id);
        return response()->json($val['body']);
    }

    public function create(Request $request)
    {
        foreach ($request->title as $value) {
           
           $validator = $this->validator($value);

            if(!$validator->fails()) {
                $new = array(
                    'title' => $value
                );
                $stop_word = StopWord::create($new);
                $new = array_merge($new, array('status' => 'created'));
                
            }else {
                $errors = $validator->errors();
                return response()->json($errors->all());
            }
        }
        return response()->json(['status' => "Items added"]);
    }

    public function update($id, Request $request)
    {
        $val = $this->val_id($id);
        if(!$val['status']) return response()->json($val['body']);

        $validator = $this->validator($request->title);

        if(!$validator->fails()) {
            $stop_word = StopWord::find($id);
            $stop_word->title = $request->input('title');
            $stop_word->save();
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
        
        $item = StopWord::find($id);
        $item_id = $item->id;

        $item->delete();

        return response()->json(['status' => "Item deleted.", 'item_id' => $item_id]);
    }

    public function del_all()
    {
        StopWord::truncate();
        return response()->json(['status' => "Items deleted."]);

    }

    public function validator($request)
    {
        $rules =  array(
            'title' => 'required|max:60',
        );

        return \Validator::make(
            array(
                'title' => $request
            ), 
            $rules
        );
    }
    public function val_id($id)
    {
        $messages = array(
            'status' => 'stop_word not found'
        );

        $stop_word = StopWord::find($id);

        return (empty($stop_word))
            ? array( 'status' => false, 'body' => $messages )
            : array( 'status' => true, 'body' => $stop_word );
    }

}