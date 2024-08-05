<?php
declare(strict_types=1);

namespace app\tests\repository;

use app\entity\Transaction;
use app\repository\FileTransactionRepository;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class FileTransactionRepositoryTest extends TestCase
{
    public function testFindWhenValidFile(): void
    {
        $file = __DIR__ . '/testdata/valid_transactions.txt';
        $repository = new FileTransactionRepository($file);

        /**
         * @var Transaction[] $transactions
         */
        $transactions = [...$repository->find()];

        $this->assertCount(5, $transactions);
        $this->assertSame('45717360', $transactions[0]->getBin());
        $this->assertSame('100.00', $transactions[0]->getMoney()->getAmount());
        $this->assertSame('EUR', $transactions[0]->getMoney()->getCurrency()->getCode());
        $this->assertSame('4745030', $transactions[4]->getBin());
        $this->assertSame('2000.00', $transactions[4]->getMoney()->getAmount());
        $this->assertSame('GBP', $transactions[4]->getMoney()->getCurrency()->getCode());
    }

    public function testFindWhenInvalidFile(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('File does not exist');

        new FileTransactionRepository('invalid.txt');
    }

    public function testFindWhenInvalidJson(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid JSON');

        $repository = new FileTransactionRepository(__DIR__ . '/testdata/invalid_file_structure_transactions.txt');
        [...$repository->find()];
    }

    public function testFindWhenCannotCreateTransactions(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid data');

        $repository = new FileTransactionRepository(__DIR__ . '/testdata/invalid_file_data_transactions.txt');
        [...$repository->find()];
    }
}
