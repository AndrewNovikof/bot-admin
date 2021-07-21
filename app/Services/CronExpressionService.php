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
        $nexDueDate = $this->getNexDueDate($cronExpression, $settings, true);
        return [
            'cron_expression' => $cronExpression,
            'next_due_date' => $nexDueDate
        ];
    }

    /**
     * @param string $cronExpression
     * @param array $settings
     * @param bool $isHumanEdit
     * @return \DateTime
     * @throws \Exception
     */
    public function getNexDueDate(string $cronExpression, array $settings, bool $isHumanEdit = false): \DateTime
    {
        $expressionClass = new CronExpression($cronExpression);
        $timeZone = $settings['timezone']['values'][0] ?? 'Europe/Moscow';
        $nth = 0;
        if ($settings['frequency']['checked'] ?? false) {
            if ($isHumanEdit) {
                $nth = $settings['frequency']['values'][1];
            } else {
                $nth = (int)$settings['frequency']['values'][0] - 1;
            }
            $nth = $nth > 0 ? $nth : 0;
        }

        return $expressionClass->getNextRunDate('now', $nth, false, $timeZone);
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
