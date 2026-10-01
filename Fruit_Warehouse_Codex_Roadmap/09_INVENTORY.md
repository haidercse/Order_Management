# Phase 09 — Inventory Management

## Goal
Build reliable inventory tracking.

## Inventory
Maintain current stock in KG for comparison.

## Inventory transactions
Record:
- purchase
- adjustment
- damaged
- correction
- issue/other business transaction

Fields include fruit, quantity_kg, transaction type, reference, user, timestamp.

## CRUD UX
One-page AJAX interface.

## Rules
Never silently overwrite stock without a transaction/audit trail.

## Reports
- current inventory
- transaction history
- fruit-level stock
- stock alerts

## Future compatibility
Keep design ready for suppliers, batches, expiry and multiple warehouses.
