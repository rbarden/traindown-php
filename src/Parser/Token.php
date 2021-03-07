<?php

declare(strict_types=1);

namespace Traindown\Parser;

class Token
{
    private string $tokenType;
    private array $value;
    private int $position;

    public function __construct(string $tokenType, int $position, ...$value)
    {
        $this->tokenType = $tokenType;
        $this->position = $position;
        $this->value = array_filter(array_map('trim', $value));
    }

    public function isValid(): bool
    {
        return isset($this->tokenType, $this->value);
    }

    public function getType(): string
    {
        return $this->tokenType;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getValue(): array|string
    {
        if (count($this->value) === 1) {
            return $this->value[array_key_first($this->value)];
        }

        return $this->value;
    }

    public function __toString(): string
    {
        return "{$this->tokenType} [" . implode(',', $this->value) . "] at position {$this->position}";
    }
}
