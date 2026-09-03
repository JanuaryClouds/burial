<?php

use Livewire\Component;

new class extends Component {
    public ?string $label;

    public string $contents;

    public ?string $helpText;

    public function mount(?string $label, string $contents, ?string $helpText = null)
    {
        $this->label = $label;
        $this->contents = $contents;
        $this->helpText = $helpText;
    }
};
?>

<div class="d-flex flex-column">
	<div>
		<small class="text-muted">{{ $label }}</small>
	</div>
	<div class="bg-gray-100 rounded px-4 py-3 w-auto">
		{{ $contents }}
	</div>
	@isset($helpText)
		<small>{{ $helpText }}</small>
	@endisset
</div>
