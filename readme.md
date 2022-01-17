<h1 align="center">🗒 KeepEnv</h1>
<h2 align="center">Environment Variables Specification</h2>
<p align="center">
  <img width="700" align="center" src="https://github.com/andriichuk/keepenv/blob/main/art/logo.jpeg" alt="Logo"/>
</p>
<p align="center">
  <a href="https://github.com/andriichuk/keepenv">
    <img alt="Stars" src="https://img.shields.io/github/stars/andriichuk/keepenv?color=blue" target="_blank" />
  </a>
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
</p>

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
