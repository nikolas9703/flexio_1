<?php

namespace Flexio\Notifications\Channels;

use RuntimeException;
use Flexio\Notifications\Notification;

class MailChannel
{

    private $ci;
    private $config = ['mailtype' => 'html', 'charset' => 'utf-8', 'wordwrap' => TRUE];

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->library('email', $this->config);
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function send($notifiable, Notification $notification)
    {
        $data = $this->getData($notifiable, $notification);
        $filepath = realpath('./public/templates/email/notifications/main.html');
        $htmlmail = read_file($filepath);

        $htmlmail = str_replace("__SITE_URL__", base_url('/'), $htmlmail);
        $htmlmail = str_replace("__BODY__", $data["text"], $htmlmail);
        $htmlmail = str_replace("__YEAR__", date('Y'), $htmlmail);

        $this->ci->email->from('no-reply@pensanomica.com', 'Flexio');
        $this->ci->email->to($notifiable->routeNotificationFor('mail'));
        $this->ci->email->subject('Flexio - Info');
        $this->ci->email->message($htmlmail);
        return $this->ci->email->send();
        //dd($success, $this->ci->email->print_debugger());
    }

    /**
     * Get the data for the notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return array
     *
     * @throws \RuntimeException
     */
    protected function getData($notifiable, Notification $notification)
    {
        if (method_exists($notification, 'toDatabase')) {
            $data = $notification->toDatabase($notifiable);

            return is_array($data) ? $data : $data->data;
        } elseif (method_exists($notification, 'toArray')) {
            return $notification->toArray($notifiable);
        }

        throw new RuntimeException(
            'Notification is missing toDatabase / toArray method.'
        );
    }
}
