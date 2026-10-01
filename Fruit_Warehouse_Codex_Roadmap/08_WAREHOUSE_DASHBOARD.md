# Phase 08 — Attractive Warehouse Dashboard

## Goal
Create a highly informative, modern dashboard.

## KPI cards
- total shops
- orders received
- pending
- preparing
- ready
- sent
- fruit types
- shortage items

## Sections
1. Order status overview
2. Shop order progress
3. Fruit demand summary
4. Inventory alerts
5. Shortage alerts
6. Recent orders
7. Recent activity
8. Charts

## AJAX
Dashboard data should be loaded/updated without full reload.

## Performance
Only load current-day data initially. Do not load years of historical data on dashboard load.

## Polling
Poll lightweight changes every 10–15 seconds if desired. Do not reload the whole dashboard.

Use last_seen_order_id or equivalent incremental endpoint.
