# Phase 15 — Security Audit

## Requirements
- CSRF protection
- authentication
- authorization
- policies
- Form Requests
- mass-assignment protection
- HTTPS
- secure sessions
- rate limiting where useful
- server-side ownership checks

## Never trust browser input
Especially:
- shop_id
- user_id
- order_id
- status
- box_configuration_id

Verify ownership and relationships server-side.

## Production
APP_DEBUG=false.

Do not expose stack traces.

## Audit
Record important actions:
order submit/update/status change, inventory adjustment, box configuration change, permission changes.
