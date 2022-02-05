<h1 align="center">🗒 KeepEnv</h1>
<h2 align="center">Track Your Environment Variable Changes Using Specification<h2>
<p align="center">
  <img width="700" align="center" src="https://github.com/andriichuk/keepenv/blob/main/art/logo.jpeg" alt="Logo"/>
</p>
<p align="center">
  <a href="https://github.com/andriichuk/keepenv/blob/master/LICENSE" target="_blank">
    <img alt="License: MIT" src="https://poser.pugx.org/andriichuk/keepenv/license?format=flat" />
  </a>
  <a href="https://github.com/andriichuk/keepenv/actions" target="_blank">
    <img alt="Tests" src="https://github.com/andriichuk/keepenv/actions/workflows/test.yml/badge.svg" />
  </a>
  <a href="https://codecov.io/gh/andriichuk/keepenv">
    <img alt="Code coverage" src="https://codecov.io/gh/andriichuk/keepenv/branch/main/graph/badge.svg?token=07FR1W9XVE"/>
  </a>
  <a href="https://github.com/andriichuk/keepenv/blob/main/psalm.xml">
    <img alt="Psalm type coverage" src="https://shepherd.dev/github/andriichuk/keepenv/coverage.svg" />
  </a>
  <a href="https://github.com/andriichuk/keepenv/blob/main/psalm.xml">
    <img alt="Psalm level" src="https://shepherd.dev/github/andriichuk/keepenv/level.svg" />
  </a>
  <a href="https://github.com/andriichuk/keepenv">
    <img alt="Stars" src="https://img.shields.io/github/stars/andriichuk/keepenv?color=blue" />
  </a>
  <a href="https://sonarcloud.io/summary/new_code?id=andriichuk_keepenv" target="_blank">
    <img alt="Quality Gate Status" src="https://sonarcloud.io/api/project_badges/measure?project=andriichuk_keepenv&metric=alert_status" target="_blank" />
  </a>
</p>

### Table Of Contents

