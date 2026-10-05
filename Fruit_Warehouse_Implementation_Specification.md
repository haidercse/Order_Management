# Fruit Warehouse Management System — Implementation Specification

## 1. Project Objective

Build a Laravel-based Fruit Warehouse Management System for approximately 80–85 fruit seller shops and one central warehouse.

The system replaces the current manual paper/WhatsApp-style ordering process.

### Main business flow

1. Shop Manager logs in.
2. Shop Manager sees only the products available for ordering.
3. Shop Manager enters the required quantity primarily in **Caja**.
4. Shop Manager submits the next-day order.
5. Warehouse Manager immediately sees the submitted order.
6. Warehouse Manager can open each shop separately and prepare that shop's order.
7. Warehouse Manager also sees an aggregated product-wise demand from all shops.
8. Warehouse compares total demand against current warehouse stock.
9. If stock is sufficient, purchase required is `0`.
10. If stock is insufficient, the system shows shortage and purchase required.
11. Warehouse Manager can print/download a professional PDF.
12. PDF must contain product, quantity, weight information, price/kg or price/Caja where applicable, and total price.
13. Shop Managers must never be able to see another shop's orders.

---

# 2. Important Business Terminology

The real paper list uses Spanish product names and the application should preserve those names.

Examples:

- ACELGAS
- AGUACATE C
- AGUACATE DOMINICANO
- AGUACATE OFF
- AJO
- ALBAHACA
- APIO
- ARANDANOS
- BANANAS B
- BATATA
- BATATA ROJA
- BERENJENA
- BROCOLI
- CALABACIN
- CILANTRO
- KIWI B
- LIMON
- MANGO
- MANZANA
- NARANJA
- PATATA
- PEPINO
- TOMATE
- UVAS
- ZANAHORIA

Do not automatically translate the product names into English.

---

# 3. Roles

The existing Spatie Permission system must be reused.

Minimum roles:

### Super Admin

Can manage the complete system.

### Warehouse Manager

Can:

- View all shop orders
- View individual shop orders
- Change warehouse order status
- View total demand by product
- View inventory
- Compare demand vs stock
- See shortages
- See purchase requirements
- View prices
- Generate/print PDF reports
- Export reports where implemented

### Shop Manager

Can:

- Login
- View own shop
- View available products
- Create today's/next-day order
- Enter Caja quantity
- Submit order
- View own submitted order
- View own order history if permitted

Cannot:

- View another shop
- View another shop's order
- View warehouse-wide totals
- View warehouse inventory
- Change another shop's order

---

# 4. Shop Manager Frontend

The Shop Manager ordering page should be mobile-friendly.

Do not make the user open a separate page for every product.

Use one main ordering page.

Example:

```text
------------------------------------------------
SHOP: ENTREVÍAS
ORDER DATE: 04/10/2026
------------------------------------------------

PRODUCT                     CAJAS

ACELGAS                     [     ]
AGUACATE C                  [     ]
AGUACATE DOMINICANO         [     ]
AGUACATE OFF                [     ]
AJO                         [     ]
ALBAHACA                    [     ]
APIO                        [     ]
ARANDANOS                   [     ]
BANANAS B                   [     ]
BATATA                      [     ]
...
PATATA AGRIA                [     ]
PEPINO                      [     ]
TOMATE                      [     ]

                [ SAVE DRAFT ]

                [ SUBMIT ORDER ]
```

### UX requirements

- AJAX submission
- No full-page reload
- Loading indicator
- Success toast
- Validation errors
- Disable submit button while submitting
- Confirmation before final submission
- Mobile responsive
- Search product field
- Optional category filter
- Show only active products
- Preserve entered values when validation fails

---

# 5. Order Input

The primary shop input is **CAJA** because the actual business workflow is based on Caja.

However, the system must also support KG where required.

The order item should be capable of storing:

```text
quantity
unit
box_quantity
gross_weight_kg
tare_weight_kg
net_weight_kg
unit_price
price_type
total_price
```

Do not assume every product has the same Caja weight.

Different products can have different Caja configurations.

---

# 6. Box/Caja Weight Logic

The system must support product-specific Caja weights.

Example:

```text
PATATA AGRIA
1 Caja = 10 KG
```

Another product may have:

```text
AGUACATE
1 Caja = 4 KG
```

Do not hard-code:

```text
1 Caja = 10 KG
```

globally.

The weight must come from the product's configured Caja/box configuration.

If a product supports multiple Caja types, the configuration should identify the type.

