<?php

namespace App\Repositories;

use App\Helper\Response;
use App\Models\Task;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class TaskRepository
{
    private $task, $response;

    public function __construct(Task $task, Response $response)
    {
        $this->task = $task;
        $this->response = $response;
    }

    public function getData($request)
    {
        try {
            $query = $this->task;
            if ($request->has('title')) {
                $query->where('title', 'like', "%{$request->title}%");
            } else if ($request->has('status')) {
                $query->where('status', 'like', "%{$request->status}%");
            }

            $task = $query->paginate(10);

            if ($task->isEmpty()) {
                return $this->response->empty('Data task kosong');
            }

            return $this->response->pagination($task, 'tasks');
        } catch (Exception $e) {
            return $this->response->empty($e->getMessage());
        }
    }

    public function store($request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'project_id' => 'required|integer|exists:projects,id',
                'user_id' => 'required|integer|exists:users,id',
                'title' => 'required|string|max:255',
                'description' => 'string|max:255',
                'due_date' => 'required|date|after_or_equal:today',
            ]);

            if ($validator->fails()) {
                return $this->response->storeError($validator->errors());
            }

            $task = $this->task->create([
                'project_id' => $request->project_id,
                'user_id' => $request->user_id,
                'title' => $request->title,
                'description' => $request->description,
                'due_date' => $request->due_date
            ]);

            return $this->response->store($task);
        } catch (Exception $e) {
            return $this->response->empty($e->getMessage());
        }
    }

    public function findById($id)
    {
        return $this->task->find($id);
    }

    public function update($request, $id)
    {
        try {
            $task = $this->findById($id);

            if (!$task) {
                return $this->response->notFound();
            }

            $validator = Validator::make($request->all(), [
                'title' => 'string|max:255',
                'description' => 'string|max:255',
                'due_date' => 'date|after_or_equal:today',
                'status' => 'string|in:in_progress,completed,over_due'
            ]);

            if ($validator->fails()) {
                return $this->response->updateError($validator->errors());
            }

            $currentDate = Carbon::now()->toDateString(); 
            $status = $request->status;

            if ($status === null) {
                if ($currentDate > $request->due_date) {
                    $status = 'over_due';
                } else if ($task->status === 'completed') {
                    $status = 'completed';
                } else {
                    $status = 'in_progress';
                }
            }

            $task->update([
                'title' => $request->title,
                'description' => $request->description,
                'due_date' => $request->due_date,
                'status' => $request->status
            ]);

            return $this->response->update($task);
        } catch (Exception $e) {
            return $this->response->empty($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $task = $this->findById($id);

            if (!$task) {
                return $this->response->notFound();
            }

            $task->delete();

            return $this->response->destroy($task);
        } catch (Exception $e) {
            return $this->response->empty($e->getMessage());
        }
    }
}
