# Phase 18 — Production Deployment

## Recommended
VPS with optional cPanel/WHM.

Starting range:
- 2–4 vCPU
- 4–8 GB RAM
- NVMe SSD

## Server
- Linux
- PHP 8.2+
- MySQL 8+
- Nginx/Apache
- SSL
- Composer
- Cron
- Git/SSH if available

## Laravel
- production environment
- APP_DEBUG=false
- cache config/routes/views where appropriate
- storage permissions
- queue worker if required
- scheduler/cron

## Backup
- daily database backup
- weekly full backup
- off-server backup
- retention policy

## Monitoring
- CPU
- RAM
- disk
- database size
- Laravel logs
- failed jobs
- HTTP errors
- backup status

## Scaling
Upgrade VPS based on actual metrics, not simply data age.
