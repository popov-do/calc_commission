<?php
declare(strict_types=1);

namespace app\repository;

use app\entity\Currency;
use app\entity\Money;
use app\entity\Transaction;
use InvalidArgumentException;
use RuntimeException;

/**
 * @implements TransactionRepository<Transaction>
 *
 * Example file content:
 * ```json
 *  {"bin":"45717360","amount":"100.00","currency":"EUR"}
 *  {"bin":"516793","amount":"50.00","currency":"USD"}
 *  {"bin":"45417360","amount":"10000.00","currency":"JPY"}
 *  {"bin":"41417360","amount":"130.00","currency":"USD"}
 *  {"bin":"4745030","amount":"2000.00","currency":"GBP"}
 * ```
 */
class FileTransactionRepository implements TransactionRepository
{
    public function __construct(
        private string $filename
    ) {
        if (!file_exists($filename)) {
            throw new InvalidArgumentException('File does not exist');
        }
    }

    /**
     * @return iterable<Transaction>
     */
    public function find(): iterable
    {
        $file = fopen($this->filename, 'r');
        if ($file === false) {
            throw new RuntimeException('Cannot open file');
        }
        try {
            while (($line = fgets($file)) !== false) {
                $line = trim($line);
                if ($line !== '') {
                    yield $this->parseLine($line);
                }
            }
        } finally {
            fclose($file);
        }
    }

    private function parseLine(string $line): Transaction
    {
        $data = json_decode($line, true);
        if ($data === null) {
            throw new RuntimeException('Invalid JSON');
        }
        try {
            return new Transaction($data['bin'], new Money($data['amount'], new Currency($data['currency'])));
        } catch (InvalidArgumentException $e) {
            throw new RuntimeException('Invalid data', 0, $e);
        }
    }
}
