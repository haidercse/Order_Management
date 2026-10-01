# Fruit Warehouse Management System — Codex Roadmap

Use these files in order. Each file is a self-contained implementation phase.

## Rules for Codex
1. Inspect the existing project before changing anything.
2. Do not rewrite existing working features unnecessarily.
3. Follow Laravel conventions.
4. Use AJAX for interactive CRUD and normal business operations.
5. Avoid unnecessary full-page reloads.
6. Preserve role/permission architecture if it already exists.
7. Never trust shop_id/user_id/status from the browser.
8. Add migrations, validation, policies, tests and indexes where applicable.
9. Keep business logic in Services/Actions/Queries instead of fat controllers.
10. Before finishing each phase, test the affected functionality and report files changed.

## Execution Order

01 FOUNDATION
02 DATABASE
03 AUTHORIZATION
04 MASTER DATA
05 BOX CONFIGURATION
06 SHOP ORDER
07 WAREHOUSE ORDERS
08 WAREHOUSE DASHBOARD
09 INVENTORY
10 SHORTAGE PURCHASE
11 REPORTING
12 PDF
13 EXCEL
14 AJAX UX
15 SECURITY AUDIT
16 PERFORMANCE
17 TESTING
18 DEPLOYMENT
19 FUTURE EXTENSIONS

Do not skip phases unless the project already contains the required functionality.
