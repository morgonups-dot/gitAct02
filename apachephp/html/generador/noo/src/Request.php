<?php

class Request
{
    private $get;
    private $post;
    private $data = [];
    private $errors = [];

    public function __construct(array $get, array $post)
    {
        $this->get = $get;
        $this->post = $post;
    }

    public function getInt(string $key, int $default = null): ?int
    {
        $value = $this->post[$key] ?? $this->get[$key] ?? $default;
        if ($value === null) {
            return null;
        }
        $filtered = filter_var($value, FILTER_VALIDATE_INT);
        return $filtered === false ? null : $filtered;
    }

    public function validate(): array
    {
        $this->data = [];
        $this->errors = [];

        $n = $this->getInt('n', 10);
        $min = $this->getInt('min');
        $max = $this->getInt('max');

        if ($n === null || $n < 1 || $n > 1000) {
            $this->errors['n'] = 'N debe ser un entero entre 1 y 1000';
            $n = 10;
        }

        $defaultMin = 1;
        $defaultMax = 10000;

        if ($min !== null && $max !== null) {
            if ($min >= $max) {
                $this->errors['range'] = 'El valor mínimo debe ser menor que el máximo';
                $min = $defaultMin;
                $max = $defaultMax;
            }
        } else {
            $min = $defaultMin;
            $max = $defaultMax;
        }

        $this->data = [
            'n' => $n,
            'min' => $min,
            'max' => $max
        ];

        return ['errors' => $this->errors, 'data' => $this->data];
    }

    public function all(): array
    {
        return $this->data;
    }
}
