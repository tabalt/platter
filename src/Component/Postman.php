<?php

namespace Platter\Component;

class Postman
{

    const CHARSET_UTF8 = 'utf-8';

    const CHARSET_GBK = 'gb2312';

    const DEF_MIME_VERSION = '1.0';

    const DEF_CONTENT_TYPE = 'text/plain';

    const HTML_CONTENT_TYPE = 'text/html';

    const ADDR_SPR = ',';

    const HEADER_SPR = "\r\n";

    private $header = array();

    private $from = '';

    private $to = array();

    private $subject = '';

    private $msg = '';

    private $cc = array();

    private $bcc = array();

    private $charset = '';

    private $mimeVersion = '';

    private $contentType = '';

    private $errorMessage = '';

    private function initHeader()
    {
        $this->header = array(
            'MIME-Version' => self::DEF_MIME_VERSION, 
            'Content-type' => self::DEF_CONTENT_TYPE, 
            'Content-Transfer-Encoding' => 'base64', 
            'From' => '', 
            'CC' => '', 
            'BCC' => ''
        );
    }

    private function initParams($from, $to, $subject, $msg, $cc, $bcc, $charset, $mimeVersion, $contentType)
    {
        $this->from = $from;
        $this->to = $to;
        $this->subject = $subject;
        $this->msg = base64_encode($msg);
        $this->cc = $cc;
        $this->bcc = $bcc;
        $this->charset = $charset;
        $this->mimeVersion = $mimeVersion;
        $this->contentType = $contentType;
    }

    private function verifyParams()
    {
        if (! is_string($this->from)) {
            return false;
        }
        if (! is_array($this->to) || empty($this->to)) {
            return false;
        }
        if (! is_string($this->subject) || '' == $this->subject) {
            return false;
        }
        if (! is_string($this->msg) || '' == $this->msg) {
            return false;
        }
        if (! is_array($this->cc)) {
            return false;
        }
        if (! is_array($this->bcc)) {
            return false;
        }
        if (self::CHARSET_UTF8 !== $this->charset && self::CHARSET_GBK !== $this->charset) {
            return false;
        }
        if (! is_string($this->mimeVersion)) {
            return false;
        }
        if (! is_string($this->contentType)) {
            return false;
        }
        return true;
    }

    private function prepareSend()
    {
        $this->header['From'] = $this->from;
        if (! empty($this->cc)) {
            $this->header['CC'] = $this->fmtAddr($this->cc);
        }
        if (! empty($this->bcc)) {
            $this->header['BCC'] = $this->fmtAddr($this->bcc);
        }
        if ('' != $this->mimeVersion) {
            $this->header['MIME-Version'] = $this->mimeVersion;
        }
        if ('' != $this->contentType) {
            $this->header['Content-type'] = $this->contentType;
        }
        $this->header['Content-type'] .= '; charset=' . $this->charset;
    }

    private function fmtAddr($addr)
    {
        return implode(self::ADDR_SPR, $addr);
    }

    private function send()
    {
        $to = $this->fmtAddr($this->to);
        $subject = (self::CHARSET_UTF8 == $this->charset) ? $this->convertSubjectToUtf8() : $this->subject;
        $msg = $this->msg;
        $header = $this->getHeader();
        return mail($to, $subject, $msg, $header);
    }

    private function convertSubjectToUtf8()
    {
        return '=?UTF-8?B?' . base64_encode($this->subject) . '?=';
    }

    private function getHeader()
    {
        $result = array();
        foreach ($this->header as $key => $value) {
            if ('' != $value) {
                $result[] = "$key: $value";
            }
        }
        return implode(self::HEADER_SPR, $result);
    }

    public function __construct()
    {
        $this->initHeader();
    }

    public function isHtml()
    {
        $this->header['Content-type'] = self::HTML_CONTENT_TYPE;
    }

    public function sendMail($from = '', $to = array(), $subject = '', $msg = '', $cc = array(), $bcc = array(), $charset = self::CHARSET_UTF8, $mimeVersion = '', $contentType = '')
    {
        $this->initParams($from, $to, $subject, $msg, $cc, $bcc, $charset, $mimeVersion, $contentType);
        if (false === $this->verifyParams()) {
            $this->setError("Require params are: from, to, subject, msg");
            return false;
        }
        
        $this->prepareSend();
        if (! $this->send()) {
            $this->setError("email send failed");
            return false;
        }
        return true;
    }

    public function setError($message)
    {
        $this->errorMessage = $message;
    }

    public function getError()
    {
        return $this->errorMessage;
    }
}
