<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBotRequest;
use App\Http\Requests\UpdateBotRequest;
use App\Models\Bot;
use App\Services\BotService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Jetstream\Jetstream;

class BotController extends Controller
{
    protected $botService;

    public function __construct(BotService $botService)
    {
        $this->authorizeResource(Bot::class, 'bot');
        $this->botService = $botService;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('Bot/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateBotRequest $request
     * @return RedirectResponse
     */
    public function store(CreateBotRequest $request)
    {
        $newBot = $request->user()->bots()->create($request->validated());
        $this->botService->getMe($newBot);
        return redirect()->route('bots.show', [$newBot]);
    }

    /**
     * Display the specified resource.
     *
     * @param Bot $bot
     * @return Response
     */
    public function show(Bot $bot)
    {
        return Inertia::render( 'Bot/Show', [
            'bot' => $bot->load('user', 'team', 'chats', 'messages', 'scheduleEvents'),
            'permissions' => [
                'canDeleteBot' => Gate::check('delete', $bot),
                'canUpdateBot' => Gate::check('update', $bot),
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateBotRequest $request
     * @param Bot $bot
     * @return RedirectResponse
     */
    public function update(UpdateBotRequest $request, Bot $bot)
    {
        $bot->update($request->validated());
        $this->botService->getMe($bot);
        return back(303);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Bot $bot
     * @return \Illuminate\Contracts\Foundation\Application|RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function destroy(Bot $bot)
    {
        $bot->delete();
        return redirect()->route('dashboard.index');
    }
}
