<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Models\Bot;
use App\Models\Message;
use App\Services\BotService;
use Illuminate\Http\RedirectResponse;

class MessageController extends Controller
{
    protected $botService;

    public function __construct(BotService $botService)
    {
        $this->authorizeResource(Message::class, 'message');
        $this->botService = $botService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreMessageRequest $request
     * @param Bot $bot
     * @return RedirectResponse
     */
    public function store(StoreMessageRequest $request, Bot $bot): RedirectResponse
    {
        $message = $bot->messages()->create($request->validated());
        return redirect()->route('bots.show', [$bot]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StoreMessageRequest $request
     * @param Bot $bot
     * @param Message $message
     * @return RedirectResponse
     */
    public function update(StoreMessageRequest $request, Bot $bot, Message $message): RedirectResponse
    {
        $message->update($request->validated());
        return redirect()->route('bots.show', [$bot]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Bot $bot
     * @param Message $message
     * @return RedirectResponse
     */
    public function destroy(Bot $bot, Message $message)
    {
        $message->delete();
        return redirect()->route('bots.show', [$bot]);
    }
}
