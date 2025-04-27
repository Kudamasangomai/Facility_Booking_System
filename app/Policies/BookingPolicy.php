<?php

namespace App\Policies;

use App\Models\User;
use App\Enum\UserType;
use App\Models\Booking;
use Illuminate\Auth\Access\Response;
use PhpParser\Node\Stmt\Return_;

class BookingPolicy
{

    public function viewAny(User $user)
    {
        return $user->usertype === UserType::Admin->value
            ? Response::allow()
            : Response::denyWithStatus(403,'UnAuthorized Action');
    }
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Booking $booking)
    {
        return $user->usertype === UserType::Admin->value || $user->id == $booking->user_id
            ? Response::allow()
            : Response::denyWithStatus(403,'UnAuthorized Action');
    }

    /**
     * Determine whether the user can create models.
     */
    public function store(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Booking $booking)
    {
        return $user->usertype === UserType::Admin->value ||
            $user->id == $booking->user_id
            ? Response::allow()
            : Response::denyWithStatus(403,'UnAuthorized Action');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Booking $booking)
    {
        return $user->usertype === UserType::Admin->value ||
        $user->id == $booking->user_id
            ? Response::allow()
            : Response::denyWithStatus(403,'UnAuthorized Action');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Booking $booking)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Booking $booking)
    {
        //
    }
}
