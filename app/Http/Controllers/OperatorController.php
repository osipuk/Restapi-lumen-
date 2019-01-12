<?php

namespace App\Http\Controllers;

use App\Models\Operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class OperatorController extends Controller
{

    public function Create(Request $request)
    {
        $rules = array(
            'name' => 'required|string|max:30',
        );
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
        $validator = Validator::make(
            $request->all(),
            $rules, $this->messages
        );
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400)
                ->header('Content-Type', 'application/json');
        }
        $result = Operator::where("name", $request->name)->where("country", json_decode($request->country, true)['id'])->first();
        if (is_object($result)) {
            return response()->json(["error" => "There are already exist the name and country parameters"], 400)
                ->header('Content-Type', 'application/json');
        }
        $operator = new Operator();
        $operator->name = $request->name;
        $operator->country = json_decode($request->country, true)['id'];
        $operator->headOperator = json_decode($request->headOperator, true)['id'];
        $operator->save();
        return response(null, 201)->header('Location', '/global/v1/operator/' . $operator->id)
            ->header('Content-Type', 'application/json');
    }

    public function Read(Request $request)
    {
        $result = $this->embedPagination($request, ['country', 'headoperator', 'mobilenetwork', 'headOperator']);
        if ($result != "success") {
            return $result;
        }
        $operator = Operator::select('*')
            ->limit($this->paginationValue['limit'])
            ->offset($this->paginationValue['offset'])
            ->orderByRaw($this->paginationValue['orderBy'] == "" ? "id" : $this->paginationValue['orderBy'])
            ->get();
        if ($operator->count() == 0) {
            return response()->json(["error" => "The resource not found"], 404)
                ->header('Content-Type', 'application/json');
        }
        foreach ($operator as $row) {
            if (($this->embed == "true" || $this->embed == "") && empty($this->embedArray)) {
                $row->country = ["id" => $row->country];
                $row->headOperator = ["id" => $row->headOperator];
            } else {
                if ($this->embed != "") {
                    $temp_array = explode(",", $this->embed);

                    foreach ($temp_array as $tbl_name) {
                        $fid = 0;
                        if ($tbl_name == 'country') {
                            $resources = DB::table('country')->where('id', $row->country)->first();
                            $resources->currency = json_decode($resources->currency, true);
                            $row->country = $resources;
                        } else if ($tbl_name == 'headOperator') {
                            $resources = DB::table('headoperator')->where('id', $row->headOperator)->first();
                            $row->headOperator = $resources;
                        } else if ($tbl_name = "mobileNetworks") {
                            $resources = DB::table('mobilenetwork')->where('operator', $row->id)->first();
                            $row['mobileNetworks'] = $resources;
                        }
                    }
                }
                if (!empty($this->embedArray)) {
                    $country_field = "";
                    $headOperator_field = "";
                    foreach ($this->embedArray as $parameters) {
                        foreach ($parameters as $key => $value) {
                            if ($key == 'country') {
                                $country_field .= $value . ",";
                            }
                            if ($key == 'headOperator') {
                                $headOperator_field = $value . ",";
                            }
                        }
                    }
                    if ($country_field != "") {
                        $resources = DB::table('country')->selectRaw(substr($country_field, 0, -1))->where('id', $row->country)->first();
                        if (property_exists($resources, 'currency')) {
                            $resources->currency = json_decode($resources->currency, true);
                        }
                        $row->country = $resources;

                    }
                    if ($headOperator_field != "") {
                        $resources = DB::table('headoperator')->selectRaw(substr($headOperator_field, 0, -1))->where('id', $row->headOperator)->first();
                        $row->headOperator = $resources;
                    }
                }
            }

        }
        if ($this->paginationValue['getTotal'] == 'true') {
            return response()->json(['_total' => $operator->count(), 'data' => $operator], 200)->header('Content-Type', 'application/json');
        }
        return response()->json($operator, 200)->header('Content-Type', 'application/json');

    }

    public function ReadById(Request $request, $id = 0)
    {
        $result = $this->embedPagination($request, ['country', 'mobilenetwork', 'headOperator']);
        if ($result != "success") {
            return $result;
        }
        $operator = Operator::select('*')
            ->where('id', '=', $id)
            ->limit($this->paginationValue['limit'])
            ->offset($this->paginationValue['offset'])
            ->orderByRaw($this->paginationValue['orderBy'] == "" ? "id" : $this->paginationValue['orderBy'])
            ->first();
        if (!is_object($operator)) {
            return response()->json(["error" => "The resource not found"], 404)
                ->header('Content-Type', 'application/json');
        }

        if (($this->embed == "true" || $this->embed == "") && empty($this->embedArray)) {
            $operator->country = ["id" => $operator->country];
            $operator->headOperator = ["id" => $operator->headOperator];
        } else {
            if ($this->embed != "") {
                $temp_array = explode(",", $this->embed);

                foreach ($temp_array as $tbl_name) {
                    $fid = 0;
                    if ($tbl_name == 'country') {
                        $resources = DB::table('country')->where('id', $operator->country)->first();
                        $resources->currency = json_decode($resources->currency, true);
                        $operator->country = $resources;
                    } else if ($tbl_name == 'headOperator') {
                        $resources = DB::table('headoperator')->where('id', $operator->headOperator)->first();
                        $operator->headOperator = $resources;
                    } else if ($tbl_name = "mobileNetworks") {
                        $resources = DB::table('mobilenetwork')->where('operator', $operator->id)->first();
                        $operator['mobileNetworks'] = $resources;
                    }
                }
            }
            if (!empty($this->embedArray)) {
                $country_field = "";
                $headOperator_field = "";
                foreach ($this->embedArray as $parameters) {
                    foreach ($parameters as $key => $value) {
                        if ($key == 'country') {
                            $country_field .= $value . ",";
                        }
                        if ($key == 'headOperator') {
                            $headOperator_field = $value . ",";
                        }
                    }
                }
                if ($country_field != "") {
                    $resources = DB::table('country')->selectRaw(substr($country_field, 0, -1))->where('id', $operator->country)->first();
                    if (property_exists($resources, 'currency')) {
                        $resources->currency = json_decode($resources->currency, true);
                    }
                    $operator->country = $resources;
                }
                if ($headOperator_field != "") {
                    $resources = DB::table('headoperator')->selectRaw(substr($headOperator_field, 0, -1))->where('id', $operator->headOperator)->first();
                    $operator->headOperator = $resources;
                }
            }
        }
        if ($this->paginationValue['getTotal'] == 'true') {
            return response()->json(['_total' => $operator->count(), 'data' => $operator], 200)->header('Content-Type', 'application/json');
        }
        return response()->json($operator, 200)->header('Content-Type', 'application/json');
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
