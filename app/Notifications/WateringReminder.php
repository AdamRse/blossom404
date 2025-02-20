<?php

namespace App\Notifications;

use App\Models\Plant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WateringReminder extends Notification implements ShouldQueue {
    use Queueable;

    private Plant $plant;
    private \DateTime $wateringDate;

    public function __construct(Plant $plant, \DateTime $wateringDate) {
        $this->plant = $plant;
        $this->wateringDate = $wateringDate;
    }

    public function via($notifiable): array {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage {
        return (new MailMessage)
            ->subject('Time to Water Your ' . $this->plant->common_name)
            ->greeting('Hello ' . $notifiable->firstname . '!')
            ->line("It's time to water your " . $this->plant->common_name . ".")
            ->line('Recommended watering date: ' . $this->wateringDate->format('Y-m-d'))
            ->line($this->getWateringInstructions())
            ->action('View Plant Details', url('/plants/' . $this->plant->id))
            ->line('Thank you for using Blossom Buddy!');
    }

    private function getWateringInstructions(): string {
        $instructions = "Watering instructions:";

        if ($this->plant->watering) {
            $instructions .= " " . $this->plant->watering;
        } else {
            $instructions .= " Water thoroughly and allow soil to dry slightly between waterings.";
        }

        return $instructions;
    }
}
