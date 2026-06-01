<?php

namespace Incrudible\Incrudible\Schema;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SchemaRulesResolver
{
    public function __construct(private string $table) {}

    public function generate(): array
    {
        $columns = Schema::getColumns($this->table);
        $rules = [];

        foreach ($columns as $column) {
            if ($column['auto_increment']) {
                continue;
            }

            $rules[$column['name']] = $this->rulesForColumn($column);
        }

        return $rules;
    }

    private function rulesForColumn(array $column): array
    {
        $name = $column['name'];
        $typeName = strtolower($column['type_name']);
        $type = strtolower($column['type'] ?? $typeName);
        $nullable = $column['nullable'];

        $rules = $this->baseRules($typeName, $type, $nullable);

        if (Str::contains($name, 'password')) {
            $rules[] = 'min:8';
            $rules[] = 'confirmed';
        } elseif (Str::endsWith($name, '_email') || $name === 'email') {
            $rules[] = 'email';
        } elseif (Str::endsWith($name, '_id')) {
            $related = Str::plural(Str::beforeLast($name, '_id'));
            $rules[] = "exists:{$related},id";
        }

        return $rules;
    }

    private function baseRules(string $typeName, string $type, bool $nullable): array
    {
        $required = $nullable ? 'nullable' : 'required';

        // tinyint(1) is stored boolean in MySQL; boolean is native in PostgreSQL
        if (($typeName === 'tinyint' && str_contains($type, '(1)')) || $typeName === 'boolean') {
            return [$required, 'boolean'];
        }

        return match (true) {
            in_array($typeName, ['varchar', 'string', 'char', 'nvarchar']) => [$required, 'string', 'max:255'],
            in_array($typeName, ['text', 'mediumtext', 'longtext', 'tinytext', 'ntext']) => ['nullable', 'string'],
            in_array($typeName, ['integer', 'int', 'bigint', 'smallint', 'mediumint', 'tinyint']) => [$required, 'integer'],
            $typeName === 'date' => [$required, 'date'],
            in_array($typeName, ['datetime', 'timestamp', 'datetime2']) => [$required, 'date_format:Y-m-d H:i:s'],
            in_array($typeName, ['decimal', 'float', 'double', 'numeric', 'real', 'money']) => [$required, 'numeric'],
            default => [$required, 'string'],
        };
    }
}
