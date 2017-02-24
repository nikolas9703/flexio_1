<?php

namespace Flexio\Notifications;

use Ramsey\Uuid\Uuid;
use InvalidArgumentException;
use Illuminate\Support\Manager;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Bus\Dispatcher as Bus;
use Illuminate\Database\Eloquent\Collection as ModelCollection;
use Flexio\Notifications\Contracts\Factory as FactoryContract;
use Flexio\Notifications\Contracts\Dispatcher as DispatcherContract;

//class ChannelManager extends Manager implements DispatcherContract, FactoryContract
class ChannelManager implements DispatcherContract, FactoryContract
{
    /**
     * The default channel used to deliver messages.
     *
     * @var string
     */
    protected $defaultChannel = 'mail';

    /**
     * Send the given notification to the given notifiable entities.
     *
     * @param  \Illuminate\Support\Collection|array|mixed  $notifiables
     * @param  mixed  $notification
     * @return void
     */
    public function send($notifiables, $notification)
    {
        $notifiables = $this->formatNotifiables($notifiables);

        if ($notification instanceof ShouldQueue) {
            return $this->queueNotification($notifiables, $notification);
        }

        return $this->sendNow($notifiables, $notification);
    }

    /**
     * Send the given notification immediately.
     *
     * @param  \Illuminate\Support\Collection|array|mixed  $notifiables
     * @param  mixed  $notification
     * @return void
     */
    public function sendNow($notifiables, $notification, array $channels = null)
    {
        $notifiables = $this->formatNotifiables($notifiables);

        $original = clone $notification;

        foreach ($notifiables as $notifiable) {
            $notificationId = Uuid::uuid4()->toString();

            $channels = $channels ?: $notification->via($notifiable);

            if (empty($channels)) {
                continue;
            }

            foreach ($channels as $channel) {
                $notification = clone $original;

                if (! $notification->id) {
                    $notification->id = $notificationId;
                }

                if (! $this->shouldSendNotification($notifiable, $notification, $channel)) {
                    continue;
                }
                $response = $this->driver($channel)->send($notifiable, $notification);
            }
        }
    }

    public function driver($driver = null)
    {
        $driver = $driver ?: $this->getDefaultDriver();

        // If the given driver has not been created before, we will create the instances
        // here and cache it so we can return it next time very quickly. If there is
        // already a driver created by this name, we'll just return that instance.
        if (! isset($this->drivers[$driver])) {
            $this->drivers[$driver] = $this->createDriver($driver);
        }

        return $this->drivers[$driver];
    }

    /**
     * Determines if the notification can be sent.
     *
     * @param  mixed  $notifiable
     * @param  mixed  $notification
     * @param  string  $channel
     * @return bool
     */
    protected function shouldSendNotification($notifiable, $notification, $channel)
    {
        return true;
    }

    /**
     * Queue the given notification instances.
     *
     * @param  mixed  $notifiables
     * @param  array[\Illuminate\Notifications\Channels\Notification]  $notification
     * @return void
     */
    protected function queueNotification($notifiables, $notification)
    {
        $notifiables = $this->formatNotifiables($notifiables);

        $bus = $this->app->make(Bus::class);

        $original = clone $notification;

        foreach ($notifiables as $notifiable) {
            $notificationId = Uuid::uuid4()->toString();

            foreach ($notification->via($notifiable) as $channel) {
                $notification = clone $original;

                $notification->id = $notificationId;

                $bus->dispatch(
                    (new SendQueuedNotifications($this->formatNotifiables($notifiable), $notification, [$channel]))
                            ->onConnection($notification->connection)
                            ->onQueue($notification->queue)
                            ->delay($notification->delay)
                );
            }
        }
    }

    /**
     * Format the notifiables into a Collection / array if necessary.
     *
     * @param  mixed  $notifiables
     * @return ModelCollection|array
     */
    protected function formatNotifiables($notifiables)
    {
        if (! $notifiables instanceof Collection && ! is_array($notifiables)) {
            return $notifiables instanceof Model
                            ? new ModelCollection([$notifiables]) : [$notifiables];
        }

        return $notifiables;
    }

    /**
     * Get a channel instance.
     *
     * @param  string|null  $name
     * @return mixed
     */
    public function channel($name = null)
    {
        return $this->driver($name);
    }

    /**
     * Create an instance of the database driver.
     *
     * @return \Illuminate\Notifications\Channels\DatabaseChannel
     */
    protected function createDatabaseDriver()
    {
        $app = new \Illuminate\Container\Container;
        return $app->make(Channels\DatabaseChannel::class);
    }

    /**
     * Create an instance of the database driver.
     *
     * @return \Illuminate\Notifications\Channels\DatabaseChannel
     */
    protected function createDesktopDriver()
    {
        $app = new \Illuminate\Container\Container;
        return $app->make(Channels\DatabaseChannel::class);
    }

    /**
     * Create an instance of the broadcast driver.
     *
     * @return \Illuminate\Notifications\Channels\BroadcastChannel
     */
    protected function createBroadcastDriver()
    {
        return $this->app->make(Channels\BroadcastChannel::class);
    }

    /**
     * Create an instance of the mail driver.
     *
     * @return \Illuminate\Notifications\Channels\MailChannel
     */
    protected function createMailDriver()
    {
        $app = new \Illuminate\Container\Container;
        return $app->make(Channels\MailChannel::class);
    }

    /**
     * Create a new driver instance.
     *
     * @param  string  $driver
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    protected function createDriver($driver)
    {
        $method = 'create'.Str::studly($driver).'Driver';

        // We'll check to see if a creator method exists for the given driver. If not we
        // will check for a custom driver creator, which allows developers to create
        // drivers using their own customized driver creator Closure to create it.
        if (isset($this->customCreators[$driver])) {
            return $this->callCustomCreator($driver);
        } elseif (method_exists($this, $method)) {
            return $this->$method();
        }

        throw new InvalidArgumentException("Driver [$driver] not supported.");
    }

    /**
     * Get the default channel driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->defaultChannel;
    }

    /**
     * Get the default channel driver name.
     *
     * @return string
     */
    public function deliversVia()
    {
        return $this->getDefaultDriver();
    }

    /**
     * Set the default channel driver name.
     *
     * @param  string  $channel
     * @return void
     */
    public function deliverVia($channel)
    {
        $this->defaultChannel = $channel;
    }
}
