<?php

namespace FireAPI;

class Credentials
{
    private string $token {
        get {
            return $this->token;
        }
    }
    public string $baseUrl {
        get {
            return $this->baseUrl;
        }
    }

    /**
     * @param string $token
     * @param bool $sandbox
     */
    public function __construct(string $token, bool $sandbox = false)
    {
        $this->token = $token;
        $this->baseUrl = $sandbox ? "https://sandbox.fireapi.de" : "https://live.fireapi.de";
    }

}