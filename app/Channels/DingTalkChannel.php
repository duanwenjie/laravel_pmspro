<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;

class DingTalkChannel
{
    /**
     * 发送指定的通知。
     *
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $notification->toDingTalk($notifiable);
    }
}
