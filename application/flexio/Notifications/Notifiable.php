<?php

namespace Flexio\Notifications;

trait Notifiable
{
    use HasDatabaseNotifications, RoutesNotifications;
}
