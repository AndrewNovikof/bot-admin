<?php

namespace App\Services;

use App\Models\Bot;
use App\Models\Chat;
use App\Models\ScheduleEvent;
use Carbon\Carbon;
use Telegram\Bot\Api;

class BotService
{
    /**
     * Get bot's information
     *
     * @param Bot $bot
     * @return \Telegram\Bot\Objects\User|null
     */
    public function getMe(Bot $bot)
    {
        try {
            $telegramBot = new Api($bot->token);
            $response = $telegramBot->getMe();
            $bot->update([
                'is_available' => true,
                'real_first_name' => $response->firstName,
                'real_id' => $response->id,
                'real_user_name' => $response->username,
                'last_check_at' => Carbon::now(),
            ]);
            return $response;
        } catch (\Exception $exception) {
            $bot->update([
                'is_available' => false,
                'last_check_at' => Carbon::now(),
                'last_error_message' => $exception->getMessage()
            ]);
            return null;
        }
    }

    /**
     * @param Bot $bot
     * @param Chat $chat
     * @return bool
     */
    public function sendChatAction(Bot $bot, Chat $chat): bool
    {
        try {
            $telegramBot = new Api($bot->token);
            $realChatId = null;
            $response = $telegramBot->sendChatAction([
                'chat_id' => $chat->real_chat_id,
                'action' => 'typing'
            ]);
            if ($response === true && preg_match('/^@/', $chat->real_chat_id) === 1) {
                $messageResponse = $telegramBot->sendMessage([
                    'chat_id' => $chat->real_chat_id,
                    'text' => 'BotScheduler connection completed successfully',
                    'parse_mode' => 'HTML',
                ]);
                $realChatId = $messageResponse->chat->id;
            }

            $chat->update([
                'is_available' => true,
                'last_check_at' => Carbon::now(),
                'real_chat_id' => $realChatId ?? $chat->real_chat_id
            ]);
            return $response;
        } catch (\Exception $exception) {
            $chat->update([
                'is_available' => false,
                'last_check_at' => Carbon::now(),
                'last_error_message' => $exception->getMessage()
            ]);
            return false;
        }
    }

    public function sendMessage(ScheduleEvent $scheduleEvent)
    {
        try {
            $telegramBot = new Api($scheduleEvent->bot->token);
            $response = $telegramBot->sendMessage([
                'chat_id' => $scheduleEvent->chat->real_chat_id,
                'text' => $scheduleEvent->message->text,
                'parse_mode' => $scheduleEvent->message->parse_mode,
                'disable_web_page_preview' => $scheduleEvent->message->disable_web_page_preview,
                'disable_notification' => $scheduleEvent->message->disable_notification,
                'reply_to_message_id' => $scheduleEvent->message->reply_to_message_id,
                'reply_markup' => $scheduleEvent->message->reply_markup,
            ]);
            $scheduleEvent->update([
                'is_available' => true,
            ]);
            return $response;
        } catch (\Exception $exception) {
            $scheduleEvent->update([
                'is_available' => false,
                'last_error_message' => $exception->getMessage()
            ]);
            return null;
        }
    }
}
