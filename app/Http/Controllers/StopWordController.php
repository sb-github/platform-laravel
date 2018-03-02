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

    public function showByCrawler($id)
    {
        $val = $this->val_craw($id);

        return response()->json($val['body']);
    }

    public function create(Request $request)
    {
        foreach ($request->title as $value) {
           $validator = $this->validator($value);

            if(!$validator->fails()) {

                $new = array (
                    'title' => $value,
                    'crawler_id' => $request->input('crawler_id')
                );
                $stop_word = StopWord::create($new);
            }
            else {
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
        $val = $this->val_craw( $request->input('crawler_id') );
        if(!$val['status']) return response()->json($val['body']);

        $validator = $this->validator($request->title);

        if(!$validator->fails()) {
            $stopword = StopWord::find($id);
            $stopword->title = $request->input('title');
            $stopword->crawler_id = $request->input('crawler_id');
            $stopword->save();
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

    public function deleteByCrawler($id)
    {
        $val = $this->val_craw($id);
        if(!$val['status']) return response()->json($val['body']);

        $item = StopWord::where([
            ['crawler_id', $id]
            ])
            ->take(200);
        $item->delete();

        return response()->json(['status' => "Item deleted by crawler.", 'crawler_id' => $id]);
    }

    public function del_all()
    {
        StopWord::truncate();
        return response()->json(['status' => "Items deleted."]);

    }

    public function validator($request)
    {
        $rules =  array(
            'title' => 'required|max:60|unique:stopword|size:200'
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

        $stopword = StopWord::find($id);

        return (empty($stopword))
            ? array( 'status' => false, 'body' => $messages )
            : array( 'status' => true, 'body' => $stopword );
    }

    public function val_craw($id)
    {
        $messages = array(
            'status' => 'crawler_id not found'
        );
        $stopword = StopWord::where([
                ['crawler_id', $id]
            ])
            ->take(200)
            ->get();
        return (empty($stopword))
            ? array( 'status' => false, 'body' => $messages )
            : array( 'status' => true, 'body' => $stopword );
    }

}
