<?php

declare(strict_types=1);

namespace Spacetab\Paginate\Adapter;

use Amp\Promise;

interface AdapterInterface
{
    /**
     * @return \Amp\Promise
     */
    public function getCount(): Promise;

    /**
     * @param int $offset
     * @param int $limit
     *
     * @return \Amp\Promise
     */
    public function getSlice(int $offset, int $limit): Promise;
}
