{{-- TODO add progressbar --}}

@includeWhen(class_basename($data) === 'BurialAssistance', 'burial.partials.timeline', [
    'burialAssistance' => $data,
])
