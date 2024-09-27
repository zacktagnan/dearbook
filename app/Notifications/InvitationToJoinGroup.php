<?php

namespace App\Notifications;

use App\Models\Group;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvitationToJoinGroup extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Group $group, public User $userReceiver, public int $expirationTimeInHours, public string $token)
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
            ->subject(__('dearbook/group.process_to_join.by_invitation.mailing.invitation_to_join_group.subject'))
            ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
            ->line(__('dearbook/group.process_to_join.by_invitation.mailing.invitation_to_join_group.greeting', [
                'user_name' => $this->userReceiver->name,
            ]))
            ->line(__('dearbook/group.process_to_join.by_invitation.mailing.invitation_to_join_group.opening_phrase', [
                'admin_group' => $this->group->user->name,
                'group_name' => $this->group->name,
            ]))
            ->action(__('dearbook/group.process_to_join.by_invitation.mailing.invitation_to_join_group.btn_text'), route('group.accept-invitation', [
                'token' => $this->token,
            ]))
            ->line(__('dearbook/group.process_to_join.by_invitation.mailing.invitation_to_join_group.closing_phrase', [
                'num_hours' => $this->expirationTimeInHours,
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
