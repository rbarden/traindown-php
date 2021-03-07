<?php

declare(strict_types=1);

namespace Traindown\Parser;

use Generator;

class Lexer
{
    private string $data = '';
    private int $dataLength = 0;
    private int $position = 0;
    private string $charAtPosition = '';
    private string $unknownToken = '';
    private bool $continue = true;

    public function setData(string $data): void
    {
        $this->data = $data;
        $this->dataLength = mb_strlen($this->data);
        $this->position = 0;
        $this->charAtPosition = mb_substr($this->data, $this->position, 1);
        $this->unknownToken = '';
    }

    public function lex(): Generator
    {
        while ($this->continue) {
            $this->eatWhitespace();

            $toSearch = mb_substr($this->data, $this->position);

            $found = false;

            foreach (TokenType::RE as $tokenType => $pattern) {
                if (preg_match($pattern, $toSearch, $matches)) {
                    $fullMatch = array_shift($matches);
                    $found = new Token($tokenType, $this->position, ...$matches);

                    $this->incrementPosition(mb_strlen($fullMatch));

                    break;
                }
            }

            if (! $found) {
                $this->unknownToken .= $this->charAtPosition;
                $this->incrementPosition();

                continue;
            }

            yield from $this->yieldUnknownTokenIfPresent();

            yield $found;
        }
       
        yield from $this->yieldUnknownTokenIfPresent();
    }

    private function incrementPosition(int $amount = 1): void
    {
        $this->position += $amount;
        $this->charAtPosition = mb_substr($this->data, $this->position, 1);

        $this->continue = $this->position < $this->dataLength - 1;
    }

    public function eatWhitespace(): void
    {
        while (preg_match('/\s/', $this->charAtPosition)) {
            if ($this->unknownToken) {
                $this->unknownToken .= $this->charAtPosition;
            }
            $this->incrementPosition();
        }
    }

    private function yieldUnknownTokenIfPresent(): Generator
    {
        if ($this->unknownToken) {
            yield new Token(TokenType::T_UNKNOWN, $this->position - mb_strlen($this->unknownToken), $this->unknownToken);
            $this->unknownToken = '';
        }
    }
}
