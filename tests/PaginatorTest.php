<?php declare(strict_types=1);

namespace Spacetab\Tests;

use Amp\PHPUnit\AsyncTestCase;
use Amp\Sql\Pool as PoolInterface;
use Amp\Sql\ResultSet as AmpSqlResultSet;
use Amp\Sql\Statement;
use Amp\Success;
use Spacetab\Paginate\Adapter\ArrayAdapter;
use Spacetab\Paginate\Adapter\SqlAdapter;
use Spacetab\Paginate\Paginator;

class PaginatorTest extends AsyncTestCase
{
    public function testBasicArrayPaginationWorks()
    {
        $items = [1, 2, 3, 4, 5, 6, 7];
        $pagination = new Paginator(new ArrayAdapter($items));

        /** @var \Spacetab\Paginate\ResultSet $results */
        $results = yield $pagination->doPaginate();

        $this->assertSame(1, $results->getTotal());
        $this->assertSame(7, $results->getCount());
        $this->assertSame(null, $results->getNext());
        $this->assertSame(null, $results->getPrev());
        $this->assertSame($items, $results->getItems());
    }

    public function testPaginationSetters()
    {
        $adapter = new ArrayAdapter([1, 2, 3, 4, 5, 6, 7]);

        $pagination = new Paginator($adapter);
        $pagination->setPage(1);
        $pagination->setPerPage(3);
        $pagination->setPerPageMax(5);

        $this->assertSame(1, $pagination->getPage());
        $this->assertSame(3, $pagination->getPerPage());
        $this->assertSame(5, $pagination->getPerPageMax());

        $pagination = new Paginator($adapter);
        $pagination->setPage(-1);
        $pagination->setPerPage(null);
        $pagination->setPerPageMax(5);

        $this->assertSame(1, $pagination->getPage());
        $this->assertSame(Paginator::PAGINATION_DEFAULT_PER_PAGE_MAX, $pagination->getPerPage());
        $this->assertSame(5, $pagination->getPerPageMax());

        $pagination = new Paginator($adapter);
        $pagination->setPage(10);
        $pagination->setPerPage(20000);

        $this->assertSame(10, $pagination->getPage());
        $this->assertSame(Paginator::PAGINATION_DEFAULT_PER_PAGE_MAX, $pagination->getPerPage());

        $pagination = new Paginator($adapter);
        $pagination->setPerPageMax(PHP_INT_MAX + 100);

        $this->assertSame(PHP_INT_MAX, $pagination->getPerPageMax());
    }

    public function testHowToSqlAdapterReturnsCountOfRecords()
    {
        $result = $this->createMock(AmpSqlResultSet::class);
        $result->expects($this->once())
            ->method('advance')
            ->willReturn(new Success($result));

        $result->expects($this->once())
            ->method('getCurrent')
            ->willReturn(['count' => 10]);

        $pool = $this->createMock(PoolInterface::class);
        $pool->expects($this->once())
            ->method('query')
            ->willReturn(new Success($result));

        $sql = new SqlAdapter($pool, 'table');

        $this->assertSame(10, yield $sql->getCount());
    }

    public function testHowToSqlAdapterReturnsSliceOfRecords()
    {
        $result = $this->createMock(AmpSqlResultSet::class);
        $result->expects($this->any())
               ->method('advance')
               ->willReturnOnConsecutiveCalls(new Success(true), new Success(false));

        $result->expects($this->any())
                ->method('getCurrent')
                ->willReturn([1]);

        $stmt = $this->createMock(Statement::class);
        $stmt->expects($this->once())
            ->method('execute')
            ->with([0, 10])
            ->willReturn(new Success($result));

        $pool = $this->createMock(PoolInterface::class);
        $pool->expects($this->once())
             ->method('prepare')
             ->willReturn(new Success($stmt));

        $sql = new SqlAdapter($pool, 'table');

        $this->assertSame([[1]], yield $sql->getSlice(0, 10));
    }
}
