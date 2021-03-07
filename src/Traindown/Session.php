<?php

declare(strict_types=1);

namespace Traindown\Traindown;

use DateTime;
use Traindown\Parser\Token;
use Traindown\Traindown\Meta\HasMetadata;
use Throwable;

class Session
{
    use HasMetadata;

    private DateTime $date;
    private bool $defaultDate = false;
    private array $movements = [];

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function hasDefaultDate(): bool
    {
        return $this->defaultDate;
    }

    public function getMovements(): array
    {
        return $this->movements;
    }

    public function setDateTime(Token $token): void
    {
        try {
            $this->date = new DateTime($token->getValue());
        } catch (Throwable) {
            $this->defaultDate = true;
            $this->date = new DateTime();
        }
    }

    public function addMovement(Movement $movement): void
    {
        $this->movements[] = $movement;
    }
}