Example:

```text
Product: LIMON

Caja S = 5 KG
Caja M = 10 KG
Caja L = 15 KG
```

---

# 7. Historical Data Rule

This is very important.

When an order is submitted, do not depend only on the current product price or current box weight.

Save a snapshot in the order item.

Example:

```text
product_id
quantity
unit
box_configuration_id
box_weight_kg
gross_weight_kg
tare_weight_kg
net_weight_kg
unit_price
price_type
total_price
```

If tomorrow the price changes, yesterday's order must still show yesterday's price.

If next month the Caja weight changes, old orders must still retain the original weight used for that order.

---

# 8. Gross Weight / Tare / Net Weight

The paper report contains:

```text
KILO
T KILO
TARA
NETTO
PRECIO
TOTAL
```

The system should preserve this calculation logic.

### Gross/Total weight

`T KILO` represents the total/gross weight before tare.

### Tare

`TARA` represents packaging/container tare weight.

### Net weight

```text
NETTO = T KILO - TARA
```

Example:

```text
T KILO = 12.2 KG
TARA   = 1.0 KG

NETTO  = 11.2 KG
```

The backend must calculate this server-side.

Do not trust a browser-calculated total.

---

# 9. Price Calculation

If the product is priced by KG:

```text
TOTAL = NETTO KG × PRICE PER KG
```

Example:

```text
NETTO = 11.2 KG
PRICE = €0.80/KG

TOTAL = 11.2 × 0.80
      = €8.96
```

If the product is priced directly by Caja:

```text
TOTAL = CAJA QUANTITY × PRICE PER CAJA
```

The product configuration should determine which price type is used.

Possible values:

```text
kg
caja
```

The backend must calculate the final amount.

---

# 10. Order Lifecycle

Recommended order statuses:

```text
DRAFT
SUBMITTED
PREPARING
READY
SENT
CANCELLED
```

### DRAFT

Shop has started the order but has not submitted it.

### SUBMITTED

Shop has submitted the order.

Warehouse can now see it.

### PREPARING

Warehouse is preparing the order.

### READY

Order is ready to leave warehouse.

### SENT

Order has been sent to the shop.

### CANCELLED

Order was cancelled by an authorized user.

---

# 11. One Shop — One Order Per Day

Normally each shop should have one order for a particular order date.

Recommended database rule:

```text
UNIQUE(shop_id, order_date)
```

This prevents accidental duplicate orders.

If the business later needs multiple orders per day, this rule can be changed intentionally.

Do not allow duplicate orders accidentally through double clicking or repeated AJAX requests.

---

# 12. Shop Order Ownership

Every shop request must be protected server-side.

Never trust:

```text
shop_id
user_id
order_id
```

from the browser.

For a Shop Manager:

```text
authenticated_user.shop_id
```

must determine the shop.

When opening an order:

```text
order.shop_id === authenticated_user.shop_id
```

must be checked.

If not, return HTTP 403.

This check must happen in backend authorization/policy/service logic.

Do not rely only on hiding buttons in JavaScript.

---

# 13. Warehouse Orders Page

Warehouse Manager needs a separate order management page.

Suggested layout:

```text
--------------------------------------------------------
WAREHOUSE ORDERS
Date: [ 04/10/2026 ]

Search Shop: [____________]
Status:      [All ▼]
--------------------------------------------------------

SHOP             ORDER DATE       ITEMS    STATUS
--------------------------------------------------------
ENTREVÍAS        04/10/2026       32      SUBMITTED
SHOP 02          04/10/2026       18      PREPARING
SHOP 03          04/10/2026       25      READY
...
--------------------------------------------------------
```

Use server-side pagination.

Do not load all historical orders into the browser.

---

# 14. Individual Shop Order

Warehouse Manager can open one shop's order.

Example:

```text
TIENDA: ENTREVÍAS
DATE: 04/10/2026
STATUS: SUBMITTED

PRODUCT             CAJAS
--------------------------------
ACELGAS                1
AGUACATE C             1
AJO                    5
ALBAHACA               1
APIO                   3
BANANAS B             18
...
```

For products where detailed weight information is required:

```text
PRODUCT
CAJA
KILO
T KILO
TARA
NETTO
PRICE
TOTAL
```

---

# 15. Warehouse Product Summary

This is one of the most important features.

Warehouse Manager needs an aggregated product-wise list.

Example:

