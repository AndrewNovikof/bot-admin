<?php

namespace App\Services;

use Cron\CronExpression;
use Illuminate\Console\Scheduling\ManagesFrequencies;

class CronExpressionService
{
    use ManagesFrequencies;

    /**
     * The cron expression representing the event's frequency.
     *
     * @var string
     */
    public $expression = '* * * * *';

    /**
     * @param array $settings
     * @param $cronExp
     * @return array
     * @throws \Exception
     */
    public function handleCronSettings(array $settings, $cronExp): array
    {
        $cronExpression = $cronExp ?? $this->getCronExpression($settings);
        $nexDueDate = $this->getNexDueDate($cronExpression);
        return [
            'cron_expression' => $cronExpression,
            'next_due_date' => $nexDueDate
        ];
    }

    /**
     * @param string $cronExpression
     * @return \DateTime
     * @throws \Exception
     */
    public function getNexDueDate(string $cronExpression)
    {
        $expressionClass = new CronExpression($cronExpression);
        return $expressionClass->getNextRunDate('now', 0, false, $settings['timezone']['values'][0] ?? 'Europe/Moscow');
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
}
