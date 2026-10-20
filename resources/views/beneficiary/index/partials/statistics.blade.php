<div class="row">
	<div class="col-6 col-lg-4">
		<x-counter :count="$currentMonthBeneficiariesCount"
			label="Current Month's Total Beneficiaries"
			iconName="profile-circle"
			:iconPathsCount="3" />
	</div>
	<div class="col-6 col-lg-4">
		<x-counter :count="$pwdBeneficiariesCount"
			label="PWD Beneficiaries"
			iconName="profile-user"
			:iconPathsCount="4" />
	</div>
</div>
