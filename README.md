# Simple task manager

A simple Laravel task management application with projects and tasks.

## Setup with DDEV
DDEV handles PHP, the web server, and the database automatically via Docker.

### Prerequisites

- [Docker](https://docs.docker.com/get-docker/)
- [DDEV](https://ddev.readthedocs.io/en/stable/users/install/ddev-installation/)

### Steps

```bash
# 1. Start DDEV
ddev start

# 2. Install PHP dependencies
ddev composer install

# 3. Copy the environment file
cp .env.example .env

# 4. Generate application key
ddev artisan key:generate

# 5. Run migrations
ddev artisan migrate

# 6. Install and build frontend assets
ddev npm install
ddev npm run build
```

Use `ddev st` to get information about the project.

use `ddev ssh` to get into project container where you can run artisan commands without `ddev` prefix.

```bash
php artisan migrate
npm install
npm run build
```

---

## Running tests

```bash
# With DDEV
ddev composer test

# Without DDEV - once you are in container
composer test
```

Tests are written with [Pest](https://pestphp.com/) and cover project/task controllers and the task service.
