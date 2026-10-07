<?php

namespace App\Livewire\Remark;

use App\Models\WorkflowHistory;
use App\Traits\Livewire\HasPlaceholder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    use HasPlaceholder;

    public ?Model $model = null;

    public ?string $modelClass = null;

    public ?string $id = null;

    public ?Collection $remarks = null;

    public bool $loading = true;

    #[On('load-remarks')]
    public function loadRemarks(?string $modelClass = null, ?string $id = null)
    {
        $this->modelClass = $modelClass;
        $this->id = $id;

        if ($this->modelClass && $this->id) {
            dd($this->modelClass);

            $this->model = $this->modelClass::where('uuid', $this->id)->first();

            if ($this->model) {
                $this->remarks = $this->model
                    ->remarks()
                    ->with('user')
                    ->oldest()
                    ->get();
            } else {
                $this->remarks = collect();
            }
        } else {
            $this->reset('model', 'remarks');
        }

        $this->loading = false;
    }

    public function refreshRemarks()
    {
        if ($this->modelClass && $this->id) {
            $this->loadRemarks($this->modelClass, $this->id);
        }
    }

    public function render()
    {
        return view('livewire.remark.index');
    }
}
