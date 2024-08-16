<?php

namespace App\Repositories;

use App\Helper\Response;
use App\Models\Project;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class ProjectRepository
{
    private $project, $response;

    public function __construct(Project $project, Response $response)
    {
        $this->project = $project;
        $this->response = $response;
    }

    public function getData($request)
    {
        try {
            $query = $this->project;
            if ($request->has('name')) {
                $query->where('name', 'like', "%{$request->name}%");
            }

            $project = $query->paginate(10);

            if ($project->isEmpty()) {
                return $this->response->empty('Data project kosong');
            }

            return $this->response->pagination($project, 'projects');
        } catch (Exception $e) {
            return $this->response->empty($e->getMessage());
        }
    }

    public function getProjectAndTask()
    {
        try {
            $projects = $this->project->with('tasks')->get();

            $formattedProjects = $projects->mapWithKeys(function ($project) {
                return [
                    $project->name => $project->tasks->map(function ($task) {
                        return $task->title;
                    })->toArray()
                ];
            });

            return $this->response->index($formattedProjects);
        } catch (Exception $e) {
            return $this->response->empty($e->getMessage());
        }
    }

    public function store($request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'string|max:255',
                'start_date' => 'required|date|after_or_equal:today|before:end_date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            if ($validator->fails()) {
                return $this->response->storeError($validator->errors());
            }

            $project = $this->project->create([
                'name' => $request->name,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date
            ]);

            return $this->response->store($project);
        } catch (Exception $e) {
            return $this->response->empty($e->getMessage());
        }
    }

    public function findById($id)
    {
        return $this->project->find($id);
    }

    public function update($request, $id)
    {
        try {
            $project = $this->findById($id);

            if (!$project) {
                return $this->response->notFound();
            }

            $validator = Validator::make($request->all(), [
                'name' => 'string|max:255',
                'description' => 'string|max:255',
                'start_date' => 'date|after_or_equal:today|before:end_date',
                'end_date' => 'date|after_or_equal:start_date',
            ]);

            if ($validator->fails()) {
                return $this->response->updateError($validator->errors());
            }

            $project->update([
                'name' => $request->name,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);

            return $this->response->update($project);
        } catch (Exception $e) {
            return $this->response->empty($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $project = $this->findById($id);

            if (!$project) {
                return $this->response->notFound();
            }

            $project->delete();

            return $this->response->destroy($project);
        } catch (Exception $e) {
            return $this->response->empty($e->getMessage());
        }
    }
}
