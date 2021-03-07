<?php

declare(strict_types=1);

namespace Traindown\Parser;

use Traindown\Exceptions\BadTokenException;
use Traindown\Traindown\Document;
use Traindown\Traindown\Meta\Data;
use Traindown\Traindown\Meta\Note;
use Traindown\Traindown\Movement;
use Traindown\Traindown\Performance;
use Traindown\Traindown\Session;

class Parser
{
    private Lexer $lexer;

    private const S_DOCUMENT = 'document';
    private const S_SESSION = 'session';
    private const S_MOVEMENT = 'movement';
    private const S_PERFORMANCE = 'performance';

    private string $scope = self::S_DOCUMENT;

    private Document $document;
    private ?Session $session = null;
    private ?Movement $movement = null;
    private ?Performance $performance = null;

    private int $movementSequence = 1;

    private bool $throwOnBadToken;
    private array $badTokens = [];

    public function __construct(bool $throwOnBadToken = false)
    {
        $this->lexer = new Lexer();
        $this->throwOnBadToken = $throwOnBadToken;
    }

    public function parse(string $data): Document
    {
        $this->resetState();

        $this->lexer->setData($data);

        foreach ($this->lexer->lex() as $token) {
            echo $token . "\n";
            switch ($token->getType()) {
                case TokenType::T_DATETIME:
                    $this->session = new Session();
                    $this->session->setDateTime($token);
                    $this->document->addSession($this->session);

                    $this->movementSequence = 1;

                    $this->scope = self::S_SESSION;

                    break;
                case TokenType::T_METADATA:
                    $metadata = new Data($token);
                    $this->{$this->scope}->addMetaData($metadata);

                    break;
                case TokenType::T_NOTE:
                    $note = new Note($token);
                    $this->{$this->scope}->addNote($note);

                    break;
                case TokenType::T_MOVEMENT:
                    if (! $this->session) {
                        $this->handleBadToken($token);

                        break;
                    }

                    $this->movement = new Movement($token, $this->movementSequence++);
                    $this->performance = new Performance();
                    $this->movement->addPerformance($this->performance);
                    $this->session->addMovement($this->movement);

                    $this->scope = self::S_MOVEMENT;

                    break;
                case TokenType::T_LOAD:
                    if (! $this->movement) {
                        $this->handleBadToken($token);

                        break;
                    }

                    $this->setPerformanceIfNeeded('load');
                    $this->performance->setLoad($token);

                    break;
                case TokenType::T_REP:
                    if (! $this->movement) {
                        $this->handleBadToken($token);

                        break;
                    }

                    $this->setPerformanceIfNeeded('reps');
                    $this->performance->setReps($token);

                    break;
                case TokenType::T_SET:
                    if (! $this->movement) {
                        $this->handleBadToken($token);

                        break;
                    }

                    $this->setPerformanceIfNeeded('sets');
                    $this->performance->setSets($token);

                    break;
                case TokenType::T_FAIL:
                    if (! $this->movement) {
                        $this->handleBadToken($token);

                        break;
                    }

                    $this->setPerformanceIfNeeded('fails');
                    $this->performance->setFails($token);

                    break;
                case TokenType::T_UNKNOWN:
                default:
                    $this->handleBadToken($token);

                    continue 2;
            }
        }

        return $this->document;
    }

    private function setPerformanceIfNeeded(string $prop): void
    {
        if ($this->performance->getRaw($prop)) {
            $this->performance = new Performance();
            $this->movement->addPerformance($this->performance);
            $this->scope = self::S_PERFORMANCE;
        }
    }

    public function handleBadToken(Token $token): void
    {
        $this->badTokens[] = $token;
        if ($this->throwOnBadToken) {
            throw new BadTokenException($token);
        }
    }

    public function hasBadTokens(): bool
    {
        return ! empty($this->badTokens);
    }

    public function getBadTokens(): array
    {
        return $this->badTokens;
    }

    private function resetState(): void
    {
        $this->document = new Document();
        $this->session = null;
        $this->movement = null;
        $this->performance = null;
        
        $this->movementSequence = 1;
        
        $this->badTokens = [];
        
        $this->scope = self::S_DOCUMENT;
    }
}
