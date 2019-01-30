<?php

namespace App\Http\Controllers;

use App\Models\Mvno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MvnoController extends Controller
{

    private $fields = ['id', 'name', 'mobileNetworks'];

    public function Create(Request $request)
    {
        $rules = array(
            'name' => 'required|string|max:30|unique:mvno',
        );
        foreach ($request->all() as $key => $value) {
            if (!in_array($key, $this->fields)) {
                return response()->json(['error' => "The field " . $key . " not defined in the resource"], 400)
                    ->header('Content-Type', 'application/json');
            }
        }
        $validator = Validator::make(
            $request->all(),
            $rules, $this->messages
        );
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400)
                ->header('Content-Type', 'application/json');
        }
        $mvno = new Mvno();
        $mvno->name = $request->name;
        $mvno->save();
        return response(null, 201)->header('Location', '/global/v1/mvno/' . $mvno->id)
            ->header('Content-Type', 'application/json');
    }

    public function Read(Request $request)
    {
        $result = $this->embedPagination($request, ['mobileNetworks']);
        if ($result != "success") {
            return $result;
        }
        $mvno = Mvno::select('*')
            ->limit($this->paginationValue['limit'])
            ->offset($this->paginationValue['offset'])
            ->orderByRaw($this->paginationValue['orderBy'] == "" ? "id" : $this->paginationValue['orderBy'])
            ->get();
        if ($mvno->count() == 0) {
            return response()->json(["error" => "The resource not found"], 404)
                ->header('Content-Type', 'application/json');
        }
        foreach ($mvno as $row) {
            if (($this->embed == "true" || $this->embed == "") && empty($this->embedArray)) {
            } else {
                if ($this->embed != "") {
                    $temp_array = explode(",", $this->embed);
                    foreach ($temp_array as $tbl_name) {
                        if ($tbl_name == 'mobileNetworks') {
                            $resources = DB::table('mobilenetwork')->where('mvno', $row->id)->get();
                            $row->mobileNetworks = $resources;
                        }
                    }
                }

                if (!empty($this->embedArray)) {
                    $operator_field = "";
                    foreach ($this->embedArray as $parameters) {
                        foreach ($parameters as $key => $value) {
                            if ($key == 'mobileNetworks') {
                                $operator_field .= $value . ",";
                            }
                        }
                    }
                    if ($operator_field != "") {
                        $resources = DB::table('mobilenetwork')->selectRaw(substr($operator_field, 0, -1))->where('mvno', $row->id)->get();
                        $row->mobileNetworks = $resources;
                    }
                }
            }

        }
        if ($this->paginationValue['getTotal'] == 'true') {
            return response()->json(['_total' => $mvno->count(), 'data' => $mvno], 200)->header('Content-Type', 'application/json');
        }
        return response()->json($mvno, 200)->header('Content-Type', 'application/json');
    }

    public function ReadById(Request $request, $id = 0)
    {
        $result = $this->embedPagination($request, ['mobileNetworks']);
        if ($result != "success") {
            return $result;
        }
        $mvno = Mvno::select('*')
            ->where('id', '=', $id)
            ->limit($this->paginationValue['limit'])
            ->offset($this->paginationValue['offset'])
            ->orderByRaw($this->paginationValue['orderBy'] == "" ? "id" : $this->paginationValue['orderBy'])
            ->first();
        if (!is_object($mvno)) {
            return response()->json(["error" => "The resource not found"], 404)
                ->header('Content-Type', 'application/json');
        }

        if (($this->embed == "true" || $this->embed == "") && empty($this->embedArray)) {

        } else {
            if ($this->embed != "") {
                $temp_array = explode(",", $this->embed);

                foreach ($temp_array as $tbl_name) {
                    if ($tbl_name == 'mobileNetworks') {
                        $resources = DB::table('mobilenetwork')->where('mvno', $mvno->id)->get();
                        $mvno->mobileNetworks = $resources;
                    }
                }
            }
            if (!empty($this->embedArray)) {
                $mvno_field = "";
                foreach ($this->embedArray as $parameters) {
                    foreach ($parameters as $key => $value) {
                        if ($key == 'mobileNetworks') {
                            $mvno_field .= $value . ",";
                        }
                    }
                }
                if ($mvno_field != "") {
                    $resources = DB::table('mobilenetwork')->selectRaw(substr($mvno_field, 0, -1))->where('mvno', $mvno->id)->get();
                    $mvno->mobileNetworks = $resources;
                }

            }
        }
        if ($this->paginationValue['getTotal'] == 'true') {
            return response()->json(['_total' => $mvno->count(), 'data' => $mvno], 200)->header('Content-Type', 'application/json');
        }
        return response()->json($mvno, 200)->header('Content-Type', 'application/json');
    }

    public function Replace(Request $request, $id)
    {
        $rules = array(
            'name' => 'required|string|max:30',
        );
        $mvno = Mvno::find($id);
        if (!is_object($mvno)) {
            return response()->json(["error" => "The resource not found"], 404)
                ->header('Content-Type', 'application/json');
        }
        $validator = Validator::make(
            $request->all(),
            $rules, $this->messages
        );
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400)
                ->header('Content-Type', 'application/json');
        }
        foreach ($request->all() as $key => $value) {
            if (!in_array($key, $this->fields)) {
                return response()->json(['error' => "The field " . $key . " not defined in the resource"], 400)
                    ->header('Content-Type', 'application/json');
            }
        }
        $mvno->name = $request->name;
        $mvno->save();
        return response(null, 200)->header('Content-Type', 'application/json');
    }

    public function Modify(Request $request, $id)
    {
        if (count($request->all()) == 0) {
            return response()->json(["error" => "The request was not properly formed"], 400)
                ->header('Content-Type', 'application/json');
        }
        foreach ($request->all() as $key => $val) {
            if (!in_array($key, $this->fields)) {
                return response()->json(['error' => "The field " . $key . " not defined in the resource"], 400)
                    ->header('Content-Type', 'application/json');
            }
        }
        $mvno = Mvno::find($id);
        if (!is_object($mvno)) {
            return response()->json(["error" => "The resource not found"], 404)
                ->header('Content-Type', 'application/json');
        }
        if ($request->name != "") {
            $mvno->name = $request->name;
        }

        $mvno->save();
        return response(null, 200)->header('Content-Type', 'application/json');
    }

    public function Delete($id)
    {
        $mvno = Mvno::find($id);
        if (!is_object($mvno)) {
            return response()->json(["error" => "The resource not found"], 404)
                ->header('Content-Type', 'application/json');
        }
        $mvno->delete();
        return response(null, 200)->header('Content-Type', 'application/json');

    }

    public function Search(Request $request)
    {
        $return = $this->searchOperation($request, 'operator', ['country', 'headOperator']);
        if ($return != "success") {
            return response()->json(["error" => $return], 400)
                ->header('Content-Type', 'application/json');
        }
        $operator = Operator::select('*')
            ->whereRaw($this->termsCondition == "" ? "1" : $this->termsCondition)
            ->limit($this->parametersValue['limit'])
            ->offset($this->parametersValue['offset'])
            ->orderByRaw($this->parametersValue['orderBy'] . '  ' . $this->parametersValue['orderType'])
            ->get();
        if ($operator->count() == 0) {
            return response()->json(["error" => "The resource not found"], 404)
                ->header('Content-Type', 'application/json');
        }
        if (!empty($this->embedArray)) {
            dd($this->embedArray);
        } else {
            dd('ff');
        }

        if ($this->parametersValue['getTotal'] == 'true') {
            return response()->json(['_total' => $operator->count(), 'data' => $operator], 200)->header('Content-Type', 'application/json');
        }
        return response()->json($operator, 200)->header('Content-Type', 'application/json');
    }

}
