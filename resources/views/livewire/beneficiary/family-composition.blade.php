@php
    $maxMembers = max(1, (int) ($maxMembers ?? 5));
    $readonly = $readonly ?? false;

    $initialMembers = [];

    // Restore previously submitted values after a failed validation.
    $oldNames = old('fam_name');

    if (is_array($oldNames) && count($oldNames) > 0) {
        foreach (array_slice($oldNames, 0, $maxMembers) as $index => $name) {
            $initialMembers[] = [
                'name' => $name,
                'sex_id' => old("fam_sex_id.{$index}"),
                'age' => old("fam_age.{$index}"),
                'civil_id' => old("fam_civil_id.{$index}"),
                'relationship_id' => old("fam_relationship_id.{$index}"),
                'occupation' => old("fam_occupation.{$index}"),
                'income' => old("fam_income.{$index}"),
            ];
        }
    } elseif (isset($members) && $members instanceof \Illuminate\Support\Collection && $members->count() > 0) {
        foreach ($members as $member) {
            $initialMembers[] = [
                'name' => $member->name,
                'sex_id' => $member->sex_id,
                'age' => $member->age,
                'civil_id' => $member->civil_id,
                'relationship_id' => $member->relationship_id,
                'occupation' => $member->occupation,
                'income' => $member->income,
            ];
        }
    }

    if (count($initialMembers) === 0) {
        $initialMembers[] = [
            'name' => '',
            'sex_id' => '',
            'age' => '',
            'civil_id' => '',
            'relationship_id' => '',
            'occupation' => '',
            'income' => '',
        ];
    }
@endphp

<div id="family-composition" data-max-members="{{ $maxMembers }}">
    <div id="family-members">
        @foreach ($initialMembers as $key => $member)
            @include('beneficiary.family.partials.row', [
                'member' => $member,
                'id' => $key,
                'readonly' => $readonly,
                'showRemove' => ! $readonly && count($initialMembers) > 1 && $key > 0,
            ])
        @endforeach
    </div>

    @if (! $readonly)
        <template id="family-member-template">
            @include('beneficiary.family.partials.row', [
                'member' => [],
                'id' => 'INDEX',
                'readonly' => false,
                'showRemove' => true,
            ])
        </template>

        <div class="mt-3">
            <button type="button"
                id="family-add-btn"
                class="btn btn-sm btn-primary {{ count($initialMembers) >= $maxMembers ? 'd-none' : '' }}">
                <i class="fa fa-plus"></i> Add Family Member
            </button>
        </div>
    @endif
</div>

@if (! $readonly)
    <script nonce="{{ $nonce ?? '' }}">
        (function () {
            'use strict';

            const container = document.getElementById('family-members');
            if (!container) {
                return;
            }

            const root = document.getElementById('family-composition');
            const maxMembers = parseInt((root && root.dataset.maxMembers) || '5', 10);
            const template = document.getElementById('family-member-template');
            const addButton = document.getElementById('family-add-btn');

            // Keeps growing so ids stay unique even after rows are removed.
            let nextId = container.querySelectorAll('.family-member-row').length;

            function rowCount() {
                return container.querySelectorAll('.family-member-row').length;
            }

            function syncButtons() {
                const rows = container.querySelectorAll('.family-member-row');

                rows.forEach((row, index) => {
                    const removeButton = row.querySelector('[data-family-remove]');
                    if (removeButton) {
                        removeButton.classList.toggle('d-none', rows.length <= 1 || index === 0);
                    }
                });

                if (addButton) {
                    addButton.classList.toggle('d-none', rowCount() >= maxMembers);
                }
            }

            function showLimitNotice() {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Notice',
                        text: 'You have reached the maximum number of family members.',
                        icon: 'info',
                        timerProgressBar: true,
                    });
                }
            }

            function addMember() {
                if (rowCount() >= maxMembers) {
                    showLimitNotice();

                    return;
                }

                if (!template) {
                    return;
                }

                const fragment = template.content.cloneNode(true);
                const row = fragment.querySelector('.family-member-row');
                const id = nextId++;

                // Give every field in the cloned row a unique id/label pair.
                row.querySelectorAll('[id]').forEach((el) => {
                    el.id = el.id.replace('INDEX', id);
                });
                row.querySelectorAll('label[for]').forEach((label) => {
                    label.htmlFor = label.htmlFor.replace('INDEX', id);
                });

                container.appendChild(row);

                // Apply Select2 to the newly added dropdowns.
                if (typeof window.initSelect2 === 'function') {
                    window.initSelect2(row);
                }

                syncButtons();
            }

            function removeMember(button) {
                if (rowCount() <= 1) {
                    return;
                }

                const row = button.closest('.family-member-row');
                if (row) {
                    row.remove();
                    syncButtons();
                }
            }

            container.addEventListener('click', (event) => {
                if (!(event.target instanceof Element)) {
                    return;
                }

                const removeButton = event.target.closest('[data-family-remove]');
                if (removeButton) {
                    removeMember(removeButton);
                }
            });

            if (addButton) {
                addButton.addEventListener('click', addMember);
            }

            syncButtons();
        })();
    </script>
@endif
