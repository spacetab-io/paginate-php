<?php

declare(strict_types=1);

namespace Spacetab\Paginate;

use Amp\Promise;
use Spacetab\Paginate\Adapter\AdapterInterface;

use function Amp\call;

class Paginator
{
    public const PAGINATION_DEFAULT_PAGE         = 1;
    public const PAGINATION_DEFAULT_PER_PAGE     = 15;
    public const PAGINATION_DEFAULT_PER_PAGE_MAX = 1000;

    /**
     * @var \Spacetab\Paginate\Adapter\AdapterInterface
     */
    private AdapterInterface $adapter;

    /**
     * @var int
     */
    private int $page = self::PAGINATION_DEFAULT_PAGE;

    /**
     * @var int
     */
    private int $perPage = self::PAGINATION_DEFAULT_PER_PAGE;

    /**
     * @var int
     */
    private int $perPageMax = self::PAGINATION_DEFAULT_PER_PAGE_MAX;

    /**
     * Paginator constructor.
     *
     * @param \Spacetab\Paginate\Adapter\AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param mixed $value
     */
    public function setPage($value): void
    {
        $this->page = $this->getSafeIntValue($value);
    }

    /**
     * @param mixed $value
     */
    public function setPerPage($value): void
    {
        if (empty($value)) {
            $this->perPage = $this->perPageMax;
            return;
        }

        $value = $this->getSafeIntValue($value);

        if ($value >= $this->perPageMax) {
            $value = $this->perPageMax;
        }

        $this->perPage = $value;
    }

    /**
     * @param mixed $perPageMax
     */
    public function setPerPageMax($perPageMax): void
    {
        $this->perPageMax = $this->getSafeIntValue($perPageMax);
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * @return int
     */
    public function getPerPageMax(): int
    {
        return $this->perPageMax;
    }

    /**
     * @return \Amp\Promise
     */
    public function doPaginate(): Promise
    {
        return call(function () {
            $count = yield $this->adapter->getCount();
            $totalPages = $this->calculateTotalPages($count);
            $offset = $this->calculateOffset();
            $prevNum = $this->calculatePrevPage();
            $nextNum = $this->calculateNextPage($totalPages);
            $items = yield $this->adapter->getSlice($offset, $this->perPage);

            return new ResultSet($count, $items, $totalPages, $prevNum, $nextNum);
        });
    }

    /**
     * @param int $recordsCount
     * @return int
     */
    private function calculateTotalPages(int $recordsCount): int
    {
        return (int) ceil($recordsCount / $this->perPage);
    }

    /**
     * @return int
     */
    private function calculateOffset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }

    /**
     * @return int|null
     */
    private function calculatePrevPage(): ?int
    {
        return ($this->page > 1) ? $this->page - 1 : null;
    }

    /**
     * @param int $totalPages
     * @return int|null
     */
    private function calculateNextPage(int $totalPages): ?int
    {
        return ($this->page < $totalPages) ? $this->page + 1 : null;
    }

    /**
     * @param mixed $value
     * @return int
     */
    private function getSafeIntValue($value): int
    {
        if ($value < 1) {
            $value = 1;
        }

        if ($value >= PHP_INT_MAX) {
            $value = PHP_INT_MAX;
        }

        return (int) $value;
    }
}
