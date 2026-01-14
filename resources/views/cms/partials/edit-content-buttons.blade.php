@if (!$readonly)
    <span class="d-flex gap-3">
        <button class="btn btn-primary" id="saveContentBtn">
            Save
        </button>
    </span>

    <script>
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
