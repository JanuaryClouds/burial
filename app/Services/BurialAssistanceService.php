<?php

namespace App\Services;

use App\Models\BurialAssistance;
use App\Models\Claimant;
use App\Models\Deceased;

class BurialAssistanceService
{
	public function store (array $data) {
		return BurialAssistance::create($data);
	}

	/**
	* @param array $data data to update
	* @param BurialAssistance $application original application
	 */
	public function update(array $data, $application)
	{
		$application->update($data);
		$application->claimant->update($data['claimant']);
		$application->deceased->update($data['deceased']);
		return $application;
	}


	public function delete (int $id) {
		$assistance = BurialAssistance::find($id);
		if ($assistance && $assistance->delete()) {
			return true;
		}
		return false;
	}

}