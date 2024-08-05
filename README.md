### Requirements
- PHP 8.0 or higher
- bcmath
### Installation
```bash
composer install
```
### Usage
```bash
php app.php input.txt
```
### Example input
```text
{"bin":"45717360","amount":"100.00","currency":"EUR"}
{"bin":"516793","amount":"50.00","currency":"USD"}
{"bin":"45417360","amount":"10000.00","currency":"JPY"}
{"bin":"41417360","amount":"130.00","currency":"USD"}
{"bin":"4745030","amount":"2000.00","currency":"GBP"}
``` 
### Example output
```text
1.00
0.46
1.25
Error: Code country not found
Error: BinList service is not available - HTTP/2 429  returned for "https://lookup.binlist.net/4745030".
```

### Testing
```bash
./vendor/bin/phpunit src/tests
```