<?php

namespace HafizRuslan\Finance\app\Exports;

use HafizRuslan\Finance\app\Enums\FinanceTypeEnum;
use HafizRuslan\Finance\app\Models\MoneyAccount;
use HafizRuslan\Finance\app\Models\MoneyCategory;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MoneyTransactionTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new class implements FromArray, WithHeadings, WithStyles, WithTitle, WithEvents {
                public function array(): array
                {
                    $account = MoneyAccount::first()?->name ?? 'Example Account';
                    $category = MoneyCategory::first()?->name ?? 'Example Category';

                    return [
                        [
                            now()->format('Y-m-d'),
                            '150.00',
                            'EXPENSE',
                            'Example Expense',
                            $account,
                            $category,
                            '',
                        ],
                        [
                            now()->addDay()->format('Y-m-d'),
                            '2000.00',
                            'INCOME',
                            'Example Income',
                            $account,
                            $category,
                            '',
                        ],
                    ];
                }

                public function headings(): array
                {
                    return [
                        'Date (YYYY-MM-DD)',
                        'Amount',
                        'Type (INCOME/EXPENSE)',
                        'Description',
                        'Account Name',
                        'Category Name',
                        'Subcategory Name (Optional)',
                    ];
                }

                public function styles(Worksheet $sheet)
                {
                    return [
                        1 => ['font' => ['bold' => true]],
                    ];
                }

                public function title(): string
                {
                    return 'Template';
                }

                public function registerEvents(): array
                {
                    return [
                        AfterSheet::class => function(AfterSheet $event) {
                            $sheet = $event->sheet->getDelegate();
                            $rowCount = 1000;

                            // C: Type
                            $typeValidation = $sheet->getCell('C2')->getDataValidation();
                            $typeValidation->setType(DataValidation::TYPE_LIST);
                            $typeValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                            $typeValidation->setAllowBlank(false);
                            $typeValidation->setShowInputMessage(true);
                            $typeValidation->setShowErrorMessage(true);
                            $typeValidation->setShowDropDown(true);
                            $typeValidation->setErrorTitle('Input error');
                            $typeValidation->setError('Value is not in list.');
                            $typeValidation->setPromptTitle('Pick from list');
                            $typeValidation->setPrompt('Please pick a value from the drop-down list.');
                            $typeValidation->setFormula1('\'DataList\'!$A$1:$A$2');

                            // E: Account Name
                            $accountValidation = clone $typeValidation;
                            $accountValidation->setFormula1('\'DataList\'!$B$1:$B$100');

                            // F: Category Name
                            $categoryValidation = clone $typeValidation;
                            $categoryValidation->setFormula1('\'DataList\'!$C$1:$C$100');

                            for ($i = 2; $i <= $rowCount; $i++) {
                                $sheet->getCell("C{$i}")->setDataValidation(clone $typeValidation);
                                $sheet->getCell("E{$i}")->setDataValidation(clone $accountValidation);
                                $sheet->getCell("F{$i}")->setDataValidation(clone $categoryValidation);
                            }
                            
                            // Adjust column widths
                            foreach (range('A', 'G') as $col) {
                                $sheet->getColumnDimension($col)->setAutoSize(true);
                            }
                        },
                    ];
                }
            },
            new class implements FromArray, WithTitle, WithEvents {
                public function array(): array
                {
                    $accounts = MoneyAccount::pluck('name')->toArray();
                    $categories = MoneyCategory::pluck('name')->toArray();
                    $types = array_column(FinanceTypeEnum::cases(), 'value');

                    $maxRows = max(count($accounts), count($categories), count($types));
                    $rows = [];

                    for ($i = 0; $i < $maxRows; $i++) {
                        $rows[] = [
                            $types[$i] ?? '',
                            $accounts[$i] ?? '',
                            $categories[$i] ?? '',
                        ];
                    }

                    return $rows;
                }

                public function title(): string
                {
                    return 'DataList';
                }

                public function registerEvents(): array
                {
                    return [
                        AfterSheet::class => function(AfterSheet $event) {
                            $sheet = $event->sheet->getDelegate();
                            $sheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);
                        },
                    ];
                }
            }
        ];
    }
}
