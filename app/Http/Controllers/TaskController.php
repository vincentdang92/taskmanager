<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index( Request $request){
        return new TaskCollection(Task::all());
    }
    public function show(Request $request, Task $task){
        return new TaskResource($task);
    }
    public function store(StoreTaskRequest $request){
        $validate = $request->validate();
        $task = Task::create($validate);
        return new TaskResource($task);
    }
}
