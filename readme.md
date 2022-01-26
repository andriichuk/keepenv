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
* [Verification](#verification)
* [Syntax](#syntax)

### About

KeepEnv is a tool for checking and managing environment variables based on a specification file.

Main features:

* Environment specification generation based on current `.env` files.
* Split variables between environments.
* Extend variables from particular environment e.g. `local` from `common`.
* Variable value validation.
* Split system (`$_ENV`) and regular variables from `.env` files.

### Installation

```shell
composer require andriichuk/keepenv
```

### Initialization

This command allows you to generate a new environment specification file based on your current `.env` structure.

Options:

* `env` target environment name (default: `common`)
* `env-file` paths to DotEnv files (default: project root `./`)
  * for `vlucas/dotenv` package it should be a path to directory
  * for `symfony/dotenv` package it should be a path to files
* `spec` path to the environment specification file that will be generated (default: `./keepenv.yaml`)
* `env-reader` reader name (default: `auto`). Available values: `auto`, `vlucas/phpdotenv`, `symfony/dotenv`.
* `preset` preset alias (default: `null`). Available values: `laravel`, `symfony`.

Basic usage:

```shell
./keepenv init
```

Using preset:

```shell
./keepenv init --preset=laravel
```

For custom `.env` files (`vlucas/dotenv`):

```shell
./keepenv init --env-file=./ --env-file=./config/
```

For custom `.env` files (`symfony/dotenv`):

```shell
./keepenv init --env-file=./.env --env-file=./.env.local
```



### Verification

Command:

```shell
./keepenv verify local
```

To customize:

```shell
./keepenv verify local --env-file=./.env --spec=./env.spec.yaml
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
  * `numeric: true`
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
  * `ip` (string)
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
                    equals: 'local'

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
