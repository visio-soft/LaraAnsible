# AnsiblePHP

A modern Ansible management system built with Laravel 12, FilamentPHP 3, and PostgreSQL. Inspired by SemaphoreUI, this project provides a web-based interface for managing Ansible inventories, task templates, keystores, and deployments.

## Features

- **Inventory Management**: Define and manage Ansible servers (inventories)
- **Task Templates**: Create reusable Ansible job templates
- **Keystore Management**: Securely manage SSH keys and credentials
- **Environment Configuration**: Define environment variables and settings
- **Deployment Execution**: Run Ansible tasks on selected servers with real-time console output
- **Job Queue Management**: Laravel Horizon for managing background jobs
- **Dashboard**: Statistics and recent deployment monitoring

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
- Node.js & NPM (for frontend assets)

## Installation

### Quick Setup (Recommended)

1. Clone the repository:
```bash
git clone https://github.com/visio-soft/ansiblephp.git
cd ansiblephp
```

2. Run the setup script:
```bash
chmod +x setup.sh
./setup.sh
```

3. Follow the on-screen instructions to complete the setup.

### Manual Installation

1. Clone the repository:
```bash
git clone https://github.com/visio-soft/ansiblephp.git
cd ansiblephp
```

2. Install dependencies:
```bash
composer install
npm install
```

3. Copy the environment file:
```bash
cp .env.example .env
```

4. Configure your database and Redis in `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ansiblephp
DB_USERNAME=postgres
DB_PASSWORD=your_password

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

QUEUE_CONNECTION=redis
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Run migrations:
```bash
php artisan migrate
```

7. (Optional) Seed demo data:
```bash
php artisan db:seed --class=DemoDataSeeder
```

This will create:
- Sample SSH keystores and password credentials
- 3 example servers (web, database, development)
- 2 environments (Production, Staging)
- 3 task templates (Update Packages, Deploy Application, Restart Services)

8. Create a Filament admin user:
```bash
php artisan make:filament-user
```

9. Build frontend assets:
```bash
npm run build
```

10. Start Laravel Horizon (in a separate terminal):
```bash
php artisan horizon
```

11. Start the development server:
```bash
php artisan serve
```

## Usage

1. Access the admin panel at `http://localhost:8000/admin`
2. Log in with your admin credentials
3. View the dashboard for system statistics

### Managing Inventory (Servers)

1. Navigate to **Inventory** in the sidebar
2. Click **New** to add a server
3. Fill in server details:
   - Name and description
   - Hostname/IP address
   - SSH port (default: 22)
   - Username for SSH connection
   - Select or create a keystore for authentication
   - Add any Ansible variables needed
4. Save the server

### Managing Keystores

1. Navigate to **Keystores** in the sidebar
2. Click **New** to create a keystore
3. Choose authentication type:
   - **SSH Key**: Paste your private key (and optionally public key and passphrase)
   - **Password**: Enter the password for authentication
4. Save the keystore

### Creating Task Templates

1. Navigate to **Task Templates** in the sidebar
2. Click **New** to create a template
3. Fill in template details:
   - Name and description
   - Type (Playbook or Ad-hoc)
   - Playbook path or paste playbook content directly
   - Add extra variables if needed
4. Save the template

### Configuring Environments

1. Navigate to **Environments** in the sidebar
2. Click **New** to create an environment
3. Add environment variables (e.g., APP_ENV, DEBUG settings)
4. Save the environment

### Executing Deployments

1. Navigate to **Deployments** in the sidebar
2. Click **New** to create a deployment
3. Select:
   - Task template to execute
   - Environment (optional)
   - One or more servers to run on
4. Save the deployment (status: pending)
5. Click the **Execute** button to run the deployment
6. The deployment will be queued and processed by Laravel Horizon
7. View the deployment to see console output and execution status

### Monitoring

- **Dashboard**: View statistics about servers, templates, and deployments
- **Horizon**: Access at `http://localhost:8000/horizon` to monitor job queues
- **Deployments**: Check status, view logs, and track execution time

## Project Structure

```
app/
├── Filament/
│   ├── Resources/         # Filament CRUD resources
│   │   ├── InventoryResource.php
│   │   ├── TaskTemplateResource.php
│   │   ├── KeystoreResource.php
│   │   ├── EnvironmentResource.php
│   │   └── DeploymentResource.php
│   └── Widgets/           # Dashboard widgets
├── Jobs/
│   └── ExecuteAnsibleDeployment.php  # Background job for deployments
├── Models/                # Eloquent models
│   ├── Inventory.php
│   ├── TaskTemplate.php
│   ├── Keystore.php
│   ├── Environment.php
│   └── Deployment.php
└── Services/
    └── AnsibleService.php # Ansible execution logic

database/
└── migrations/            # Database schema
```

## Security Considerations

- **Keystore Data**: Private keys and passwords are stored in the database. Consider encrypting sensitive columns in production.
- **Ansible Execution**: The system executes Ansible commands on the server. Ensure proper file permissions and user access controls.
- **Authentication**: FilamentPHP handles user authentication. Configure proper user roles and permissions.

## Troubleshooting

### Horizon not processing jobs
- Ensure Redis is running: `redis-cli ping`
- Check Horizon status: `php artisan horizon:status`
- Restart Horizon: `php artisan horizon:terminate` then start again

### Database connection errors
- Verify PostgreSQL is running
- Check database credentials in `.env`
- Ensure the database exists: `createdb ansiblephp`

### Ansible execution errors
- Verify Ansible is installed: `ansible --version`
- Check server connectivity and credentials
- Review deployment console output for specific errors

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

Open-source software licensed under the MIT license.
