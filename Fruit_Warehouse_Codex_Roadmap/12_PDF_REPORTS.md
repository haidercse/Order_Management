# Phase 12 — PDF Reports

## Goal
Generate professional printable reports.

## Reports
- daily warehouse PDF
- shop picking-sheet PDF
- warehouse-weight final shop-order PDF
- shortage PDF
- purchase requirement PDF
- monthly summary PDF

## Shop order and warehouse weights
- Generate each shop's picking sheet from the seeded fruit catalog and that shop's submitted Caja quantities.
- Leave the weight cells blank on the picking sheet for warehouse entry.
- Warehouse managers record one actual total weight in kilograms for every ordered fruit before marking the order prepared.
- Generate the final order PDF only after all item weights are saved. Use the recorded weight in warehouse demand and stock-issue calculations; fall back to the configured box estimate until a warehouse weight is recorded.
- Do not invent tare values; show tare as unavailable unless tare is added to the order data model.

## Daily PDF
Include:
- date
- shop summary
- order status
- shop-wise items
- fruit demand
- inventory comparison
- shortages
- purchase requirement

## Rules
PDF generation must use optimized queries and only required fields.

Do not paginate the PDF itself like a web table.

For large reports consider queued generation.

## Design
Professional header, date, report title, tables, totals and footer/page numbers.
