<?php

namespace Tests\Feature\KpopCollection;

use HafizRuslan\KpopCollection\app\Models\KpopEra;
use HafizRuslan\KpopCollection\app\Models\KpopEraVersion;
use HafizRuslan\KpopCollection\app\Models\KpopItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KpopEraTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_kpop_era()
    {
        $era = new KpopEra();
        $era->name = 'Born Pink';
        $era->project_id = 1;
        $era->save();

        $this->assertDatabaseHas('kpop_eras', [
            'name' => 'Born Pink',
            'project_id' => 1,
        ]);
    }

    public function test_kpop_era_has_versions_relationship()
    {
        $era = new KpopEra();
        $era->name = 'Born Pink';
        $era->project_id = 1;
        $era->save();

        $version = new KpopEraVersion();
        $version->kpop_era_id = $era->id;
        $version->name = 'Black Version';
        $version->project_id = 1;
        $version->save();

        $this->assertTrue($era->versions->contains($version));
        $this->assertEquals(1, $era->versions()->count());
    }

    public function test_can_create_kpop_item_associated_with_era_and_version()
    {
        $era = new KpopEra();
        $era->name = 'Born Pink';
        $era->project_id = 1;
        $era->save();

        $version = new KpopEraVersion();
        $version->kpop_era_id = $era->id;
        $version->name = 'Black Version';
        $version->project_id = 1;
        $version->save();

        $item = new KpopItem();
        $item->artist_name = 'Blackpink';
        $item->kpop_era_id = $era->id;
        $item->kpop_era_version_id = $version->id;
        $item->bought_price = 2500;
        $item->bought_place = 'Weverse';
        $item->user_id = 1;
        $item->project_id = 1;
        $item->save();

        $this->assertDatabaseHas('kpop_items', [
            'artist_name' => 'Blackpink',
            'bought_price' => 2500,
        ]);

        $this->assertEquals($era->id, $item->era->id);
        $this->assertEquals($version->id, $item->version->id);
    }
}
