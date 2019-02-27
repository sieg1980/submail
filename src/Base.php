<?php

namespace Zimutech;

class Base
{
	const BASE_URL = 'https://api.mysubmail.com/';

	protected $signType = 'sha1';
	protected $appId;
	protected $appKey;
	protected $smsSubhookKey;
	protected $emailSubhookKey;
	
	function __construct(string $appId, string $appKey, string $smsSubhookKey, string $emailSubhookKey)
	{
		$this->appId = $appId;
		$this->appKey = $appKey;
		$this->smsSubhookKey = $smsSubhookKey;
		$this->emailSubhookKey = $emailSubhookKey
	}
	
	protected function buildSignature(array $request) : string
	{
		ksort($request);
		
		$tmp = [];

		foreach($request as $k => $v)
		{
			if($k !== 'attachments') {
				$tmp[] = $k . '=' . $v;
			}
		}
		
		$arg = implode('&', $tmp);

		if(get_magic_quotes_gpc()) {
			$arg = stripslashes($arg);
		}

		$result = sha1($this->appId . $this->appKey . $arg . $this->appId . $this->appKey);

		return $result;
	}

	protected function httpRequest(string $api, array $data, string $method = 'post') : array
	{
		if($method === 'post') {
			$ch = curl_init($api);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/x-www-form-urlencoded"]);
		} else {
			$url = $api . '?' . http_build_query($data);
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
		}

		$output = curl_exec($ch);
		curl_close($ch);
		$output = trim($output, "\xEF\xBB\xBF");
		return json_decode($output, true);
	}

	protected function getTimestamp() : string
	{
        $api = self::BASE_URL . 'service/timestamp.json';
        
        $ch = curl_init($api) ;
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ;
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ;
        
        $output = curl_exec($ch) ;
        $timestamp = json_decode($output, true);
        
        return $timestamp['timestamp'];
    }
}