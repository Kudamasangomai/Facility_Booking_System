<?php

namespace App\Http\Controllers\API\v1;

use App\Enum\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::paginate(20);
        return new UserCollection($users);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {

        $this->authorize('create',User::class);
        $user = User::create($request->validated());
        return response()->json([
            'date' => new UserResource($user),
            'message' => 'User Successfully Created'
        ], Response::HTTP_CREATED);

    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {

        $user= User::with('bookings')->findorFail($user->id);
        return new  UserResource($user);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $currentUser = Auth::user();
        // $this->authorize('update',$user);// policy not working smh
        if ($currentUser->id == $user->id || $currentUser->usertype == UserType::Admin->value) {

            $user->update($request->validated());
            return new UserResource($user);
        }
        return response()->json([
            'message' => 'Unauthorized'
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function updateuserstatus(User $user)
    {

        $user = User::findOrFail($user->id);
        $this->authorize('updateuserstatus', User::class);
        if ($user) {

            $user->active = !$user->active;
            $user->save();
            return new UserResource($user);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
