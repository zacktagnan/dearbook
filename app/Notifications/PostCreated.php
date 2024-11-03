<?php

namespace App\Notifications;

use App\Models\Group;
use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostCreated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Post $post, public ?Group $group = null)
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
        $subject = '';
        if ($this->group) {
            $subject = __('dearbook/post/notify.created_on_group.mailing.subject', [
                'group_name' => $this->group->name,
            ]);
        } else {
            $subject = __('dearbook/post/notify.created.mailing.subject', [
                'author_name' => $this->post->user->name,
            ]);
        }

        return (new MailMessage)
            ->subject($subject)
            ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))

            ->lineIf(!!$this->group, __('dearbook/post/notify.created_on_group.mailing.greeting', [
                'group_name' => $this->group?->name,
            ]))
            ->lineIf(!$this->group, __('dearbook/post/notify.created.mailing.greeting'))

            ->lineIf(!!$this->group, __('dearbook/post/notify.created_on_group.mailing.opening_phrase', [
                'author_name' => $this->post->user->name,
            ]))
            ->lineIf(!$this->group, __('dearbook/post/notify.created.mailing.opening_phrase', [
                'author_name' => $this->post->user->name,
            ]))

            ->action(__('dearbook/post/notify.created_on_group_or_not.mailing.btn_text'), route('post.show', [
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
