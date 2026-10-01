# Phase 03 — Authentication, Roles & Shop Isolation

## Goal
Guarantee strict role-based access.

## Roles
- Admin
- Warehouse Manager
- Shop Manager

Reuse existing dynamic role/permission functionality if available.

## Shop Manager rule
A shop manager may only access records belonging to their assigned shop.

Never trust shop_id from request input.

## Required protections
- middleware
- policies/gates
- server-side ownership checks
- route protection
- permission checks

## Test cases
- Shop A cannot view Shop B order.
- Shop A cannot edit Shop B order.
- Shop A cannot change Shop B status.
- Warehouse can access authorized warehouse records.
- Admin has configured access.

Return 403 or safe not-found responses.

## Completion
Create/update policies and authorization tests.
