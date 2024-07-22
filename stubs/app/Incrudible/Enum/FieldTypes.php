<?php

namespace App\Incrudible\Enum;

enum FieldTypes: string
{
    case TEXT = 'text';
    case NUMBER = 'number';
    case EMAIL = 'email';
    case PASSWORD = 'password';
    case CHECKBOX = 'checkbox';
    case RADIO = 'radio';
    case SELECT = 'select';
    case TEXTAREA = 'textarea';
    case FILE = 'file';
    case IMAGE = 'image';
    case DATE = 'date';
    case TIME = 'time';
    case DATETIME = 'datetime';
    case COLOR = 'color';
    case URL = 'url';
    case TEL = 'tel';
    case RANGE = 'range';
}
