# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Initial release as FilamentPHP 3 plugin
- Inventory Resource for managing Ansible servers
- Task Template Resource for creating Ansible playbooks
- Keystore Resource for managing SSH keys and passwords
- Deployment Resource for executing Ansible tasks
- DeploymentStatsWidget for dashboard statistics
- LatestDeployments widget for recent deployment monitoring
- Background job processing with Laravel Horizon integration
- Real-time deployment output viewing
- Service provider with auto-discovery
- Publishable migrations and configuration

### Changed
- Converted from standalone Laravel application to FilamentPHP 3 plugin
- Changed package name from `laravel/laravel` to `visio-soft/laraansible`
- Changed package type from `project` to `library`
- Updated all namespaces from `App\` to `VisioSoft\LaraAnsible\`
- Restructured code to follow FilamentPHP plugin conventions

### Security
- Keystore data (SSH keys and passwords) stored securely in database
- Sensitive fields hidden from API responses

## [0.1.0] - 2025-11-14

### Added
- Initial standalone application release
