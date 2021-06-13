<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleEvent extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'is_available', 'chat_id', 'message_id', 'cron_expression', 'settings', 'next_due_date', 'last_error_message'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_available' => 'boolean',
        'settings' => 'array',
    ];

    /**
     * Set the next_due_date attribute.
     *
     * @param \DateTime $value
     * @return void
     */
    public function setNextDueDateAttribute(\DateTime $value)
    {
        $value->setTimezone(new \DateTimeZone('UTC'));
        $this->attributes['next_due_date'] = $value;
    }

    /**
     * Get the next_due_date attribute.
     *
     * @param  string  $value
     * @return string
     */
    public function getNextDueDateAttribute($value)
    {
        return Carbon::parse($value, 'UTC')->setTimezone($this->settings['timezone']['values'][0])->toDateTimeString('minute');
    }

    public function bot(): BelongsTo
    {
        return $this->belongsTo(Bot::class);
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }
}
