<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departament;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

        $departaments = $departaments->get();

        return response()->json($departaments);
    }

    public function getDepartament(Request $request)
    {
        $departament = $this->departament->where('id', $request->id)->first();

        if (!$request->id) {
            return response()->json(['error' => 'Informe o id do departamento!'], 503);
        }

        if (!$departament) {
            return response()->json(['error' => 'Departamento não localizado'], 503);
        }

        $departament->parent = $departament->getParents();

        return response()->json($departament, 200);
    }

    public function save(Request $request)
    {
        if (!$request->name) {
            return response()->json(['error' => 'Informe o nome do departamento!'], 503);
        }

        $seo = Str::slug($request->name, '-');

        $exist = $this->departament->where('seo', 'like', '%' . $seo . '%')->first();

        if ($exist) {
            return response()->json(['error' => 'Já existe um departamento com esse nome!'], 503);
        }

        $this->departament->name = $request->name;
        $this->departament->seo = $seo;

        if (sizeof($request->parent_id) > 0) {
            //futuramente vai poder inserir mais de um ao mesmo tempo
            $this->departament->parent_id = $request->parent_id[0]['id'];
        }
        $newDepartament = $this->departament->save();
        if (!$newDepartament) {
            return response()->json(['error' => 'Houve um erro na requisição, tente novamente!'], 503);
        }

        return response()->json(['msg' => 'Departamento criado com sucesso!'], 200);
    }

    public function update(Request $request)
    {
        if (!$request->id) {
            return response()->json(['error' => 'Informe o id do departamento!'], 503);
        }

        $departament = $this->departament->find($request->id);

        if (!$departament) {
            return response()->json(['error' => 'Departamento não localizado'], 503);
        }

        if (!$request->name) {
            return response()->json(['error' => 'Informe o nome do departamento!'], 503);
        }

        $seo = Str::slug($request->name, '-');

        if ($request->name !== $departament->name) {
            $exist = $this->departament->where('seo', '=', $seo)->first();
            if ($exist) {
                return response()->json(['error' => 'Já existe um departamento com esse nome!'], 503);
            }
        }

        $departament->name = $request->name;
        $departament->seo = $seo;

        $departament->parent_id = null;

        if (sizeof($request->parent_id) > 0) {
            //futuramente vai poder inserir mais de um ao mesmo tempo
            $departament->parent_id = $request->parent_id[0]['id'];
        }

        $departament = $departament->update();

        if (!$departament) {
            return response()->json(['error' => 'Houve um erro na requisição, tente novamente!'], 503);
        }

        return response()->json(['msg' => 'Departamento atualizado com sucesso!'], 200);
    }

    public function delete(Request $request)
    {
        if (!$request->id) {
            return response()->json(['error' => 'Informe o id do departamento!'], 503);
        }

        $departament = $this->departament->find($request->id);

        if (!$departament) {
            return response()->json(['error' => 'Departamento não localizado'], 503);
        }

        $departament = $departament->delete();

        if (!$departament) {
            return response()->json(['error' => 'Houve um erro na requisição, tente novamente!'], 503);
        }

        return response()->json(['msg' => 'Departamento apagado com sucesso!'], 200);
    }
}
