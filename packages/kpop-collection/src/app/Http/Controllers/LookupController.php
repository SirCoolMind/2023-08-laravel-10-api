<?php
namespace HafizRuslan\KpopCollection\app\Http\Controllers;

use Illuminate\Http\Request;

class LookupController extends \App\Http\Controllers\Controller {

    public $defaultProjectId = 111111;

    public function kpopEras() {

        if ($return = $this->validateScope()) {
            return $return;
        }

        $projectId = $this->defaultProjectId;

        $record = \HafizRuslan\KpopCollection\app\Models\KpopEra::query()
            ->where('project_id', $projectId)
            ->orderBy('name', 'asc')
            ->get();

        return \HafizRuslan\KpopCollection\app\Http\Resources\KpopEraResource::collection($record);
    }

    public function kpopVersions(Request $request) {

        if ($return = $this->validateScope()) {
            return $return;
        }

        $projectId = $this->defaultProjectId;
        $kpopEraId = $request->input('kpop_era_id');

        $record = \HafizRuslan\KpopCollection\app\Models\KpopEraVersion::query()
            ->when($kpopEraId, function($query) use($kpopEraId) {
                $query->where('kpop_era_id', $kpopEraId);
            })
            ->where('project_id', $projectId)
            ->orderBy('name', 'asc')
            ->get();

        return \HafizRuslan\KpopCollection\app\Http\Resources\KpopEraVersionResource::collection($record);
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
