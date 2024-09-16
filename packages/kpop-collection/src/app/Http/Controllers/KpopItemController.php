<?php
namespace HafizRuslan\KpopCollection\app\Http\Controllers;

use HafizRuslan\KpopCollection\app\Http\Resources\KpopItemResource;
use HafizRuslan\KpopCollection\app\Models\KpopItem;
use Illuminate\Http\Request;

class KpopItemController extends \App\Http\Controllers\Controller {

    public function index() {

        if ($return = $this->validateScope()) {
            return $return;
        }

        // * sort
        $sortBy = '';
        switch (request()->input('sort_by')) {
            default:
                $sortBy = 'id';
                break;
        }

        $descending = request()->input('descending') == 'true' ? 'DESC' : 'ASC';
        $search = request()->input('filter.search');

        // $projectData = \App\Models\Project::find(request()->input('project_id'));

        // * fetch
        $record = KpopItem::with($this->withRelations())
            ->orderBy($sortBy, $descending)
            ->paginate(request()->input('rows_per_page'));

        return KpopItemResource::collection($record);
    }

    public function show($id)
    {
        if ($return = $this->validateScope()) {
            return $return;
        }

        $record = KpopItem::with($this->withRelations())
            ->find($id);

        if (!$record)
            return response()->json(['message' => __('Record not found.')], 404);

        return new KpopItemResource($record);
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

        $record = new KpopItem;

        try {
            \DB::beginTransaction();

            $record = $this->setRecord($request, $record);
            $record->load($this->withRelations());

            \DB::commit();
        } catch (\Throwable $th) {

            \DB::rollback();
            \Log::error($th->getMessage());
            return response()->json([
                'message' => __('Error saving record.'),
                'errors' => $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => __('Record successfully created.'),
            'data' => new KpopItemResource($record),
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
                'errors' => $validator->errors(),
            ], 500);
        }

        $record = KpopItem::with($this->withRelations())
            ->find($id);

        if (! $record) {
            return response()->json(['message' => __('Record not found.')], 404);
        }

        try {
            \DB::beginTransaction();

            $record = $this->setRecord($request, $record);
            $record->load($this->withRelations());

            \DB::commit();
        } catch (\Throwable $th) {

            \DB::rollback();
            \Log::error($th->getMessage());
            return response()->json([
                'message' => __('Error saving record.'),
                'errors' => $th->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => __('Record successfully created.'),
            'data' => new KpopItemResource($record),
        ]);
    }

    public function destroy($id)
    {
        if ($return = $this->validateScope()) {
            return $return;
        }

        $record = KpopItem::find($id);

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

        $record->artist_name = $request->input('artist_name');
        $record->era_name = $request->input('era_name');
        $record->version_name = $request->input('version_name');
        $record->kpop_era_id = $request->input('kpop_era_id');
        $record->kpop_era_version_id = $request->input('kpop_era_version_id');
        $record->comment = $request->input('comment');
        $record->bought_price = $request->input('bought_price');
        $record->bought_place = $request->input('bought_place');
        $record->bought_comment = $request->input('bought_comment');
        $record->user_id = \Auth::user()->id;
        $record->project_id = 1;
        $record->save();

        return $record;
    }



    private function withRelations($otherRelations = [])
    {
        $relations = [
            // 'version',
            // 'era',
        ];

        return array_merge($relations, $otherRelations);
    }

    private function getValidator($request, $otherRules = [])
    {
        $rules =[
            'artist_name' => ['required'],
            'version_name' => ['required'],
            'kpop_era_id' => ['required'],
            'kpop_era_version_id' => ['required'],

        ] + $otherRules;

        $messages = [];

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
