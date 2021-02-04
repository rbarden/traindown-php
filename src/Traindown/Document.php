<?php

declare(strict_types=1);

namespace Rbarden\Traindown\Traindown;

use Rbarden\Traindown\Traindown\Meta\HasMetadata;

class Document
{
    use HasMetadata;

    private array $sessions = [];

    public function getSessions(): array
    {
        return $this->sessions;
    }

    public function addSession(Session $session): void
    {
        $this->sessions[] = $session;
    }
}
