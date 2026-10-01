# Fruit Warehouse - Database + Seeder Package

This package is based on the existing Laravel project, the existing Spatie Permission setup, the earlier warehouse requirements, and the two supplied paper-list photos.

## Important

- `RouteSeeder` is intentionally NOT included.
- The old `permission_groups` migration should remain in your project.
- The existing Spatie Permission migration should remain in your project.
- `add_group_id_to_permissions_table` is a separate migration and must run AFTER both `permissions` and `permission_groups` exist.
- Do not duplicate the `users` migration. This package only adds `shop_id`.
- The seeded box weights and prices are TEST PLACEHOLDERS. Replace them with the real values before production.

## Main business data model

Shop -> User -> Order -> Order Items -> Fruit
Fruit -> Category / Unit / Box Configuration / Price
Fruit + Unit + Box Configuration -> Inventory Stock
Orders + Inventory -> Warehouse shortage/purchase comparison

## Why order_items stores snapshots

Historical orders must not change when a fruit's box weight or price changes later.

Each item therefore stores:
- quantity
- unit
- box configuration
- unit_weight_kg
- converted_kg
- unit_price
- line_total

## Warehouse comparison

Example:
- Inventory: PATATA AGRIA = 30 CAJA
- Orders: shops request 10 CAJA
- Remaining = 20 CAJA
- Purchase needed = 0

If orders become 40 CAJA:
- Available = 30 CAJA
- Required = 40 CAJA
- Shortage = 10 CAJA
- Purchase needed = 10 CAJA

For KG/BOX mixed demand, use `converted_kg` as the common comparison value while still displaying native requested units.

## Run order

```bash
php artisan config:clear
php artisan cache:clear
php artisan migrate:fresh
php artisan db:seed
```

## Test credentials

Super Admin:
`super.admin@gmail.com` / `12345678`

Warehouse Manager:
`warehouse.manager@gmail.com` / `12345678`

Shop managers:
`shop01@example.com` through `shop85@example.com`
Password: `12345678`

## Do not use these test passwords in production.
