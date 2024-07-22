<?php

namespace App\Incrudible\Filters;

use Closure;

use Illuminate\Database\Eloquent\Builder;

class SearchFilter
{
    protected $search;
    protected $fields;

    public function __construct($search,  $fields)
    {
        $this->search = $search;
        $this->fields = $fields;
    }

    public function handle($query, Closure $next)
    {
        if ($this->search) {
            return $query
                ->where(function (Builder $query) {
                    $query->where($this->fields[0], 'like', "%{$this->search}%");
                    for ($i = 1; $i < sizeof($this->fields); $i++) {
                        $query->orWhere($this->fields[$i], 'like', "%{$this->search}%");
                    }
                });
        }

        return $next($query);
    }
}
