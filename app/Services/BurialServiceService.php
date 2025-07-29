<?php

namespace App\Services;

use App\Models\BurialService;

class BurialServiceService 
{
                public function store( array $data ) {
                        return BurialService::create($data);
                } 

                public function update( array $data ) {
                                return BurialService::update($data);
                }

                public function delete( BurialService $service ) {
                        if ($service->delete()) {
                                return true;
                        }
                        return false;
                }
}