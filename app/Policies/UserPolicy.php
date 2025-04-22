<?php

namespace App\Policies;

use App\Models\User;
use App\Enum\UserType;
use Illuminate\Auth\Access\Response;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model)
    {
        return $user->usertype === UserType::Admin->value
        ? Response::allow()
        : Response::denyAsNotFound();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return $user->usertype === UserType::Admin->value
        ? Response::allow()
        : Response::denyWithStatus(HttpResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user)
    {
        $currentUser = Auth::user();
        return $user->usertype === UserType::Admin->value
        ? Response::allow()
        : Response::denyWithStatus(HttpResponse::HTTP_UNAUTHORIZED);
    }

    public function updateuserstatus(User $user) 
     {
        return $user->usertype === UserType::Admin->value
        ? Response::allow()
        : Response::denyWithStatus(HttpResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model)
    {
        return $user->usertype === UserType::Admin->value
        ? Response::allow()
        : Response::denyAsNotFound();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model)
    {
        //
    }
}
