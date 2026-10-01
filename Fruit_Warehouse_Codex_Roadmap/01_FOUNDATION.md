# Phase 01 — Foundation & Project Audit

## Goal
Prepare the existing Laravel project for the Fruit Warehouse system without destroying existing functionality.

## Tasks
- Inspect Laravel/PHP versions.
- Inspect existing authentication.
- Inspect existing roles/permissions.
- Inspect layouts, Bootstrap, jQuery and AJAX conventions.
- Inspect existing route structure.
- Inspect database conventions.
- Inspect existing reusable components.
- Identify conflicts before implementation.

## Required architecture
Prefer:
- app/Actions
- app/Services
- app/Queries
- app/Policies
- app/Http/Requests
- app/Events
- app/Jobs
- app/Exports

## AJAX standard
Use a consistent JSON response:
success, message, data, errors.

## Completion
Document what already exists and what must be added. Do not duplicate existing systems.
