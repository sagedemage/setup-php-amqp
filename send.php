#!/usr/bin/php
<?php
    // Connection
    $connection = new AMQPConnection();
    $connection->setHost('127.0.0.1');
    $connection->setLogin('guest');
    $connection->setPassword('guest');
    $connection->connect();

    // Channel
    $channel = new AMQPChannel($connection);

    // Exchange
    $exchange = new AMQPExchange($channel);

    try {
        $routing_key = 'hello';

        // Queue
        $queue = new AMQPQueue($channel);
        $queue->setName($routing_key);
        $queue->setFlags(AMQP_NOPARAM);
        $queue->declareQueue();

        $message = 'Hello World!';

        // Publish
        $exchange->publish($message, $routing_key);

        $connection->disconnect();
    }

    catch(Exception $ex) {
        print_r($ex);
    }
?>
