<?php

namespace App\Livewire\Remark;

use App\Livewire\Forms\RemarkForm;
use App\Models\Remark;
use App\Services\RemarkService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Create extends Component
{
    public ?Model $model = null;

    public ?string $modelClass = null;

    public ?string $id = null;

    public RemarkForm $form;

    public bool $enableSubmission = true;

    #[On(('load-remarks'))]
    public function loadModel(?string $modelClass = null, ?string $id = null)
    {
        if ($modelClass && $id) {
            $this->modelClass = $modelClass;
            $this->id = $id;
        } else {
            $this->reset('modelClass', 'id');
        }
    }

    public function save()
    {
        $this->enableSubmission = false;

        try {
            $this->form->validate();
        } catch (\Exception $e) {
            $this->dispatch('notification:toast', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $e->getMessage() : config('constants.errors.validation'),
            ]);
        }

        try {
            $this->model = $this->modelClass::findOrFail($this->id);

            app(RemarkService::class)->store(
                $this->form->all(),
                $this->model
            );

            $this->dispatch('load-remarks', 
                modelClass: get_class($this->model), 
                id: $this->model->uuid
            );

            $this->form->reset();

            $this->dispatch('notification:toast', [
                'type' => 'success',
                'text' => 'Remark created successfully.',
            ]);
        } catch (\Throwable $th) {
            $this->dispatch('notification:toast', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown'),
            ]);

            report($th);
        } finally {
            $this->enableSubmission = true;
        }
    }

    public function render()
    {
        return view('livewire.remark.create');
    }
}
