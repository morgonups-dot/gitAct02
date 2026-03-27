<?php

class RandomGenerator
{
    private $n;
    private $min;
    private $max;
    private $numbers = [];

    public function __construct(int $n, int $min = 1, int $max = 10000)
    {
        $this->n = $n;
        $this->min = $min;
        $this->max = $max;
    }

    public function generate(): array
    {
        $this->numbers = [];
        for ($i = 0; $i < $this->n; $i++) {
            $this->numbers[] = random_int($this->min, $this->max);
        }
        return $this->numbers;
    }

    public function getSum(): int
    {
        $sum = 0;
        foreach ($this->numbers as $num) {
            $sum += $num;
        }
        return $sum;
    }

    public function getAverage(): float
    {
        if (empty($this->numbers)) {
            return 0.0;
        }
        return $this->getSum() / count($this->numbers);
    }

    public function getMin(): int
    {
        if (empty($this->numbers)) {
            return 0;
        }
        $min = $this->numbers[0];
        foreach ($this->numbers as $num) {
            if ($num < $min) {
                $min = $num;
            }
        }
        return $min;
    }

    public function getMax(): int
    {
        if (empty($this->numbers)) {
            return 0;
        }
        $max = $this->numbers[0];
        foreach ($this->numbers as $num) {
            if ($num > $max) {
                $max = $num;
            }
        }
        return $max;
    }
}
