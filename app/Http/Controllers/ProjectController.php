<?php
namespace App\Http\Controllers;

use App\Http\Requests\SyncUsersRequest;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Services\ProjectService;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use Exception;
use App\Http\Requests\AttachUserRequest;
use App\Http\Requests\DetachUserRequest;
use Carbon\Carbon;
class ProjectController extends Controller
{
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    /**
     * Display a listing of the projects.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $itemsPerPage = $request->query('items_per_page', 15);
            $projects = $this->projectService->getAllProjects($itemsPerPage);
            return response()->json(($projects), 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * Store a newly created project in storage.
     *
     * @param StoreProjectRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreProjectRequest $request)
    {
        try {
            $project = $this->projectService->createProject($request->validated());
            return response()->json(new ProjectResource($project), 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * Display the specified project.
     *
     * @param Project $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Project $project)
    {
        return response()->json(new ProjectResource($project), 200);
    }

    /**
     * Update the specified project in storage.
     *
     * @param UpdateProjectRequest $request
     * @param Project $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        try {
            $project = $this->projectService->updateProject($project, $request->validated());
            return response()->json(new ProjectResource($project), 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * Remove the specified project from storage.
     *
     * @param Project $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Project $project)
    {
        try {
            $this->projectService->deleteProject($project);
            return response()->json(null, 204);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    public function attachUser(AttachUserRequest $request, $projectId)
    {
        $validated = $request->validated();
        $result = $this->projectService->attachUser($validated, $projectId);
        return response()->json($result);
    }

    public function startWork($projectId, $role)
    {
        $user = auth()->user();
        $result = $this->projectService->startWork($user, $projectId, $role);
        return response()->json($result);
    }

    public function stopWork($projectId, $role)
    {
        $user = auth()->user();
        $result = $this->projectService->stopWork($user, $projectId, $role);
        return response()->json($result);
    }

    public function detachUser(DetachUserRequest $request, $projectId)
    {
        $validated = $request->validated();
        $result = $this->projectService->detachUser($validated, $projectId);
        return response()->json($result);
    }

    public function syncUsers(SyncUsersRequest $request, $projectId)
    {
        $validated = $request->validated();
        $result = $this->projectService->syncUsers($validated, $projectId);
        return response()->json($result);
    }
}
