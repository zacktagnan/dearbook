<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class InvitationToJoinGroupApproved extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Group $group, public User $userInvited)
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
            ->subject(__('dearbook/group.mail.invitation_approved_by_user.subject'))
            ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
            ->line(__('dearbook/group.mail.invitation_approved_by_user.greeting', [
                'admin_group' => $this->group->user->name,
            ]))
            ->line(__('dearbook/group.mail.invitation_approved_by_user.opening_phrase', [
                'user_name' => $this->userInvited->name,
                'group_name' => $this->group->name,
            ]))
            ->action(__('dearbook/group.mail.invitation_approved_by_user.btn_text'), route('group.profile', [
                'group' => $this->group->slug,
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