```text
--------------------------------------------------------------
PRODUCT          TOTAL DEMAND    STOCK    SHORTAGE    PURCHASE
--------------------------------------------------------------
ACELGAS              25           40        0            0
AJO                  32           50        0            0
BANANAS B            60           45       15           15
PATATA AGRIA         40           30       10           10
PEPINO               20           35        0            0
--------------------------------------------------------------
```

---

# 16. Inventory Comparison

The system must compare:

```text
TOTAL SHOP DEMAND
vs
CURRENT WAREHOUSE STOCK
```

Formula:

```text
shortage = max(required - available, 0)
```

Purchase:

```text
purchase_needed = max(required - available, 0)
```

Remaining stock when sufficient:

```text
remaining_stock = max(available - required, 0)
```

Example 1:

```text
Required = 10 Caja
Available = 30 Caja

Shortage = 0
Purchase = 0
Remaining = 20 Caja
```

Example 2:

```text
Required = 40 Caja
Available = 30 Caja

Shortage = 10 Caja
Purchase = 10 Caja
Remaining = 0
```

---

# 17. Do Not Mix KG and Caja Incorrectly

This is critical.

The system must compare quantities in a common unit.

Recommended internal comparison unit:

```text
KG
```

If shops order:

```text
10 Caja
```

and:

```text
1 Caja = 10 KG
```

then:

```text
10 Caja = 100 KG
```

Warehouse comparison can therefore use:

```text
Required KG
Available KG
Shortage KG
Purchase KG
```

But the UI should still show the original Caja quantity.

Example:

```text
PATATA AGRIA

Shop demand:
40 Caja

Equivalent:
400 KG

Warehouse:
30 Caja
300 KG

Shortage:
10 Caja
100 KG
```

Do not silently assume a fixed conversion for all products.

---

# 18. Warehouse Dashboard

Dashboard should show current-day operational information.

### KPI cards

```text
TOTAL SHOPS
85

ORDERS RECEIVED
72

PENDING
13

PREPARING
25

READY
34

SENT
10
```

Additional cards:

```text
PRODUCTS ORDERED
SHORTAGE ITEMS
PURCHASE REQUIRED
TOTAL ORDER VALUE
```

---

# 19. Dashboard Product Shortage Widget

Example:

```text
TOP SHORTAGE ITEMS

PATATA AGRIA       10 Caja
BANANAS B          15 Caja
AGUACATE C          8 Caja
PEPINO              5 Caja
```

Use clear visual indicators for:

- Sufficient
- Low
- Shortage
- Critical

---

# 20. AJAX Requirements

The project should use AJAX for operational actions.

Use AJAX for:

- Create order
- Save draft
- Submit order
- Update order
- Change warehouse status
- Inventory adjustments
- CRUD master data
- Search
- Filters
- Modal forms
- PDF generation request where appropriate

No unnecessary full-page reloads.

---

# 21. Warehouse Refresh

WebSockets are NOT required for the first version.

Use AJAX polling.

Recommended:

```text
10–15 seconds
```

for the warehouse dashboard.

Do not reload the entire page.

Only refresh relevant sections:

```text
order count
new orders
status cards
shortage summary
recent orders
```

The system should be designed so WebSockets can be introduced later without rewriting business logic.

---

# 22. PDF — Shop Order

The system should generate a printable PDF for one shop.

Example:

```text
BISMILLAHIR RAHMANIR RAHEEM

TIENDA : ENTREVÍAS
FECHA  : 04/10/2026

---------------------------------------------------------
NO | PRODUCT | CAJA | T KILO | TARA | NETTO | PRICE | TOTAL
---------------------------------------------------------
1  | ACELGAS | 1    | 12.2   | 1.0  | 11.2  | 0.80  | 8.96
2  | AJO     | 5    | 5.0    | 0    | 5.0   | 2.40  | 12.00
...
---------------------------------------------------------
TOTAL: € XXXXX
```

The PDF should be A4 printable.

---

# 23. PDF — Warehouse Consolidated Report

Warehouse needs another PDF.

Recommended sections:

## Section A — Shop Summary

```text
SHOP             ITEMS       STATUS
-------------------------------------
ENTREVÍAS        32          READY
SHOP 02          18          PREPARING
SHOP 03          25          SUBMITTED
```

## Section B — Product Demand

```text
PRODUCT         DEMAND      STOCK     SHORTAGE    PURCHASE
------------------------------------------------------------
PATATA AGRIA    40 Caja     30 Caja   10 Caja     10 Caja
BANANAS B       60 Caja     45 Caja   15 Caja     15 Caja
AJO             32 Caja     50 Caja    0 Caja      0 Caja
```

