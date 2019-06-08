<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ChecklistItem;
use App\Checklists;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;

class ChecklistItemsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showAll($checklistId)
    {
        $checklists = Checklists::with('checklistItems')->find($checklistId);
        $collection = collect([$checklists]);

        $newChecklist = $collection->map(function ($value, $key){
                              if($value['is_completed'] == 0){
                                  $is_completed = false;
                                }else{
                                  $is_completed = true;
                                }

                              $data = array(
                                'object_domain' => $value['object_domain'],
                                'object_id' => $value['object_id'],
                                'description' => $value['description'],
                                'is_completed' => $is_completed,
                                'due' => $value['due'],
                                'urgency' => $value['urgency'],
                                'completed_at' => $value['completed_at'],
                                'last_update_by' => $value['updated_by'],
                                'update_at' => $value['updated_at'],
                                'created_at' => $value['created_at'],
                                'items' => $value['checklistItems']
                              );

                              return $data;
                          });

        if ($checklists) {
            return response()->json([
                'data' => array('type' => 'checklists', 'id' => $checklistId, 'attributes' => $newChecklist)
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data not found!',
                'data' => ''
            ], 404);
        }
    }

    public function show($checklistId, $itemId)
    {
        $item = ChecklistItem::where('checklist_id', $checklistId)
                        ->where('id', $itemId)
                        ->get();

        if (count($item) > 0) {
            return response()->json([
                'data' => array('type' => 'checklists', 'id' => $checklistId, 'attributes' => $item)
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data not found!',
                'data' => ''
            ], 404);
        }
    }

    public function create(Request $request, $checklistId)
    {

        $allData = $request->input('data.attribute');
        $allData['checklist_id'] = $checklistId;
        $input = ChecklistItem::create($allData);

        if ($input) {
            return response()->json([
                'data' => array('attribute' => $input ),
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Register Failed!',
                'data' => ''
            ], 400);
        }
    }

    public function update(Request $request, $checklistId, $itemId)
    {
        $input = $request->input('data.attribute');
        $checklists = ChecklistItem::where('checklist_id', $checklistId)
                        ->where('id', $itemId)
                        ->first();
        foreach ($input as $key => $value) {
          $checklists->$key = $value;
        }

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

    public function update_bulk(Request $request, $checklistId)
    {
        $arrData = $request->input('data');
        $output = [];

        foreach ($arrData as $key => $value) {
            $checklists = ChecklistItem::find($value['id']);
            if($checklists){
              foreach ($value['attributes'] as $keyItem => $valueItem) {
                $checklists->$keyItem = $valueItem;
              }

              $checklists->save();
              $status = 200;
            }else {
              $status = 404;
            }
            array_push($output, [
                "id" => $value['id'],
                "action" => $value['action'],
                "status" => $status
            ]);
        }

        return response()->json([
            'data' => $output
        ], 400);
    }

    public function complete(Request $request)
    {
        $arrData = $request->input('data');
        $output  = [];

        foreach ($arrData as $key => $value) {
            $checklists = ChecklistItem::find($value['item_id']);
            $checklists->is_completed = true;
            $checklists->save();
            array_push($output, [
                "id" => $checklists->id,
                "item_id" => $checklists->id,
                "is_completed" => true,
                "checklist_id" => $checklists->checklist_id
            ]);
        }
        return response()->json([
            'data' => $output
        ], 400);
    }

    public function incomplete(Request $request)
    {
        $arrData = $request->input('data');
        $output  = [];

        foreach ($arrData as $key => $value) {
            $checklists = ChecklistItem::find($value['item_id']);
            $checklists->is_completed = false;
            $checklists->save();
            array_push($output, [
                "id" => $checklists->id,
                "item_id" => $checklists->id,
                "is_completed" => false,
                "checklist_id" => $checklists->checklist_id
            ]);
        }
        return response()->json([
            'data' => $output
        ], 400);
    }

    public function delete($checklistId, $itemId)
    {
        $checklists = ChecklistItem::where('checklist_id', $checklistId)
                        ->where('id', $itemId)
                        ->first();

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

    public function summaries()
    {
        return response()->json([ 'data' => array(
          'today' => ChecklistItem::where('created_at', 'like', '%'.carbon::now()->format('Y-m-d').'%')->count(),
          'past_due' => ChecklistItem::where('created_at', 'like', '%'.carbon::yesterday()->format('Y-m-d').'%')->count(),
          'this_week' => ChecklistItem::whereBetween('created_at', [carbon::now()->startOfWeek()->format('Y-m-d'), carbon::now()->endOfWeek()->format('Y-m-d')])->count(),
          'past_week' => ChecklistItem::whereBetween('created_at', [carbon::now()->startOfWeek()->subWeek()->format('Y-m-d'), carbon::now()->startOfWeek()->subWeek()->endOfWeek()->format('Y-m-d')])->count(),
          'this_month' => ChecklistItem::whereBetween('created_at', [carbon::now()->startOfMonth()->format('Y-m-d'), carbon::now()->endOfMonth()->format('Y-m-d')])->count(),
          'past_month' => ChecklistItem::whereBetween('created_at', [carbon::now()->startOfMonth()->subMonth()->format('Y-m-d'), carbon::now()->startOfMonth()->subMonth()->endOfMonth()->format('Y-m-d')])->count(),
          'total' => ChecklistItem::count()
        )], 201);
    }
}
