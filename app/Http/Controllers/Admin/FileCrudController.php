<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\FileRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class FileCrudController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class FileCrudController extends CrudController
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
        CRUD::setModel(\App\Models\File::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/file');
        CRUD::setEntityNameStrings('file', 'files');
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
        CRUD::column('id');
        CRUD::column('name');
        CRUD::column('alternative_text');
        CRUD::column('caption');
        CRUD::column('width');
        CRUD::column('height');
        CRUD::column('formats');
        CRUD::column('hash');
        CRUD::column('ext');
        CRUD::column('mime');
        CRUD::column('size');
        CRUD::column('url');
        CRUD::column('preview_url');
        CRUD::column('provider');
        CRUD::column('provider_metadata');
        CRUD::column('created_at');
        CRUD::column('updated_at');
        CRUD::column('created_by_id');
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
        CRUD::setValidation(FileRequest::class);

        CRUD::field('id');
        CRUD::field('name');
        CRUD::field('alternative_text');
        CRUD::field('caption');
        CRUD::field('width');
        CRUD::field('height');
        CRUD::field('formats');
        CRUD::field('hash');
        CRUD::field('ext');
        CRUD::field('mime');
        CRUD::field('size');
        CRUD::field('url');
        CRUD::field('preview_url');
        CRUD::field('provider');
        CRUD::field('provider_metadata');
        CRUD::field('created_at');
        CRUD::field('updated_at');
        CRUD::field('created_by_id');
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
