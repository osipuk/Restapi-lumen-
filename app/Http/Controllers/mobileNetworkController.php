<?php

namespace App\Http\Controllers;

use App\Models\mobileNetwork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class mobileNetworkController extends Controller
{
    private $fields = ['id', 'mccmnc', 'operator', 'mvno'];

    public function Create(Request $request)
    {
        $rules = array(
            'mccmnc' => 'required|string|max:6|min:5|unique:mobilenetwork',
            'operator' => 'required',
        );
        foreach ($request->all() as $key => $value) {
            if (!in_array($key, $this->fields)) {
                return response()->json(['error' => "The field " . $key . " not defined in the resource"], 400)
                    ->header('Content-Type', 'application/json');
            }
            if ($key == "operator") {
                $temp = json_decode($value, true);
                if (gettype($temp) != 'array') {
                    return response()->json(["error" => "Wrong value for " . $key . " parameter"], 400)
                        ->header('Content-Type', 'application/json');
                }
                if (!array_key_exists("id", $temp)) {
                    return response()->json(["error" => "Wrong value for " . $key . " parameter"], 400)
                        ->header('Content-Type', 'application/json');
                }
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

        $mobilenetwork = new mobileNetwork();
        $mobilenetwork->mccmnc = $request->mccmnc;
        $mobilenetwork->operator = json_decode($request->operator, true)['id'];
        $mobilenetwork->mvno = json_decode($request->mvno, true)['id'];
        $mobilenetwork->save();
        return response(null, 201)->header('Location', '/global/v1/mobileNetwork/' . $mobilenetwork->id)
            ->header('Content-Type', 'application/json');
    }

    public function Read(Request $request)
    {
        $result = $this->embedPagination($request, $this->fields);
        if ($result != "success") {
            return $result;
        }
        $mobilenetwork = mobileNetwork::select('*')
            ->limit($this->paginationValue['limit'])
            ->offset($this->paginationValue['offset'])
            ->orderByRaw($this->paginationValue['orderBy'] == "" ? "id" : $this->paginationValue['orderBy'])
            ->get();
        if ($mobilenetwork->count() == 0) {
            return response()->json(["error" => "The resource not found"], 404)
                ->header('Content-Type', 'application/json');
        }
        foreach ($mobilenetwork as $row) {
            if (($this->embed == "true" || $this->embed == "") && empty($this->embedArray)) {
                $row->operator = ["id" => $row->operator];
            } else {
                if ($this->embed != "") {
                    $temp_array = explode(",", $this->embed);

                    foreach ($temp_array as $tbl_name) {
                        if ($tbl_name == 'operator') {
                            $resources = DB::table('operator')->where('id', $row->operator)->first();
                            $row->operator = $resources;
                        }
                    }
                }
                if (!empty($this->embedArray)) {
                    $operator_field = "";
                    $mvno_field = "";
                    foreach ($this->embedArray as $parameters) {
                        foreach ($parameters as $key => $value) {
                            if ($key == 'operator') {
                                $operator_field .= $value . ",";
                            }
                            if ($key == 'mvno') {
                                $mvno_field = $value . ",";
                            }
                        }
                    }
                    if ($operator_field != "") {
                        $resources = DB::table('operator')->selectRaw(substr($operator_field, 0, -1))->where('id', $row->operator)->first();
                        $row->operator = $resources;

                    }
                    if ($mvno_field != "") {
                        $resources = DB::table('mvno')->selectRaw(substr($mvno_field, 0, -1))->where('id', $row->mvno)->first();
                        $row->mvno = $resources;
                    }
                }
            }

        }
        if ($this->paginationValue['getTotal'] == 'true') {
            return response()->json(['_total' => $mobilenetwork->count(), 'data' => $mobilenetwork], 200)->header('Content-Type', 'application/json');
        }
        return response()->json($mobilenetwork, 200)->header('Content-Type', 'application/json');

    }

    public function ReadById(Request $request, $id = 0)
    {
        $result = $this->embedPagination($request, $this->fields);
        if ($result != "success") {
            return $result;
        }
        $mobileNetwork = mobileNetwork::select('*')
            ->where('id', '=', $id)
            ->limit($this->paginationValue['limit'])
            ->offset($this->paginationValue['offset'])
            ->orderByRaw($this->paginationValue['orderBy'] == "" ? "id" : $this->paginationValue['orderBy'])
            ->first();
        if (!is_object($mobileNetwork)) {
            return response()->json(["error" => "The resource not found"], 404)
                ->header('Content-Type', 'application/json');
        }

        if (($this->embed == "true" || $this->embed == "") && empty($this->embedArray)) {
            $mobileNetwork->operator = ["id" => $mobileNetwork->operator];
        } else {
            if ($this->embed != "") {
                $temp_array = explode(",", $this->embed);

                foreach ($temp_array as $tbl_name) {
                    if ($tbl_name == 'operator') {
                        $resources = DB::table('operator')->where('id', $mobileNetwork->operator)->first();
                        $mobileNetwork->operator = $resources;
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
                    $resources = DB::table('operator')->selectRaw(substr($operator_field, 0, -1))->where('id', $mobileNetwork->operator)->first();
                    $mobileNetwork->operator = $resources;
                }
            }
        }
        if ($this->paginationValue['getTotal'] == 'true') {
            return response()->json(['_total' => $mobileNetwork->count(), 'data' => $mobileNetwork], 200)->header('Content-Type', 'application/json');
        }
        return response()->json($mobileNetwork, 200)->header('Content-Type', 'application/json');
    }

    public function Replace(Request $request, $id)
    {
        $rules = array(
            'name' => 'required|string|max:30',
        );
        $operator = Operator::find($id);
        if (!is_object($operator)) {
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
            if (!Schema::hasColumn('operator', $key)) {
                return response()->json(['error' => "The field " . $key . " not defined in the resource"], 400)
                    ->header('Content-Type', 'application/json');
            }
            if ($key != "name") {
                $temp = json_decode($value, true);
                if (gettype($temp) != 'array') {
                    return response()->json(["error" => "Wrong value for " . $key . " parameter"], 400)
                        ->header('Content-Type', 'application/json');
                }
                if (!array_key_exists("id", $temp)) {
                    return response()->json(["error" => "Wrong value for " . $key . " parameter"], 400)
                        ->header('Content-Type', 'application/json');
                }
            }
        }

        $operator->name = $request->name;
        $operator->country = json_decode($request->country, true)['id'];
        $operator->headOperator = json_decode($request->headOperator, true)['id'];
        $operator->save();
        return response(null, 200)->header('Content-Type', 'application/json');
    }

    public function Modify(Request $request, $id)
    {

        if (count($request->all()) == 0) {
            return response()->json(["error" => "The request was not properly formed"], 400)
                ->header('Content-Type', 'application/json');
        }
        foreach ($request->all() as $col => $key) {
            if (!Schema::hasColumn('operator', $col)) {
                return response()->json(['error' => "The field " . $col . " not defined in the resource"], 400)
                    ->header('Content-Type', 'application/json');
            }
        }
        $operator = Operator::find($id);
        if (!is_object($operator)) {
            return response()->json(["error" => "The resource not found"], 404)
                ->header('Content-Type', 'application/json');
        }
        if ($request->name != "") {
            $operator->name = $request->name;
        }

        if ($request->country != "") {
            $operator->country = json_decode($request->country, true)['id'];
        }

        if ($request->headOperator != "") {
            $operator->headOperator = json_decode($request->headOperator, true)['id'];
        }

        $operator->save();
        return response(null, 200)->header('Content-Type', 'application/json');

    }

    public function Delete($id)
    {
        $operator = Operator::find($id);
        if (!is_object($operator)) {
            return response()->json(["error" => "The resource not found"], 404)
                ->header('Content-Type', 'application/json');
        }
        $operator->delete();
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
