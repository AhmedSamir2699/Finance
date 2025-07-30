<?php

namespace App\Livewire\Requests;

use App\Models\RequestForm;
use App\Models\RequestFormCategory;
use Livewire\Component;

class Create extends Component
{
    public $categories;
    public $forms;
    public $selectedCategory;
    public $selectedForm;
    public $fields = [];

    public function mount()
    {
        $this->categories = RequestFormCategory::pluck('name','id')->unique();
    }

    public function updatedSelectedCategory($value)
    {

        $this->forms = RequestForm::with('departments')->where('request_form_category_id',$value)
                                    ->whereHas('departments', function($query){
                                        $query->where('department_id',auth()->user()->department_id);
                                    })
                                    ->whereHas('path')
                                    ->get();

    }

    public function selectForm($form)
    {
        $this->selectedForm = RequestForm::with('fields')->find($form);

    }

    public function render()
    {
        return view('livewire.requests.create');
    }
}
