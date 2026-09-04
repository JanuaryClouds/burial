<?php

use Livewire\Component;
use Livewire\Attributes\On;

new class extends Component {
    public string $title = 'info';

    public ?string $type = 'info';

    public string $message;

    #[On('alert-modal')]
    public function showAlert(string $title, string $message, ?string $type = 'info')
    {
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
    }
};
?>

<div>
	<script nonce="{{ $nonce ?? '' }}">
		sweetalert(
			'{{ $title }}',
			'{{ $type }}',
			'{{ $message }}',
			true,
			true
		);
	</script>
</div>
