[![GitHub stars](https://img.shields.io/github/stars/andriichuk/enviro)](https://github.com/andriichuk/enviro)
![Tests](https://github.com/andriichuk/enviro/actions/workflows/test.yml/badge.svg)
[![Psalm type coverage](https://shepherd.dev/github/andriichuk/enviro/coverage.svg)](https://packagist.org/packages/andriichuk/enviro)
[![Psalm enabled](https://shepherd.dev/github/andriichuk/enviro/level.svg)](https://packagist.org/packages/andriichuk/enviro)
[![License](https://poser.pugx.org/andriichuk/enviro/license?format=flat)](https://packagist.org/packages/andriichuk/enviro)

### Initialization

Command:

```shell
./keepenv init
```

To customize for `vlucas/dotenv`:

```shell
./keepenv verify --env=local --env-file=./ --spec=./env.spec.yaml
```

To customize for `symfony/dotenv`:

```shell
./keepenv verify --env=local --env-file=./.env --env-file=./.env.local --spec=./env.spec.yaml
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

### Filling

Command:

```shell
./keepenv fill
```
