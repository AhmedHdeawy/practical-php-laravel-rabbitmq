<?php

namespace App\Console\Commands;

use App\Jobs\RabbitMqConsumerJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RabbitMqSubscriber extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:rabbit-mq-subscriber';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $queueName = config('queue.connections.rabbitmq_consume.queue');
        $exchangeName = config('queue.connections.rabbitmq_consume.exchange');

        $this->info("Subscribe to all published messages into the {$queueName} queue.");

        $connection = app()->make('queue.connection');
        $channel = $connection->getChannel();


        // Declare the exchange
        $channel->exchange_declare($exchangeName, 'direct', false, true, false);

        // Declare the queue
        $channel->queue_declare($queueName, false, true, false, false);

        // Bind the queue to the exchange
        $channel->queue_bind($queueName, $exchangeName);


        $callback = function ($msg) {

            $this->info("a New Message Recevied...");

            $data = json_decode($msg->body, true);
            Log::alert("Command Data", ['data' => $data, 'name' => $data['name']]);

            RabbitMqConsumerJob::dispatch($data['name']);
            $this->warn("The Message has being dispatched...");
        };

        $channel->basic_consume($queueName, '', false, true, false, false, $callback);

        while ($channel->is_open()) {
            $channel->wait();
        }


        $channel->close();
        $connection->close();
    }
}
