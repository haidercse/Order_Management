# Phase 05 — Different Box Weights

## Goal
Support different box types and weights for different fruits.

## Example
Apple:
- Small Box = 8 KG
- Large Box = 12 KG

Banana:
- Standard = 10 KG
- Large = 15 KG

## CRUD
One-page AJAX management under Fruit/Box Configuration.

Fields:
fruit_id, box_name, box_code, weight_kg, is_default, status.

## Ordering behavior
If unit=KG:
box selector hidden.

If unit=BOX:
show box selector and effective weight.

Calculation:
quantity × box weight = converted_weight_kg.

## Historical integrity
When an order item is submitted, store unit_weight_kg and converted_weight_kg.

Never recalculate historical orders from the current box configuration.