* [About](#about)
* [Installation](#installation)
* [Initialization](#initialization)
* [Validation](#validation)
* [Filling](#filling)
* [Adding](#adding)
* [Dumping](#dumping)
* [Syntax](#syntax)
* [Tips](#tips)
* [Contributing](#contributing)

### About

KeepEnv is a CLI tool for checking and managing environment variables based on a specification file.

Motivations:

- I want to have a way to describe all environment variables in one specification file.
- I want to make sure that all required variables are filled in correctly before deploying the application.
- I don't want to check variables in runtime.
- I want to keep track of new environment variables when they are added by one of my colleagues.
- I want to have a convenient and safe way to fill in new variables.
- I want to check variables from different state providers (system $_ENV, from .env file + system or only from .env file).
- I don't want to manually describe all 100+ existing environment variables.
- I want to use a tool that will not be tied to a specific framework, because I work with several frameworks.

Features:

* Environment specification generation based on current `.env` files.
* Environment variables validation.
* Split variable definition between environments.
* Extend variables from particular environment e.g. `local` from `common`.
* Split system (`$_ENV`) and regular variables from `.env` files.
* Ability to fill missing variables through console command.

Supported dotenv file state loaders:

* [vlucas/phpdotenv](https://packagist.org/packages/vlucas/phpdotenv)
* [symfony/dotenv](https://packagist.org/packages/symfony/dotenv)
* [josegonzalez/dotenv](https://packagist.org/packages/josegonzalez/dotenv)

### Installation

Install composer package:

```shell
composer require andriichuk/keepenv
```

### Initialization

This command allows you to generate a new environment specification file based on your current `.env` structure.

Basic usage:

```shell
./vendor/bin/keepenv init
```

This will create a specification file (`keepenv.yaml`) in your root directory with `common` environment. 

Using preset (available presets: `laravel`, `symfony`):

```shell
./vendor/bin/keepenv init --preset=laravel
```

For Laravel Sail:

```shell
./vendor/bin/sail php ./vendor/bin/keepenv init --preset=laravel
```

Using custom `.env` files for `vlucas/dotenv` (paths to the folders with `.env` file):

```shell
./vendor/bin/keepenv init --env-file=./ --env-file=./config/
```

Using custom `.env` files for `symfony/dotenv` (direct file paths):

```shell
./vendor/bin/keepenv init --env-file=./.env --env-file=./.env.local
```

Environment file reader will be detected automatically, but you can customize it:

```shell
./vendor/bin/keepenv init --env-reader=symfony/dotenv --env-file=./.env
```

### Validation

Using this command you can check your environment variables according to the specification file `keepenv.yaml`.

Basic usage:

```shell
./vendor/bin/keepenv validate common
```

Check only system variables (`$_ENV`) without looking at the `.env` file:

```shell
./vendor/bin/keepenv validate common --env-provider=system
```

Use `--help` option to check other parameters.

### Filling

This command allows you to fill in and validate missing variable values from your `.env` file (use `--help` for list of all options). 

Command:

```shell
./vendor/bin/keepenv fill
```

For specific environment:

```shell
./vendor/bin/keepenv fill --env=common
```

### Adding

The following command can help you to add a new variable definition to specification and dotenv files: 

```shell
./vendor/bin/keepenv add
```

### Dumping

Using this command you can export all your variables defined in `keepenv.yaml` file into the custom `.env` file.

Create a new `.env` file according to variables defined in the `keepenv.yaml` (same as `cp .env.example .env`). Variables will be filled in only with default values. Perhaps now you can delete the `.env.example` file:

```shell
./vendor/bin/keepenv dump
```

Dump system variables into the file:

```shell
./vendor/bin/keepenv dump --target-env-file=./.env.system --env-provider=system --with-values=true
```

Create a new `.env.stage` file based on `production` environment specification and current `.env` file:

```shell
./vendor/bin/keepenv dump --env=production --target-env-file=./.env.stage --env-file=./ --with-values=true
```

### Syntax

Currently, only the YAML syntax format is supported.

Environments definition:

```yaml
version: '1.0'
environments:
    common:
        variables:
        # ...
    local:
        extends: common
        variables:
        # ...
    testing:
        variables:
        # ...
```

Variables definition:

* Describe the purpose of variables:
```yaml
SESSION_LIFETIME:
    description: 'Session lifetime in minutes.'
```
* Mark that variable should be followed by `export` keyword in `.env` file (`export APP_LOCALE=en`):
```yaml
APP_LOCALE:
    export: true
```
* Mark that variable should be set on the server-side (`$_ENV` or `$_SERVER`) not from `.env` file:
```yaml
APP_TIMEZONE:
    system: true
```
* Specify default value (please use this only for non-sensitive data):
```yaml
REDIS_PORT:
    default: 6379
```
* Describe validation rules:
  * Mark variable as required:
  ```yaml
  APP_ENV:
      rules:
          required: true
  ```
  * Check that variable value is a string (can usually be omitted because all values in the `.env` file are read as strings by default):
  ```yaml
  APP_ENV:
      rules:
          string: true
  ```
  * String with length range
  ```yaml
  APP_KEY:
      rules:
          string:
              min: 32
              max: 60
  ```
  * Numeric
  ```yaml
  REDIS_PORT:
      rules:
          numeric: true
  ```
  * Boolean (true/false, on/off, yes/no, 1/0)
  ```yaml
  APP_DEBUG:
      rules:
          boolean: true
  ```
  * Boolean with custom options
  ```yaml
  PAYMENT_FEATURE:
      rules:
          boolean:
              'true': Y
              'false': N
  ```
  * Email address
  ```yaml
  MAIL_FROM_ADDRESS:
      rules:
          email: true
  ```
  * Enumeration:
  ```yaml
  APP_ENV:
      rules:
          enum:
              - local
              - production
  ```
  * Means that the value of the variable must be equal (`==`) to a specific value.
  ```yaml
  APP_ENV:
      rules:
          equals: local
  ```
  * IP address
  ```yaml
  DB_HOST:
      rules:
          ip: true
  ```

Full example:

```yaml
version: '1.0'
environments:
    common:
        variables:
            APP_NAME:
                description: 'Application name.'
            APP_ENV:
                description: 'Application environment name.'
                default: local
                rules:
                    required: true
                    enum:
                        - local
                        - production
            APP_DEBUG:
                rules:
                    boolean: true
            DB_HOST:
                description: 'Database host.'
                default: 127.0.0.1
                rules:
                    required: true
                    ip: true
            DB_PORT:
                description: 'Database port.'
                default: 3306
                rules:
                    required: true
                    numeric: true

    local:
        extends: common
        variables:
            APP_ENV:
                rules:
                    equals: local

    testing:
        variables:
            DB_DATABASE:
                description: 'Database name.'
                default: testing
                rules:
                    required: true
            DB_USERNAME:
                description: 'Database username.'
                rules:
                    required: true
            DB_PASSWORD:
                description: 'Database password.'
                rules:
                    required: true
```

### Tips

Use `equals` rule to check for a specific value for the environment, e.g., a useful example for `APP_ENV`:

```yaml
version: '1.0'
environments:
    common:
        variables:
            APP_ENV:
                rules:
                    required: true
                    enum:
                        - local
                        - production
            # ...
    production:
        extends: common
        variables:
            APP_ENV:
                rules:
                    equals: production
```

You can add a composer script for the new environment variables filling and validation: 

```json
"scripts": {
    "keepenv": "./vendor/bin/keepenv fill && ./vendor/bin/keepenv validate",
},
```

Then use:

```shell
composer keepenv common
```

You can also define `keepenv` common on `post-update-cmd` composer event, so environment filling and validation will be running after each `composer update`:

```json
"scripts": {
    "post-update-cmd": [
        "@keepenv common"
    ]
},
```

### Contributing

Contributions, issues and feature requests are welcome.<br />
Feel free to check [issues page](https://github.com/andriichuk/keepenv/issues) if you want to contribute.
[Check the contributing guide](https://github.com/andriichuk/keepenv/blob/main/CONTRIBUTING.md).

### Credits

- [Serhii Andriichuk](https://github.com/andriichuk)
- [All Contributors](https://github.com/andriichuk/keepenv/graphs/contributors)

### License

Copyright © 2022 [Serhii Andriichuk](https://github.com/andriichuk).<br />
This project is [MIT](https://github.com/andriichuk/keepenv/blob/main/LICENSE) licensed.
