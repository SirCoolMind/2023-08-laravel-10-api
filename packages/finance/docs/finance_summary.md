# Finance Package Summary

The `finance` package provides comprehensive financial management capabilities within the Laravel application. It is designed to handle money-related entities such as accounts, categories, transactions, and transfers.

## Key Features

- **Account Management**: Supports managing various money accounts and keeping track of their balances.
- **Categorization**: Enables organization of transactions through Categories and Sub-categories.
- **Transactions Management**: Facilitates the recording and tracking of money transactions (incomes, expenses).
- **Transfers**: Handles transferring money between different accounts.
- **Excel Batch Processing**: Supports importing/exporting large batches of transaction data using Excel files.
- **Dashboard & Lookups**: Provides endpoints for dashboard summaries and lookups for frontend interfaces.

## Structure

The package follows a standard Laravel package structure with modular components:
- **Models**: Defines the core entities (`MoneyAccount`, `MoneyTransaction`, etc.).
- **Controllers**: API endpoints for managing the entities.
- **Jobs**: Background jobs like `ProcessAccountBalance`.
- **Routes**: API and lookup routing definitions.
- **Database Migrations**: Schema creation for the required tables.
