<?php

namespace App\Policies;

use App\Enum\UserType;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Http\Response as HttpResponse;

class FacilityPolicy
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
    public function view(User $user, Facility $facility)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user )
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
         return $user->usertype === UserType::Admin->value
        ? Response::allow()
        : Response::deny('UnAuthorized Action.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Facility $facility)
    {
        return $user->usertype === UserType::Admin->value
        ? Response::allow()
        : Response::denyWithStatus(HttpResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Facility $facility)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Facility $facility)
    {
        //
    }
}
