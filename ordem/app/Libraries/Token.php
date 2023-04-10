<?php

namespace App\Libraries;

class Token
{
    private $token;

    /**
     * Método construtor da classe
     *
     * @param string|null $token
     */
    public function __construct(string $token = null)
    {
        if ($token === null) {
            $this->token = bin2hex(random_bytes(16));
        }else{
            $this->token = $token;
        }
    }

    /**
     * Método que retorna o valor do token.
     *
     * @return string
     */
    public function getValue():string
    {
        return $this->token;
    }

    public function getHash():string
    {
        return hash_hmac("sha256", $this->token, getenv('CHAVE_RECUPERACAO_SENHA'));
    }
}