<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Incrudible\Incrudible\Schema\SchemaRulesResolver;

beforeEach(function () {
    Schema::dropIfExists('test_schema_rules');
});

afterEach(function () {
    Schema::dropIfExists('test_schema_rules');
});

it('generates required string rules for varchar columns', function () {
    Schema::create('test_schema_rules', function (Blueprint $table) {
        $table->string('name');
    });

    $rules = (new SchemaRulesResolver('test_schema_rules'))->generate();

    expect($rules['name'])->toBe(['required', 'string', 'max:255']);
});

it('generates nullable string rules for nullable varchar columns', function () {
    Schema::create('test_schema_rules', function (Blueprint $table) {
        $table->string('nickname')->nullable();
    });

    $rules = (new SchemaRulesResolver('test_schema_rules'))->generate();

    expect($rules['nickname'])->toBe(['nullable', 'string', 'max:255']);
});

it('generates nullable string rules for text columns', function () {
    Schema::create('test_schema_rules', function (Blueprint $table) {
        $table->text('bio');
    });

    $rules = (new SchemaRulesResolver('test_schema_rules'))->generate();

    expect($rules['bio'])->toBe(['nullable', 'string']);
});

it('generates required integer rules for integer columns', function () {
    Schema::create('test_schema_rules', function (Blueprint $table) {
        $table->integer('age');
    });

    $rules = (new SchemaRulesResolver('test_schema_rules'))->generate();

    expect($rules['age'])->toBe(['required', 'integer']);
});

it('generates nullable integer rules for nullable integer columns', function () {
    Schema::create('test_schema_rules', function (Blueprint $table) {
        $table->integer('score')->nullable();
    });

    $rules = (new SchemaRulesResolver('test_schema_rules'))->generate();

    expect($rules['score'])->toBe(['nullable', 'integer']);
});

it('generates boolean rules for boolean columns', function () {
    Schema::create('test_schema_rules', function (Blueprint $table) {
        $table->boolean('is_active');
    });

    $rules = (new SchemaRulesResolver('test_schema_rules'))->generate();

    expect($rules['is_active'])->toBe(['required', 'boolean']);
});

it('generates date rules for date columns', function () {
    Schema::create('test_schema_rules', function (Blueprint $table) {
        $table->date('birth_date');
    });

    $rules = (new SchemaRulesResolver('test_schema_rules'))->generate();

    expect($rules['birth_date'])->toBe(['required', 'date']);
});

it('generates datetime rules for datetime columns', function () {
    Schema::create('test_schema_rules', function (Blueprint $table) {
        $table->dateTime('published_at');
    });

    $rules = (new SchemaRulesResolver('test_schema_rules'))->generate();

    expect($rules['published_at'])->toBe(['required', 'date_format:Y-m-d H:i:s']);
});

it('generates numeric rules for decimal columns', function () {
    Schema::create('test_schema_rules', function (Blueprint $table) {
        $table->decimal('price', 10, 2);
    });

    $rules = (new SchemaRulesResolver('test_schema_rules'))->generate();

    expect($rules['price'])->toBe(['required', 'numeric']);
});

it('skips auto_increment columns', function () {
    Schema::create('test_schema_rules', function (Blueprint $table) {
        $table->id();
        $table->string('name');
    });

    $rules = (new SchemaRulesResolver('test_schema_rules'))->generate();

    expect($rules)->not->toHaveKey('id');
    expect($rules)->toHaveKey('name');
});

it('appends email rule for email columns', function () {
    Schema::create('test_schema_rules', function (Blueprint $table) {
        $table->string('email');
    });

    $rules = (new SchemaRulesResolver('test_schema_rules'))->generate();

    expect($rules['email'])->toContain('email');
});

it('appends email rule for columns ending in _email', function () {
    Schema::create('test_schema_rules', function (Blueprint $table) {
        $table->string('work_email');
    });

    $rules = (new SchemaRulesResolver('test_schema_rules'))->generate();

    expect($rules['work_email'])->toContain('email');
});

it('appends password rules for columns containing password', function () {
    Schema::create('test_schema_rules', function (Blueprint $table) {
        $table->string('password');
    });

    $rules = (new SchemaRulesResolver('test_schema_rules'))->generate();

    expect($rules['password'])->toContain('min:8');
    expect($rules['password'])->toContain('confirmed');
});

it('appends exists rule for columns ending in _id', function () {
    Schema::create('test_schema_rules', function (Blueprint $table) {
        $table->unsignedBigInteger('user_id');
    });

    $rules = (new SchemaRulesResolver('test_schema_rules'))->generate();

    expect($rules['user_id'])->toContain('exists:users,id');
});
