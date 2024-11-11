<?php

namespace HafizRuslan\KpopCollection\app\Http\Controllers;

use HafizRuslan\KpopCollection\app\Http\Resources\KpopItemResource;
use HafizRuslan\KpopCollection\app\Models\KpopItem;
use Illuminate\Http\Request;
use SirCoolMind\UploadedFiles\app\Models\UploadedFile;

class KpopItemController extends \App\Http\Controllers\Controller
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

        if (!$record) {
            return response()->json(['message' => __('Record not found.')], 404);
        }

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
                'errors'  => $validator->errors(),
            ], 500);
        }

        $record = new KpopItem();

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
            'data'    => new KpopItemResource($record),
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

        $record = KpopItem::with($this->withRelations())
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
            'data'    => new KpopItemResource($record),
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

    private function setRecord(Request $request, $record)
    {
        $isNew = $record->exists ? true : false;
        $originals = $record->getOriginal();

        $record->artist_name = $request->input('artist_name');
        $record->kpop_era_id = $request->input('kpop_era_id.id');
        $record->kpop_era_version_id = $request->input('kpop_era_version_id.id');
        $record->comment = $request->input('comment');
        $record->bought_price = $request->input('bought_price');
        $record->bought_place = $request->input('bought_place');
        $record->bought_comment = $request->input('bought_comment');
        $record->user_id = \Auth::user()->id;
        $record->project_id = 1;

        $record->save();

        if ($request->hasFile('photocard_image_upload')) {
            UploadedFile::store($record, 'photocard_image', $request->file('photocard_image_upload'));
        }

        return $record;
    }

    private function withRelations($otherRelations = [])
    {
        $relations = [
            'version',
            'era',
        ];

        return array_merge($relations, $otherRelations);
    }

    private function getValidator($request, $otherRules = [], $otherMessages = [])
    {
        $rules = [
            'artist_name' => ['required'],
            // 'version_name' => ['required'],
            'kpop_era_id'         => ['required'],
            'kpop_era_version_id' => ['required'],
            'photocard_image'     => ['nullable', 'file', 'image', 'max:2048'],
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
