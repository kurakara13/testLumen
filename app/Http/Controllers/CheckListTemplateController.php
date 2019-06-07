<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\ChecklistTemplate;

class CheckListTemplateController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
      $this->middleware('auth');
    }

    public function showAll(Request $request)
    {
        if($request->page_limit){
          $checklists = ChecklistTemplate::paginate(1);
        }else{
          $checklists = ChecklistTemplate::all();
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
                'meta' => array('count'=>$newData->count(), 'total' => ChecklistTemplate::all()->count()),
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
        $checklists = ChecklistTemplate::find($id);

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
        $input = ChecklistTemplate::create($allData);

        if ($input) {
            return response()->json([
                'id' => $input->id,
                'attributes' => $input
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
        $checklists = ChecklistTemplate::find($id)->first();
        $checklists->name = $input['name'];
        $checklists->checklist = $input['checklist'];
        $checklists->items = $input['items'];

        $proses = $checklists->save();

        if ($proses) {
            return response()->json([
                'id' => $checklists->id,
                'attributes' => $input
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
        $checklists = ChecklistTemplate::find($id);

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
