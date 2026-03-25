<?php

declare(strict_types=1);

namespace App\Src;

use function filter_var;
use function is_array;

class Request
{
    private array $get;
    private array $post;

    public function __construct(array $get, array $post)
    {
        $this->get = $get;
        $this->post = $post;
    }

    public function getInt(string $key, int $default = null): ?int
    {
        $value = $this->post[$key] ?? $this->get[$key] ?? null;

        if ($value === null) {
            return $default;
        }

        $parsed = filter_var($value, FILTER_VALIDATE_INT);

        return $parsed === false ? $default : $parsed;
    }

    public function validateN(int $minAllowed, int $maxAllowed): array
    {
        $errors = [];
        $n = $this->getInt('n');

        if ($n === null) {
            $errors['n'] = 'El campo n es requerido.';
        } elseif ($n < $minAllowed || $n > $maxAllowed) {
            $errors['n'] = "El valor de n debe estar entre {$minAllowed} y {$maxAllowed}.";
        }

        $min = $this->getInt('min');
        $max = $this->getInt('max');

        if ($min !== null && $max !== null && $min >= $max) {
            $errors['range'] = 'El valor mínimo debe ser menor que el máximo.';
        }

        return $errors;
    }

    public function all(): array
    {
        return array_merge($this->get, $this->post);
    }
}
