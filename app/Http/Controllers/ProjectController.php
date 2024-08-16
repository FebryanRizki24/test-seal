<?php

namespace App\Http\Controllers;

use App\Repositories\ProjectRepository;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    private $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }
    public function index(Request $request)
    {
        return $this->projectRepository->getData($request);
    }

    public function getProjectAndTask(Request $request)
    {
        return $this->projectRepository->getProjectAndTask($request);
    }

    public function store(Request $request)
    {
        return $this->projectRepository->store($request);
    }

    public function update(Request $request, $id)
    {
        return $this->projectRepository->update($request, $id);
    }

    public function destroy($id)
    {
        return $this->projectRepository->destroy($id);
    }
}
