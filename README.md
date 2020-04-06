PHP Async Paginator
===================

[![CircleCI](https://circleci.com/gh/spacetab-io/paginate-php/tree/master.svg?style=svg)](https://circleci.com/gh/spacetab-io/paginate-php/tree/master)
[![codecov](https://codecov.io/gh/spacetab-io/paginate-php/branch/master/graph/badge.svg)](https://codecov.io/gh/spacetab-io/paginate-php)

Simple async paginator based on Amphp.

## Installation

```bash
composer install spacetab-io/paginate-php
```

## Usage

```php
use Spacetab\Paginate\Paginator;
use Spacetab\Paginate\Adapter\SqlAdapter;
use Amp\Postgres;
use function Amp\call;

call(function() {
    $connection = new Postgres\ConnectionConfig('127.0.0.1');
    $paginator  = new Paginator(new SqlAdapter(new Postgres\Pool($connection), 'table_name'));
    $paginator->setPage($query['page'] ?? null);
    $paginator->setPerPage($query['per_page'] ?? null);
    
    /** @var \Spacetab\Paginate\ResultSet $results */
    $results = yield $paginator->doPaginate();

    $results->getItems();
    $results->getCount();
    $results->getPrev();
    $results->getNext();
    $results->getTotal();
});
```

`ArrayAdapter` also available.

## Depends

* \>= PHP 7.4
* Composer for install package

## License

The MIT License

Copyright © 2020 spacetab.io, Inc. https://spacetab.io

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

