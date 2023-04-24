<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class ViewStoryEnum extends Enum
{
    public const MALE = 1;
    public const FEMALE = 2;
    public const OTHER = 3;
}
