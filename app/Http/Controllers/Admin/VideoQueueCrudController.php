<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class VideoQueueCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class VideoQueueCrudController extends CrudController
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
        CRUD::setModel(\App\Models\VideoQueue::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/video-queue');
        CRUD::setEntityNameStrings('video queue', 'video queues');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('actor')->type('closure')->function(function($entry) {
            return $entry->actor->name ?? '-';
        });
        CRUD::column('locales')->type('closure')->function(function($entry) {
            return $entry->locales->name ?? '-';
        })->label('Country');
        CRUD::column('voices')->type('closure')->function(function($entry) {
            return $entry->voices->name ?? '-';
        })->label('Voice');
        CRUD::column('state')->type('closure')->function(function($entry) {
            return $entry->countries->state ?? '-';
        })->label('State');
        
        CRUD::column('process_time_start');
        CRUD::column('created_at');

        // Add video preview column
        CRUD::column('video_local_path')->type('closure')->function(function($entry) {
            if ($entry->video_local_path) {
                return '<video width="320" height="240" controls>
                            <source src="' . asset('storage/' . $entry->video_local_path) . '" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>';
            }
            return 'No video available';
        })->escaped(false);

        // Remove default buttons
        $this->crud->removeButton('update');
        $this->crud->removeButton('delete');

        $this->crud->addButton('line', 'conditional_update', 'view', 'vendor.backpack.crud.buttons.conditional_update_video_q');

        
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'actor_id' => 'required|exists:actors,id',
            'locales_id' => 'required|exists:locales,id',
            'voices_id' => 'required|exists:voices,id',
            'rate_change' => 'required|integer|min:-50|max:100',
            'pitch_change' => 'required|integer|min:-50|max:100',
            'volume_change' => 'required|integer|min:-100|max:0',
            'prosody_contour' => 'nullable|string',
            'text' => 'required|string',
            'guidance_scale' => 'nullable|numeric|min:0|max:100',
            'inference_steps' => 'nullable|integer|min:1',
            'seed' => 'nullable|integer',
        ]);
    
        // Dropdown to select Actor
        CRUD::addField([
            'name' => 'actor_id',
            'label' => 'Actor',
            'type' => 'select',
            'entity' => 'actor',
            'attribute' => 'name',
            'model' => 'App\\Models\\Actor',
            'wrapper' => [
                'class' => 'form-group col-md-6'
            ]
        ]);

        CRUD::addField([
            'name' => 'locales_id',
            'label' => 'Country',
            'type' => 'select',
            'entity' => 'locales',
            'attribute' => 'name',
            'model' => 'App\\Models\\Locales',
            'allows_null' => false,
            'wrapper' => [
                'class' => 'form-group col-md-6'
            ],
            'attributes' => [
                'id' => 'locales_id',
                'required' => 'required',
                'onchange' => 'fetch(`/admin/api/voices?locales_id=${this.value}`)
                    .then(response => response.json())
                    .then(data => {
                        const voiceSelect = document.getElementById("voice_id");
                        voiceSelect.innerHTML = "";
                        data.forEach(voice => {
                            const option = new Option(voice.name, voice.voice_id);
                            voiceSelect.add(option);
                        });
                    })'
            ]
        ]);
        
        // Add Voice select field, set voices_id = voice_id

        CRUD::addField([
            'name' => 'voices_id',
            'label' => 'Voice',
            'type' => 'select',
            'wrapper' => [
                'class' => 'form-group col-md-6'
            ],
            'attributes' => [
                'id' => 'voice_id'
            ]
        ]);

        // Country code, checkbox, list of all EU countries
        CRUD::addField([
            'name' => 'countries',
            'label' => 'Select Countries',
            'type' => 'checklist',
            'wrapper' => [
                'class' => 'form-group col-md-6'
            ]
        ]);

        // Additional Fields
        CRUD::addFields([
            [
                'name' => 'rate_change',
                'label' => 'Rate Change',
                'type' => 'number',
                'attributes' => ['min' => -50, 'max' => 100],
                'wrapper' => [
                    'class' => 'form-group col-md-6'
                ]
            ],
            [
                'name' => 'pitch_change',
                'label' => 'Pitch Change',
                'type' => 'number',
                'attributes' => ['min' => -50, 'max' => 100],
                'wrapper' => [
                    'class' => 'form-group col-md-6'
                ]
            ],
            [
                'name' => 'volume_change',
                'label' => 'Volume Change',
                'type' => 'number',
                'attributes' => ['min' => -100, 'max' => 0],
                'wrapper' => [
                    'class' => 'form-group col-md-6'
                ]
            ],
            [
                'name' => 'prosody_contour',
                'label' => 'Prosody Contour',
                'type' => 'textarea',
                'default' => '(15%, +30%) (30%, -30%) (45%, +30%) (60%, -30%) (75%, +30%)',
                'wrapper' => [
                    'class' => 'form-group col-md-6'
                ]
            ],
            [
                'name' => 'guidance_scale',
                'label' => 'Guidance Scale',
                'type' => 'number',
                'attributes' => ['step' => '0.01'],
                'wrapper' => [
                    'class' => 'form-group col-md-6'
                ]
            ],
            [
                'name' => 'inference_steps',
                'label' => 'Inference Steps',
                'type' => 'number',
                'wrapper' => [
                    'class' => 'form-group col-md-6'
                ]
            ],
            [
                'name' => 'seed',
                'label' => 'Seed',
                'type' => 'number',
                'wrapper' => [
                    'class' => 'form-group col-md-6'
                ]
            ],
            [
                'name' => 'text',
                'label' => 'Text',
                'type' => 'textarea',
                'wrapper' => [
                    'class' => 'form-group col-md-12'
                ]
            ],
        ]);
    }
    
    public function store()
    {
        $this->crud->hasAccessOrFail('create');

        // Validate form
        $request = $this->crud->validateRequest();
        
        // Get selected countries and decode if it's a JSON string
        $countries = $request->get('countries');
        if (is_string($countries)) {
            $countries = json_decode($countries, true) ?? [];
        }
        
        // Remove countries from the request data
        $data = $request->except(['countries']);
        
        // Create an entry for each selected country
        foreach ($countries as $country) {
            $data['state'] = $country; // Remove json encoding/string array notation
            $this->crud->create($data);
        }

        return redirect($this->crud->route);
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
