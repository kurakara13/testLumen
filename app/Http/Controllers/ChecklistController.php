<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Checklists;

class ChecklistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->type = 'checklists';
    }

    public function showAll(Request $request)
    {
      if($request->page_limit){
        $checklists = Checklists::paginate(1);
      }else{
        $checklists = Checklists::all();
      }

      $newData = $checklists;

      if(is_array($request->filter)){
        foreach($request->filter as $item => $values){
          if(is_array($values)){
            foreach ($values as $key => $value) {
              if($key == 'like'){
                $newData = $checklists->filter(function ($items) use ($value, $item){
                                            if(is_array($items[$item])){
                                              return stripos(implode($items[$item]),$value) !== false;
                                            }else {
                                              return stripos($items[$item],$value) !== false;
                                            }
                                        });
              }else if($key == '!like'){
                $newData = $checklists->filter(function ($items) use ($value, $item){
                                            if(is_array($items[$item])){
                                              return stripos(implode($items[$item]),$value) === false;
                                            }else {
                                              return stripos($items[$item],$value) === false;
                                            }
                                        });
              }else if($key == 'is'){
                $newData = $checklists->filter(function ($items) use ($value, $item){
                                            if(is_array($items[$item])){
                                              return implode($items[$item]) == $value;
                                            }else {
                                              return $items[$item] == $value;
                                            }
                                        });
              }else if($key == '!is'){
                $newData = $checklists->filter(function ($items) use ($value, $item){
                                            if(is_array($items[$item])){
                                              return implode($items[$item]) != $value;
                                            }else {
                                              return $items[$item] != $value;
                                            }
                                        });
              }
            }
          }
        }
      }


      if ($newData) {
          return response()->json([
              'meta' => array('count'=>$newData->count(), 'total' => Checklists::all()->count()),
              'data' => $newData
          ], 200);
      } else {
          return response()->json([
              'success' => false,
              'message' => 'Data not found!',
              'data' => ''
          ], 404);
      }
    }

    public function show($id)
    {
        $checklists = Checklists::find($id);

        if ($checklists) {
            return response()->json([
                'success' => true,
                'message' => 'Data found!',
                'data' => $checklists
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data not found!',
                'data' => ''
            ], 404);
        }
    }

    public function create(Request $request)
    {

        $allData = $request->input('data.attributes');
        $input = Checklists::create($allData);
        $checklists = Checklists::find($input->id);

        if ($input) {
            return response()->json([
                'type' => $this->type,
                'id' => $input->id,
                'attributes' => $checklists,
                'links' => [
                    'self' => $_SERVER['APP_URL'].$_SERVER['REDIRECT_URL'].'/'.$input->id
                ]
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Register Failed!',
                'data' => ''
            ], 400);
        }
    }

    public function update(Request $request, $id)
    {
        $input = $request->input('data.attributes');
        $checklists = Checklists::find($id)->first();
        $checklists->object_domain = $input['object_domain'];
        $checklists->object_id = $input['object_id'];
        $checklists->description = $input['description'];
        $checklists->is_completed = $input['is_completed'];
        $checklists->completed_at = $input['completed_at'];

        $proses = $checklists->save();

        if ($proses) {
            return response()->json([
                'type' => $this->type,
                'id' => $checklists->id,
                'attributes' => $input,
                'links' => [
                  'self' => $_SERVER['APP_URL'].$_SERVER['REDIRECT_URL'].'/'.$id
                ]
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Update Failed!',
                'data' => ''
            ], 400);
        }
    }

    public function delete($id)
    {
        $checklists = Checklists::find($id);

        $proses = $checklists->delete();

        if ($proses) {
            return response()->json([
                'success' => true,
                'message' => 'Data Deleted!'
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed Deleted!'
            ], 400);
        }
    }

}
