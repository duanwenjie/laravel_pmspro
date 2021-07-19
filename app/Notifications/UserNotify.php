<?php

namespace App\Notifications;

use App\Channels\DingTalkChannel;
use App\Exceptions\InvalidRequestException;
use App\Tools\Client\UserInfoClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class UserNotify extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;
    protected $messageType;

    public function __construct($message, $messageType = '')
    {
        $this->message = $message;
        $this->messageType = $messageType;
    }

    /**
     * 获取通知频道。
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return [DingTalkChannel::class, 'database'];
    }


    public function toArray($notifiable)
    {
        return [
            'message'      => $this->message,
            'message_type' => $this->messageType
        ];
    }

    /**
     * Desc:
     * @param $notifiable
     * @throws InvalidRequestException
     */
    public function toDingTalk($notifiable)
    {
        UserInfoClient::sendMessage(['user' => $notifiable->username, 'msg' => $this->message]);
    }
}
