<?php

namespace Zimutech;

require_once 'Base.php';

class Mail extends Base
{
    private $from;
    private $from_name;
    private $reply;

    public function __construct(string $appId, string $appKey, string $from, string $from_name, string $reply)
    {
        $this->from = $from;
        $this->from_name = $from_name;
        $this->reply = $reply;

        parent::__construct($appId, $appKey);
    }

    public function send(array $request) : array
    {
        $api = 'mail/send.json';

        $request['from'] = $this->from;
        $request['from_name'] = $this->from_name;
        $request['reply'] = $this->reply;

        $request['appid'] = $this->appId;
        $request['timestamp'] = $this->getTimestamp();
        $request['sign_type'] = $this->signType;
        $request['signature'] = $this->buildSignature($request);

        return $this->httpRequest($api, $request);
    }

    public function validate(string $subhookKey) : bool
    {
        $token = $_POST['token'];
        $signature = $_POST['signature'];

        return md5($token . $subhookKey) === $signature;
    }
}