<?php
namespace App\Services;

use App\Models\Project;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;
class ProjectService
{
    public function getAllProjects($itemsPerPage = 15)
    {
        try {
            return Project::orderBy('created_at', 'DESC')->paginate($itemsPerPage);
        } catch (Exception $e) {
            throw new Exception('Unable to fetch projects: ' . $e->getMessage());
        }
    }

    public function createProject(array $data)
    {
        $this->authorizeAdmin();

        try {
            return Project::create($data);
        } catch (Exception $e) {
            throw new Exception('Unable to create project: ' . $e->getMessage());
        }
    }

    public function updateProject(Project $project, array $data)
    {
        $this->authorizeAdmin();

        try {
            $project->update($data);
            return $project;
        } catch (Exception $e) {
            throw new Exception('Unable to update project: ' . $e->getMessage());
        }
    }

    public function deleteProject(Project $project)
    {
        $this->authorizeAdmin();

        try {
            $project->delete();
        } catch (Exception $e) {
            throw new Exception('Unable to delete project: ' . $e->getMessage());
        }
    }

    /**
     * Check if the current user is an admin.
     *
     * @throws Exception
     */
    protected function authorizeAdmin()
    {
        if (Auth::user()->role !== 'admin') {
            throw new Exception('Unauthorized: You do not have permission to perform this action.', 403);
        }
    }

    public function attachUser($validated, $projectId)
    {
        try {
            $project = Project::findOrFail($projectId);

            $project->users()->attach($validated['user_id'], [
                'user_role' => $validated['user_role'],
                'contribution_hours' => 0,  // Default to 0
                'last_activity' => now()
            ]);

            return ['message' => 'User attached to project successfully'];
        } catch (ModelNotFoundException $e) {
            return ['error' => 'Project not found'];
        } catch (Exception $e) {
            return ['error' => 'An error occurred while attaching the user'];
        }
    }

    public function startWork($user, $projectId)
    {
        try {
            $user->projects()->updateExistingPivot($projectId, [
                'start_time' => now(),
                'last_activity' => now(),
            ]);

            return ['message' => 'Work started successfully'];
        } catch (Exception $e) {
            return ['error' => 'An error occurred while starting work'];
        }
    }

    public function stopWork($user, $projectId)
    {
        try {
            $project = $user->projects()->where('project_id', $projectId)->first();

            if (!$project) {
                return ['error' => 'Project not found for this user'];
            }

            $pivot = $project->pivot;

            if (is_null($pivot->start_time)) {
                return ['error' => 'No work session started'];
            }

            $startTime = new Carbon($pivot->start_time);
            $endTime = Carbon::now();
            $workedMinutes = $endTime->diffInMinutes($startTime);
            $workedHours = $workedMinutes / 60;

            $user->projects()->updateExistingPivot($projectId, [
                'end_time' => $endTime,
                'contribution_hours' => $pivot->contribution_hours + $workedHours,
                'last_activity' => $endTime,
                'start_time' => null,
                'end_time' => null,
            ]);

            return ['message' => 'Work stopped successfully, hours recorded'];
        } catch (Exception $e) {
            return ['error' => 'An error occurred while stopping work'];
        }
    }

    public function detachUser($validated, $projectId)
    {
        try {
            $project = Project::findOrFail($projectId);
            $project->users()->detach($validated['user_id']);

            return ['message' => 'User detached from project successfully'];
        } catch (ModelNotFoundException $e) {
            return ['error' => 'Project not found'];
        } catch (Exception $e) {
            return ['error' => 'An error occurred while detaching the user'];
        }
    }

    public function syncUsers($validated, $projectId)
    {
        try {
            $project = Project::findOrFail($projectId);
            $users = $validated['users'];  // Format: [user_id => ['user_role' => 'developer']]
            $project->users()->syncWithoutDetaching($users);

            return ['message' => 'Users synced to project successfully'];
        } catch (ModelNotFoundException $e) {
            return ['error' => 'Project not found'];
        } catch (Exception $e) {
            return ['error' => 'An error occurred while syncing users'];
        }
    }
}
