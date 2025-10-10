@props([
    'user',
    'routes' => [],
    'restrictions' => []  
])
<!-- Permissions -->
<div class="card">
    <form action="{{ route('superadmin.user.restrictions.update', ['userId' => $user->id]) }}" method="post" id="userRestrictionsForm">
        @csrf
        <div class="card-header">
            <h4>{{ $user->first_name }} {{ $user->last_name }} can access</h4>
        </div>
        <div class="card-body">
            @foreach ($routes as $route)
                <div class="form-check">
                    <input 
                        class="form-check-input" 
                        type="checkbox"
                        name="allowed[]"
                        value="{{ $route }}"
                        {{ !in_array($route, $restrictions) ? 'checked' : '' }}
                    >
                    <label class="form-check-label">
                        {{ Str::title(Str::replace('.', ' ', $route)) }}
                    </label>
                </div>
            @endforeach
        </div>
        <div class="card-footer">
            <span>
                <button class="btn btn-primary mr-2" type="submit">Save</button>
                <button class="btn btn-secondary mr-2" id="allowAllBtn">Allow All</button>
                <button class="btn btn-danger" id="denyAllBtn">Deny All</button>
            </span>
        </div>
    </form>
</div>
<script>
    const form = document.getElementById('userRestrictionsForm');
    const allowAllBtn = document.getElementById('allowAllBtn');
    const denyAllBtn = document.getElementById('denyAllBtn');

    allowAllBtn.addEventListener('click', () => {
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach((checkbox) => {
            checkbox.checked = true;
        });
        form.submit();
    });

    denyAllBtn.addEventListener('click', () => {
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach((checkbox) => {
            checkbox.checked = false;
        });
        form.submit();
    });
</script>