<?php

require_once __DIR__ . './../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// Define the conection
$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$requestQueueName = 'rpc_queue';
$channel->queue_declare($requestQueueName, false, true, false, false);

$serverReplyQueueName = 'rpc_reply_queue';
$channel->queue_declare($serverReplyQueueName, false, true, false, false);


// Call a function from a remote server
$data = [ 'id' => 6, 'name' => "Call Fibonacci" ];
$msg = new AMQPMessage(json_encode($data), [
    'reply_to'  =>  $serverReplyQueueName,
    'correlation_id'    =>  122
]);
$channel->basic_publish($msg, '', $requestQueueName);
echo "Message Sent \n \n";



// Recieve the response form a remote server
$responseReceived = false;
$callback = function ($request) use (&$responseReceived) {
    $response = intval($request->body);
    // var_dump($request);
    echo 'Response Received with value: ' . $response . " And Corr ID " . $request->get('correlation_id') . "\n";

    $responseReceived = true;
};

$channel->basic_consume($serverReplyQueueName, '', false, false, false, false, $callback);


while (!$responseReceived) {
    $channel->wait();
}


$channel->close();
$connection->close();
