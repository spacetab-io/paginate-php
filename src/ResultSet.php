<?php

declare(strict_types=1);

namespace Spacetab\Paginate;

final class ResultSet
{
    /**
     * @var int
     */
    private int $count;

    /**
     * @var array
     */
    private array $items;

    /**
     * @var int
     */
    private int $total;

    /**
     * @var int|null
     */
    private ?int $prev;

    /**
     * @var int|null
     */
    private ?int $next;

    /**
     * ResultSet constructor.
     *
     * @param int $count
     * @param array $items
     * @param int $total
     * @param int|null $prev
     * @param int|null $next
     */
    public function __construct(int $count, array $items, int $total, ?int $prev, ?int $next)
    {
        $this->count = $count;
        $this->items = $items;
        $this->total = $total;
        $this->prev = $prev;
        $this->next = $next;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @return int|null
     */
    public function getPrev(): ?int
    {
        return $this->prev;
    }

    /**
     * @return int|null
     */
    public function getNext(): ?int
    {
        return $this->next;
    }
}
