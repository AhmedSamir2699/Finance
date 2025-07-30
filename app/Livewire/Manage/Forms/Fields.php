<?php

namespace App\Livewire\Manage\Forms;

use App\Models\RequestFormField;
use Livewire\Component;

class Fields extends Component
{

    public $fields = [];
    public $hasOptions = false;
    public $form;
    public $field = [
        'name' => '',
        'type' => 'text',
        'required' => false,
        'options' => []
    ];
    public $fieldTypes = [
        'text' => [
            'has_options' => false
        ],
        'textarea' => [
            'has_options' => false
        ],
        'select' => [
            'has_options' => true
        ],
        'radio' => [
            'has_options' => true
        ],
        'checkbox' => [
            'has_options' => true
        ],
        'file' => [
            'has_options' => false
        ],
        'date' => [
            'has_options' => false
        ],
        'time' => [
            'has_options' => false
        ],
        'datetime' => [
            'has_options' => false
        ],
        'number' => [
            'has_options' => false
        ],
        'email' => [
            'has_options' => false
        ],
        'tel' => [
            'has_options' => false
        ],
        'url' => [
            'has_options' => false
        ],
        'color' => [
            'has_options' => false
        ],
    ];

    public function mount($form)
    {
        $this->form = $form;
        $this->fields = $form->fields()->orderBy('order')->get()->toArray() ?? [];
    }

    public function updateFieldOrder($orderedIds)
    {
        foreach ($orderedIds as $item) {
            RequestFormField::where('id', $item['value'])
                ->update(['order' => $item['order']]);
        }
    
        // Refresh the fields for the current form after updating
        $this->fields = RequestFormField::where('request_form_id', $this->form->id)
            ->orderBy('order')
            ->get()
            ->toArray();
    }

    public function changeFieldType()
    {
        if ($this->fieldTypes[$this->field['type']]['has_options'] == true)
            $this->hasOptions = true;
        else
            $this->hasOptions = false;
    }

    public function deleteField($index)
    {
        RequestFormField::destroy($index);

        $this->fields = RequestFormField::where('request_form_id', $this->form->id)
            ->orderBy('order')
            ->get()
            ->toArray();
    }
    public function addOption()
    {
        $this->field['options'][] = ''; // Add a new empty option
    }

    public function removeOption($index)
    {
        unset($this->field['options'][$index]); // Remove the option at the given index
        $this->field['options'] = array_values($this->field['options']); // Re-index the array
    }

    public function storeField()
    {
        RequestFormField::create([
            'name' => $this->field['name'],
            'type' => $this->field['type'],
            'is_required' => $this->field['required'],
            'options' => json_encode($this->field['options']),
            'request_form_id' => $this->form->id
        ]);

        $this->fields = RequestFormField::where('request_form_id', $this->form->id)
            ->orderBy('order')
            ->get()
            ->toArray();
            
        $this->field = [
            'name' => '',
            'type' => 'text',
            'required' => false,
            'options' => []
        ];
    }
    public function render()
    {
        return view('livewire.manage.forms.fields');
    }
}
