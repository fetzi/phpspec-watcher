<?php

namespace Fetzi\PhpspecWatcher;

use Joli\JoliNotif\NotifierFactory;

class Notification
{
    const ICON_SUCCESS = 'success.png';
    const ICON_ERROR = 'error.png';

    private $body;
    private $icon;

    public static function create(string $body, string $icon) : Notification
    {
        return new static($body, $icon);
    }

    private function __construct(string $body, string $icon)
    {
        $this->body = $body;
        $this->icon = $icon;
    }

    public function send()
    {
        $notification = new \Joli\JoliNotif\Notification();
        $notification
            ->setTitle('PHPSpec Watcher')
            ->setBody($this->body)
            ->setIcon(__DIR__.'/../assets/'.$this->icon);

        NotifierFactory::create()->send($notification);
    }
}
