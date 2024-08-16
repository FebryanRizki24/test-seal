<?php

namespace App\Http\Controllers;

use App\Repositories\TaskRepository;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    private $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }
    public function index(Request $request)
    {
        return $this->taskRepository->getData($request);
    }

    public function store(Request $request)
    {
        return $this->taskRepository->store($request);
    }

    public function update(Request $request, $id)
    {
        return $this->taskRepository->update($request, $id);
    }

    public function destroy($id)
    {
        return $this->taskRepository->destroy($id);
    }
}
