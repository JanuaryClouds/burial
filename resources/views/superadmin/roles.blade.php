@extends('layouts.stisla.superadmin')
<title>Roles</title>
@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h4>Roles</h4>
        </div>
    </section>
    <div class="section-body">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Roles</h4>
                <button class="btn btn-primary rounded py-1" type="button" data-toggle="modal" data-target="#new-role-modal">
                    <i class="fas fa-plus"></i>
                    Add New Roles
                </button>
            </div>
            <div class="card-body">
                <x-cms-data-table :type="$type" :data="$data" />
            </div>
        </div>
    </div>
    <div id="new-role-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="new-role-modal" aria-hidden="true">
		<form action="{{ route('roles.store') }}" method="post">
			@csrf
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="newContent">Add New {{ Str::substr(Str::ucfirst($type), 0, -1) }}</h5>
						<button class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						@foreach ($data->last()->getAttributes() as $field => $value)
							@if(!in_array($field, ['id','created_at','updated_at', 'email_verified_at', 'remember_token', 'is_active'])) 
								<div class="form-group">
									<label for="{{ $field }}">{{ ucfirst(str_replace('_',' ', $field)) }}</label>
									<input type="text" 
										name="{{ $field }}" 
										id="{{ $field }}"
										class="form-control">
								</div>
							@endif
						@endforeach
					</div>
					<div class="modal-footer">
						<button class="btn btn-primary" type="submit">
							<i class="fas fa-save"></i>
							Save
						</button>
						<button class="btn btn-secondary" type="button" data-dismiss="modal">
							<i class="fas fa-times"></i>
							Cancel
						</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
@endsection