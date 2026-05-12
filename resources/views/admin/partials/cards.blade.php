<div class="row">
    <div class="col-6 col-lg-3">
        <livewire:counter :model="'App\Models\Client'" :label="'Total Clients'" :scope="'Total'" :iconName="'people'" :iconPathsCount="5" />
    </div>
    <div class="col-6 col-lg-3">
        <livewire:counter :model="'App\Models\Client'" :label="'Referred Clients'" :scope="'Referral'" :iconName="'route'" :iconPathsCount="4" />
    </div>
    <div class="col-6 col-lg-3">
        <livewire:counter :model="'App\Models\Client'" :label="'With Burial Assistance'" :scope="'BurialAssistance'" :iconName="'file-up'" :iconPathsCount="2"
            :route="route('burial.index')" />
    </div>
    <div class="col-6 col-lg-3">
        <livewire:counter :model="'App\Models\Client'" :label="'With Libreng Libing'" :scope="'FuneralAssistance'" :iconName="'file-up'" :iconPathsCount="2"
            :route="route('funeral.index')" />
    </div>
</div>
