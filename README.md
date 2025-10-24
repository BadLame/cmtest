## Установка проекта

```bash
cp .env.example .env
docker-compose up -d
docker-compose exec laravel.test bash
composer install
php artisan key:generate
php artisan test
```

## Проект

- Посмотреть документацию / попробовать запросы можно по ссылке `/docs/api`
- Реализованы слои репозиториев (`TransactionRepository`/`UserBalanceRepository`) и сервисов (`OperationsService`)
- Тесты находятся в `tests/Http/Controllers/OperationsControllerTest.php`
- При запросах с несуществующим `user_id` возвращается 404 (кроме пополнения)
- При запросах снятия/перевода при недостатке средств возвращается 409 с соответствующим сообщением (
  `app/Exceptions/InsufficientFundsException.php`)
- Так же 409 будет, если сохранить финансовую операцию не удалось (
  `app/Exceptions/TransactionException.php`, в тестах эта ситуация проверяется)
