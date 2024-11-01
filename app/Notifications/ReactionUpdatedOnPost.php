<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ReactionUpdatedOnPost extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public User $user, public Post $post, public string $reactionType)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('dearbook/post/notify.updated_reaction.mailing.subject'))
            ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
            ->line(__('dearbook/post/notify.updated_reaction.mailing.greeting', [
                'user_name' => $this->post->user->name,
            ]))
            ->line(__('dearbook/post/notify.updated_reaction.mailing.opening_phrase', [
                'user_reaction_name' => $this->user->name,
                'type' => Str::upper($this->reactionType),
            ]))
            ->action(__('dearbook/post/notify.updated_reaction.mailing.btn_text'), route('post.show', [
                'user' => $this->post->user->username,
                'id' => $this->post->id,
            ]));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
