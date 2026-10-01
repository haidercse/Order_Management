# Phase 11 — Reporting Architecture

## Goal
Create reusable report services/queries.

## Reports
- daily warehouse report
- shop-wise report
- fruit demand report
- inventory report
- shortage report
- purchase requirement report
- monthly report

## Architecture
Use ReportService plus dedicated Query classes.

Example:
DailyOrderQuery
FruitDemandQuery
ShortageQuery
WarehouseDashboardQuery

## Rules
Dashboard, PDF and Excel should use the same business calculations where possible.

## Filters
date/date range, shop, fruit, category, status.

## Pagination
Use server-side pagination for on-screen historical tables.
