# Testing

- Every change must be programmatically tested. Write a new test or update an existing test, then run it.
- Do not create verification scripts or tinker when tests cover the functionality. Unit and feature tests are more important.
- All tests must be PHPUnit classes. Use `docker compose exec -T solder php artisan make:test --phpunit {name}` to create a new test. Pass `--unit` for unit tests. Most tests should be feature tests.
- If you see a test using "Pest", convert it to PHPUnit.
- Use factories for test models. Check for custom factory states before manual setup.
- Faker: follow existing conventions (`$this->faker` vs `fake()`).
- Cover all happy paths, failure paths, and edge cases.
- Run the singular test after every update. When feature tests pass, ask user about running the full suite.
- Do not remove tests or test files without approval.

## Running Tests

- Run the minimal number of tests using an appropriate filter before finalizing.
- All tests: `docker compose exec -T solder php artisan test --compact`
- Single file: `docker compose exec -T solder php artisan test --compact tests/Feature/ExampleTest.php`
- Specific test: `docker compose exec -T solder php artisan test --compact --filter=testName` (recommended)
