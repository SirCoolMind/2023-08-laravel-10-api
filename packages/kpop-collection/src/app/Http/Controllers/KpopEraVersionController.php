<?php

namespace HafizRuslan\KpopCollection\app\Http\Controllers;

use HafizRuslan\KpopCollection\app\Http\Resources\KpopEraVersionResource;
use HafizRuslan\KpopCollection\app\Models\KpopEraVersion;
use Illuminate\Http\Request;

class KpopEraVersionController extends \App\Http\Controllers\Controller
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
        $kpopEraId = request()->input('filter.kpop_era_id');

        // $projectData = \App\Models\Project::find(request()->input('project_id'));

        // * fetch
        $record = KpopEraVersion::with($this->withRelations())
            ->when($kpopEraId, function ($query) use ($kpopEraId) {
                $query->where('kpop_era_id', $kpopEraId);
            })
            ->orderBy($sortBy, $descending)
            ->paginate(request()->input('rows_per_page'));

        return KpopEraVersionResource::collection($record);
    }

    public function show($id)
    {
        if ($return = $this->validateScope()) {
            return $return;
        }

        $record = KpopEraVersion::with($this->withRelations())
            ->find($id);

        if (!$record) {
            return response()->json(['message' => __('Record not found.')], 404);
        }

        return new KpopEraVersionResource($record);
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

        $record = new KpopEraVersion();

        try {
            \DB::beginTransaction();

            $record = $this->setRecord($request, $record);
            $record->load($this->withRelations());

            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollback();
            \Log::error($th);

            return response()->json([
                'message' => __('Error saving record.'),
                'errors'  => $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => __('Record successfully created.'),
            'data'    => new KpopEraVersionResource($record),
        ]);
    }

    public function update(Request $request, $id)
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

        $record = KpopEraVersion::with($this->withRelations())
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
            \DB::rollback();
            \Log::error($th);

            return response()->json([
                'message' => __('Error saving record.'),
                'errors'  => $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => __('Record successfully created.'),
            'data'    => new KpopEraVersionResource($record),
        ]);
    }

    public function destroy($id)
    {
        if ($return = $this->validateScope()) {
            return $return;
        }

        $record = KpopEraVersion::find($id);

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
        $record->kpop_era_id = $request->input('kpop_era_id');
        $record->project_id = 111111;
        $record->save();

        return $record;
    }

    private function withRelations($otherRelations = [])
    {
        $relations = ['era'];

        return array_merge($relations, $otherRelations);
    }

    private function getValidator($request, $otherRules = [], $otherMessages = [])
    {
        $rules = [
            'name'        => ['required'],
            'kpop_era_id' => ['required'],
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
