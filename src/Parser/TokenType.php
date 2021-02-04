<?php

declare(strict_types=1);

namespace Rbarden\Traindown\Parser;

class TokenType
{
    public const T_DATETIME = 'T_DATETIME';
    public const T_METADATA = 'T_METADATA';
    public const T_NOTE = 'T_NOTE';
    public const T_MOVEMENT = 'T_MOVEMENT';
    public const T_LOAD = 'T_LOAD';
    public const T_REP = 'T_REP';
    public const T_FAIL = 'T_FAIL';
    public const T_SET = 'T_SET';

    public const T_UNKNOWN = 'T_UNKNOWN';

    public const RE = [
        self::T_DATETIME => '/^@\s*(.*?)[\r?\n;]+/',
        self::T_METADATA => '/^#\s*(.*?):(.*?)[\r?\n;]+/',
        self::T_NOTE => '/^\*\s*(.*?)[\r?\n;]+/',
        self::T_MOVEMENT => '/^(\+?)\s*(\S*\s*\S*)\s*:/',
        self::T_LOAD => '/^((?:\d*\.)?\d++)(?![fFrRsS])|^([bB][wW](?:\+\d+)?)/',
        self::T_REP => '/^((?:\d*\.)?\d++)[rR]/',
        self::T_FAIL => '/^((?:\d*\.)?\d++)[fF]/',
        self::T_SET => '/^((?:\d*\.)?\d++)[sS]/',
    ];
}
