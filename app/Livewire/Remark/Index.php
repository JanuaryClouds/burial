<?php

namespace App\Livewire\Remark;

use App\Traits\Livewire\HasPlaceholder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    use HasPlaceholder;

    public ?Model $model = null;

    public ?Collection $remarks = null;

    #[On('load-remarks')]
    public function loadRemarks(Model $model)
    {
        $this->model = $model;
        $this->remarks = $model->remarks()
            ->orderBy('created_at', 'desc')
            ->with('user')
            ->get();
    }

    public function render()
    {
        return view('livewire.remark.index');
    }
}
