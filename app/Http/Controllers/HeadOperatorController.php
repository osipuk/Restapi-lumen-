<?php

namespace App\Http\Controllers;

use App\Models\HeadOperator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HeadOperatorController extends Controller
{

    private $fields = ['id', 'name', 'operators'];

    public function Create(Request $request)
    {
        $rules = array(
            'name' => 'required|string|max:30|unique:headoperator',
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
        $headoperator = new HeadOperator();
        $headoperator->name = $request->name;
        $headoperator->save();
        return response(null, 201)->header('Location', '/global/v1/headoperator/' . $headoperator->id)
            ->header('Content-Type', 'application/json');
    }

    public function Read(Request $request)
    {
        $result = $this->embedPagination($request, ['name', 'operator']);
        if ($result != "success") {
            return $result;
        }
        $headoperator = HeadOperator::select('*')
            ->limit($this->paginationValue['limit'])
            ->offset($this->paginationValue['offset'])
            ->orderByRaw($this->paginationValue['orderBy'] == "" ? "id" : $this->paginationValue['orderBy'])
            ->get();
        if ($headoperator->count() == 0) {
            return response()->json(["error" => "The resource not found"], 404)
                ->header('Content-Type', 'application/json');
        }
        foreach ($headoperator as $row) {
            if (($this->embed == "true" || $this->embed == "") && empty($this->embedArray)) {
            } else {
                if ($this->embed != "") {
                    $temp_array = explode(",", $this->embed);
                    foreach ($temp_array as $tbl_name) {
                        if ($tbl_name == 'operator') {
                            $resources = DB::table('operator')->where('headOperator', $row->id)->get();
                            $row->operator = $resources;
                        }
                    }
                }

                if (!empty($this->embedArray)) {
                    $operator_field = "";
                    foreach ($this->embedArray as $parameters) {
                        foreach ($parameters as $key => $value) {
                            if ($key == 'operator') {
                                $operator_field .= $value . ",";
                            }
                        }
                    }
                    if ($operator_field != "") {
                        $resources = DB::table('operator')->selectRaw(substr($operator_field, 0, -1))->where('headOperator', $row->id)->get();
                        $row->operator = $resources;
                    }
                }
            }

        }
        if ($this->paginationValue['getTotal'] == 'true') {
            return response()->json(['_total' => $headoperator->count(), 'data' => $headoperator], 200)->header('Content-Type', 'application/json');
        }
        return response()->json($headoperator, 200)->header('Content-Type', 'application/json');

    }

    public function ReadById(Request $request, $id = 0)
    {
        $result = $this->embedPagination($request, ['operator']);
        if ($result != "success") {
            return $result;
        }
        $headoperator = HeadOperator::select('*')
            ->where('id', '=', $id)
            ->limit($this->paginationValue['limit'])
            ->offset($this->paginationValue['offset'])
            ->orderByRaw($this->paginationValue['orderBy'] == "" ? "id" : $this->paginationValue['orderBy'])
            ->first();
        if (!is_object($headoperator)) {
            return response()->json(["error" => "The resource not found"], 404)
                ->header('Content-Type', 'application/json');
        }

        if (($this->embed == "true" || $this->embed == "") && empty($this->embedArray)) {

        } else {
            if ($this->embed != "") {
                $temp_array = explode(",", $this->embed);

                foreach ($temp_array as $tbl_name) {
                    if ($tbl_name == 'operator') {
                        $resources = DB::table('operator')->where('headOperator', $headoperator->id)->get();
                        $headoperator->operator = $resources;
                    }
                }
            }
            if (!empty($this->embedArray)) {
                $headoperator_field = "";
                foreach ($this->embedArray as $parameters) {
                    foreach ($parameters as $key => $value) {
                        if ($key == 'operator') {
                            $headoperator_field .= $value . ",";
                        }
                    }
                }
                if ($headoperator_field != "") {
                    $resources = DB::table('operator')->selectRaw(substr($headoperator_field, 0, -1))->where('headOperator', $headoperator->id)->get();
                    $headoperator->operator = $resources;
                }

            }
        }
        if ($this->paginationValue['getTotal'] == 'true') {
            return response()->json(['_total' => $headoperator->count(), 'data' => $headoperator], 200)->header('Content-Type', 'application/json');
        }
        return response()->json($headoperator, 200)->header('Content-Type', 'application/json');
    }

    public function Replace(Request $request, $id)
    {
        $rules = array(
            'name' => 'required|string|max:30',
        );
        $headoperator = HeadOperator::find($id);
        if (!is_object($headoperator)) {
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
        $headoperator->name = $request->name;
        $headoperator->save();
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
        $headoperator = HeadOperator::find($id);
        if (!is_object($headoperator)) {
            return response()->json(["error" => "The resource not found"], 404)
                ->header('Content-Type', 'application/json');
        }
        if ($request->name != "") {
            $headoperator->name = $request->name;
        }

        $headoperator->save();
        return response(null, 200)->header('Content-Type', 'application/json');

    }

    public function Delete($id)
    {
        $headoperator = HeadOperator::find($id);
        if (!is_object($headoperator)) {
            return response()->json(["error" => "The resource not found"], 404)
                ->header('Content-Type', 'application/json');
        }
        $headoperator->delete();
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
