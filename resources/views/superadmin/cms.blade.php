@extends('layouts.superadmin')
<title>CMS - {{ $type }}</title>
@php
	$routeName = 'superadmin.cms.update';
	if($type === 'barangays') {
		$paramKey = 'id';	
	} elseif ($type === 'relationships') {
		$paramKey = 'id';	
	} elseif ($type === 'providers') {
		$paramKey = 'id';
	} elseif ($type === 'services') {
		$paramKey = 'id';
	} else {
		$paramKey = 'uuid';
	}

@endphp

@section('content')
<div class="container d-flex flex-column gap-4 g-0 p-0">
	<div class="g-0 bg-white p-3 d-flex justify-content-between align-items-center rounded shadow-sm w-100">
		<h1 class="mb-0">Manage {{ Str::ucfirst($type) }}</h1>
		<!-- Modal trigger button -->
		<button
			type="button"
			class="btn btn-primary btn-lg"
			data-bs-toggle="modal"
			data-bs-target="#newContent"
		>
			<i class="fa-solid fa-plus"></i>Add {{ Str::ucfirst($type) }}
		</button>
		
		<!-- Modal Body -->
		<!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
		<div
			class="modal fade"
			id="newContent"
			tabindex="-1"
			data-bs-backdrop="static"
			data-bs-keyboard="false"
			
			role="dialog"
			aria-labelledby="modalTitleId"
			aria-hidden="true"
		>
			<div
				class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm"
				role="document"
			>
				<div class="modal-content">
					<form action="{{ route('superadmin.cms.store', ['type' => $type]) }}" method="post">
						@csrf
						@method('post')
						<div class="modal-header">
							<h5 class="modal-title" id="modalTitleId">
								New {{ Str::ucfirst($type) }}
							</h5>
							<button
								type="button"
								class="btn-close"
								data-bs-dismiss="modal"
								aria-label="Close"
							></button>
						</div>
						<div class="modal-body">
							<div class="mb-3">
								<label for="name" class="form-label">Name</label>
								<input
									type="text"
									class="form-control"
									name="name"
									id="name"
									aria-describedby="helpId"
									placeholder=""
								/>
								<small id="helpId" class="form-text text-muted">Help text</small>
							</div>
							@if ($type === 'barangays')
								<div class="mb-3">
									<label for="district_id" class="form-label">District</label>
									<select
										class="form-select form-select-lg"
										name="district_id"
										id="district_id"
									>
										@foreach ($districts as $district)
											<option value="{{ $district->id }}">{{ $district->name }}</option>
										@endforeach
									</select>
								</div>
							@endif
							<div class="mb-3">
								<label for="remarks" class="form-label">Remarks</label>
								<input
									type="text"
									class="form-control"
									name="remarks"
									id="remarks"
									aria-describedby="helpId"
									placeholder=""
								/>
								<small id="helpId" class="form-text text-muted">Comments or additional information</small>
							</div>
						</div>
						<div class="modal-footer">
							<button
							type="button"
							class="btn btn-secondary"
							data-bs-dismiss="modal"
							>
							Cancel
						</button>
						<button type="submit" class="btn btn-primary">Save</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		
		<!-- Optional: Place to the bottom of scripts -->
		<script>
			const myModal = new bootstrap.Modal(
				document.getElementById("modalId"),
				options,
			);
		</script>
		
	</div>
	<div class="d-flex justify-content-start flex-wrap p-0 gap-2">
		@foreach ($data as $item)
			<form 
				action="{{ route($routeName, ['type' => $type, 'id' => $item->$paramKey]) }}" 
				method="post"
				class="d-flex" 
				style="width: 49%;"
			>
				@csrf
				@method('post')
				<div class="card w-100" x-data="{ showSave: false }">
					<div class="card-body d-flex flex-column gap-2">
						<div class="card-title d-flex justify-content-between align-items-center">
							<h5 class="mb-0">{{ $item->name }}</h5>
						</div>
						@foreach ($fields as $field => $options)
							@if ($options['type'] === 'select')
								<span class="input-group">
									<span class="input-group-text">{{ $options['label'] }}</span>
									<select name="{{ $field }}" id="{{ $field }}" class="form-select" x-on:change="showSave = true">
										<!-- @foreach($options['options'] ?? [] as $value => $label)
											<option value="{{ $value }}" {{ $item->$field == $value ? 'selected' : '' }}>
												{{ $label->name }}
											</option>
										@endforeach -->
										<option value="{{ $item->$field }}">{{ $item->$field }}</option>
										@foreach($districts as $district)
											@if ($item->$field !== $district->id)
												<option value="{{ $district->id }}">{{ $district->name }}</option>
											@endif
										@endforeach
									</select>
								</span>
							@else
								<span class="input-group">
									<span class="input-group-text">{{ $options['label'] }}</span>
									<input 
										type="{{ $options['type'] }}"
										name="{{ $field }}"
										id="{{ $field }}"
										class="form-control"
										value="{{ $item->$field }}"
										x-on:input="showSave = true"
									>
								</span>
							@endif
						@endforeach
						<small class="card-text">{{ $item->updated_at }}</small>
						<div class="d-flex justify-content-end">
							<!-- Modal trigger button -->
								<button
									type="button"
									class="btn btn-danger btn-lg"
									data-bs-toggle="modal"
									data-bs-target="#deleteModal"
								>
									Delete
								</button>

								<!-- Delete Modal -->
								<div
									class="modal fade"
									id="deleteModal"
									tabindex="-1"
									data-bs-backdrop="static"
									data-bs-keyboard="false"
									
									role="dialog"
									aria-labelledby="modalTitleId"
									aria-hidden="true"
								>
									<div
										class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm"
										role="document"
									>
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title" id="modalTitleId">
													Delete {{ Str::ucfirst($type) }}
												</h5>
												<button
													type="button"
													class="btn-close"
													data-bs-dismiss="modal"
													aria-label="Close"
												></button>
											</div>
											<div class="modal-body">Are you sure you want do delete {{ $item->name }} in the database? Once submitted, it cannot be undone and may cause issues with other data.</div>
											<div class="modal-footer">
												<button
													type="button"
													class="btn btn-secondary"
													data-bs-dismiss="modal"
												>
													Close
												</button>
												<button type="submit" name="action" value="delete" class="btn btn-danger">Delete</button>
											</div>
										</div>
									</div>
								</div>
								
								<!-- Optional: Place to the bottom of scripts -->
								<script>
									const myModal = new bootstrap.Modal(
										document.getElementById("modalId"),
										options,
									);
								</script>
							<button
								type="submit"
								class="btn btn-primary"
								name="action"
								value="update"
								x-show="showSave"
								x-cloak
							>
								Update
							</button>
						</div>
					</div>
				</div>
			</form>		
		@endforeach
	</div>
</div>
@endsection