[![GitHub stars](https://img.shields.io/github/stars/andriichuk/enviro)](https://github.com/andriichuk/enviro)
![Tests](https://github.com/andriichuk/enviro/actions/workflows/test.yml/badge.svg)
[![Psalm type coverage](https://shepherd.dev/github/andriichuk/enviro/coverage.svg)](https://packagist.org/packages/andriichuk/enviro)
[![Psalm enabled](https://shepherd.dev/github/andriichuk/enviro/level.svg)](https://packagist.org/packages/andriichuk/enviro)
[![License](https://poser.pugx.org/andriichuk/enviro/license?format=flat)](https://packagist.org/packages/andriichuk/enviro)

TODO:

- [ ] variables validation and fill
- [x] split by environment
- [ ] check in .env.example
- [x] smart autocomplete on pull new changes
- [ ] change env value (CRUD) create/set/update/delete/read
- [ ] create from existing (add key to group by name)
- [ ] generate or remove .env.example
- [ ] validate pattern/string/numeric/email/ip/required/
- [ ] bidirect sync (spec/env)
- [ ] disabled by comment #TRUSTED_HOSTS='^(localhost|example\.com)$'
- [x] add version and environment fields
- [ ] fill all descriptions and messages
- [ ] support PHP and JSON format
- [ ] warning about variables from .env file that are missing in specification
- [ ] support system variables
- [ ] support `export` key 
- [ ] raw blueprint (print all variables with checkmarks like dry run)

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
