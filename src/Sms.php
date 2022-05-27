<?php

namespace Zimutech;

require_once 'Base.php';

class Sms extends Base
{
    public function send(string $to, string $content, string $tag = '') : array
    {
        $api = 'sms/send.json';

        $request['to'] = $to;
        $request['content'] = $content;
        $request['signature'] = $this->buildSignature($request);

        if(!empty($tag)) {
            $request['tag'] = $tag;
        }

        return $this->httpRequest($api, $request);
    }

    public function xsend(string $to, string $projectId, array $vars, string $tag = '')
    {
        $api = 'sms/xsend.json';

        $request['to'] = $to;
        $request['project'] = $projectId;
        $request['vars'] = json_encode($vars);
        $request['signature'] = $this->buildSignature($request);

        if(!empty($tag)) {
            $request['tag'] = $tag;
        }

        return $this->httpRequest($api, $request);
    }

    public function multiSend(array $to, string $content, string $tag = '') : array
    {
        $api = 'sms/multisend.json';

        $request['multi'] = json_encode($to);
        $request['content'] = $content;
        $request['signature'] = $this->buildSignature($request);

        if(!empty($tag)) {
            $request['tag'] = $tag;
        }

        return $this->httpRequest($api, $request);
    }

    public function multiXSend(array $to, string $projectId, array $multi, $tag = '')
    {
        $api = 'sms/multixsend.json';

        $request['project'] = $projectId;
        $request['multi'] = json_encode($multi);
        $request['signature'] = $this->buildSignature($request);

        if(!empty($tag)) {
            $request['tag'] = $tag;
        }

        return $this->httpRequest($api, $request);
    }

    public function balance()
    {
        $api = 'balance/sms.json';
        $request = [];
        $request['signature'] = $this->buildSignature($request);

        return $this->httpRequest($api, $request);
    }

    public function validate(string $subHookKey) : bool
    {
        $token = $_POST['token'];
        $signature = $_POST['signature'];

        return md5($token . $subHookKey) === $signature;
    }
}
