<?php
namespace App\Http\Controllers;
use \App\Direction;
use \App\Skill;
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

    public function get(Request $request)
    {
        if($request->input('relationships') == 'true') return response()->json(Direction::get());
        else return response()->json(Direction::where('parent', null)->get());
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
            return response()->json(['status' => "Item created.", 'item' => $dir]);
        } else {
            $errors = $validator->errors();
            return response()->json($errors->all());
        }
    }

    public function getspecific(Request $request, $id)
    {
        $val = $this->val_id($id);
        if(!$val['status']) return response()->json($val['body']);
        if($request->input('relationships') == 'true') return Direction::with('subdirections')->where('id', $id)->get();
        else return response()->json($val['body']);
    }

    public function update($id, Request $request)
    {
        $val = $this->val_id($id);
        if(!$val['status']) return response()->json($val['body']);

        $validator = $this->validator($request);

        if(!$validator->fails()) {

            $dir = Direction::find($id);
            $dir->id = $id;
            $dir->title = $request->input('title');
            $dir->image = $request->input('image');

            if($request->has('main_skill_id')) {
                $main_skill = Skill::find($request->main_skill_id);

                if(empty($main_skill)) {
                    $messages = array(
                      'status' => 'Skill not found.'
                    );
                    return response()->json($messages);
                }
                $dir->main_skill_id = $request->input('main_skill_id');
            } else $dir->main_skill_id = null;

            if($request->has('parent')) {
                $dirparent = Direction::find($request->parent);

                if(empty($dirparent)) {
                    $messages = array(
                        'status' => 'parent not found.'
                    );
                    return response()->json($messages);
                }

                $dir->parent = $request->parent;
            } else $dir->parent = null;
            $dir->save();
            return response()->json(['status' => "Item updated.", 'item' => $dir]);

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

        $dir->delete();

        return response()->json(['status' => "Item deleted.", 'item' => $dir]);
    }

    public function subdir($id)
    {
        $val = $this->val_id($id);
        if(!$val['status']) return response()->json($val['body']);

        $sub = Direction::with('skills')->where('parent', $id)->get();
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

            return response()->json(['status' => "Subitem created.", 'item' => $subdir]);

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
            'parent' => 'nullable|integer'
        );

        return \Validator::make(array('title' => $request->input('title'), 'image' => $request->input('image'), 'parent' => $request->input('parent')), $rules);
    }

    public function val_id($id)
    {
        $messages = array(
            'status' => 'not found'
        );

        $dir = Direction::find($id);

        return (empty($dir))
            ? array( 'status' => false, 'body' => $messages )
            : array( 'status' => true, 'body' => $dir );
    }
}