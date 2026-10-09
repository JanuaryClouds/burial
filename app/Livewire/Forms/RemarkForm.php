<?php

namespace App\Livewire\Forms;

use App\Models\Remark;
use Livewire\Attributes\Validate;
use Livewire\Form;

class RemarkForm extends Form
{
    #[Validate('nullable|string|max:65535')]
    public ?string $content = null;

    public function setRemark(Remark $remark): void
    {
        $this->content = $remark->content;
    }
}
