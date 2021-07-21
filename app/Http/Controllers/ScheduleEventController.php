<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreScheduleEventReuest;
use App\Models\Bot;
use App\Models\ScheduleEvent;
use App\Services\BotService;
use App\Services\CronExpressionService;
use Cron\CronExpression;
use Illuminate\Http\RedirectResponse;

class ScheduleEventController extends Controller
{
    /**
     * The service for execute telegram commands
     *
     * @var BotService $botService
     */
    protected $botService;

    /**
     * The service for working with cron expression
     *
     * @var CronExpressionService $cronExpressionService
     */
    protected $cronExpressionService;

    public function __construct(BotService $botService, CronExpressionService $cronExpressionService)
    {
        $this->authorizeResource(ScheduleEvent::class, 'scheduleEvent');
        $this->botService = $botService;
        $this->cronExpressionService = $cronExpressionService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreScheduleEventReuest $request
     * @param Bot $bot
     * @return RedirectResponse
     * @throws \Exception
     */
    public function store(StoreScheduleEventReuest $request, Bot $bot): RedirectResponse
    {
        $settings = $request->validated()['settings'];
        $preparedArray = $this->cronExpressionService->handleCronSettings($settings, null, true);
        $event = $bot->scheduleEvents()->create(array_merge($request->validated(), $preparedArray));
        return redirect()->route('bots.show', [$bot]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param StoreScheduleEventReuest $request
     * @param Bot $bot
     * @param ScheduleEvent $scheduleEvent
     * @return RedirectResponse
     * @throws \Exception
     */
    public function update(StoreScheduleEventReuest $request, Bot $bot, ScheduleEvent $scheduleEvent): RedirectResponse
    {
        $settings = $request->validated()['settings'];
        $preparedArray = $this->cronExpressionService->handleCronSettings($settings, $request->cron_expression, true);
        $scheduleEvent->update(array_merge($request->validated(), $preparedArray));
        return redirect()->route('bots.show', [$bot]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Bot $bot
     * @param ScheduleEvent $scheduleEvent
     * @return RedirectResponse
     */
    public function destroy(Bot $bot, ScheduleEvent $scheduleEvent): RedirectResponse
    {
        $scheduleEvent->delete();
        return redirect()->route('bots.show', [$bot]);
    }

    /**
     * Call event
     *
     * @param Bot $bot
     * @param ScheduleEvent $scheduleEvent
     * @return RedirectResponse
     */
    public function call(Bot $bot, ScheduleEvent $scheduleEvent): RedirectResponse
    {
        $this->botService->sendMessage($scheduleEvent);
        return redirect()->route('bots.show', [$bot]);
    }
}
