<?php

namespace Incrudible\Incrudible\Traits;

use Illuminate\Support\Str;
use App\Incrudible\Enum\FieldTypes;
use Illuminate\Support\Facades\Schema;
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

    /**
     * Get the form metadata for the database model.
     */
    public function getFormMetaData(string $table)
    {
        $fields = $this->getFormFields($table);
        $rules = $this->getFormRules($table);

        $metadata = [
            'fields' => [],
            'rules' => $rules,
        ];

        foreach ($fields as $field) {
            $fieldRules = $rules[$field] ?? [];
            $metadata['fields'][] = [
                'name' => $field,
                'type' => $this->getFormFieldType($field, $fieldRules),
                'label' => $this->getFormFieldLabel($field),
                'placeholder' => $this->getFormFieldPlaceholder($field),
                'options' => $this->getFormFieldOptions($field),
                'required' => $this->isFormFieldRequired($field, $fieldRules),
                'rules' => $fieldRules,
            ];
        }

        return $metadata;
    }

    /**
     * Get the form field type for the given field.
     *
     * @param  string  $field
     * @param  array  $rules
     * @return string
     */
    protected static function getFormFieldType($field, $rules)
    {
        // Implement your logic to determine the form field type based on the field
        // For example, you can use the field's data type or any custom logic

        if (in_array($field, ['password', 'password_confirmation'])) {
            return FieldTypes::PASSWORD;
        }

        if (in_array($field, ['email'])) {
            return FieldTypes::EMAIL;
        }

        if (in_array('date', $rules)) {
            return FieldTypes::DATE;
        }

        // Define common date and datetime formats
        $dateFormats = [
            'Y-m-d',
            'd/m/Y',
            'm/d/Y',
            'd-m-Y',
            'm-d-Y',
            'Y/m/d',
        ];

        $dateTimeFormats = [
            'Y-m-d H:i:s',
            'Y-m-d H:i',
        ];

        $timeFormats = [
            'H:i:s',
            'H:i',
        ];

        // Check if the rules contain any date format
        foreach ($rules as $rule) {
            if (strpos($rule, 'date_format:') === 0) {
                $format = str_replace('date_format:', '', $rule);
                if (in_array($format, $dateTimeFormats)) {
                    return FieldTypes::DATETIME;
                }
                if (in_array($format, $dateFormats)) {
                    return FieldTypes::DATE;
                }
                if (in_array($format, $timeFormats)) {
                    return FieldTypes::TIME;
                }
            }
        }

        if (in_array('integer', $rules)) {
            return FieldTypes::NUMBER;
        }

        if (in_array('boolean', $rules)) {
            return FieldTypes::CHECKBOX;
        }

        return FieldTypes::TEXT;
    }

    /**
     * Get the form field label for the given field.
     *
     * @param  string  $field
     * @return string
     */
    protected static function getFormFieldLabel($field)
    {
        // Implement your logic to determine the form field label based on the field
        // For example, you can use the field name or any custom logic

        return ucfirst($field);
    }

    /**
     * Get the form field placeholder for the given field.
     *
     * @param  string  $field
     * @return string
     */
    protected static function getFormFieldPlaceholder($field)
    {
        // Implement your logic to determine the form field placeholder based on the field
        // For example, you can use the field name or any custom logic

        return ucfirst($field);
    }

    /**
     * Get the form field options for the given field.
     *
     * @param  string  $field
     * @return array|null
     */
    protected static function getFormFieldOptions($field)
    {
        // Implement your logic to determine the form field options based on the field
        // For example, if the field represents a foreign key, you can fetch the related model's options

        return null;
    }

    /**
     * Determine if the form field is required for the given field.
     *
     * @param  string  $field
     * @param  array  $rules
     * @return bool
     */
    protected static function isFormFieldRequired($field, $rules)
    {
        // Implement your logic to determine if the form field is required based on the field
        // For example, you can use Laravel's validation rules or any custom logic

        // Log::debug($field, $rules);
        return in_array('required', $rules);
    }
}
