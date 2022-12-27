<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class GenreType extends Enum
{
    const CATEGORY = 1;
    const CHARACTER = 2;
    const WORLD = 3;
    const TAG = 4;
}
