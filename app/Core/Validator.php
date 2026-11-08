<?php

declare(strict_types=1);

namespace App\Core;

class Validator
{
    private array $data;
    private array $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public static function make(array $data, array $rules): self
    {
        $validator = new self($data);
        $validator->validate($rules);
        return $validator;
    }

    public function validate(array $rules): void
    {
        foreach ($rules as $field => $fieldRules) {
            $value = $this->data[$field] ?? null;
            $rulesList = is_string($fieldRules) ? explode('|', $fieldRules) : $fieldRules;

            foreach ($rulesList as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    [$ruleName, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                } else {
                    $ruleName = $rule;
                }

                $this->applyRule($field, $value, $ruleName, $params);
            }
        }
    }

    private function applyRule(string $field, mixed $value, string $rule, array $params): void
    {
        switch ($rule) {
            case 'required':
                if ($value === null || $value === '' || (is_array($value) && empty($value))) {
                    $this->addError($field, "El campo {$field} es obligatorio.");
                }
                break;

            case 'email':
                if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "El campo {$field} debe ser un correo electrónico válido.");
                }
                break;

            case 'min':
                $min = (int)($params[0] ?? 0);
                if (is_int($value) || is_float($value)) {
                    if ($value < $min) {
                        $this->addError($field, "El campo {$field} debe ser como mínimo {$min}.");
                    }
                } elseif (is_string($value)) {
                    if (mb_strlen($value) < $min) {
                        $this->addError($field, "El campo {$field} debe tener al menos {$min} caracteres.");
                    }
                }
                break;

            case 'max':
                $max = (int)($params[0] ?? 0);
                if (is_int($value) || is_float($value)) {
                    if ($value > $max) {
                        $this->addError($field, "El campo {$field} no puede ser mayor que {$max}.");
                    }
                } elseif (is_string($value)) {
                    if (mb_strlen($value) > $max) {
                        $this->addError($field, "El campo {$field} no puede exceder {$max} caracteres.");
                    }
                }
                break;

            case 'numeric':
                if ($value !== null && $value !== '' && !is_numeric($value)) {
                    $this->addError($field, "El campo {$field} debe ser numérico.");
                }
                break;

            case 'slug':
                if ($value !== null && $value !== '') {
                    if (!preg_match('/^[a-z0-9]+(-[a-z0-9]+)*$/', (string)$value)) {
                        $this->addError($field, "El slug solo puede contener letras minúsculas, números y guiones sencillos.");
                    }
                    $reserved = require dirname(__DIR__, 2) . '/config/slugs_reservados.php';
                    if (in_array(strtolower((string)$value), $reserved, true)) {
                        $this->addError($field, "El slug indicado es una palabra reservada del sistema.");
                    }
                }
                break;

            case 'hex_color':
                if ($value !== null && $value !== '' && !preg_match('/^#[0-9a-fA-F]{6}$/', (string)$value)) {
                    $this->addError($field, "El color debe ser un valor hexadecimal válido (ej. #E85D04).");
                }
                break;

            case 'in':
                if ($value !== null && $value !== '' && !in_array((string)$value, $params, true)) {
                    $this->addError($field, "El valor seleccionado para {$field} no es válido.");
                }
                break;
        }
    }

    private function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function fails(): bool
    {
        return !$this->passes();
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        if (empty($this->errors)) {
            return null;
        }
        $firstField = array_key_first($this->errors);
        return $this->errors[$firstField][0] ?? null;
    }
}
