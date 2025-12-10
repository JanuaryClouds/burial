<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Handler;

class HandlerController extends Controller
{
    public function index()
    {
        $page_title = 'Handler';
        $resource = 'handler';
        $data = Handler::select('id', 'name', 'type', 'department')->get();
        return view('cms.index', compact('page_title', 'resource', 'data'));
    }

    public function edit(Handler $handler) 
    {
        $page_title = 'Handler';
        $resource = 'handler';
        $data = Handler::find($handler->id)->select('id', 'name', 'department')->first();
        return view('cms.edit', compact('page_title', 'data', 'resource'));
    }
}
