<?php

namespace App\Services;

use App\Models\Remark;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class RemarkService
{
    public function __construct()
    {
        //
    }

    public function index()
    {
        //
    }

    /**
     * Summary of store
     * @param array $data
     * @param Model $model
     * @return Remark
     */
    public function store(array $data, Model $model): Remark
    {
        return Remark::create([
            'remarkable_id' => $model->uuid ?? $model->id,
            'remarkable_type' => get_class($model),
            'content' => $data['content'],
            'author_id' => Auth::id(),
        ]);
    }

    public function edit()
    {
        //
    }

    public function update()
    {
        //
    }

    public function destroy()
    {
        //
    }
}