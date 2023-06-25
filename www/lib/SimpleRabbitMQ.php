<?php

namespace Lib;

// https://php-enqueue.github.io/transport/amqp/#purge-queue-messages
use Enqueue\AmqpLib\AmqpConnectionFactory;
use Enqueue\AmqpTools\RabbitMqDlxDelayStrategy;
use Interop\Amqp\AmqpTopic;
use Interop\Amqp\AmqpQueue;
use Interop\Amqp\Impl\AmqpBind;
use Interop\Amqp\AmqpConsumer;
use Interop\Amqp\AmqpMessage;
use Interop\Queue\Message;
use Interop\Queue\Consumer;

class SimpleRabbitMQ {

    private static $host = null;
    private static $port = null;
    private static $username = null;
    private static $password = null;
    private static $persisted = null;
    private static $vhost = null;
    public static $connection = null;
    public static $context = null;
    public $channel = null;
    public $exchange = null;
    public $exchangeName = "";
    public $queue = null;
    public $queueName = "";    
    public $subscriptionConsumer = null;

    public static function config(string $host = "localhost", string $port = "5672", $username = "user", $password = "password", bool $persisted = true, string $vhost = "/")
    {
        self::$host = $host;
        self::$port = $port;
        self::$username = $username;
        self::$password = $password;
        self::$persisted = $persisted;
        self::$vhost = $vhost;
    }

    public static function open()
    {
        if (self::$connection === null) {
            error_reporting(E_ALL ^ E_DEPRECATED ^ E_WARNING);
            self::$connection = new AmqpConnectionFactory([
                'host' => self::$host,
                'port' => self::$port,
                'vhost' =>  self::$vhost,
                'user' => self::$username,
                'pass' => self::$password,
                'persisted' => self::$persisted,
            ]);
            self::$context = self::$connection->createContext();
        }
        return self::$connection;
    }

    public static function close()
    {
        if (self::$context !== null) {
            self::$context->close();
            self::$connection = null;
            return true;
        }
        return false;
    }

    public function exchange(string $exchange, string $type = 'direct', int $flag = 2)
    {
        if ($type !== 'direct' && $type !== 'fanout' && $type !== 'topic' && $type !== 'headers') {
            $type = AmqpTopic::TYPE_DIRECT;
        }
        if ($flag !== 0 && $flag !== 1 && $flag !== 2 && $flag !== 4 && $flag !== 8 && $flag !== 16) {
            $flag = AmqpQueue::FLAG_DURABLE;
        }
        $this->exchangeName = $exchange;
        $this->exchange = self::$context->createTopic($this->exchangeName);
        $this->exchange->addFlag($flag);
        $this->exchange->setType($type);
        return self::$context->declareTopic($this->exchange);
    }

    public function queue(string $queue, int $flag = 2, array $args = [/*'x-max-priority' => 10*/])
    {
        if ($flag !== 0 && $flag !== 1 && $flag !== 2 && $flag !== 4 && $flag !== 8 && $flag !== 16) {
            $flag = AmqpQueue::FLAG_DURABLE;
        }
        $this->queueName = $queue;
        $this->queue = self::$context->createQueue($this->queueName);
        $this->queue->addFlag($flag);
        if (count($args) > 0) {
            $this->queue->setArguments($args);
        }
        return self::$context->declareQueue($this->queue);
    }

    public function queueBind()
    {
        return self::$context->bind(new AmqpBind($this->exchange, $this->queue));
    }

    public function pub(string $message, string $type, int $ttl = 0, int $delay = 0)
    {
        $message = self::$context->createMessage($message);
        $producer = self::$context->createProducer();
        if ($delay > 0) {
            $producer = $producer
                ->setDelayStrategy(new RabbitMqDlxDelayStrategy())
                ->setDeliveryDelay($delay);
        }
        if ($ttl > 0) {
            $producer = $producer->setTimeToLive($ttl);
        }
        if ($type === "queue") {
            return $producer->send($this->queue, $message);
        } else if ($type === "exchange") {
            return $producer->send($this->exchange, $message);
        }
        return false;
    }

    public function pub_exchange(string $message, int $ttl = 0, int $delay = 0)
    {
        return $this->pub($message, "exchange", $ttl, $delay);
    }

    public function pub_queue(string $message, int $ttl = 0, int $delay = 0)
    {
        return $this->pub($message, "queue", $ttl, $delay);
    }

    public function sub(callable $callback, int $time = 0)
    {
        $consumer = self::$context->createConsumer($this->queue);
        if ($this->subscriptionConsumer === null) {
            $this->subscriptionConsumer = self::$context->createSubscriptionConsumer();
        }
        $this->subscriptionConsumer->subscribe($consumer, function(Message $message, Consumer $consumer) use ($callback) {
            return $callback($message, $consumer);
        });
    }

    function readMessage()
    {
        $consumer = self::$context->createConsumer($this->queue);
        return $consumer->receive();
    }

    public function waitCallbacks(int $time = 0)
    {
        return $this->subscriptionConsumer->consume($time);
    }

}
