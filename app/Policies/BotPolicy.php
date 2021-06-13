<?php

namespace App\Policies;

use App\Models\Bot;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BotPolicy
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
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Bot  $bot
     * @return mixed
     */
    public function view(User $user, Bot $bot)
    {
        return $bot->user_id === $user->id || $user->current_team_id === $bot->team_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Bot  $bot
     * @return mixed
     */
    public function update(User $user, Bot $bot)
    {
        return $bot->user_id === $user->id || $user->current_team_id === $bot->team_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Bot  $bot
     * @return mixed
     */
    public function delete(User $user, Bot $bot)
    {
        return $bot->user_id === $user->id || $user->current_team_id === $bot->team_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Bot  $bot
     * @return mixed
     */
    public function restore(User $user, Bot $bot)
    {
        return $bot->user_id === $user->id || $user->current_team_id === $bot->team_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Bot  $bot
     * @return mixed
     */
    public function forceDelete(User $user, Bot $bot)
    {
        return $bot->user_id === $user->id || $user->current_team_id === $bot->team_id;
    }

    /**
     * Determine whether the user can create chat for the bot..
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Bot  $bot
     * @return mixed
     */
    public function createChat(User $user, Bot $bot)
    {
        return $bot->user_id === $user->id || $user->current_team_id === $bot->team_id;
    }
}
