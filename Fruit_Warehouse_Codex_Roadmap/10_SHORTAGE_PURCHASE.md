# Phase 10 — Demand, Shortage & Purchase Requirement

## Goal
Automatically calculate warehouse purchase requirements.

## Demand
Aggregate order_items in MySQL.

Do not load all orders into PHP and loop manually.

## Calculation
required_kg = SUM(converted_weight_kg)
difference = available_kg - required_kg
purchase_needed = max(required_kg - available_kg, 0)

## Table
Fruit | Required KG | Available KG | Shortage KG | Purchase Needed | Status

## Status
- sufficient
- low
- shortage
- critical

Thresholds should be configurable if business requires.

## Filters
date, fruit, category, status.

## Performance
Use indexed order_date/order status and fruit_id fields.
