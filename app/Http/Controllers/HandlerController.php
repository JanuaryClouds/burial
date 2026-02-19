<?php

namespace App\Http\Controllers;

use App\Models\Handler;
use Illuminate\Http\Request;

class HandlerController extends Controller
{
    public function index()
    {
        $page_title = 'Handler';
        $resource = 'handler';
        $data = Handler::select('id', 'name', 'type', 'department')->get();

        return view('cms.index', compact('page_title', 'resource', 'data'));
    }

    public function edit($id)
    {
        $page_title = 'Handler';
        $resource = 'handler';
        $data = Handler::select('id', 'name', 'department')->findOrFail($id);
        return view('cms.edit', compact('page_title', 'data', 'resource'));
    }

    public function update($id, Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'department' => 'required',
            ]);
            $handler = Handler::find($id);
            $handler->update($request->all());
            activity()
                ->causedBy(auth()->user())
                ->performedOn($handler)
                ->withProperties(['ip' => request()->ip(), 'browser' => request()->header('User-Agent')])
                ->log('Updated a handler');
            return redirect()->route('handler.index')->with('success', 'Handler updated successfully');
        } catch (\Throwable $th) {
            return back()->with('error', 'Unable to Update Handler');
        }
    }
}
