<div class="d-flex flex-column gap-6">
    <section aria-label="General activity">
        <h2 class="fs-4 mb-4">General Activity</h2>
        <livewire:activity-log.table kind="general" lazy :key="'general-activity'" />
    </section>
    <section aria-label="Authentication and authorization activity">
        <h2 class="fs-4 mb-4">Authentication and Authorization</h2>
        <livewire:activity-log.table kind="security" lazy :key="'security-activity'" />
    </section>
</div>
