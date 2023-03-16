<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SongRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SongCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SongCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Song::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/song');
        CRUD::setEntityNameStrings('song', 'songs');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     *
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('aggressiveness');
        CRUD::column('author');
        CRUD::column('bpm');
        CRUD::column('comment');
        CRUD::column('created_at');
        CRUD::column('created_by_id');
        CRUD::column('danceability');
        CRUD::column('energy');
        CRUD::column('happy');
        CRUD::column('id');
        CRUD::column('key');
        CRUD::column('link');
        CRUD::column('path');
        CRUD::column('published_at');
        CRUD::column('relaxed');
        CRUD::column('sad');
        CRUD::column('source');
        CRUD::column('title');
        CRUD::column('extension');
        CRUD::column('updated_at');
        CRUD::column('updated_by_id');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     *
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(SongRequest::class);

        CRUD::field('aggressiveness');
        CRUD::field('author');
        CRUD::field('bpm');
        CRUD::field('comment');
        CRUD::field('created_at');
        CRUD::field('created_by_id');
        CRUD::field('danceability');
        CRUD::field('energy');
        CRUD::field('happy');
        CRUD::field('id');
        CRUD::field('key');
        CRUD::field('link');
        CRUD::field('path');
        CRUD::field('published_at');
        CRUD::field('relaxed');
        CRUD::field('sad');
        CRUD::field('source');
        CRUD::field('title');
        CRUD::field('extension');
        CRUD::field('updated_at');
        CRUD::field('updated_by_id');

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     *
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