## Section C — Purchase Requirement

Only products where:

```text
purchase_needed > 0
```

should be highlighted/listed.

---

# 24. PDF Price Information

Final warehouse/shop report should support:

```text
PRICE/KG
PRICE/CAJA
TOTAL
```

depending on product pricing configuration.

The report should never calculate using a current price if the order already has a historical price snapshot.

---

# 25. Inventory

Inventory must not simply overwrite stock without history.

Use inventory transactions for:

```text
PURCHASE
ADJUSTMENT
DAMAGE
WASTE
RETURN
MANUAL_CORRECTION
ISSUE
```

Example:

```text
PATATA AGRIA

Opening stock:       100 KG
Purchase:             50 KG
Issued to orders:     40 KG
Damage:                5 KG
Current stock:        105 KG
```

All important changes should have an audit trail.

---

# 26. Master Data

Admin should have AJAX CRUD for:

- Shops
- Shop Users
- Categories
- Fruits
- Units
- Box/Caja Configurations
- Prices
- Inventory

All CRUD pages should support:

- Search
- Pagination
- Add
- Edit
- Delete/soft delete where appropriate
- Active/Inactive status
- Modal form
- AJAX save
- Validation
- Toast messages

---

# 27. Product Master

Each fruit/product should support:

```text
name
code
category_id
unit
status
sort_order
```

Product names remain Spanish.

Example:

```text
name: PATATA AGRIA
code: PAT-AGR
category: PATATA
status: active
```

---

# 28. Price Master

Price should be managed separately from the product.

Possible fields:

```text
fruit_id
price_type
price
effective_from
effective_to
status
```

Price types:

```text
KG
CAJA
```

When an order is submitted, the active price must be copied into the order item.

---

# 29. Box Configuration

Box/Caja configuration should support:

```text
fruit_id
box_name
box_code
weight_kg
is_default
status
```

Example:

```text
PATATA AGRIA
Standard Caja
10 KG
```

If a product has multiple Caja types, each one must be separately configurable.

---

# 30. Validation

Shop order validation:

- Product must exist
- Product must be active
- Quantity must be numeric
- Quantity cannot be negative
- Box configuration must belong to the selected product
- Shop must belong to authenticated user
- Order date must be valid
- Duplicate daily order must be prevented
- User cannot submit another shop's order

Server-side validation is mandatory.

---

# 31. Security

Implement:

- Authentication
- Spatie permissions
- Policies
- Form Requests
- CSRF
- Mass assignment protection
- Server-side ownership checks
- Authorization on every sensitive action
- Secure sessions
- HTTPS in production
- `APP_DEBUG=false` in production

Never trust:

```text
shop_id
user_id
order_id
status
price
total
box_weight
```

from the browser.

---

# 32. Backend Architecture

Avoid putting the entire business logic into controllers.

Recommended structure:

```text
app/
├── Actions/
├── Services/
├── Queries/
├── Policies/
├── Http/
│   ├── Controllers/
│   └── Requests/
├── Events/
├── Jobs/
└── Exports/
```

Example:

```text
OrderController
    ↓
CreateOrderAction
    ↓
Order / OrderItem
```

Warehouse summary:

```text
WarehouseController
    ↓
WarehouseDemandQuery
    ↓
MySQL aggregation
```

PDF:

```text
ReportController
    ↓
DailyWarehouseReportService
    ↓
PDF generator
```

---

# 33. Performance

Expected scale:

```text
80–85 shops
daily orders
many products per order
```

This is manageable with proper database design.

Use:

- Database indexes
- Server-side pagination
- Eager loading
- Query aggregation
- Avoid N+1 queries
- Avoid loading all historical orders
- Chunk large exports
- Use date indexes
- Use indexed foreign keys

For daily product totals, use SQL aggregation instead of loading every order into PHP.

---

# 34. Recommended Indexes

Ensure indexes exist for frequently queried columns.

Orders:

```text
shop_id
order_date
status
(shop_id, order_date)
(order_date, status)
```

Order items:

```text
order_id
fruit_id
```

Inventory:

```text
fruit_id
```

Prices:

```text
fruit_id
effective_from
status
```

Box configurations:

```text
fruit_id
status
```

---

# 35. Order Submission Transaction

Final submission should be handled inside a database transaction.

Conceptually:

