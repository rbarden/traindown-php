<?php

declare(strict_types=1);

namespace Traindown\Traindown\Meta;

trait HasMetadata
{
    protected array $metadata = [];
    protected array $notes = [];

    public function getMetaData(): array
    {
        return $this->metadata;
    }

    public function getNotes(): array
    {
        return $this->notes;
    }

    public function addMetaData(Data $data): void
    {
        $this->metadata[] = $data;
    }

    public function addNote(Note $note): void
    {
        $this->notes[] = $note;
    }
}
