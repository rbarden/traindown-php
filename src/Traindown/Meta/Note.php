<?php

declare(strict_types=1);

namespace Rbarden\Traindown\Traindown\Meta;

use Rbarden\Traindown\Parser\Token;

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
