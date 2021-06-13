<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessagePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        $bot = request()->route()->bot;
        return $bot->user_id === $user->id || $user->current_team_id === $bot->team_id;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Message $message
     * @return bool
     */
    public function update(User $user, Message $message)
    {
        $bot = request()->route()->bot;
        return $bot->user_id === $user->id || $user->current_team_id === $bot->team_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Message $message
     * @return bool
     */
    public function delete(User $user, Message $message)
    {
        $bot = request()->route()->bot;
        return $bot->user_id === $user->id || $user->current_team_id === $bot->team_id;
    }
}
