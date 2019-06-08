<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Checklists;
use App\Query\QueryCustom;

class ChecklistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->type = 'checklists';
    }

    public function showAll(Request $request)
    {
      if($request->sort){
        $order = $request->sort;
      }else {
        $order = 'id';
      }
      if($request->page_limit){
        $checklists = Checklists::orderBy($order, 'asc')->paginate($request->page_limit);
      }else{
        $checklists = Checklists::orderBy($order, 'asc')->get();
      }

      $newData = QueryCustom::custom($checklists, $request);

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
