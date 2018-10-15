<?php

namespace Zimutech;

require_once('Base.php');

class Sms extends Base
{
	public function send(array $request) : array
	{
		$api = self::BASE_URL . 'message/send.json';
        
        $request['appid'] = $this->appId;
        $request['timestamp'] = $this->getTimestamp();
        $request['sign_type'] = 'sha1';
        $request['signature'] = $this->buildSignature($request);
        
        $result = $this->httpRequest($api, $request);
        return $result;
	}
}