<?php

namespace Incrudible\Incrudible\Traits;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use LaracraftTech\LaravelSchemaRules\Contracts\SchemaRulesResolverInterface;

trait GeneratesFormRules
{
    /**
     * Get the form fields for the given table.
     */
    public function getFormFields(string $table): array
    {
        return Schema::getColumnListing($table);
    }

    /**
     * Get the form rules for the given table.
     */
    public function getFormRules(string $table, ?string $filter = null): array
    {
        $rules = app()->make(SchemaRulesResolverInterface::class, [
            'table' => $table,
            'columns' => [],
        ])->generate();

        if ($filter) {
            $rules = collect($rules)->filter(function ($rules) use ($filter) {
                return in_array($filter, $rules);
            })->toArray();
        }

        return $rules;
    }

    /**
     * Augment the form rules with additional validation rules.
     */
    public function augmentFormRules(array $fields, string $table): array
    {
        foreach ($fields as $field => $rules) {
            if (Str::contains($field, 'password')) {
                $fields[$field] = array_merge($rules, ['required', 'min:8']);
            }

            if ($field === 'email') {
                $fields[$field] = array_merge($rules, ['email', "unique:$table,email"]);
            }

            if (Str::contains($field, ['date', '_at'])) {
                $fields[$field] = array_merge($rules, ['date_format:Y-m-d H:i:s']);
            }
        }

        return $fields;
    }
}
