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
    const MALE = 2;
    const FEMALE = 3;
    const OTHER = 1;
}
