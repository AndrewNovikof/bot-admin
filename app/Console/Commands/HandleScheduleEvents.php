<?php

namespace App\Console\Commands;

use App\Models\ScheduleEvent;
use App\Services\BotService;
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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(BotService $botService)
    {
        parent::__construct();
        $this->botService = $botService;
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
                $this->botService->sendMessage($event);
            });
        }
        return 0;
    }
}
