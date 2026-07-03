<?php

namespace Kernery\Main\Enums;

use Illuminate\Support\HtmlString;
use Kernery\Main\Facades\Html;
use Kernery\Main\Supports\Enum;

/**
 * @method static MainStatusEnum DRAFT()
 * @method static MainStatusEnum PUBLISHED()
 * @method static MainStatusEnum PENDING()
 */
class MainStatusEnum extends Enum
{
    public const PUBLISHED = 'published';

    public const DRAFT = 'draft';

    public const PENDING = 'pending';

    public static $langPath = 'core/main::enums.statuses';

    public function toHtml(): string | HtmlString
    {
        return match ($this->value) {
            self::DRAFT => Html::tag('span', self::DRAFT()->label(), ['class' => 'badge bg-secondary text-secondary-fg']),
            self::PENDING => Html::tag('span', self::PENDING()->label(), ['class' => 'badge bg-warning text-warning-fg']),
            self::PUBLISHED => Html::tag('span', self::PUBLISHED()->label(), ['class' => 'badge bg-success text-success-fg']),
            default => parent::toHtml(),
        };
    }
}
