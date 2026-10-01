# Phase 19 — Future Feature Roadmap

Do not implement these unless requested, but keep architecture extensible.

## Purchase Management
- suppliers
- supplier prices
- purchase orders
- invoices
- purchase history

## Advanced Inventory
- batches
- expiry
- damaged stock
- waste
- stock transfers
- minimum stock alerts

## Delivery
- routes
- drivers
- delivery sequence
- delivery confirmation
- proof of delivery

## Notifications
- WebSocket
- browser notifications
- email
- SMS
- other integrations

## Analytics
- demand trends
- seasonal trends
- purchase cost
- waste
- supplier performance

## Forecasting
Use historical demand to estimate future requirements.

## PWA
Allow shop managers to use the system like a mobile app.

## Multi-Warehouse
Warehouse-specific stock and order allocation.

## Multi-Company
Tenant/company separation if business expands.

## Real-Time
Laravel Events -> Redis -> Broadcasting -> WebSocket.

## Principle
Add complexity only when actual business scale or requirements justify it.
