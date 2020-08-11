<?php

namespace Zimutech;

require_once 'Base.php';

use Exception;

class Mail extends Base
{
    private $request;

    public function __construct(string $appId, string $appKey)
    {
        parent::__construct($appId, $appKey);
        $this->request = [];
    }

    public function setFrom(string $from) : Mail
    {
        $this->request['from'] = $from;
        return $this;
    }

    public function setFromName(string $fromName) : Mail
    {
        if(mb_strlen($fromName) > 50)
            throw new Exception('发件人名称的长度不可超过50个字符');

        $this->request['from_name'] = $fromName;
        return $this;
    }

    public function setReply(string $reply) : Mail
    {
        $this->request['reply'] = $reply;
        return $this;
    }

    public function setCc(string $cc) : Mail
    {
        $this->request['cc'] = $cc;
        return $this;
    }

    public function setBcc(string $bcc) : Mail
    {
        $this->request['bcc'] = $bcc;
        return $this;
    }

    public function setTo(string $to) : Mail
    {
        $this->request['to'] = $to;
        return $this;
    }

    public function setSubject(string $subject) : Mail
    {
        if(mb_strlen($subject) > 200)
            throw new Exception('邮件标题的长度不可超过200个字符');

        $this->request['subject'] = $subject;
        return $this;
    }

    public function setText(string $text) : Mail
    {
        if(mb_strlen($text) > 5000)
            throw new Exception('纯文本邮件正文的长度不可超过5000个字符');

        $this->request['text'] = $text;
        return $this;
    }

    public function setHtml(string $html) : Mail
    {
        if(strlen($html) > 60 * 1024)
            throw new Exception('HTML邮件正文的长度不可超过60KB');

        $this->request['html'] = $html;
        return $this;
    }

    public function setProject(string $project) : Mail
    {
        $this->request['project'] = $project;
        return $this;
    }

    public function setVars(array $vars) : Mail
    {
        $this->request['vars'] = json_encode($vars);
        return $this;
    }

    public function setLinks(array $links) : Mail
    {
        $this->request['links'] = json_encode($links);
        return $this;
    }

    public function addAttachments(string $attachments) : Mail
    {
        if(!array_key_exists('attachments', $this->request))
            $this->request['attachments'] = [];

        $this->request['attachments'][] = $attachments;
        return $this;
    }

    public function setTag(string $tag) : Mail
    {
        $this->request['tag'] = $tag;
        return $this;
    }

    public function send() : array
    {
        if(!array_key_exists('from', $this->request))
            throw new Exception('要发送的邮件未设置发件人（from）');

        if(!array_key_exists('subject', $this->request))
            throw new Exception('要发送的邮件未设置标题（subject）');

        if(!array_key_exists('text', $this->request) && !array_key_exists('html', $this->request))
            throw new Exception('要发送的邮件未设置内容（text || html）');

        $api = 'mail/send.json';

        $this->request['signature'] = $this->buildSignature($this->request);
        $result = $this->httpRequest($api, $this->request);
        $this->request = [];

        return $result;
    }

    public function xsend() : array
    {
        if(!array_key_exists('project', $this->request)) {
            if(!array_key_exists('from', $this->request))
                throw new Exception('要发送的邮件未设置发件人（from）');

            if(!array_key_exists('subject', $this->request))
                throw new Exception('要发送的邮件未设置标题（subject）');

            if(!array_key_exists('text', $this->request) && !array_key_exists('html', $this->request))
                throw new Exception('要发送的邮件未设置内容（text || html）');
        }

        $api = 'mail/xsend.json';

        $this->request['signature'] = $this->buildSignature($this->request);
        $result = $this->httpRequest($api, $this->request);
        $this->request = [];

        return $result;
    }

    public function validate(string $subHookKey) : bool
    {
        $token = $_POST['token'];
        $signature = $_POST['signature'];

        return md5($token . $subHookKey) === $signature;
    }
}
