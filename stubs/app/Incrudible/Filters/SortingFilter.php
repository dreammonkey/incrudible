<?php

namespace App\Incrudible\Filters;

use Closure;

class SortingFilter
{
    protected $orderBy;
    protected $orderDir;

    public function __construct($orderBy, $orderDir)
    {
        $this->orderBy = $orderBy;
        $this->orderDir = $orderDir;
    }

    public function handle($query, Closure $next)
    {
        if ($this->orderBy && $this->orderDir) {
            $query->orderBy($this->orderBy, $this->orderDir);
        }

        return $next($query);
    }
}
