<?php

declare(strict_types=1);

namespace Rbarden\Traindown\Traindown\Meta;

use Rbarden\Traindown\Parser\Token;

class Data
{
    private string $key;
    private string $value;

    public function __construct(Token $token)
    {
        [$this->key, $this->value] = $token->getValue();
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getDataPair(): array
    {
        return [
            $this->key => $this->value,
        ];
    }
}
