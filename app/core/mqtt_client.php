<?php

class MQTT_Client {
    // Connection data
    private $host;
    private $port;
    private $username;
    private $password;
    // logic data
    private $is_connected = false;
    private $client;
    private $publish_queue = [];

    // Use singleton structure to only open 1 mqtt connection
    /**
     * Call this method to get singleton
     */
    public static function instance(string $host = null, int $port = null, ?string $username = null, ?string $password = null)
    {
        static $instance = null;
        if(
            $instance === null || (
                $host &&
                $port && (
                    $instance->host !== $host ||
                    $instance->port !== $port || (
                        $username &&
                        $password && (
                            $instance->username !== $username ||
                            $instance->password !== $password
                        )
                    )
                )
            )
        ) {
            if ($instance !== null) {
                $instance->disconnect();
            }
            // Late static binding (PHP 5.3+)
            $instance = new static($host, $port, $username, $password);
        }
        return $instance;
    }
    /**
     * Make clone magic method private, so nobody can clone instance.
     */
    private function __clone() {}

    /**
     * Make sleep magic method private, so nobody can serialize instance.
     */
    private function __sleep() {}

    /**
     * Make wakeup magic method private, so nobody can unserialize instance.
     */
    private function __wakeup() {}
	/**
	 * Make constructor private, so nobody can call "new Class".
	 */
    private function __construct(string $host, int $port, ?string $username, ?string $password) {
        //save credentials
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
    }

    private function connect() {
        if (!$this->is_connected) {
            // initialize client
            $this->client = new Mosquitto\Client;
            // set callbacks
            $this->client->onConnect(Closure::fromCallable([$this, 'on_connect']));
            $this->client->onDisconnect(Closure::fromCallable([$this, 'on_disconnect']));
            // connect to broker
            if ($this->username && $this->password) {
                $this->client->setCredentials($this->username, $this->password);
            }
            $this->client->connect($this->host, $this->port);
            // wait till connection is established or failed
            $this->client->loopForever();
        }
    }

    private function on_connect(int $response_code, string $message) {
        if ($response_code === 0) { // on success
            $this->is_connected = true;
            // process any messages that were registered before connecting
            $this->process_queue();
        } else { // on error
            $trace = debug_backtrace();
            $caller = $trace[0];
            error_log($caller['class']. "::" .$caller['function']. " -> " . $message, E_USER_ERROR);
            die();
        }
        // exit loop to start processing code
        $this->client->exitLoop();
    }

    private function on_disconnect(int $response_code) {
        // exit loop if it exists
        $this->client->exitLoop();
        if ($response_code !== 0) {
            error_log("Disconnected with code $response_code!");
        }
        $this->is_connected = false;
    }

    private function disconnect() {
        if ($this->is_connected) {
            // process any messages that were not processed
            $this->process_queue();
            // disconnect gracefully
            $this->client->disconnect();
            // set flag
            $this->is_connected = false;
        }
    }

    private function process_queue() {
        // only process queue if you are already connected
        if ($this->is_connected && count($this->publish_queue) > 0) {
            foreach ($this->publish_queue as $cb) {
                $cb($this->client);
            }
            // empty queue
            $this->publish_queue = [];
        }
        // Process broker messages
        $this->client->loop();
    }

    public function is_connected() {
        return $this->is_connected;
    }

    public function publish(string $topic, string $payload, int $qos = 0, bool $retain = false) {
        // connect to MQTT since we need the connection
        $this->connect();
        // push publish command to queue
        array_push($this->publish_queue, function($client) use ($topic, $payload, $qos, $retain) {
            $client->publish($topic, $payload, $qos, $retain);
        });
        // process queue immediately if possible
        $this->process_queue();
    }

    public function __destruct() {
        $this->disconnect();
    }

}
