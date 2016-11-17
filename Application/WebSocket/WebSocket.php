<?php

namespace Finance\Application\WebSocket;


use Exception;
use Finance\Application\InstaForexApi\PrepareResponse;

class WebSocket
{
    /**
     * @var float
     */
    private $startTime;
    /**
     * @var string
     */
    private $ip;
    /**
     * @var int
     */
    private $port;

    /**
     * @var array
     */
    private $connects = [];

    /**
     * @var resource
     */
    private $socket;

    /**
     * WebSocket constructor.
     * @param string $ip
     * @param int $port
     */
    public function __construct(string $ip = '192.168.7.7', int $port = 8889)
    {
        $this->startTime = round(microtime(true), 2);
        $this->ip = $ip;
        $this->port = $port;
        $this->socket = $this->initSocket();
    }

    public function start()
    {
        while (true) {
            $read = $this->connects;
            $read[] = $this->socket;
            $write = $except = null;
            if (!stream_select($read, $write, $except, null)) {
                break;
            }

            if (in_array($this->socket, $read)) {
                $connect = stream_socket_accept($this->socket, -1);
                $this->handshake($connect);
                $this->connects[] = $connect;
                $this->onOpen($connect);

                unset($read[array_search($this->socket, $read)]);
            }

            foreach ($read as $connect) {
                $data = fread($connect, 100000);

                if (!$data) {
                    fclose($connect);
                    unset($this->connects[array_search($connect, $this->connects)]);
                    continue;
                }
                $this->onMessage($connect, $data);


                $this->sendMessage($connect);
            }

            if ((round(microtime(true), 2) - $this->startTime) > 100) {
                fclose($this->socket);
                $t = new WebSocket();
                $t->start();
            }
        }
        fclose($this->socket);
    }

    /**
     * @param $connect
     * @return array|bool
     */
    private function handshake($connect)
    {
        $info = array();

        $line = fgets($connect);
        $header = explode(' ', $line);
        $info['method'] = $header[0];
        $info['uri'] = $header[1];

        while ($line = rtrim(fgets($connect))) {
            if (preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
                $info[$matches[1]] = $matches[2];
            } else {
                break;
            }
        }

        $address = explode(':', stream_socket_get_name($connect, true)); //получаем адрес клиента
        $info['ip'] = $address[0];
        $info['port'] = $address[1];

        if (empty($info['Sec-WebSocket-Key'])) {
            return false;
        }

        $SecWebSocketAccept = base64_encode(
            pack(
                'H*',
                sha1(
                    $info['Sec-WebSocket-Key'] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11'
                )
            )
        );
        $upgrade = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
            "Upgrade: websocket\r\n" .
            "Connection: Upgrade\r\n" .
            "Sec-WebSocket-Accept:" . $SecWebSocketAccept . "\r\n\r\n";
        fwrite($connect, $upgrade);
        return $info;
    }

    /**
     * @param $payload
     * @param string $type
     * @param bool $masked
     * @return string
     */
    private function encode($payload, $type = 'text', $masked = false)
    {
        $frameHead = array();
        $payloadLength = strlen($payload);

        switch ($type) {
            case 'text':
                // first byte indicates FIN, Text-Frame (10000001):
                $frameHead[0] = 129;
                break;

            case 'close':
                // first byte indicates FIN, Close Frame(10001000):
                $frameHead[0] = 136;
                break;

            case 'ping':
                // first byte indicates FIN, Ping frame (10001001):
                $frameHead[0] = 137;
                break;

            case 'pong':
                // first byte indicates FIN, Pong frame (10001010):
                $frameHead[0] = 138;
                break;
        }

        // set mask and payload length (using 1, 3 or 9 bytes)
        if ($payloadLength > 65535) {
            $payloadLengthBin = str_split(sprintf('%064b', $payloadLength), 8);
            $frameHead[1] = ($masked === true) ? 255 : 127;
            for ($i = 0; $i < 8; $i++) {
                $frameHead[$i + 2] = bindec($payloadLengthBin[$i]);
            }
            // most significant bit MUST be 0
            if ($frameHead[2] > 127) {
                return array('type' => '', 'payload' => '', 'error' => 'frame too large (1004)');
            }
        } elseif ($payloadLength > 125) {
            $payloadLengthBin = str_split(sprintf('%016b', $payloadLength), 8);
            $frameHead[1] = ($masked === true) ? 254 : 126;
            $frameHead[2] = bindec($payloadLengthBin[0]);
            $frameHead[3] = bindec($payloadLengthBin[1]);
        } else {
            $frameHead[1] = ($masked === true) ? $payloadLength + 128 : $payloadLength;
        }

        // convert frame-head to string:
        foreach (array_keys($frameHead) as $i) {
            $frameHead[$i] = chr($frameHead[$i]);
        }
        if ($masked === true) {
            // generate a random mask:
            $mask = array();
            for ($i = 0; $i < 4; $i++) {
                $mask[$i] = chr(rand(0, 255));
            }

            $frameHead = array_merge($frameHead, $mask);
        }
        $frame = implode('', $frameHead);

        // append payload to frame:
        for ($i = 0; $i < $payloadLength; $i++) {
            $frame .= ($masked === true) ? $payload[$i] : $payload[$i];
        }

        return $frame;
    }

