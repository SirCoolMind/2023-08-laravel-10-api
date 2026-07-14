# Kpop Collection Package Details

This document provides a detailed breakdown of the internal components of the kpop-collection package.

## 1. Models
The package defines its Eloquent models within the src/app/Models directory:
- **KpopEra**: Represents a Kpop era. This is likely the top-level entity categorizing releases.
- **KpopEraVersion**: Represents a specific version of a Kpop era (e.g., standard, limited edition).
- **KpopItem**: Represents an individual item in the collection (e.g., photocard, album). This model supports soft deleting, as indicated by recent migrations.

## 2. Controllers
Controllers are located in src/app/Http/Controllers:
- **KpopEraController**: Handles CRUD operations for Kpop eras.
- **KpopEraVersionController**: Handles CRUD operations for Kpop era versions.
- **KpopItemController**: Handles CRUD operations for Kpop items.
- **LookupController**: Provides lookup endpoints, likely used for populating dropdowns or providing reference data to the frontend.

## 3. Routes
The routes are defined in the src/routes directory and loaded by the KpopCollectionServiceProvider.
- **Prefix**: api/kpop/v1
- **Admin Routes**: Protected by the auth:api middleware.
  - apiResource('admin/kpop-era', KpopEraController::class)
  - apiResource('admin/kpop-era-version', KpopEraVersionController::class)
  - apiResource('admin/kpop-item', KpopItemController::class)
- **Lookup Routes**: Additional routes are loaded from src/routes/lookup.php.
- **Public Routes**: Currently, there are no public routes defined.

## 4. Database Migrations
Migrations are provided in src/database/migrations to structure the database:
- 2024_08_03_050227_create_kpop_eras_table.php
- 2024_08_03_050228_create_kpop_eras_versions_table.php
- 2024_08_03_050229_create_kpop_items_table.php
- 2025_02_12_050229_add_soft_delete_kpop_items_table.php (Adds soft deletes to items)

## 5. Jobs
Currently, there are **no Jobs** defined within the package (src/app/Jobs directory does not exist). The package operates synchronously via controllers and models.

## 6. Service Provider
KpopCollectionServiceProvider initializes the package by loading:
- Routes from src/routes/api.php
- Migrations from src/database/migrations
