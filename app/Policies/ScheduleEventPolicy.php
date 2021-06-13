<?php

namespace App\Policies;

use App\Models\ScheduleEvent;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ScheduleEventPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        $bot = request()->route()->bot;
        return $bot->user_id === $user->id || $user->current_team_id === $bot->team_id;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ScheduleEvent  $scheduleEvent
     * @return mixed
     */
    public function update(User $user, ScheduleEvent $scheduleEvent)
    {
        $bot = request()->route()->bot;
        return $bot->user_id === $user->id || $user->current_team_id === $bot->team_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ScheduleEvent  $scheduleEvent
     * @return mixed
     */
    public function delete(User $user, ScheduleEvent $scheduleEvent)
    {
        $bot = request()->route()->bot;
        return $bot->user_id === $user->id || $user->current_team_id === $bot->team_id;
    }
}
