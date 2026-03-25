<?php

declare(strict_types=1);

namespace App\Src;

use function array_sum;
use function count;
use InvalidArgumentException;
use function max;
use function min;

class RandomGenerator
{
    private int $n;
    private int $min;
    private int $max;
    private ?array $generated = null;

    public function __construct(int $n, int $min = 1, int $max = 10000)
    {
        if ($n < 1) {
            throw new InvalidArgumentException('n must be at least 1');
        }

        if ($min >= $max) {
            throw new InvalidArgumentException('min must be less than max');
        }

        $this->n = $n;
        $this->min = $min;
        $this->max = $max;
    }

    public function generate(): array
    {
        $this->generated = [];

        foreach (range(1, $this->n) as $i) {
            $this->generated[] = random_int($this->min, $this->max);
        }

        return $this->generated;
    }

    public function getSum(): int
    {
        if ($this->generated === null) {
            $this->generate();
        }

        return array_sum($this->generated);
    }

    public function getAverage(): float
    {
        if ($this->generated === null) {
            $this->generate();
        }

        return array_sum($this->generated) / count($this->generated);
    }

    public function getMin(): int
    {
        if ($this->generated === null) {
            $this->generate();
        }

        return min($this->generated);
    }

    public function getMax(): int
    {
        if ($this->generated === null) {
            $this->generate();
        }

        return max($this->generated);
    }
}
