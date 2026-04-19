<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WaLogController extends Controller
{
    //

    public function index(Request $request)
    {
        $query = \App\Models\WaLog::query();

        // Filter berdasarkan Nomor HP
        if ($request->has('search')) {
            $query->where('target', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan Status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $logs = $query->latest()->paginate(10)->withQueryString();

        return view('admin.wa_logs.index', compact('logs'));
    }

}
