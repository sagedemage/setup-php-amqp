#!/usr/bin/php
<?php
// Connection
$connection = new AMQPConnection();
$connection->setHost('127.0.0.1');
$connection->setLogin('test');
$connection->setPassword('test');
$connection->setVhost('testHost');
$connection->connect();

// Channel
$channel = new AMQPChannel($connection);

// Exchange
$exchange = new AMQPExchange($channel);
$exchange->setName('testExchange');

try {
    $routing_key = 'hello';

    // Queue
    $queue = new AMQPQueue($channel);
    $queue->setName('testQueue');
    $queue->setFlags(AMQP_DURABLE);
    $queue->declareQueue();
    $queue->bind($exchange->getName(), $routing_key);

    $message = 'Hello World!';

    // Publish
    $exchange->publish($message, $routing_key);

    $connection->disconnect();
} catch (Exception $ex) {
    print_r($ex);
}
?>