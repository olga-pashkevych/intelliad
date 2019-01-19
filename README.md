# How to use

## Required
- PHP: ^7.1,
- MySQL: ^5.7

## Installation


```bash
git clone https://github.com/olga-pashkevych/intelliad.git
cd intelliad
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load --append

```

## Usage 

CLI
```bash
bin/console currency:import --show-data

+----------+----------+------------------+
| Currency | Rate     | Date             |
+----------+----------+------------------+
| EURUSD   | 1.137249 | 17-01-2019 12:04 |
| EURCHF   | 1.132484 | 17-01-2019 12:04 |
+----------+----------+------------------+

 [OK] New data has been added

```

Web
```bash
php bin/console server:run
open http://127.0.0.1:8000/currency/get_rates
```
