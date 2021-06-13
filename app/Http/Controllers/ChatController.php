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
     *
     * @param StoreChatRequest $request
     * @param Bot $bot
     * @return RedirectResponse
     */
    public function store(StoreChatRequest $request, Bot $bot): RedirectResponse
    {
        $chat = $bot->chats()->create($request->validated());
        $this->botService->sendChatAction($bot, $chat);
        return redirect()->route('bots.show', [$bot]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StoreChatRequest $request
     * @param Chat $chat
     * @param Bot $bot
     * @return RedirectResponse
     */
    public function update(StoreChatRequest $request, Bot $bot, Chat $chat): RedirectResponse
    {
        $chat->update($request->validated());
        $this->botService->sendChatAction($bot, $chat);
        return redirect()->route('bots.show', [$bot]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Chat $chat
     * @param Bot $bot
     * @return RedirectResponse
     */
    public function destroy(Bot $bot, Chat $chat)
    {
        $chat->delete();
        return redirect()->route('bots.show', [$bot]);
    }

    /**
     * Send chat action.
     *
     * @param Chat $chat
     * @param Bot $bot
     * @return RedirectResponse
     */
    public function call(Bot $bot, Chat $chat)
    {

        $this->botService->sendChatAction($bot, $chat);
        return redirect()->route('bots.show', [$bot]);
    }
}
