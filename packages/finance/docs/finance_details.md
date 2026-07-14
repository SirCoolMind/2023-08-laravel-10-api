# Finance Package Details

This document provides a detailed breakdown of the components included in the `finance` package.

## 1. Models
The package includes several Eloquent models to represent the financial data structure:
- `MoneyAccount`: Represents an account (e.g., Bank, Cash) that holds money.
- `MoneyBalance`: Tracks the current balance of an account over time.
- `MoneyCategory`: Top-level categorization for transactions (e.g., Food, Transport).
- `MoneySubCategory`: More granular categorization under a main category.
- `MoneyTransaction`: Represents a single financial operation (income or expense).
- `MoneyTransactionExcelBatch`: Tracks the status of Excel file batch imports for transactions.
- `MoneyTransfer`: Represents a transfer of funds from one `MoneyAccount` to another.

## 2. Controllers
The application logic and API endpoints are handled by the following controllers:
- `FinanceDashboardController`: Handles the aggregation of data for financial dashboards (summaries, statistics).
- `LookupController`: Provides lookup endpoints (e.g., dropdown data for accounts and categories).
- `MoneyAccountController`: CRUD operations for `MoneyAccount`.
- `MoneyCategoryController`: CRUD operations for categories and sub-categories.
- `MoneyTransactionController`: CRUD operations for transactions.
- `MoneyTransactionExcelController`: Handles uploading, processing, and exporting transactions via Excel.
- `MoneyTransferController`: Manages the creation and tracking of money transfers.

## 3. Jobs
Background processing is utilized for intensive tasks:
- `ProcessAccountBalance`: A background job responsible for recalculating and updating the `MoneyBalance` for accounts after transactions or transfers occur.

## 4. Routes
API routes are split into two main files:
- `api.php`: Contains the standard CRUD and action routes for accounts, transactions, categories, transfers, and dashboard data.
- `lookup.php`: Contains specific endpoints for fetching lookup/reference data for dropdowns in the frontend interface.

## 5. Database Migrations
The database schema is managed via several migrations that create and alter tables for:
- `money_transactions`, `money_categories`, `money_subcategories`
- `money_accounts`, `money_balances`, `money_transfers`
- `money_transaction_excel_batches`
Updates include adding types, user references, and relationship keys across these tables to ensure data integrity.
