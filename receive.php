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

$callback_func = function (AMQPEnvelope $message, AMQPQueue $q) use (&$max_consume) {
	echo PHP_EOL, "----------", PHP_EOL;
	echo " [X] Received ", $message->getBody(), PHP_EOL;
	echo PHP_EOL, "----------", PHP_EOL;

	$q->nack($message->getDeliveryTag());
	sleep(1);
};

// ----

try {
	$routing_key = 'hello';

	// Queue
	$queue = new AMQPQueue($channel);
	$queue->setName('testQueue');
	$queue->setFlags(AMQP_DURABLE);
	$queue->declareQueue();
	$queue->bind($exchange->getName(), $routing_key);

	echo ' [*] Waiting for messages. To exit press CTRL+C', PHP_EOL;
	$queue->consume($callback_func);
} catch (AMQPQueueException $ex) {
	print_r($ex);
} catch (Exception $ex) {
	print_r($ex);
}

// Close Connections
echo 'Close connection...', PHP_EOL;
$queue->cancel();
$connection->disconnect();
?>