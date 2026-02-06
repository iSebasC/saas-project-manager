<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the projects.
     */
    public function index(): JsonResponse
    {
        $projects = Project::with(['members'])->get();

        return response()->json([
            'projects' => ProjectResource::collection($projects)
        ], 200);
    }

    /**
     * Store a newly created project.
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status ?? 'active',
            'company_id' => Auth::user()->company_id,
        ]);

        // Automatically add the creator as a member with admin role
        $project->members()->attach(Auth::id(), ['role' => 'admin']);

        $project->load(['members']);

        return response()->json([
            'message' => 'Project created successfully.',
            'project' => new ProjectResource($project)
        ], 201);
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        $project->load(['members', 'tasks']);

        return response()->json([
            'project' => new ProjectResource($project)
        ], 200);
    }

    /**
     * Update the specified project.
     */
    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $this->authorize('update', $project);

        $project->update($request->validated());

        $project->load(['members']);

        return response()->json([
            'message' => 'Project updated successfully.',
            'project' => new ProjectResource($project)
        ], 200);
    }

    /**
     * Remove the specified project.
     */
    public function destroy(Project $project): JsonResponse
    {
        $this->authorize('delete', $project);

        $project->delete();

        return response()->json([
            'message' => 'Project deleted successfully.'
        ], 200);
    }
}
