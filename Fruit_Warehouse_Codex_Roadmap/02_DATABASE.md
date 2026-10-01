# Phase 02 — Database Architecture

## Goal
Create the database foundation.

## Tables
- shops
- categories
- fruits
- units
- orders
- order_items
- fruit_box_configurations
- inventory
- inventory_transactions
- audit_logs
- optional order_cutoff_settings
- optional system_settings

## orders
Fields:
id, shop_id, order_date, status, submitted_at, prepared_at, ready_at, sent_at, cancelled_at, created_by, updated_by, notes, timestamps.

Add:
UNIQUE(shop_id, order_date)
Indexes for shop_id, order_date, status and common combinations.

## order_items
Fields:
id, order_id, fruit_id, unit_type, box_configuration_id nullable, quantity, unit_weight_kg nullable, converted_weight_kg nullable, notes, timestamps.

Indexes:
order_id, fruit_id, order_id+fruit_id.

## Critical rule
Historical box weight must be preserved in order_items. Changing a box configuration later must not change historical orders.

## Inventory
Use KG as the internal comparison unit while preserving transaction details.

## Completion
Run migrations, verify foreign keys/indexes and document schema decisions.
