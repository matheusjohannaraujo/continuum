<?php

namespace Lib;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class SimpleRabbitMQ {

    private static $host = null;
    private static $port = null;
    private static $username = null;
    private static $password = null;
    public static $connection = null;
    public $channel = null;
    public $exchange = null;
    public $exchangeName = "";
    public $queue = null;
    public $queueName = "";    

    public static function config(string $host = "localhost", string $port = "5672", $username = "user", $password = "password")
    {
        self::$host = $host;
        self::$port = $port;
        self::$username = $username;
        self::$password = $password;
    }

    public static function open()
    {
        if (self::$connection === null) {
            error_reporting(E_ALL ^ E_DEPRECATED ^ E_WARNING);
            self::$connection = new AMQPStreamConnection(self::$host, self::$port, self::$username, self::$password);
        }
        return self::$connection;
    }

    public static function close()
    {
        if (self::$connection !== null) {
            return self::$connection->close();
        }
        return false;
    }

    /**
     * $channel é uma instância da classe PhpAmqpLib\Channel\AMQPChannel, que representa um canal de comunicação com o servidor do RabbitMQ.
     */
    public function openChannel()
    {
        $this->open();
        if (self::$connection !== null) {
            return $this->channel = self::$connection->channel();
        }
        return null;        
    }

    public function closeChannel()
    {
        if ($this->channel !== null) {
            $this->channel->close();
            $this->channel = null;
            return true;
        }
        return false;
    }

    /**
     * $exchange: o nome da exchange que será criada.
     * $type: o tipo de exchange que será criada. Os tipos disponíveis são: 'direct', 'fanout', 'topic' e 'headers'.
     * $passive: um valor booleano que indica se a exchange deve ser criada como "passiva" ou não. Quando true, a exchange é declarada como passiva, o que significa que o RabbitMQ apenas verifica se a exchange já existe, sem criar uma nova. Quando false, a exchange é criada se ainda não existir.
     * $durable: um valor booleano que indica se a exchange deve ser durável ou não. Quando true, a exchange será salva em disco e sobreviverá a reinicializações do servidor RabbitMQ. Quando false, a exchange será considerada transitória e será removida do RabbitMQ quando o servidor for reiniciado.
     * $auto_delete: um valor booleano que indica se a exchange deve ser excluída automaticamente quando não tiver mais nenhuma ligação com uma fila ou uma exchange. Quando true, a exchange será excluída automaticamente. Quando false, a exchange permanecerá no RabbitMQ mesmo que não tenha mais nenhuma ligação.
     * $internal: um valor booleano que indica se a exchange é usada apenas para ligações internas com outras exchanges, ou seja, não pode ser publicada diretamente por um produtor. Quando true, a exchange será usada apenas para ligações internas. Quando false, a exchange pode ser publicada diretamente por um produtor.
     */
    public function exchange(string $exchange, string $type = 'direct', bool $passive = false, bool $durable = true, bool $auto_delete = false, bool $internal = false)
    {
        $this->exchangeName = $exchange;
        return $this->exchange = $this->channel->exchange_declare($exchange, $type, $passive, $durable, $auto_delete, $internal);
    }

    /**
     * 
     * $queue (string): o nome da fila a ser declarada. Se este argumento for deixado em branco, o RabbitMQ criará uma fila exclusiva com um nome gerado automaticamente.
     * $passive (bool): se definido como true, o RabbitMQ irá verificar se a fila já existe sem tentar criar uma nova. Se a fila não existir, o RabbitMQ irá retornar um erro. O valor padrão é false.
     * $durable (bool): se definido como true, a fila será persistente. Isso significa que, se o servidor do RabbitMQ for reiniciado, a fila ainda existirá. O valor padrão é false.
     * $exclusive (bool): se definido como true, a fila será exclusiva para a conexão atual. Isso significa que a fila só pode ser acessada pela conexão que a criou. O valor padrão é false.
     * $auto_delete (bool): se definido como true, a fila será automaticamente excluída pelo RabbitMQ quando não tiver mais consumidores ou quando a conexão que a criou for fechada. O valor padrão é false.
    */
    public function queue(string $queue, bool $passive = false, bool $durable = true, bool $exclusive = false, bool $auto_delete = false)
    {
        $this->queueName = $queue;
        return $this->queue = $this->channel->queue_declare($queue, $passive, $durable, $exclusive, $auto_delete);
    }

