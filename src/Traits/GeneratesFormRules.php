<?php

namespace Incrudible\Incrudible\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use LaracraftTech\LaravelSchemaRules\Contracts\SchemaRulesResolverInterface;

trait GeneratesFormRules
{
    /**
     * Get the form fields for the given table.
     *
     * @param string $table
     * @return array
     */
    public function getFormFields(string $table): array
    {
        return Schema::getColumnListing($table);
    }

    /**
     * Get the form rules for the given table.
     *
     * @param string $table
     * @return array
     */
    public function getFormRules(string $table): array
    {
        return app()->make(SchemaRulesResolverInterface::class, [
            'table' => $table,
            'columns' => [],
        ])->generate();
    }

    /**
     * Augment the form rules with additional validation rules.
     *
     * @param array $fields
     * @return array
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
