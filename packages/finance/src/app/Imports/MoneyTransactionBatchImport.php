<?php

namespace HafizRuslan\Finance\app\Imports;

use HafizRuslan\Finance\app\Enums\FinanceTypeEnum;
use HafizRuslan\Finance\app\Models\MoneyTransactionExcelBatch;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MoneyTransactionBatchImport implements ToCollection, WithHeadingRow
{
    protected $batchNo;
    protected $userId;

    public function __construct($batchNo, $userId)
    {
        $this->batchNo = $batchNo;
        $this->userId = $userId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Mapping headings from Template (e.g. 'date_yyyy_mm_dd' depending on heading slugging by Laravel Excel)
            // Or we can access by index if we don't use WithHeadingRow. Let's use WithHeadingRow and look at standard slugs.
            
            // Expected headers: Date (YYYY-MM-DD), Amount, Type (INCOME/EXPENSE), Description, Account Name, Category Name, Subcategory Name (Optional)
            // Slugged by Laravel Excel: date_yyyy_mm_dd, amount, type_incomeexpense, description, account_name, category_name, subcategory_name_optional
            
            // It's safer to get the keys and fallback. Or we map exactly based on index using ToModel or ToArray without WithHeadingRow.
            // Let's check row data and map safely.

            $dateRaw = $row['date_yyyy_mm_dd'] ?? $row['date'] ?? null;
            $amount = $row['amount'] ?? 0;
            
            // Skip completely empty rows
            if (empty($dateRaw) && empty($amount)) {
                continue;
            }

            $type = $row['type_incomeexpense'] ?? $row['type'] ?? 'EXPENSE';
            $description = $row['description'] ?? null;
            $accountName = $row['account_name'] ?? null;
            $categoryName = $row['category_name'] ?? null;
            $subcategoryName = $row['subcategory_name_optional'] ?? $row['subcategory_name'] ?? null;

            // Date parsing (Excel dates might be numeric)
            $parsedDate = null;
            if (is_numeric($dateRaw)) {
                try {
                    $parsedDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateRaw)->format('Y-m-d');
                } catch (\Exception $e) {
                    $parsedDate = null;
                }
            } else {
                try {
                    $parsedDate = Carbon::parse($dateRaw)->format('Y-m-d');
                } catch (\Exception $e) {
                    $parsedDate = null;
                }
            }

            // Basic validation
            $isValid = true;
            $errors = [];

            if (!$parsedDate) {
                $isValid = false;
                $errors[] = 'Invalid date format.';
            }
            if (!$amount || !is_numeric($amount)) {
                $isValid = false;
                $errors[] = 'Invalid amount.';
            }
            if (!$accountName) {
                $isValid = false;
                $errors[] = 'Account name is required.';
            }
            if (!$categoryName) {
                $isValid = false;
                $errors[] = 'Category name is required.';
            }
            if (!in_array(strtoupper($type), array_column(FinanceTypeEnum::cases(), 'value'))) {
                $isValid = false;
                $errors[] = 'Type must be INCOME or EXPENSE.';
            }

            MoneyTransactionExcelBatch::create([
                'batch_no' => $this->batchNo,
                'user_id' => $this->userId,
                'transaction_date' => $parsedDate,
                'amount' => $amount,
                'description' => $description,
                'type' => strtoupper($type),
                'money_account_name' => $accountName,
                'money_category_name' => $categoryName,
                'money_subcategory_name' => $subcategoryName,
                'is_valid' => $isValid,
                'error_message' => empty($errors) ? null : implode(' ', $errors),
            ]);
        }
    }
}
