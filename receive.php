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

    $callback_func = function(AMQPEnvelope $message, AMQPQueue $q) use (&$max_consume) {
        echo PHP_EOL, "----------", PHP_EOL;
        echo " [X] Received ", $message->getBody(), PHP_EOL;
        echo PHP_EOL, "----------", PHP_EOL;

        $q->nack($message->getDeliveryTag());
        sleep(1);
    };

    // ----

    try {
        $routing_key = 'hello';

        $queue = new AMQPQueue($channel);
        $queue->setName($routing_key);
        $queue->setFlags(AMQP_NOPARAM);
        $queue->declareQueue();

        echo ' [*] Waiting for messages. To exit press CTRL+C', PHP_EOL;
        $queue->consume($callback_func);
    }
    catch(AMQPQueueException $ex) {
        print_r($ex);
    }
    catch(Exception $ex) {
        print_r($ex);
    }

    // Close Connections
    echo 'Close connection...', PHP_EOL;
    $queue->cancel();
    $connection->disconnect();

    /*
    $channel->queue_declare('hello', false, false, false, false);

    echo " [*] Waiting for messages. To exit press CTRL+C\n";

    $callback = function ($msg) {
        echo ' [x] Received ', $msg->body, "\n";
    };

    $channel->basic_consume('hello', '', false, true, false, false, $callback);

    while ($channel-> is_open()) {
        $channel->wait();
    }
    */
?>
