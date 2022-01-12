<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleEventReuest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'chat_id' => 'required|integer|exists:chats,id',
            'message_id' => 'required|integer|exists:messages,id',
            'is_enabled' => 'required|boolean',
            'cron_expression' => ['nullable', 'regex:/(@(annually|yearly|monthly|weekly|daily|hourly|reboot))|(@every (\d+(ns|us|µs|ms|s|m|h))+)|((((\d+,)+\d+|(\d+(\/|-)\d+)|\d+|\*|\/) ?){5,7})/'],
            'settings.timezone.values.0' => 'required|string',
            'settings.monthlyOn.checked' => 'boolean',
            'settings.monthlyOn.values.0' => 'integer|nullable',
            'settings.monthlyOn.values.1' => 'string|nullable',
            'settings.monthly.checked' => 'boolean',
            'settings.weeklyOn.checked' => 'boolean',
            'settings.weeklyOn.values.0' => 'integer|nullable',
            'settings.weeklyOn.values.1' => 'string|nullable',
            'settings.weekly.checked' => 'boolean',
            'settings.twiceDaily.checked' => 'boolean',
            'settings.twiceDaily.values.0' => 'integer|nullable',
            'settings.twiceDaily.values.1' => 'integer|nullable',
            'settings.dailyAt.checked' => 'boolean',
            'settings.dailyAt.values.0' => 'string|nullable',
            'settings.daily.checked' => 'boolean',
            'settings.everySixHours.checked' => 'boolean',
            'settings.everyFourHours.checked' => 'boolean',
            'settings.everyThreeHours.checked' => 'boolean',
            'settings.everyTwoHours.checked' => 'boolean',
            'settings.hourlyAt.checked' => 'boolean',
            'settings.hourlyAt.values.0' => 'integer|nullable',
            'settings.hourly.checked' => 'boolean',
            'settings.everyThirtyMinutes.checked' => 'boolean',
            'settings.everyFifteenMinutes.checked' => 'boolean',
            'settings.everyTenMinutes.checked' => 'boolean',
            'settings.everyFiveMinutes.checked' => 'boolean',
            'settings.weekdays.checked' => 'boolean',
            'settings.weekends.checked' => 'boolean',
            'settings.sundays.checked' => 'boolean',
            'settings.mondays.checked' => 'boolean',
            'settings.tuesdays.checked' => 'boolean',
            'settings.wednesdays.checked' => 'boolean',
            'settings.thursdays.checked' => 'boolean',
            'settings.fridays.checked' => 'boolean',
            'settings.saturdays.checked' => 'boolean',
            'settings.frequency.checked' => 'boolean',
            'settings.frequency.values.0' => 'integer|nullable|min:1',
            'settings.frequency.values.1' => 'integer|nullable',
        ];
    }
}
