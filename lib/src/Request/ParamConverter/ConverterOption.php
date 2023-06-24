<?php

namespace App\Request\ParamConverter;

enum ConverterOption: string
{
    case BRANCH = 'branch';
    case COMMIT_HASH = 'commit_hash';
    case BUILD_NUMBER = 'build_number';

    public function default(): string
    {
        return match ($this) {
            self::BRANCH => 'branch',
            self::COMMIT_HASH => 'commitHash',
            self::BUILD_NUMBER => 'buildNumber'
        };
    }

    public function type(): string | array
    {
        return match ($this) {
            self::COMMIT_HASH, self::BRANCH => 'string',
            self::BUILD_NUMBER => ['string', 'int'],
        };
    }
}
