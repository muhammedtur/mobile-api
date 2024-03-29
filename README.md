<h1 align="center">
  Mobile APP API
</h1>

<p align="center"><a href="https://github.com/muhammedtur/mobile-api/releases" target="_blank"><img src="https://img.shields.io/badge/version-v1.0-blue?style=for-the-badge&logo=none" alt="cli version" /></a>&nbsp;<img src="https://img.shields.io/badge/license-apache_2.0-red?style=for-the-badge&logo=none" alt="license" /></p>
<p align="center"><b>Mobile APP API </b> included API + Worker (PSR2 Standards)</p>

## Requirements

```bash
#Git
git version 2.35.1.windows.2

# Composer
Composer version 2.2.9 2022-03-15 22:13:37

# PHP
PHP 8.1.0 (cli) (built: Nov 23 2021 21:46:10) (NTS Visual C++ 2019 x64

# Database
MySQL 8.0.28
```

## Installation Guide

```bash
# Clone the project
git clone https://github.com/muhammedtur/mobile-api.git

# Change the directory
cd mobile-api
```

To install the dependencies:

```bash
composer update

env.example -> .env (Should be updated database variables and APP_URL)

php artisan key:generate

php artisan migrate

php artisan db:seed
```

## Running

To run the app:

```bash
php artisan serve --port=80
```

To run the scheduler:

```bash
php artisan schedule:work
```

## Documentation

To API scheme documentation:

```bash
{APP_URL}/api/documentation
```
