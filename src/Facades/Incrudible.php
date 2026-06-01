<?php

namespace Incrudible\Incrudible\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * TODO: facade doc blocks
 *
 * @method static bool configNotPublished()
 * @method static \App\Incrudible\Models\Admin|null admin()
 * @method static bool check()
 * @method static string routePrefix()
 * @method static string middleware()
 * @method static array<int, class-string|string> middlewareClasses()
 * @method static string guardName()
 * @method static array<string, mixed> menu()
 * @method static array<string, mixed> toArray()
 *
 * @see \Incrudible\Incrudible
 */
class Incrudible extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Incrudible\Incrudible\Incrudible::class;
    }
}
