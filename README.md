# AnsiblePHP

A modern Ansible management system built with Laravel 12, FilamentPHP 3, and PostgreSQL. Inspired by SemaphoreUI, this project provides a web-based interface for managing Ansible inventories, task templates, keystores, and deployments.

## Features

- **Inventory Management**: Define and manage Ansible servers (inventories)
- **Task Templates**: Create reusable Ansible job templates
- **Keystore Management**: Securely manage SSH keys and credentials
- **Environment Configuration**: Define environment variables and settings
- **Deployment Execution**: Run Ansible tasks on selected servers with real-time console output
- **Job Queue Management**: Laravel Horizon for managing background jobs

## Tech Stack

- **Laravel 12**: Latest PHP framework
- **FilamentPHP 3**: Modern admin panel and form builder
- **PostgreSQL**: Robust relational database
- **Laravel Horizon**: Queue monitoring and management
- **Livewire**: Dynamic interfaces without leaving PHP

## Requirements

- PHP 8.3 or higher
- PostgreSQL 13 or higher
- Composer
- Redis (for Laravel Horizon)
- Ansible (for running playbooks)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/visio-soft/ansiblephp.git
cd ansiblephp
```

2. Install dependencies:
```bash
composer install
```

3. Copy the environment file:
```bash
cp .env.example .env
```

4. Configure your database in `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ansiblephp
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Run migrations:
```bash
php artisan migrate
```

7. Create a Filament admin user:
```bash
php artisan make:filament-user
```

8. Start Laravel Horizon:
```bash
php artisan horizon
```

9. Start the development server:
```bash
php artisan serve
```

## Usage

1. Access the admin panel at `http://localhost:8000/admin`
2. Log in with your admin credentials
3. Start by adding servers to your inventory
4. Create task templates for your Ansible playbooks
5. Configure environments and keystores
6. Execute deployments and monitor console output in real-time

## License

Open-source software licensed under the MIT license.
