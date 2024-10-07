<?php

namespace App\Notifications;

use App\Http\Enums\GroupUserStatus;
use App\Models\User;
use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RequestToJoinGroupApprovedOrNot extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Group $group, public User $userApproved, public string $decision)
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
        if ($this->decision === GroupUserStatus::APPROVED->value) {
            return (new MailMessage)
                ->subject(__('dearbook/group.process_to_join.request_approved_or_not.mailing.request_approved_or_not.subject'))
                ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                ->line(__('dearbook/group.process_to_join.request_approved_or_not.mailing.request_approved_or_not.greeting', [
                    'user_name' => $this->userApproved->name,
                ]))
                ->line(__('dearbook/group.process_to_join.request_approved_or_not.mailing.request_approved_or_not.opening_phrase', [
                    'group_name' => $this->group->name,
                    'status' => __('dearbook/group.process_to_join.request_approved_or_not.decision.' . $this->decision),
                ]))
                ->action(__('dearbook/group.process_to_join.request_approved_or_not.mailing.request_approved_or_not.btn_text'), route('group.profile', [
                    'group' => $this->group->slug,
                ]))
                ->line(__('dearbook/group.process_to_join.request_approved_or_not.mailing.request_approved_or_not.closing_phrase.' . $this->decision));
        } else if ($this->decision === GroupUserStatus::REJECTED->value) {
            return (new MailMessage)
                ->subject(__('dearbook/group.process_to_join.request_approved_or_not.mailing.request_approved_or_not.subject'))
                ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                ->line(__('dearbook/group.process_to_join.request_approved_or_not.mailing.request_approved_or_not.greeting', [
                    'user_name' => $this->userApproved->name,
                ]))
                ->line(__('dearbook/group.process_to_join.request_approved_or_not.mailing.request_approved_or_not.opening_phrase', [
                    'group_name' => $this->group->name,
                    'status' => __('dearbook/group.process_to_join.request_approved_or_not.decision.' . $this->decision),
                ]))
                ->line(__('dearbook/group.process_to_join.request_approved_or_not.mailing.request_approved_or_not.closing_phrase.' . $this->decision));
        }
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
