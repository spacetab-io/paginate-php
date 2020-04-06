<?php

declare(strict_types=1);

namespace Spacetab\Paginate\Adapter;

use Amp\Promise;
use Amp\Success;

class ArrayAdapter implements AdapterInterface
{
    /**
     * @var array
     */
    private array $items = [];

    /**
     * ArrayAdapter constructor.
     *
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * @inheritDoc
     */
    public function getCount(): Promise
    {
        return new Success(count($this->items));
    }

    /**
     * @inheritDoc
     */
    public function getSlice($offset, $limit): Promise
    {
        return new Success(array_slice($this->items, $offset, $limit));
    }
}
