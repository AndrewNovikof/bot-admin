<?php

namespace App\Console\Commands;

use App\Models\ScheduleEvent;
use App\Services\BotService;
use App\Services\CronExpressionService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class HandleScheduleEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:handle_schedule_events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for execute schedule events that need to be executed now';

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

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(BotService $botService, CronExpressionService $cronExpressionService)
    {
        parent::__construct();
        $this->botService = $botService;
        $this->cronExpressionService = $cronExpressionService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $events = ScheduleEvent::where('next_due_date', Carbon::now()->toDateTimeString('minutes'))->get();
        if ($events) {
            $events->map(function ($event) {
                try {
                    $this->botService->sendMessage($event);
                    $event->next_due_date = $this->cronExpressionService->getNexDueDate($event->cron_expression, $event->settings);
                    $event->save();
                } catch (\Exception $exception) {
                    $event->is_available = false;
                    $event->last_error_message = $exception->getMessage();
                    $event->save();
                }
            });
        }
        return 0;
    }
}
