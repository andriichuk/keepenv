[![GitHub stars](https://img.shields.io/github/stars/andriichuk/enviro)](https://github.com/andriichuk/enviro)
![Tests](https://github.com/andriichuk/enviro/actions/workflows/test.yml/badge.svg)
[![Psalm type coverage](https://shepherd.dev/github/andriichuk/enviro/coverage.svg)](https://packagist.org/packages/andriichuk/enviro)
[![Psalm enabled](https://shepherd.dev/github/andriichuk/enviro/level.svg)](https://packagist.org/packages/andriichuk/enviro)
[![License](https://poser.pugx.org/andriichuk/enviro/license?format=flat)](https://packagist.org/packages/andriichuk/enviro)

### Initialization

This command allows you to generate a new environment specification file based on your current `.env` structure.

Options:

* `env` target environment name (default: `common`)
* `env-file` paths to DotEnv files (default: project root `./`)
  * for `vlucas/dotenv` package it should be a path to directory
  * for `symfony/dotenv` package it should be a path to files
* `spec` path to the environment specification file that will be generated (default: `.env.spec.yaml`)
* `preset` preset alias (default: `null`). Available values: `laravel` 

Basic usage:

```shell
./keepenv init
```

For Laravel Framework:

```shell
./keepenv init --preset=laravel
```

For custom env files (`vlucas/dotenv`):

```shell
./keepenv init --env-file=./ --env-file=./config/
```

For custom env files (`symfony/dotenv`):

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
