<?php
declare(strict_types=1);

namespace app\repository;

use app\entity\Transaction;

interface TransactionRepository
{

    /**
     * @return iterable<Transaction>
     */
    public function find(): iterable;
}
