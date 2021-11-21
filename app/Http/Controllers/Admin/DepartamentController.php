<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departament;
use Illuminate\Http\Request;

class DepartamentController extends Controller
{
    private $departament;

    public function __construct(Departament $departament)
    {
        $this->departament = $departament;
    }

    public function index()
    {
        $departaments = $this->departament->paginate(10);
        return response()->json($departaments);
    }

    public function search()
    {
        $request = request()->query();
        $departaments = Departament::query();
        if (!empty($request['name'])) {
            $departaments->where('name', 'LIKE', "%" . $request['name'] . "%");
        }

        return $departaments->get();
    }
}
