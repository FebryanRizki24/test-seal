<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    public function index(Request $request)
    {
        return $this->userRepository->getData($request);
    }

    public function getTaskUser(Request $request)
    {
        return $this->userRepository->getTaskUser($request);
    }

    public function store(Request $request)
    {
        return $this->userRepository->store($request);
    }

    public function update(Request $request, $id)
    {
        return $this->userRepository->update($request, $id);
    }

    public function updateAvatar(Request $request, $id)
    {
        return $this->userRepository->updateAvatar($request, $id);
    }

    public function destroy($id)
    {
        return $this->userRepository->destroy($id);
    }
}
