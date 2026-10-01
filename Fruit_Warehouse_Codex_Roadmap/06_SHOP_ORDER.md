# Phase 06 — Shop Manager Daily Ordering

## Goal
Create a mobile-first, single-page AJAX daily ordering experience.

## Flow
Login -> Today's Order -> Add Fruits -> Save Draft -> Submit.

## UI
- fruit selector
- quantity input
- unit selector
- box selector when BOX
- notes
- add/remove item
- summary
- submit button

## Rules
- One active order per shop per date.
- Unique shop_id + order_date.
- Prevent duplicate submission.
- Disable button while submitting.
- Validate quantity > 0.
- Validate box belongs to selected fruit.
- Shop can only modify its own order.
- Support cutoff/lock in future.

## AJAX
Create/update order without full page reload.

Show:
- item count
- KG equivalent
- status
- submitted time.

## Mobile
Large touch targets, simple controls, sticky submit where useful.
