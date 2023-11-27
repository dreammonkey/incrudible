<?php

namespace Incrudible\Incrudible\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * TODO: facade doc blocks
 * 
 * @method static \Incrudible\Incrudible\Incrudible configNotPublished()
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
