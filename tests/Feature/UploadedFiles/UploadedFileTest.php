<?php

namespace Tests\Feature\UploadedFiles;

use HafizRuslan\KpopCollection\app\Models\KpopEra;
use SirCoolMind\UploadedFiles\app\Models\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UploadedFileTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_uploaded_file()
    {
        // Use a dummy model to attach to
        $era = new KpopEra();
        $era->name = 'Test Era';
        $era->project_id = 1;
        $era->save();

        $file = new UploadedFile();
        $file->model_type = get_class($era);
        $file->model_id = $era->id;
        $file->filename = 'test.jpg';
        $file->safe_filename = 'test_safe.jpg';
        $file->original_filename = 'original_test.jpg';
        $file->type = 'image';
        $file->path = 'uploads/test.jpg';
        $file->size = '1024';
        $file->extension = 'jpg';
        $file->save();

        $this->assertDatabaseHas('uploaded_files', [
            'filename' => 'test.jpg',
            'model_id' => $era->id,
            'model_type' => get_class($era),
        ]);
    }

    public function test_uploaded_file_model_relationship()
    {
        $era = new KpopEra();
        $era->name = 'Test Era';
        $era->project_id = 1;
        $era->save();

        $file = new UploadedFile();
        $file->model_type = get_class($era);
        $file->model_id = $era->id;
        $file->filename = 'test.jpg';
        $file->safe_filename = 'test_safe.jpg';
        $file->original_filename = 'original_test.jpg';
        $file->type = 'image';
        $file->path = 'uploads/test.jpg';
        $file->size = '1024';
        $file->extension = 'jpg';
        $file->save();

        $this->assertEquals(get_class($era), $file->model_type);
        $this->assertEquals($era->id, $file->model_id);
    }
}
