<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreScheduleEventReuest;
use App\Models\Bot;
use App\Models\ScheduleEvent;
use App\Services\BotService;
use Cron\CronExpression;
use Illuminate\Console\Scheduling\ManagesFrequencies;
use Illuminate\Http\RedirectResponse;

class ScheduleEventController extends Controller
{
    use ManagesFrequencies;

    /**
     * @var BotService $botService
     */
    protected $botService;

    /**
     * The cron expression representing the event's frequency.
     *
     * @var string
     */
    public $expression = '* * * * *';

    public function __construct(BotService $botService)
    {
        $this->authorizeResource(ScheduleEvent::class, 'scheduleEvent');
        $this->botService = $botService;
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
        $preparedArray = $this->handleCronSettings($settings, null);
        $event = $bot->scheduleEvents()->create(array_merge($request->validated(), $preparedArray));
        return redirect()->route('bots.show', [$bot]);
    }

    /**
     * @param array $settings
     * @return array
     * @throws \Exception
     */
    protected function handleCronSettings(array $settings, $cronExp): array
    {
        $cronExpression = $cronExp ?? $this->getCronExpression($settings);
        $expressionClass = new CronExpression($cronExpression);
        $nexDueDate = $expressionClass->getNextRunDate('now', 0, false, $settings['timezone']['values'][0] ?? 'Europe/Moscow');
        return [
            'cron_expression' => $cronExpression,
            'next_due_date' => $nexDueDate
        ];
    }

    /**
     * @param array $settings
     * @return string
     */
    protected function getCronExpression(array $settings): string
    {
        $checkedSettings = array_filter($settings, function ($setting) {
            return $setting['checked'] ?? false;
        });
        foreach ($checkedSettings as $key => $setting) {
            if (method_exists($this, $key)) {
                call_user_func_array([$this, $key], $setting['values'] ?? []);
            }
        }
        return $this->expression;
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
        $preparedArray = $this->handleCronSettings($settings, $request->cron_expression);
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
