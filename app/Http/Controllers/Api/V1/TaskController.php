<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::all();
        return TaskResource::collection($tasks);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
       $task =  Task::create($request->validated());

       if($task){
            return response()->json([
                'message' => "Data stored succesfully",
                'data' => TaskResource::make($task),
            ], 200);
       }else{
        return response()->json([
            'message' => 'Something went wrong',
        ], 500);
       }
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return TaskResource::make($task);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        // $this->validateScope();

        // $task = Task::with($this->loadedWithRelations())
        //         ->find($id);

        if ($task) {
            return TaskResource::make($task);
        } else {
            return response()->json(['message' => __('Record not found.')], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->update($request->validated());

        if($task){
            return response()->json([
                'message' => "Data stored succesfully",
                'data' => TaskResource::make($task),
            ], 200);
        }else{
            return response()->json(['message' => 'Error saving record',], 500);
        }

        return TaskResource::make($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        try {
            $task->delete();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting record',], 500);
        }
        return response()->json([
            'message' => "Data deleted succesfully",
        ], 200);
    }
}
