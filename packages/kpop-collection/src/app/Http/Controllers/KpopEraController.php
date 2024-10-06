<?php
namespace HafizRuslan\KpopCollection\app\Http\Controllers;

use HafizRuslan\KpopCollection\app\Http\Resources\KpopEraResource;
use HafizRuslan\KpopCollection\app\Models\KpopEra;
use HafizRuslan\KpopCollection\app\Models\KpopEraVersion;
use Illuminate\Http\Request;

class KpopEraController extends \App\Http\Controllers\Controller {

    public function index() {

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
        $record = KpopEra::with($this->withRelations())
            ->orderBy($sortBy, $descending)
            ->paginate(request()->input('rows_per_page'));

        return KpopEraResource::collection($record);
    }

    public function show($id)
    {
        if ($return = $this->validateScope()) {
            return $return;
        }

        $record = KpopEra::with($this->withRelations())
            ->find($id);

        if (!$record)
            return response()->json(['message' => __('Record not found.')], 404);

        return new KpopEraResource($record);
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
                'errors' => $validator->errors(),
            ], 500);
        }

        $record = new KpopEra;

        try {
            \DB::beginTransaction();

            $record = $this->setRecord($request, $record);
            $this->setVersions($request, $record);
            $record->load($this->withRelations());

            \DB::commit();
        } catch (\Throwable $th) {

            \DB::rollBack();
            \Log::error($th->getMessage());
            return response()->json([
                'message' => __('Error saving record.'),
                'errors' => $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => __('Record successfully created.'),
            'data' => new KpopEraResource($record),
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
            'name' => 'required|unique:kpop_eras,name,'.$id
        ];

        $validator = $this->getValidator($request, $rules);
        $fails = $validator->fails();
        if ($fails) {
            return response()->json([
                'message' => __('Error saving record.'),
                'errors' => $validator->errors(),
            ], 500);
        }

        $record = KpopEra::with($this->withRelations())
            ->find($id);

        if (! $record) {
            return response()->json(['message' => __('Record not found.')], 404);
        }

        try {
            \DB::beginTransaction();

            $record = $this->setRecord($request, $record);
            $this->setVersions($request, $record);
            $record->load($this->withRelations());

            \DB::commit();
        } catch (\Throwable $th) {

            \DB::rollBack();
            \Log::error($th->getMessage());
            return response()->json([
                'message' => __('Error saving record.'),
                'errors' => $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => __('Record successfully created.'),
            'data' => new KpopEraResource($record),
        ]);
    }

    public function destroy($id)
    {
        if ($return = $this->validateScope()) {
            return $return;
        }

        $record = KpopEra::find($id);

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
        $record->project_id = 111111;
        $record->save();

        return $record;
    }

    private function setVersions($request, $record)
    {
        $kpopEraId = $record->id;

        // Get existing data and map them by ID
        $existingData = KpopEraVersion::where('kpop_era_id', $kpopEraId)->get()->keyBy('id');
        $existingIds = $existingData->pluck('id')->toArray();

        // Create or update versions based on ID presence
        $incomingData = $request->input('versions', []);
        $incomingIds = array_filter(array_column($incomingData, 'id'));
        foreach ($incomingData as $version) {
            // Check if ID exists; if not, create a new Version instance
            $itemRecord = empty($version['id']) ? new KpopEraVersion : $existingData[$version['id']];

            $itemRecord->name = $version['name'];
            $itemRecord->kpop_era_id = $kpopEraId;
            $itemRecord->project_id = $request->input('project_id');
            $itemRecord->save();
        }

        // Delete missing versions
        $toDelete = array_diff($existingIds, $incomingIds);
        KpopEraVersion::whereIn('id', $toDelete)->delete();

        // // Create new versions
        // $toCreate = collect($incomingData)->filter(fn($v) => empty($v['id']));
        // foreach ($toCreate as $version) {
        //     $newVersion = new Version();
        //     $newVersion->name = $version['name'];
        //     $newVersion->kpop_era_id = $kpopEraId;
        //     $newVersion->project_id = $request->input('project_id');
        //     // Assign other properties if needed
        //     $newVersion->save();
        // }

        // // Update existing versions
        // $toUpdate = collect($incomingData)->filter(fn($v) => !empty($v['id']));
        // foreach ($toUpdate as $version) {
        //     $existingVersion = $existingVersions[$version['id']];
        //     $originals = $existingVersion->getOriginal();  // Get original values

        //     $existingVersion->name = $version['name'];
        //     $existingVersion->project_id = $request->input('project_id');
        //     // Update other fields as needed
        //     $existingVersion->save();
        // }

    }

    private function withRelations($otherRelations = [])
    {
        $relations = ['versions'];

        return array_merge($relations, $otherRelations);
    }

    private function getValidator($request, $otherRules = [])
    {
        $rules = [
            'name' => ['required', 'unique:kpop_eras,name'],
            'versions' => ['array'],
            'versions.*.name' => ['required','sometimes'],
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
                'errors' => $validator->errors(),
            ], 422);
        }
    }
}
