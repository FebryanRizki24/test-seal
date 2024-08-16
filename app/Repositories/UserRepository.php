<?php

namespace App\Repositories;

use App\Helper\Response;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class UserRepository
{
    private $user, $response;

    public function __construct(User $user, Response $response)
    {
        $this->user = $user;
        $this->response = $response;
    }

    public function getData($request)
    {
        try {
            $query = $this->user;
            if ($request->has('name')) {
                $query->where('name', 'like', "%{$request->name}%");
            }

            $user = $query->paginate(10);

            if ($user->isEmpty()) {
                return $this->response->empty('Data user kosong');
            }

            return $this->response->pagination($user, 'users');
        } catch (Exception $e) {
            return $this->response->empty($e->getMessage());
        }
    }

    public function getTaskUser($request)
    {
        try {
            $users = $this->user->with('tasks.projects')->where('name', 'like', "%{$request->name}%")->get();

            $formattedUsers = $users->mapWithKeys(function ($user) {
                return [
                    $user->name => $user->tasks->map(function ($task) {
                        return [
                            $task->projects->name => [$task->title]
                        ];
                    })->toArray()
                ];
            });

            return $this->response->index($formattedUsers);

        } catch (Exception $e) {
            return $this->response->empty($e->getMessage());
        }
    }

    public function store($request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|unique:users,email',
                'password' => ['required', 'confirmed', Rules\Password::defaults()]
            ]);

            if ($validator->fails()) {
                return $this->response->storeError($validator->errors());
            }

            $user = new User();
            $user->name = $request['name'];
            $user->email = $request['email'];
            $user->password = Hash::make($request['password']);
            $user->save();

            return $this->response->store($user);
        } catch (Exception $e) {
            return $this->response->empty($e->getMessage());
        }
    }

    public function findById($id)
    {
        return $this->user->find($id);
    }

    public function update($request, $id)
    {
        try {
            $user = $this->findById($id);

            if (!$user) {
                return $this->response->notFound();
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|unique:users,email,' . $id,
            ]);

            if ($validator->fails()) {
                return $this->response->updateError($validator->errors());
            }

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            return $this->response->update($user);
        } catch (Exception $e) {
            return $this->response->empty($e->getMessage());
        }
    }

    public function updateAvatar($request, $id)
    {
        try {
            $user = $this->findById($id);

            if (!$user) {
                return $this->response->notFound();
            }

            $validator = Validator::make($request->all(), [
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($validator->fails()) {
                return $this->response->updateError($validator->errors());
            }

            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');
                $extension = $avatar->getClientOriginalExtension();
                $avatarName = time() . '_' . $user->name . '.' . $extension;
                $avatar->move(app()->basePath('public') . '/avatars/users', $avatarName);

                if ($user->avatar && File::exists(app()->basePath('public') . '/avatars/users/' . $user->avatar)) {
                    File::delete(app()->basePath('public') . '/avatars/users/' . $user->avatar);
                }

                $user->update([
                    'avatar' => $avatarName,
                ]);
            }

            return $this->response->update($user);
        } catch (Exception $e) {
            return $this->response->empty($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $user = $this->findById($id);

            if (!$user) {
                return $this->response->notFound();
            }

            File::delete(app()->basePath('public') . '/avatars/users/' . $user->avatar);

            $user->delete();

            return $this->response->destroy($user);
        } catch (Exception $e) {
            return $this->response->empty($e->getMessage());
        }
    }
}
