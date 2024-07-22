<?php

namespace App\Incrudible\Traits;

use App\Incrudible\Enum\FieldTypes;
use Incrudible\Incrudible\Traits\GeneratesFormRules;

trait FormBuilder
{
    use GeneratesFormRules;

    /**
     * Get the form metadata for the model.
     *
     * @return array
     */
    public function getFormMetaData()
    {
        $fields = $this->getFormFields('admins');
        $rules = $this->getFormRules('admins');
        // dd($rules);

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

    public function generateFormMetadata($rules)
    {
        $metadata = [
            'fields' => [],
            'rules' => $rules,
        ];

        foreach ($rules as $field => $fieldRules) {
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
     * @param string $field
     * @param array $rules
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

        return FieldTypes::TEXT;
    }

    /**
     * Get the form field label for the given field.
     *
     * @param string $field
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
     * @param string $field
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
     * @param string $field
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
     * @param string $field
     * @param array $rules
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
