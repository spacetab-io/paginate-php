<?php

declare(strict_types=1);

namespace Spacetab\Paginate\Adapter;

use Amp\Postgres\ResultSet;
use Amp\Promise;
use Amp\Sql\Pool;

use function Amp\call;

class SqlAdapter implements AdapterInterface
{
    /**
     * @var \Amp\Sql\Pool
     */
    private Pool $pool;

    /**
     * @var string
     */
    private string $table;

    /**
     * SqlAdapter constructor.
     *
     * @param \Amp\Sql\Pool $pool
     * @param string $table
     */
    public function __construct(Pool $pool, string $table)
    {
        $this->pool  = $pool;
        $this->table = $table;
    }

    /**
     * @return \Amp\Promise
     */
    public function getCount(): Promise
    {
        return call(function () {
            /** @var ResultSet $countQuery */
            $countQuery = yield $this->pool->query("select count(*) from {$this->table}");

            $recordsCount = 0;
            if (yield $countQuery->advance()) {
                $recordsCount = $countQuery->getCurrent()['count'];
            }

            return $recordsCount;
        });
    }

    /**
     * @param int $offset
     * @param int $limit
     *
     * @return \Amp\Promise
     */
    public function getSlice(int $offset, int $limit): Promise
    {
        return call(function () use ($offset, $limit) {
            /** @var \Amp\Sql\Statement $stmt */
            $stmt = yield $this->pool->prepare("SELECT * FROM {$this->table} OFFSET ? LIMIT ?");

            /** @var ResultSet $result */
            $result = yield $stmt->execute([$offset, $limit]);

            $items = [];
            while (yield $result->advance()) {
                $items[] = $result->getCurrent();
            }

            return $items;
        });
    }
}
