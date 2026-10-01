# Phase 13 — Excel Exports

## Goal
Provide professional Excel exports.

## Exports
- daily orders
- shop orders
- fruit demand
- inventory
- shortage
- purchase requirement
- monthly report

## Implementation
Use Laravel Excel/PhpSpreadsheet-compatible tooling.

For large exports:
- FromQuery
- chunking
- lazy collections where appropriate

Never load a massive dataset into PHP memory unnecessarily.

## Columns
Keep business-friendly column names:
Date, Shop, Fruit, Quantity, Unit, Box Type, Box Weight KG, Equivalent KG, Status.

## AJAX
Export request can show loading state. Large exports can later become queued jobs.