    private function decode($data)
    {
        $unmaskedPayload = '';
        $decodedData = array();

        // estimate frame type:
        $firstByteBinary = sprintf('%08b', ord($data[0]));
        $secondByteBinary = sprintf('%08b', ord($data[1]));
        $opcode = bindec(substr($firstByteBinary, 4, 4));
        $isMasked = ($secondByteBinary[0] == '1') ? true : false;
        $payloadLength = ord($data[1]) & 127;

        // unmasked frame is received:
        if (!$isMasked) {
            return array('type' => '', 'payload' => '', 'error' => 'protocol error (1002)');
        }

        switch ($opcode) {
            // text frame:
            case 1:
                $decodedData['type'] = 'text';
                break;
            case 2:
                $decodedData['type'] = 'binary';
                break;
            // connection close frame:
            case 8:
                $decodedData['type'] = 'close';
                break;
            // ping frame:
            case 9:
                $decodedData['type'] = 'ping';
                break;
            // pong frame:
            case 10:
                $decodedData['type'] = 'pong';
                break;
            default:
                return array('type' => '', 'payload' => '', 'error' => 'unknown opcode (1003)');
        }

        if ($payloadLength === 126) {
            $mask = substr($data, 4, 4);
            $payloadOffset = 8;
            $dataLength = bindec(sprintf('%08b', ord($data[2])) . sprintf('%08b', ord($data[3]))) + $payloadOffset;
        } elseif ($payloadLength === 127) {
            $mask = substr($data, 10, 4);
            $payloadOffset = 14;
            $tmp = '';
            for ($i = 0; $i < 8; $i++) {
                $tmp .= sprintf('%08b', ord($data[$i + 2]));
            }
            $dataLength = bindec($tmp) + $payloadOffset;
            unset($tmp);
        } else {
            $mask = substr($data, 2, 4);
            $payloadOffset = 6;
            $dataLength = $payloadLength + $payloadOffset;
        }

        if (strlen($data) < $dataLength) {
            return false;
        }

        if ($isMasked) {
            for ($i = $payloadOffset; $i < $dataLength; $i++) {
                $j = $i - $payloadOffset;
                if (isset($data[$i])) {
                    $unmaskedPayload .= $data[$i] ^ $mask[$j % 4];
                }
            }
            $decodedData['payload'] = $unmaskedPayload;
        } else {
            $payloadOffset = $payloadOffset - 4;
            $decodedData['payload'] = substr($data, $payloadOffset);
        }

        return $decodedData;
    }


    private function onOpen($connect)
    {
        fwrite($connect, $this->encode('Привет, ee'));
    }

    public function sendMessage($connect)
    {
        $eurUsd = new prepareResponse("EURUSD");

        while(true) {
            $financialData = $eurUsd->sendRequest();
            fwrite($connect, $this->encode(strval($financialData->getBid())));
        }
    }

    private function onMessage($connect, $data)
    {
        $f = $this->decode($data);
        fwrite($connect, $this->encode($f['payload']));
    }

    private function initSocket()
    {
        error_reporting(E_ALL); //Выводим все ошибки и предупреждения
        set_time_limit(180);    //Время выполнения скрипта ограничено 180 секундами
        ob_implicit_flush();    //Включаем вывод без буферизации
        $socket = stream_socket_server("tcp://" . $this->ip . ":" . $this->port, $errno, $errstr);
        $this->assertConnectSocketServer($socket);
        return $socket;
    }

    /**
     * @param $socket
     * @throws Exception
     */
    private function assertConnectSocketServer($socket)
    {
        if (!$socket) {
            throw new Exception("Socket unavailable!");
        }
    }
}