<?php

namespace Zimutech;

require_once('Base.php');

class Mail extends Base
{
    public function send(array $request) : array
    {
        $api = self::BASE_URL . 'mail/send.json';

        if(array_key_exists('attachments', $request)) {
            $attachments = $request['attachments'];

            for($i = 0; $i < count($attachments); $i++)
            {
                $request['attachments['. $i . ']'] = curl_file_create($attachments[$i], mime_content_type($attachments[$i]), $attachments[$i]);
            }

            unset($request['attachments']);
        }

        $request['appid'] = $this->appId;
        $request['timestamp'] = $this->getTimestamp();
        $request['sign_type'] = 'sha1';
        $request['signature'] = $this->buildSignature($request);

        $result = $this->httpRequest($api, $request);
        return $result;
    }

    public function subhookValidate() : bool
    {
        $token = $_POST['token'];
        $signature = $_POST['signature'];

        return md5($token . $this->emailSubhookKey) === $signature;
    }
}