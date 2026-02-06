<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of tasks for a project.
     */
    public function index(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        $tasks = $project->tasks()->with(['assignedUser'])->get();

        return response()->json([
            'tasks' => TaskResource::collection($tasks)
        ], 200);
    }

    /**
     * Store a newly created task.
     */
    public function store(StoreTaskRequest $request, Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status ?? 'pending',
            'project_id' => $project->id,
            'assigned_to' => $request->assigned_to,
            'due_date' => $request->due_date,
        ]);

        $task->load(['project', 'assignedUser']);

        return response()->json([
            'message' => 'Task created successfully.',
            'task' => new TaskResource($task)
        ], 201);
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        $task->load(['project', 'assignedUser']);

        return response()->json([
            'task' => new TaskResource($task)
        ], 200);
    }

    /**
     * Update the specified task.
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $task->update($request->validated());

        $task->load(['project', 'assignedUser']);

        return response()->json([
            'message' => 'Task updated successfully.',
            'task' => new TaskResource($task)
        ], 200);
    }

    /**
     * Remove the specified task.
     */
    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('delete', $task);

        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully.'
        ], 200);
    }
}
