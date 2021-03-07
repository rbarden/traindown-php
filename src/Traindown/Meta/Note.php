<?php

declare(strict_types=1);

namespace Traindown\Traindown\Meta;

use Traindown\Parser\Token;

class Note
{
    private string $value;

    public function __construct(Token $token)
    {
        $this->value = $token->getValue();
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
