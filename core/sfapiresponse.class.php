<?php

class SFApiResponse
{

    protected $errorCode = 0;
    protected $errorMessage = '';
    protected $data = [];

    public function setError($code, $message = '')
    {
        $this->errorCode = $code;
        $this->errorMessage = $message;
        return $this;
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    public static function fromMixed($mixed)
    {
        if (is_scalar($mixed)) {
            return (new static)->set('result', $mixed);
        }
        if (is_array($mixed)) {
            $self = new static;
            $self->data = $mixed;
            if (isset($mixed['error']['code'])) {
                $self->data['error']['code'] = (int)$mixed['error']['code'];
                $self->data['error']['message'] = (string)($mixed['error']['message'] ?? null);
            }
            return $self;
        }
        if (is_object($mixed)) {
            if ($mixed instanceof self) {
                return $mixed;
            }
            throw new Exception('Unsupported response type ' . get_class($mixed), 800);
        }
        throw new Exception('Unsupported response: ' . substr(var_export($mixed, true), 0, 200), 801);
    }

    public function toArray()
    {
        $result = [
                'error' => [
                    'code' => $this->errorCode,
                    'text' => $this->errorMessage,
                ]
            ] + $this->data;
        if (SFConfig::$debugMode) {
            $result['debug']['sql'] = SFDB::getDebugQueries();
        }
        return $result;
    }
}