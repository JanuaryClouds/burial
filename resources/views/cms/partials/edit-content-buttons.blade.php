@if (!$readonly)
    <span class="d-flex gap-3">
        <button class="btn btn-warning" id="saveContentBtn">
            <i class="fas fa-save"></i>
            Save Changes to Data
        </button>
    </span>

    <script nonce="{{ $nonce ?? '' }}">
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('contentForm');
            const saveContentBtn = document.getElementById('saveContentBtn');

            saveContentBtn.addEventListener('click', () => {
                triggerLoading(0.3);
                form.submit();
            });
        });
    </script>
@endif
