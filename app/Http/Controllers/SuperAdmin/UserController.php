<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index()
    {
        return view('pages.admin.master.user.index');
    }

    public function getData()
    {
        $query = User::query();

        return DataTables::eloquent($query)
            ->addColumn('action', function ($user) {
                return '
                    <a href="' . url('admin.user.edit', $user->id) . '" class="btn btn-sm btn-primary">Edit</a>
                    <a href="' . url('admin.user.delete', $user->id) . '" class="btn btn-sm btn-danger">Delete</a>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
