# Phase 17 — Testing & QA

## Authentication
- login
- logout
- unauthorized routes

## Isolation
- Shop A cannot view Shop B.
- Shop A cannot edit Shop B.
- Shop A cannot change Shop B status.

## Orders
- create
- edit
- submit
- duplicate prevention
- invalid quantity
- invalid fruit
- invalid box
- status transitions

## Box conversion
Test different weights.
Verify historical conversion does not change after configuration edits.

## Warehouse
Test aggregation, inventory comparison, shortage and purchase calculation.

## Reports
Verify PDF and Excel values against database.

## AJAX
Test validation, errors, loading states and no-reload behavior.

## Data
Test realistic data volume: 85 shops, one year of orders and multiple items.
