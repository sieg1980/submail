<?php

namespace Zimutech;

require_once 'Base.php';

class Sms extends Base
{
    public function send(string $to, string $content) : array
    {
        $api = 'message/send.json';

        $request['to'] = $to;
        $request['content'] = $content;
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