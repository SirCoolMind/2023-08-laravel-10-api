<?php

namespace HafizRuslan\Finance\app\Http\Controllers;

use HafizRuslan\Finance\app\Http\Resources\MoneyAccountResource;
use HafizRuslan\Finance\app\Models\MoneyAccount;
use Illuminate\Http\Request;

class MoneyAccountController extends \App\Http\Controllers\Controller
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
        $record = MoneyAccount::with($this->withRelations())
            ->orderBy($sortBy, $descending)
            ->paginate(request()->input('rows_per_page'));

        return MoneyAccountResource::collection($record);
    }

    public function show($id)
    {
        if ($return = $this->validateScope()) {
            return $return;
        }

        $record = MoneyAccount::with($this->withRelations())
            ->find($id);

        if (!$record) {
            return response()->json(['message' => __('Record not found.')], 404);
        }

        return new MoneyAccountResource($record);
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

        $record = new MoneyAccount();

        try {
            \DB::beginTransaction();

            $record = $this->setRecord($request, $record);
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
            'data'    => new MoneyAccountResource($record),
        ]);
    }

    public function update(Request $request, $id)
    {
        // dd();
        if ($return = $this->validateScope()) {
            return $return;
        }

        $rules = [
            'name' => 'required|unique:money_accounts,name,'.$id,
        ];

        $validator = $this->getValidator($request, $rules);
        $fails = $validator->fails();
        if ($fails) {
            return response()->json([
                'message' => __('Error saving record.'),
                'errors'  => $validator->errors(),
            ], 500);
        }

        $record = MoneyAccount::with($this->withRelations())
            ->find($id);

        if (!$record) {
            return response()->json(['message' => __('Record not found.')], 404);
        }

        try {
            \DB::beginTransaction();

            $record = $this->setRecord($request, $record);
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
            'message' => __('Record successfully updated.'),
            'data'    => new MoneyAccountResource($record),
        ]);
    }

    public function destroy($id)
    {
        if ($return = $this->validateScope()) {
            return $return;
        }

        $record = MoneyAccount::find($id);

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

    private function withRelations($otherRelations = [])
    {
        $relations = [];

        return array_merge($relations, $otherRelations);
    }

    private function getValidator($request, $otherRules = [], $otherMessages = [])
    {
        $rules = [
            'name' => ['required', 'unique:money_accounts,name'],
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
