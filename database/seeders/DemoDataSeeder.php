<?php

namespace Database\Seeders;

use App\Models\Environment;
use App\Models\Inventory;
use App\Models\Keystore;
use App\Models\TaskTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample keystores
        $sshKey = Keystore::create([
            'name' => 'Production SSH Key',
            'description' => 'SSH key for production servers',
            'type' => 'ssh',
            'private_key' => '-----BEGIN OPENSSH PRIVATE KEY-----
(This would be a real SSH private key)
-----END OPENSSH PRIVATE KEY-----',
            'public_key' => 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQ...',
        ]);

        $passwordAuth = Keystore::create([
            'name' => 'Development Password',
            'description' => 'Password authentication for dev servers',
            'type' => 'password',
            'password' => 'SecurePassword123!',
        ]);

        // Create sample inventories
        Inventory::create([
            'name' => 'Web Server 01',
            'description' => 'Main production web server',
            'hostname' => '192.168.1.10',
            'port' => 22,
            'username' => 'deploy',
            'keystore_id' => $sshKey->id,
            'variables' => [
                'server_role' => 'web',
                'environment' => 'production',
            ],
            'is_active' => true,
        ]);

        Inventory::create([
            'name' => 'Database Server',
            'description' => 'Production database server',
            'hostname' => 'db.example.com',
            'port' => 22,
            'username' => 'root',
            'keystore_id' => $sshKey->id,
            'variables' => [
                'server_role' => 'database',
                'environment' => 'production',
            ],
            'is_active' => true,
        ]);

        Inventory::create([
            'name' => 'Development Server',
            'description' => 'Development environment server',
            'hostname' => '192.168.1.100',
            'port' => 22,
            'username' => 'developer',
            'keystore_id' => $passwordAuth->id,
            'variables' => [
                'server_role' => 'all',
                'environment' => 'development',
            ],
            'is_active' => true,
        ]);

        // Create sample environments
        Environment::create([
            'name' => 'Production',
            'description' => 'Production environment variables',
            'variables' => [
                'APP_ENV' => 'production',
                'APP_DEBUG' => 'false',
                'LOG_LEVEL' => 'error',
            ],
            'is_active' => true,
        ]);

        Environment::create([
            'name' => 'Staging',
            'description' => 'Staging environment variables',
            'variables' => [
                'APP_ENV' => 'staging',
                'APP_DEBUG' => 'true',
                'LOG_LEVEL' => 'debug',
            ],
            'is_active' => true,
        ]);

        // Create sample task templates
        TaskTemplate::create([
            'name' => 'Update System Packages',
            'description' => 'Update all system packages on the server',
            'playbook_path' => '/etc/ansible/playbooks/update.yml',
            'playbook_content' => '---
- name: Update system packages
  hosts: all
  become: yes
  tasks:
    - name: Update apt cache
      apt:
        update_cache: yes
        
    - name: Upgrade all packages
      apt:
        upgrade: dist',
            'extra_vars' => [
                'ansible_become_password' => '{{ become_password }}',
            ],
            'type' => 'playbook',
            'is_active' => true,
        ]);

        TaskTemplate::create([
            'name' => 'Deploy Application',
            'description' => 'Deploy the latest version of the application',
            'playbook_path' => '/etc/ansible/playbooks/deploy.yml',
            'playbook_content' => '---
- name: Deploy application
  hosts: all
  tasks:
    - name: Pull latest code
      git:
        repo: https://github.com/example/app.git
        dest: /var/www/app
        version: main
        
    - name: Install dependencies
      composer:
        command: install
        working_dir: /var/www/app
        
    - name: Run migrations
      command: php artisan migrate --force
      args:
        chdir: /var/www/app',
            'extra_vars' => [
                'branch' => 'main',
            ],
            'type' => 'playbook',
            'is_active' => true,
        ]);

        TaskTemplate::create([
            'name' => 'Restart Services',
            'description' => 'Restart web server and application services',
            'playbook_path' => '/etc/ansible/playbooks/restart.yml',
            'playbook_content' => '---
- name: Restart services
  hosts: all
  become: yes
  tasks:
    - name: Restart nginx
      service:
        name: nginx
        state: restarted
        
    - name: Restart php-fpm
      service:
        name: php8.3-fpm
        state: restarted',
            'type' => 'playbook',
            'is_active' => true,
        ]);
    }
}
