<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class CurrencyController extends Controller
{

    private $fields = ['id', 'name', 'symbol', 'usdRelation', 'euroRelation'];

    public function Create(Request $request)
    {
        $rules = array(
            'name' => 'required|string|unique:currency|max:30',
            'usdRelation' => 'required|numeric',
            'euroRelation' => 'required|numeric',
        );

        foreach ($request->all() as $key => $value) {
            if (!Schema::hasColumn('currency', $key)) {
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
        $currency = new Currency();
        $currency->name = $request->name;
        $currency->symbol = $request->symbol;
        $currency->usdRelation = $request->usdRelation;
        $currency->euroRelation = $request->euroRelation;
        $currency->save();
        return response(null, 201)->header('Location', '/global/v1/currency/' . $currency->id)
            ->header('Content-Type', 'application/json');
    }

    public function Read(Request $request)
    {
        $result = $this->embedPagination($request, $this->fields);
        if ($result != "success") {
            return $result;
        }
        $currency = Currency::select('*')
            ->limit($this->paginationValue['limit'])
            ->offset($this->paginationValue['offset'])
            ->orderByRaw($this->paginationValue['orderBy'] == "" ? "id" : $this->paginationValue['orderBy'])
            ->get();
        if ($currency->count() == 0) {
            return response()->json(["error" => "The resource not found"], 404)
                ->header('Content-Type', 'application/json');
        }
        if ($this->paginationValue['getTotal'] == 'true') {
            return response()->json(['_total' => $currency->count(), 'data' => $currency], 200)->header('Content-Type', 'application/json');
        }
        return response()->json($currency, 200)->header('Content-Type', 'application/json');
    }

    public function ReadById(Request $request, $id = 0)
    {

        $result = $this->embedPagination($request, $this->fields);
        if ($result != "success") {
            return $result;
        }
        $currency = Currency::select('*')
            ->where('id', '=', $id)
            ->limit($this->paginationValue['limit'])
            ->offset($this->paginationValue['offset'])
            ->orderByRaw($this->paginationValue['orderBy'] == "" ? "id" : $this->paginationValue['orderBy'])
            ->first();
        if (!is_object($currency)) {
            return response()->json(["error" => "The resource not found"], 404)
                ->header('Content-Type', 'application/json');
        }
        if ($this->paginationValue['getTotal'] == 'true') {
            return response()->json(['_total' => 1, 'data' => $currency], 200)->header('Content-Type', 'application/json');
        }
        return response()->json($currency, 200)->header('Content-Type', 'application/json');
    }

    public function Replace(Request $request, $id)
    {
        $rules = array(
            'name' => 'required|string|max:30',
            'usdRelation' => 'required|numeric',
            'euroRelation' => 'required|numeric',
        );
        foreach ($request->all() as $key => $value) {
            if (!Schema::hasColumn('currency', $key)) {
                return response()->json(['error' => "The field " . $key . " not defined in the resource"], 400)
                    ->header('Content-Type', 'application/json');
            }
        }
        $currency = Currency::find($id);
        if (!is_object($currency)) {
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

        $currency->name = $request->name;
        $currency->symbol = $request->symbol;
        $currency->usdRelation = $request->usdRelation;
        $currency->euroRelation = $request->euroRelation;
        $currency->save();
        return response(null, 200)->header('Content-Type', 'application/json');
    }

    public function Modify(Request $request, $id)
    {
        foreach ($request->all() as $col => $key) {
            if (!Schema::hasColumn('currency', $col)) {
                return response()->json(['error' => "The field " . $col . " not defined in the resource"], 400)
                    ->header('Content-Type', 'application/json');
            }
        }
        $currency = Currency::find($id);
        if (!is_object($currency)) {
            return response()->json(["error" => "The resource not found"], 404)
                ->header('Content-Type', 'application/json');
        }
        if (count($request->all()) == 0) {
            return response()->json(["error" => "The request was not properly formed"], 400)
                ->header('Content-Type', 'application/json');
        }
        Currency::where('id', $id)->update($request->all());
        return response(null, 200)->header('Content-Type', 'application/json');
    }

    public function Delete($id)
    {
        $currency = Currency::find($id);
        if (!is_object($currency)) {
            return response()->json(["error" => "The resource not found"], 404)
                ->header('Content-Type', 'application/json');
        }
        $currency->delete();
        return response(null, 200)->header('Content-Type', 'application/json');
    }

    public function Search(Request $request)
    {
        $return = $this->searchOperation($request, 'country', ['currency']);
        if ($return != "success") {
            return response()->json(["error" => $return], 400)
                ->header('Content-Type', 'application/json');
        }
        $currency = Currency::select('*')
            ->whereRaw($this->termsCondition == "" ? "1" : $this->termsCondition)
            ->limit($this->parametersValue['limit'])
            ->offset($this->parametersValue['offset'])
            ->orderByRaw($this->parametersValue['orderBy'] . '  ' . $this->parametersValue['orderType'])
            ->get();
        if ($currency->count() == 0) {
            return response()->json(["error" => "The resource not found"], 404)
                ->header('Content-Type', 'application/json');
        }
        if (!empty($this->embedArray)) {
            foreach ($currency as $row) {
                $row->currency = json_decode($row->currency, true);
                $temp_resouce = $row->currency;
                $temp_currency = array();
                foreach ($this->embedArray as $parameters) {
                    foreach ($parameters as $key => $value) { //@value:name,id, @key:currency
                        if (!array_key_exists($value, $temp_resouce)) {
                            return response()->json(["error" => "Wrong value for " . $value . " parameter"], 404)
                                ->header('Content-Type', 'application/json');
                        }
                        $temp_currency[$value] = $row[$key][$value];
                    }
                }
                $row->currency = $temp_currency;
                $row->mcc = json_decode($row->mcc, true);
                $row->phonePrefix = json_decode($row->phonePrefix, true);
            }
        } else {
            foreach ($currency as $row) {
                $row->currency = json_decode($row->currency, true);
                $row->mcc = json_decode($row->mcc, true);
                $row->phonePrefix = json_decode($row->phonePrefix, true);
            }
        }
        if ($this->parametersValue['getTotal'] == 'true') {
            return response()->json(['_total' => $currency->count(), 'data' => $currency], 200)->header('Content-Type', 'application/json');
        }
        return response()->json($currency, 200)->header('Content-Type', 'application/json');
    }
}