    public function queueName()
    {
        return $this->queue[0] ?? "";
    }

    public function queueCountMessage()
    {
        return $this->queue[1] ?? -1;
    }

    public function queueCountConsumer()
    {
        return $this->queue[2] ?? -1;
    }

    /**
     * $routing_key: uma chave de roteamento que será usada para filtrar as mensagens que a exchange enviará para a fila. A chave de roteamento é usada para identificar qual fila receberá a mensagem, de acordo com as regras definidas pelo tipo de exchange.
     * $queue: o nome da fila que será ligada à exchange.
     * $exchange: o nome da exchange que será ligada à fila.     
     */
    public function queueBind(string $routing_key = null, string $queue = null, string $exchange = null)
    {
        if ($queue === null) {
            $queue = $this->queueName;
        }
        if ($exchange === null) {
            $exchange = $this->exchangeName;
        }
        if ($routing_key === null) {
            $routing_key = $this->queueName;
        }
        return $this->channel->queue_bind($queue, $exchange, $routing_key);
    }    

    public function pub(string $message, string $exchange = null, string $routing_key = null, array $message_properties = ['delivery_mode' => 2/*make message persistent*/])
    {
        if ($exchange === null) {
            $exchange = $this->exchangeName;
        }
        if ($routing_key === null) {
            $routing_key = $this->queueName;
        }
        return $this->channel->basic_publish(new AMQPMessage($message, $message_properties), $exchange, $routing_key);
    }

    /**
     * $callback: Uma função de retorno que será chamada pelo RabbitMQ quando uma nova mensagem for entregue ao consumidor. Essa função deve aceitar um argumento do tipo \PhpAmqpLib\Message\AMQPMessage, que contém a mensagem entregue.
     * $queue: O nome da fila da qual o consumidor receberá as mensagens.
     * $consumer_tag: Uma tag que identifica o consumidor. Se essa tag não for fornecida, o RabbitMQ gerará uma tag aleatória para o consumidor.
     * $no_local: Quando definido como true, indica que as mensagens publicadas pelo próprio consumidor não devem ser entregues a ele.
     * $no_ack: Quando definido como true, indica que o RabbitMQ não deve esperar uma confirmação de recebimento de mensagens pelo consumidor. Isso significa que as mensagens serão consideradas automaticamente confirmadas e removidas da fila após serem entregues ao consumidor.
     * $exclusive: Quando definido como true, indica que a fila só deve ser usada por este consumidor e será excluída quando o consumidor se desconectar.
     * $nowait: Quando definido como true, indica que a chamada não deve esperar a resposta do RabbitMQ.     
     */
    public function sub(callable $callback, string $queue = null, string $consumer_tag = "", bool $no_local = false, bool $no_ack = false, bool $exclusive = false, bool $nowait = false)
    {
        if ($queue === null) {
            $queue = $this->queueName;
        }
        $cb = $callback;
        if (!$no_ack) {
            $channel = &$this->channel;
            $cb = function ($msg) use ($callback, $channel) {
                if ($callback($msg) === true) {
                    $channel->basic_ack($msg->delivery_info['delivery_tag']);
                }            
            };
        }
        return $this->channel->basic_consume($queue, $consumer_tag, $no_local, $no_ack, $exclusive, $nowait, $cb);
    }

    function readMessage()
    {
        return $this->channel->wait();
    }

    function readAllMessages(int $sleep = 0)
    {
        $queueCountMessages = $this->queueCountMessage();
        for ($i = 0; $i < $queueCountMessages; $i++) {
            $this->readMessage();
            if ($sleep > 0) {
                usleep($sleep);
            }
        }
    }

    public function waitCallbacks(int $sleep = 0)
    {
        while ($this->channel !== null && count($this->channel->callbacks)) {
            $this->readMessage();
            if ($sleep > 0) {
                usleep($sleep);
            }
        }
    }

}
