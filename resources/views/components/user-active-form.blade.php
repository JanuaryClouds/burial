@props(['user'])
<div class="card card-statistic-1">
    <div class="card-icon d-flex align-items-center justify-content-center bg-{{ $user->is_active ? 'success' : 'danger' }}">
        <i class="fas fa-{{ $user->is_active ? 'lock-open' : 'lock' }}"></i>
    </div>
    <div class="card-wrap">
        <div class="card-body">
            <div class="card-header">
                <h4>Account Status</h4>
            </div>
            <div class="card-body mt-3">
                <form action="{{ route('superadmin.cms.update', ['type' => 'users', 'id' => $user->id]) }}" method="post" id="userActiveForm">
                    @csrf
                    <label class="custom-switch pl-0">
                        <input 
                            type="checkbox"
                            class="custom-switch-input"
                            name="is_active"
                            {{ $user->is_active ? 'checked' : '' }}
                            x-on:change="document.getElementById('userActiveForm').submit()"
                        >
                        <span class="custom-switch-indicator"></span>
                    </label>
                </form>
            </div>
        </div>
    </div>
</div>