<?php

namespace HafizRuslan\Finance\app\Http\Controllers;

use HafizRuslan\Finance\app\Enums\FinanceCategoryEnum;
use HafizRuslan\Finance\app\Models\MoneyCategory;
use Illuminate\Http\Request;

class LookupController extends \App\Http\Controllers\Controller
{
    public $defaultProjectId = 111111;

    public function clearCategoriesCache()
    {
        \Cache::forget('categories');
        \Cache::forget('all_subcategories');

        return response()->json(['message' => 'Done']);
    }

    public function getCategoriesEnum()
    {
        // Cache the categories for 24 hours
        $categories = \Cache::remember('categories_enum', 86400, function () {
            return array_map(fn ($category) => [
                'id'   => $category->value,
                'name' => $category->label(),
            ], FinanceCategoryEnum::cases());
        });

        return response()->json(['data' => $categories]);
    }

    public function getSubCategoriesEnum(Request $request)
    {
        $categoryName = $request->query('category');

        if ($categoryName) {
            $cacheKey = "subcategories_enum_{$categoryName}";

            $subcategories = \Cache::remember($cacheKey, 86400, function () use ($categoryName) {
                $category = FinanceCategoryEnum::tryFrom($categoryName);
                if (!$category) {
                    return response()->json(['error' => 'Invalid category'], 400);
                }

                return $category->subcategories();
            });

            return response()->json(['data' => $subcategories]);
        }

        // Cache all subcategories grouped by category for 24 hours
        $allSubCategories = \Cache::remember('all_subcategories_enum', 86400, function () {
            $result = [];
            foreach (FinanceCategoryEnum::cases() as $category) {
                $result = array_merge($result, $category->subcategories());
            }

            return $result;
        });

        return response()->json(['data' => $allSubCategories]);
    }

    public function getCategories()
    {
        // Cache the categories for 24 hours
        $categories = \Cache::remember('categories', 86400, function () {
            return MoneyCategory::query()
                ->get()
                ->map(fn ($category) => [
                    'id'   => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                ])->toArray();
        });

        return response()->json(['data' => $categories]);
    }

    public function getSubCategories(Request $request)
    {
        $moneyCategoryId = $request->input('money_category_id');

        if ($moneyCategoryId) {
            $cacheKey = "subcategories_{$moneyCategoryId}";

            $subcategories = \Cache::remember($cacheKey, 86400, function () use ($moneyCategoryId) {
                $category = MoneyCategory::find($moneyCategoryId);
                if (!$category) {
                    return response()->json(['error' => 'Invalid category'], 400);
                }

                return $category->subCategory()
                    ->get()
                    ->map(fn ($subCategory) => [
                        'id'   => $subCategory->id,
                        'name' => $subCategory->name,
                        'description' => $subCategory->description,
                    ])->toArray();
            });

            return response()->json(['data' => $subcategories]);
        }

        // Cache all subcategories grouped by category for 24 hours
        $allSubCategories = \Cache::remember('all_subcategories', 86400, function () {
            $result = [];
            $categories = MoneyCategory::get();
            foreach ($categories as $category) {
                $result = array_merge(
                    $result,
                    $category->subCategory()
                        ->get()
                        ->map(fn ($subCategory) => [
                            'id'   => $subCategory->id,
                            'name' => $subCategory->name,
                            'description' => $subCategory->description,
                        ])->toArray()
                );
            }

            return $result;
        });

        return response()->json(['data' => $allSubCategories]);
    }

    private function validateScope()
    {
        $rules = [
            // 'project_id' => 'required|exists:projects,project_id',
            // 'company_id' => 'required|exists:companies,company_id',
        ];

        $validator = \Validator::make(request()->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'message' => __('Invalid scope'),
                'errors'  => $validator->errors(),
            ], 422);
        }
    }
}
