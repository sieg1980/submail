<?php

namespace Zimutech;

use GuzzleHttp\Client;

const BASE_URL = 'https://api-v4.mysubmail.com';

class Base
{
    protected string $signType = 'sha1';
    protected string $appId;
    protected string $appKey;
    protected Client $client;

    function __construct(string $appId, string $appKey)
    {
        $this->appId = $appId;
        $this->appKey = $appKey;
        $this->client = new Client(['base_uri' => BASE_URL]);
    }

    protected function buildSignature(array &$request) : string
    {
        $request['appid'] = $this->appId;
        $request['timestamp'] = $this->getTimestamp();
        $request['sign_type'] = $this->signType;

        ksort($request);

        $tmp = [];

        foreach($request as $k => $v)
        {
            if($k !== 'attachments' && $k !== 'tag')
                $tmp[] = $k . '=' . $v;
        }

        $arg = implode('&', $tmp);

        return sha1($this->appId . $this->appKey . $arg . $this->appId . $this->appKey);
    }

    protected function httpRequest(string $api, array $data) : array
    {
        $multipart = [];
        $count = 0;
        foreach ($data as $name => $contents)
        {
            if($name !== 'attachments') {
                $multipart[] = [
                    'name' => $name,
                    'contents' => $contents
                ];
            } else {
                if(!is_array($contents)) {
                    $multipart[] = [
                        'name' => "attachments[$count]",
                        'contents' => fopen($contents, 'r')
                    ];
                } else {
                    foreach($contents as $file) {
                        $multipart[] = [
                            'name' => "attachments[$count]",
                            'contents' => fopen($file, 'r')
                        ];
                        $count++;
                    }
                }
            }
        }

        $output = $this->client
            ->post($api, ['multipart' => $multipart])
            ->getBody()
            ->getContents();

        return json_decode($output, true);
    }

    protected function getTimestamp() : string
    {
        $output = $this->client
            ->get('/service/timestamp.json')
            ->getBody()
            ->getContents();

        $timestamp = json_decode($output, true);

        return $timestamp['timestamp'];
    }
}
