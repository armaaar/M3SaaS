# Multi-Tenant Multi-Database Modular Software As A Service

A modular approach to create a multi-tenant API as a service with multiple databases. Based on [DBStalker](https://github.com/armaaar/dbstalker) and [miniRouter](https://github.com/armaaar/miniRouter).

## Setup

To run the app, you need to install [Docker](https://www.docker.com/get-started) and [Docker Compose](https://docs.docker.com/compose/install/). After you are done you can start the app using `docker-compose up`

## Configure Databases

- Create a new `secrets/db_root_password.secret` file with only the default db root password in it.
- Create a new `secrets/config.json.secret` with the same structure as `secrets/config.json.secret.example`.
- Use [DBStalker configuration options](https://github.com/armaaar/dbstalker#configuration) to store the connection options under `tenants` in `config.json.secret`.
- Optional: add a salt for databases passwords: `"dbSalt": "yourUniqueSalt"` in `config.json.secret`.

See `secrets/config.json.secret.example` for example.

## Register tenants in database

Add tenants' information as [main seeds](https://github.com/armaaar/dbstalker#main-seeds) in [`tenants.seed.php`](https://github.com/armaaar/M3SaaS/blob/master/tenants_db/seeds/tenants.seed.php), each tenant with unique:

- `id`: Will be used to access the tenants API and modules
- `name`: Will be used as a database name
- `user`: Will be used as a database username to access the tenant database
- `password`: Will be hashed and salted to be used as a password for the database user created
- `per_day_backups` The maximum number of backups that can be taken per day, default to `1`
- `max_backups` The maximum number of backups that can be taken, default to `10`

The tenant's APIs will be added under the unique tenant URL: `/{tenant}`

## Reusable modules

Modules are pieces or reusable code that defines your business logic You can use modules to define:

- Database tables and views
- API endpoints
- Controllers, interfaces, business logic, etc.

To create a new module with name `unique_module_name`:

- Create a new directory called `unique_module_name` under `modules` directory
- Create a new directory for each version of the module. Add directory `v1` in your newly created module directory. More on [module versionning](#modules-versionning) later
- Create a root module file called `unique_module_name.module.php` inside each version directory. This file will be included for each tenant subscribed to this version of the module

### Modules versionning

Modules work ONLY in versions. Versions are used to introduce breaking changes to a module without breaking old versions. Each module version is treated as a separate moduleby the system.

Recommendations while using modules (unless you know what you are doing):

- A new version should ideally be introduced if it's uncompatible with the old version.
- Tenants should ideally subscribe to 1 version of each module to avoid conflict in database.

#### Create a new version

To create a new version, create a directory called `v{base_version_number}` under the module directory, where `{base_version_number}` is the major version number obtained by flooring the version number to a single integer.

Right version directories:

- `v1`
- `v4`
- `v64`

Wrong version directories:

- `1`
- `V1`
- `v1.1`
- `v1z`

#### Sub-versions

Although only major version numbers can be created, sub-versions can be accessed in the underlying major version. The full version number can be access through `$version_number` variable.

For example: versions `1`, `1.4`, `1.0.1` all access `v1` module and the full version can be accessed through `$version_number` variable.

The module version's APIs will be added under the unique URL: `/{tenant_id}/{unique_module_name}/v{version_number}`

## Register modules in database

Add modules' information as [main seeds](https://github.com/armaaar/dbstalker#main-seeds) in `modules.seed.php`, each module with unique:

- `id`: Will be used to connect tenants to modules
- `name`: The same name used for the module's directory name in `modules`

## Subscribe tenants to modules

For each module a tenant should subscribe to, add a [main seeds](https://github.com/armaaar/dbstalker#main-seeds) in `subscriptions.seed.php` with:

- `id`: Just a unique identifier required for main seeds, not used
- `tenant_id`: The id of the tenant that will subscribe to the module
- `module_id` The id of the module to be subscribed to
- `version` The major version number of the module that should be used, default to `1`

## Database migration

In order to migrate changes in tables' structure and seeds. You can either:

- Set `AUTO_MIGRATION` in `settings/constants.php` to `true`
- Set `AUTO_MIGRATION` in `settings/constants.php` to `false` and migrate manually using the following routes:
  - To migrate the master tenants database, send a get request to: `/migrate`
  - To migrate a tenant database, send a get request to: `/{tenant_id}/migrate`
  - To force seed main seeds, send the request to `/migrate/force`
  - To seed temporary seeds, send the request to `/migrate/seed`
  - To remove temporary seeds, send the request to `/migrate/deseed`

Note: renaming columns or tables might result DROPPING the column or table entirely. so be careful!

## Config file fallback

The app gets its config file path ferom environment varialbe `CONFIG_FILE` defined in `docker-compose`. If you are using the app outside docker or wants to add a fallback for the config file, Add your configuration to `config.json` inside the 'app' directory.

## Future Features

Bellow is a list of features or tasks to do in the future:

- Manage modules dependancies

## License

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
