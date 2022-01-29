<h1 align="center">🗒 KeepEnv</h1>
<h2 align="center">Track Your Environment Variable Changes Using Specification<h2>
<p align="center">
  <img width="700" align="center" src="https://github.com/andriichuk/keepenv/blob/main/art/logo.jpeg" alt="Logo"/>
</p>
<p align="center">
  <a href="https://github.com/andriichuk/keepenv/blob/master/LICENSE">
    <img alt="License: MIT" src="https://poser.pugx.org/andriichuk/keepenv/license?format=flat" target="_blank" />
  </a>
  <a href="https://github.com/andriichuk/keepenv/actions">
    <img alt="Tests" src="https://github.com/andriichuk/keepenv/actions/workflows/test.yml/badge.svg" target="_blank" />
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
    <img alt="Stars" src="https://img.shields.io/github/stars/andriichuk/keepenv?color=blue" target="_blank" />
  </a>
</p>

### Table Of Contents

* [About](#about)
* [Installation](#installation)
* [Initialization](#initialization)
* [Validation](#validation)
* [Filling](#filling)
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
- I want to use a tool that will not be tied to a specific framework, because I work with several frameworks

Features:

* Environment specification generation based on current `.env` files.
* Environment variables validation.
* Split variable definition between environments.
* Extend variables from particular environment e.g. `local` from `common`.
* Split system (`$_ENV`) and regular variables from `.env` files.
* Ability to fill missing variables through console command (see [filling](#filling)).

### Installation

Install composer package:

```shell
composer require andriichuk/keepenv
```

### Initialization

This command allows you to generate a new environment specification file based on your current `.env` structure.

Basic usage:

```shell
./keepenv init
```

This will create a specification file (`keepenv.yaml`) in your root directory with `common` environment. 

Using preset (available presets: `laravel`, `symfony`):

```shell
./keepenv init --preset=laravel
```

Using custom `.env` files for `vlucas/dotenv` (paths to the folders with `.env` file):

```shell
./keepenv init --env-file=./ --env-file=./config/
```

Using custom `.env` files for `symfony/dotenv` (direct file paths):

```shell
./keepenv init --env-file=./.env --env-file=./.env.local
```

Environment file reader will be detected automatically, but you can customize it:

```shell
./keepenv init --env-reader=symfony/dotenv
```

### Validation

Command:

```shell
./keepenv validate local
```

To customize:

```shell
./keepenv validate local --env-file=./.env --spec=./env.spec.yaml
```

### Filling

Command:

```shell
./keepenv fill
```

### Syntax

Environments:

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

Variable

* `description` (string) variable description
```yaml
SESSION_LIFETIME:
    description: 'Session lifetime in minutes.'
```
* `export` (boolean)
```yaml
SESSION_LIFETIME: # TODO
    description: 'Session lifetime in minutes.'
```
* `system` (boolean)
```yaml
APP_TIMEZONE:
    system: true
```
* `default` (mixed)
```yaml
REDIS_PORT:
    default: 6379
```
* `rules` (array) validation rules, available rules
  * `required` (boolean)
  ```yaml
  APP_ENV:
      rules:
          required: true
  ```
  * `string` (boolean)
  ```yaml
  APP_ENV:
      rules:
          string: true
  ```
  * `string` (array) with range
  ```yaml
  APP_ENV:
      rules:
          string:
              min: 2
              max: 10
  ```
  * `numeric: true` (boolean)
  ```yaml
  APP_ENV:
      rules:
          numeric: true
  ```
  * `email` (boolean)
  ```yaml
  MAIL_FROM_ADDRESS:
      rules:
          email: true
  ```
  * `enum` (array)
  ```yaml
  APP_ENV:
      rules:
          enum:
              - local
              - production
  ```
  * `equals` (mixed)
  ```yaml
  APP_ENV:
      rules:
          equals: local
  ```
  * `ip` (boolean)
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
                description: 'Application environment.'
                default: local
                rules:
                    required: true
                    enum:
                        - local
                        - production
            DB_HOST:
                description: 'Database host.'
                default: 127.0.0.1
                rules:
                    required: true
                    ip: true
            DB_PORT:
                description: 'Database port.'
                default: '3306'
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

Boolean type is not supported yet, so for now you can use `enum` rule ([true, false,], [yes, no], [on, off], [1, 0]): 

```yaml
APP_PAYMENT_FEATURE:
    rules:
        enum:
            - on
            - off
```

You can add a composer post update scripts for the new environment variables filling and validation: 

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
    // ... 
    "post-update-cmd": [
        "@keepenv common"
    ]
},
```

Kubernetes

```shell
./vendor/bin/keepenv validate --env-provider=system
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
