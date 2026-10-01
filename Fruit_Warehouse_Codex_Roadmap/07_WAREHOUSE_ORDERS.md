# Phase 07 — Warehouse Order Processing

## Goal
Allow warehouse staff to process every shop order.

## Order statuses
DRAFT -> SUBMITTED -> PREPARING -> READY -> SENT
Alternative: CANCELLED.

## Warehouse list
Show:
- shop
- date
- status
- item count
- KG equivalent
- submitted time
- actions

Use:
- AJAX filters
- server-side pagination
- AJAX status updates
- modal/detail panel

## Order detail
Show all fruit items, quantity, unit, box type, box weight, KG equivalent and notes.

## Important
Status transitions must be authorized server-side.
