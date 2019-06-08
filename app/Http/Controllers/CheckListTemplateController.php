<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\ChecklistTemplate;
use App\Query\QueryCustom;

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
        if($request->sort){
          $order = $request->sort;
        }else {
          $order = 'id';
        }
        if($request->page_limit){
          $checklists = ChecklistTemplate::orderBy($order, 'asc')->paginate($request->page_limit);
        }else{
          $checklists = ChecklistTemplate::orderBy($order, 'asc')->get();
        }

        $newData = QueryCustom::custom($checklists, $request);

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
                'status' => '404',
                'message' => 'Not found!'
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
