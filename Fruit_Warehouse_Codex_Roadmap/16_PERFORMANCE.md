# Phase 16 — Performance & Scalability

## Target
Support 80–85 shops and growing historical data.

## Indexes
Orders:
- shop_id
- order_date
- status
- order_date + status
- shop_id + order_date
- unique shop_id + order_date

Order items:
- order_id
- fruit_id
- order_id + fruit_id

Inventory transactions:
- fruit_id
- created_at
- transaction_type

## Rules
- Avoid N+1 queries.
- Use eager loading when needed.
- Select only required columns.
- Aggregate in MySQL.
- Use EXPLAIN on slow queries.
- Avoid DATE(created_at) in indexed daily business queries.
- Use dedicated order_date.

## Pagination
25/50/100 records.

## Growth
Do not archive/partition prematurely. Scale based on actual metrics.
