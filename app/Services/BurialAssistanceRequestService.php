<?php

namespace App\Services;

use App\Models\BurialAssistanceRequest;

class BurialAssistanceRequestService
{
                public function store (array $data)
                {
                                return BurialAssistanceRequest::create($data);
                }

                public function update(array $data)
                {
                                return BurialAssistanceRequest::update($data);
                }
                
                public function delete(BurialAssistanceRequest $request)
                {
                                if ($request->delete()) {
                                                return true;
                                }
                                return false;
                }
}