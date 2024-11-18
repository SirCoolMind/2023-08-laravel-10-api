<?php

namespace HafizRuslan\Finance\app\Http\Controllers;

use HafizRuslan\Finance\app\Http\Resources\MoneyCategoryResource;
use HafizRuslan\Finance\app\Models\MoneyCategory;
use HafizRuslan\Finance\app\Models\MoneySubCategory;
use Illuminate\Http\Request;

class MoneyCategoryController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        if ($return = $this->validateScope()) {
            return $return;
        }

        // * sort
        $sortBy = '';
        switch (request()->input('sort_by')) {
            default:
                $sortBy = 'name';
                break;
        }

        $descending = request()->input('descending') == 'true' ? 'DESC' : 'ASC';
        $search = request()->input('filter.search');

        // $projectData = \App\Models\Project::find(request()->input('project_id'));

        // * fetch
        $record = MoneyCategory::with($this->withRelations())
            ->orderBy($sortBy, $descending)
            ->paginate(request()->input('rows_per_page'));

        return MoneyCategoryResource::collection($record);
    }

    public function show($id)
    {
        if ($return = $this->validateScope()) {
            return $return;
        }

        $record = MoneyCategory::with($this->withRelations())
            ->find($id);

        if (!$record) {
            return response()->json(['message' => __('Record not found.')], 404);
        }

        return new MoneyCategoryResource($record);
    }

    public function store(Request $request)
    {
        if ($return = $this->validateScope()) {
            return $return;
        }

        $validator = $this->getValidator($request);
        $fails = $validator->fails();
        if ($fails) {
            return response()->json([
                'message' => __('Error saving record.'),
                'errors'  => $validator->errors(),
            ], 500);
        }

        $record = new MoneyCategory();

        try {
            \DB::beginTransaction();

            $record = $this->setRecord($request, $record);
            $this->setItems($request, $record);
            $record->load($this->withRelations());

            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            \Log::error($th);

            return response()->json([
                'message' => __('Error saving record.'),
                'errors'  => $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => __('Record successfully created.'),
            'data'    => new MoneyCategoryResource($record),
        ]);
    }

    public function update(Request $request, $id)
    {
        // \Log::debug($request->all());
        \Log::debug($request->input('versions'));
        // dd();
        if ($return = $this->validateScope()) {
            return $return;
        }

        $rules = [
            'name' => 'required|unique:money_categories,name,'.$id,
        ];

        $validator = $this->getValidator($request, $rules);
        $fails = $validator->fails();
        if ($fails) {
            return response()->json([
                'message' => __('Error saving record.'),
                'errors'  => $validator->errors(),
            ], 500);
        }

        $record = MoneyCategory::with($this->withRelations())
            ->find($id);

        if (!$record) {
            return response()->json(['message' => __('Record not found.')], 404);
        }

        try {
            \DB::beginTransaction();

            $record = $this->setRecord($request, $record);
            $this->setItems($request, $record);
            $record->load($this->withRelations());

            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            \Log::error($th);

            return response()->json([
                'message' => __('Error saving record.'),
                'errors'  => $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => __('Record successfully created.'),
            'data'    => new MoneyCategoryResource($record),
        ]);
    }

    public function destroy($id)
    {
        if ($return = $this->validateScope()) {
            return $return;
        }

        $record = MoneyCategory::find($id);

        if ($record) {
            $record->delete();

            return response()->json(['message' => __('Record successfully deleted.')]);
        } else {
            return response()->json(['message' => __('Record not found.')], 404);
        }
    }

    private function setRecord($request, $record)
    {
        $isNew = $record->exists ? true : false;
        $originals = $record->getOriginal();

        $record->name = $request->input('name');
        $record->description = $request->input('description');
        $record->user_id = data_get($record, 'user_id', \Auth::user()?->id);
        $record->save();

        return $record;
    }

    private function setItems($request, $record)
    {
        $recordId = $record->id;

        // Get existing data and map them by ID
        $existingData = MoneySubCategory::where('money_category_id', $recordId)->get()->keyBy('id');
        $existingIds = $existingData->pluck('id')->toArray();

        // Create or update versions based on ID presence
        $incomingData = $request->input('items', []);
        $incomingIds = array_filter(array_column($incomingData, 'id'));
        foreach ($incomingData as $item) {
            // Check if ID exists; if not, create a new item instance
            $itemRecord = (empty($item['id']) || !isset($existingData[$item['id']])) ? new MoneySubCategory() : $existingData[$item['id']];

            $itemRecord->money_category_id = $recordId;
            $itemRecord->name = data_get($item, 'name');
            $itemRecord->description = data_get($item, 'description');
            $itemRecord->user_id = data_get($item, 'user_id', \Auth::user()?->id);
            $itemRecord->save();
        }

        // Delete missing versions
        $toDelete = array_diff($existingIds, $incomingIds);
        MoneySubCategory::whereIn('id', $toDelete)->delete();
    }

    private function withRelations($otherRelations = [])
    {
        $relations = ['subCategory'];

        return array_merge($relations, $otherRelations);
    }

    private function getValidator($request, $otherRules = [], $otherMessages = [])
    {
        $rules = [
            'name'         => ['required', 'unique:money_categories,name'],
            'items'        => ['array'],
            'items.*.name' => ['required', 'sometimes'],
        ];
        $rules = array_merge($rules, $otherRules);

        $messages = [];
        $messages = array_merge($messages, $otherMessages);

        $validator = \Validator::make($request->all(), $rules, $messages);

        return $validator;
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
