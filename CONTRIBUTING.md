# Contributing

Contributions are **welcome** and will be fully **credited**.

We accept contributions via Pull Requests on [Github](https://github.com/andriichuk/keepenv).

### Pull Requests

- Feature-braching flow (one git-branch pre feature)
- PHP Coding Standards
  * [PSR-1](https://www.php-fig.org/psr/psr-1/)
  * [PSR-2](https://www.php-fig.org/psr/psr-2/)
  * [PSR-12](https://www.php-fig.org/psr/psr-12/)
- Add feature or unit tests to the new functionality
- Describe your feature in `README.md`

### Run PHP CS Fixer

```shell
vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.php
```

### Run Psalm

``` shell
vendor/bin/psalm
```

### Run tests

``` shell
vendor/bin/phpunit
```
