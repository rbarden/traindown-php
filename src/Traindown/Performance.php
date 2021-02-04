<?php

declare(strict_types=1);

namespace Rbarden\Traindown\Traindown;

use Rbarden\Traindown\Parser\Token;
use Rbarden\Traindown\Traindown\Meta\HasMetadata;

class Performance
{
    use HasMetadata;

    private float|string|null $load = null;
    private float|null $reps = null;
    private float|null $sets = null;
    private float|null $fails = null;

    public function getLoad(): float|string|null
    {
        return $this->load ?? null;
    }

    public function getReps(): float
    {
        return $this->reps ?? 1.0;
    }

    public function getSets(): float
    {
        return $this->sets ?? 1.0;
    }

    public function getFails(): float
    {
        return $this->fails ?? 0.0;
    }

    public function getRaw(string $prop): ?float
    {
        return $this->{$prop};
    }

    public function getCompletedReps(): float
    {
        return ($this->reps ?? 1.0) - ($this->fails ?? 0.0);
    }

    public function setLoad(Token $token): void
    {
        $this->load = $token->getValue();
    }

    public function setReps(Token $token): void
    {
        $this->reps = (float)$token->getValue();
    }

    public function setSets(Token $token): void
    {
        $this->sets = (float)$token->getValue();
    }

    public function setFails(Token $token): void
    {
        $this->fails = (float)$token->getValue();
    }

    public function isValid(): bool
    {
        return isset($this->load);
    }
}
