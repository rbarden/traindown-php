<?php

declare(strict_types=1);

namespace Traindown\Exceptions;

use Exception;
use Traindown\Parser\Token;

class BadTokenException extends Exception
{
    private Token $token;

    public function __construct(Token $token)
    {
        $this->token = $token;

        parent::__construct((string) $token);
    }

    public function getToken(): Token
    {
        return $this->token;
    }
}
