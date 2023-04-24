<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class StatusStoryEnum extends Enum
{
    public const NEW = 1;
    public const RELEASING = 2;
    public const FINISHED = 3;
}