```text
BEGIN

validate order
validate shop ownership
validate products
load active prices
load box configuration
calculate weights
calculate net weight
calculate totals
save order
save order items
set status = SUBMITTED

COMMIT
```

If any critical operation fails:

```text
ROLLBACK
```

Do not leave half-created orders.

---

# 36. Prevent Double Submission

A user may click the Submit button twice.

Frontend:

- Disable button immediately
- Show loading state

Backend:

- Validate current order status
- Use unique constraints
- Use transaction
- Make submission operation idempotent where practical

---

# 37. Testing Plan

Before production, test:

### Authentication

- Super Admin login
- Warehouse Manager login
- Shop Manager login

### Shop isolation

Shop 01 must not see Shop 02 order.

### Ordering

- Create draft
- Edit draft
- Submit order
- Duplicate submit
- Empty order
- Invalid quantity
- Inactive product

### Box calculation

Test multiple box weights.

### Price calculation

Test KG price and Caja price.

### Tare calculation

```text
gross - tare = net
```

### Warehouse

- Aggregate demand
- Compare inventory
- Calculate shortage
- Calculate purchase requirement

### PDF

- Shop PDF
- Warehouse PDF
- Price
- Gross
- Tare
- Net
- Total

### Security

Try manually changing:

```text
shop_id
order_id
user_id
```

and verify access is denied.

---

# 38. Suggested Development Order

Implement in this order.

## Phase 1

Existing database + Models + Relationships.

## Phase 2

Roles + permissions + policies.

## Phase 3

Shop Management.

## Phase 4

Category/Product Management.

## Phase 5

Box/Caja Configuration.

## Phase 6

Price Management.

## Phase 7

Shop Manager Ordering Page.

## Phase 8

Order Submission + Validation.

## Phase 9

Warehouse Order Management.

## Phase 10

Inventory.

## Phase 11

Demand vs Inventory comparison.

## Phase 12

Shortage/Purchase calculation.

## Phase 13

Warehouse Dashboard.

## Phase 14

Shop PDF.

## Phase 15

Warehouse Consolidated PDF.

## Phase 16

Excel export.

## Phase 17

Security audit.

## Phase 18

Performance optimization.

## Phase 19

Full testing.

---

# 39. Final User Experience

## Shop Manager

```text
LOGIN
  ↓
SHOP DASHBOARD
  ↓
TODAY / TOMORROW ORDER
  ↓
PRODUCT LIST
  ↓
ENTER CAJAS
  ↓
SAVE / SUBMIT
  ↓
ORDER CONFIRMED
```

## Warehouse Manager

```text
LOGIN
  ↓
WAREHOUSE DASHBOARD
  ↓
NEW ORDERS
  ↓
SHOP-WISE ORDERS
  ↓
PREPARE ORDERS
  ↓
PRODUCT-WISE TOTAL
  ↓
COMPARE WITH STOCK
  ↓
SHORTAGE / PURCHASE
  ↓
PRINT PDF
```

---

# 40. Important Implementation Rule

Do not change the database structure just because the frontend can calculate something.

The backend must remain the source of truth.

Frontend can calculate/display temporary values for UX, but final:

- quantity
- box weight
- gross weight
- tare
- net weight
- price
- total
- shortage
- purchase requirement

must be validated/calculated server-side.

---

# 41. Definition of Done

The feature is considered complete only when:

- Shop Manager can submit an order.
- Shop Manager can only access their own shop.
- Warehouse Manager can see all submitted orders.
- Warehouse Manager can open each shop order.
- Product totals are aggregated correctly.
- Inventory comparison works.
- Shortage calculation works.
- Purchase requirement works.
- Caja/KG conversion works.
- Tare/net calculation works.
- Historical price is preserved.
- Historical box weight is preserved.
- PDF contains required business fields.
- AJAX operations do not require unnecessary page reloads.
- Permissions are enforced server-side.
- Validation works.
- Duplicate orders/submissions are prevented.
- Tests cover the important business rules.

---

# 42. Important Note About Existing Database

The implementation must use the database that has already been created.

Before creating new migrations, models, or relationships:

1. Inspect the actual existing migrations.
2. Inspect table names and columns.
3. Inspect foreign keys.
4. Inspect Spatie Permission configuration.
5. Inspect existing User model.
6. Do not duplicate existing tables.
7. Do not delete existing working features.
8. Add only the missing pieces required by this specification.

If the existing database differs from this document, treat the **actual database schema as the source of truth** and adapt the implementation accordingly.

The business workflow described here is the source of truth for the application behavior.
