<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChatRequest;
use App\Models\Bot;
use App\Models\Chat;
use App\Services\BotService;
use Illuminate\Http\RedirectResponse;

class ChatController extends Controller
{
    protected $botService;

    public function __construct(BotService $botService)
    {
        $this->authorizeResource(Chat::class, 'chat');
        $this->botService = $botService;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChatRequest $request, Bot $bot): RedirectResponse
    {
        /** @var Chat $chat */
        $chat = $bot->chats()->create($request->validated());
        $this->botService->sendChatAction($bot, $chat);
        return redirect()->route('bots.show', [$bot]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreChatRequest $request, Bot $bot, Chat $chat): RedirectResponse
    {
        $chat->update($request->validated());
        $this->botService->sendChatAction($bot, $chat);
        return redirect()->route('bots.show', [$bot]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bot $bot, Chat $chat): RedirectResponse
    {
        $chat->delete();
        return redirect()->route('bots.show', [$bot]);
    }

    /**
     * Send chat action.
     */
    public function call(Bot $bot, Chat $chat): RedirectResponse
    {
        $this->botService->sendChatAction($bot, $chat);
        return redirect()->route('bots.show', [$bot]);
    }
}
