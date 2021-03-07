<?php

declare(strict_types=1);

namespace Traindown\Traindown;

use Traindown\Parser\Token;
use Traindown\Traindown\Meta\HasMetadata;

class Movement
{
    use HasMetadata;

    private string $name;
    private bool $superset;
    private int $sequence;
    private array $performances = [];

    public function __construct(Token $token, int $sequence)
    {
        if (! is_array($value = $token->getValue())) {
            $value = [$value];
            array_unshift($value, false);
        }
        [$this->superset, $this->name] = $value;
        $this->sequence = $sequence;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isSuperset(): bool
    {
        return $this->superset;
    }

    public function getSequence(): int
    {
        return $this->sequence;
    }

    public function getPerformances(): array
    {
        return $this->performances;
    }

    public function addPerformance(Performance $performance): void
    {
        $this->performances[] = $performance;
    }
}
