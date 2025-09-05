<?php

namespace App\Services;

use App\Models\BurialAssistance;

class BurialAssistanceService
{
	public function store (array $data) {
		return BurialAssistance::create($data);
	}

	public function update (int $id, array $data) {
		$assistance = BurialAssistance::find($id);
		if ($assistance) {
			$assistance->update($data);
			return $assistance;
		}
		return null;
	}

	public function delete (int $id) {
		$assistance = BurialAssistance::find($id);
		if ($assistance && $assistance->delete()) {
			return true;
		}
		return false;
	}
}