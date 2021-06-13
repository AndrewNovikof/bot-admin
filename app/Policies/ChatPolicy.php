<?php

namespace App\Policies;

use App\Models\Bot;
use App\Models\Chat;
use App\Models\User;
use http\Env\Request;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChatPolicy
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
     * @param User $user
     * @param Chat $chat
     * @return mixed
     */
    public function update(User $user, Chat $chat)
    {
        $bot = request()->route()->bot;
        return $bot->user_id === $user->id || $user->current_team_id === $bot->team_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Chat $chat
     * @return mixed
     */
    public function delete(User $user, Chat $chat)
    {
        $bot = request()->route()->bot;
        return $bot->user_id === $user->id || $user->current_team_id === $bot->team_id;
    }
}
