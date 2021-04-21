<?php
declare(strict_types=1);

class Collection implements \IteratorAggregate {

    /** @var callable */
    protected $valuesGenerator;

    protected function __construct(){}

    public static function fromIterable(iterable $values = []) {
        return static::fromGenerator(function () use ($values) {
            yield from $values;
        });
    }

    public static function fromGenerator(callable $callback) : self {
        $new = new static();
        $new->valuesGenerator = $callback;
        return $new;
    }

    public function getIterator() : iterable {
        return ($this->valuesGenerator)();
    }

    public function append(iterable ...$collections) : self {
        return static::fromGenerator(function() use ($collections) {
            yield from ($this->valuesGenerator)();
            foreach ($collections as $col) {
                yield from $col;
            }
        });
    }

    public function add(...$items) : self {
        return $this->append($items);
    }

    public function map(callable $fn) : self {
        return static::fromGenerator(function () use ($fn) {
            foreach (($this->valuesGenerator)() as $key => $val) {
                yield $key => $fn($val, $key);
            }
        });
    }

    public function toArray() : array {
        return iterator_to_array($this, false);
    }
}

$c = Collection::fromIterable([1, 2, 3]);

$c = $c->add(4, 5, 6);
$c = $c->map(fn($v) => $v * 10);

print_r($c->toArray());

