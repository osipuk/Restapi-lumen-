<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class CountryController extends Controller
{
    /*
     * create a country
     * method: post
     * URL : /country and /country/{id}
     */

    private $continent = ['Asia', 'Africa', 'North America', 'Europe', 'Antarctica', 'South America', 'Australia'];

    public function Create(Request $request)
    {
        $rules = array(
            'name' => 'required|unique:country|string|max:30',
            'iso2' => 'required|unique:country|string|max:2',
            'iso3' => 'required|unique:country|string|max:4',
            'mcc' => 'required|string',
            'continent' => 'required|string',
        );
        foreach ($request->all() as $key => $value) {
            if (!Schema::hasColumn('country', $key)) {
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
        if (!in_array($request->continent, $this->continent)) {
            return response()->json(['error' => "Wrong value for continent parameter"], 400)
                ->header('Content-Type', 'application/json');
        }
        $country = new Country();
        $country->name = $request->name;
        $country->iso2 = $request->iso2;
        $country->iso3 = $request->iso3;
        $country->mcc = $request->mcc;
        if (!array_key_exists('id', json_decode($request->currency,true))) {
            return response()->json(['error' => "Wrong value for currency parameter"], 400)
                ->header('Content-Type', 'application/json');
        }
        $country->currency =json_decode($request->currency,true)['id'];
        $country->continent = $request->continent;
        $country->phonePrefix = $request->phonePrefix;
        $country->save();
        return response(null, 201)->header('Location', '/global/v1/country/' . $country->id)
            ->header('Content-Type', 'application/json');
    }

    public function Read(Request $request)
    {
        $result=$this->embedPagination($request,['currency']);
        if($result!="success"){
            return $result;
        }
        $country=Country::select('*')
            ->limit($this->paginationValue['limit'])
            ->offset($this->paginationValue['offset'])
            ->orderByRaw($this->paginationValue['orderBy']==""?"id":$this->paginationValue['orderBy'])
            ->get();
        if ($country->count()==0) {
            return response()->json(["error" => "The resource not found"], 404)
                ->header('Content-Type', 'application/json');
        }
        foreach ($country as $row) {
            if (($this->embed == "true" || $this->embed == "") && empty($this->embedArray)) {
                $resources = DB::table('currency')->where('id', $row->currency)->first();
                $row->currency=$resources;
                $row->mcc=json_decode($row->mcc,true);
                $row->phonePrefix=json_decode($row->phonePrefix,true);
            }
            else if($this->embed == "false"){
                unset($row->currency);
            }
            else {
                if ($this->embed != "") {
                    $temp_array = explode(",", $this->embed);
                    foreach ($temp_array as $tbl_name) {
                        if ($tbl_name == 'currency') {
                            $resources = DB::table('currency')->where('id', $row->currency)->first();
                            $row->currency = $resources;
                        }
                    }
                }
                else if (!empty($this->embedArray)) {
                    $currency_field = "";
                    foreach ($this->embedArray as $parameters) {
                        foreach ($parameters as $key => $value) {
                            if ($key == 'currency') {
                                $currency_field .= $value . ",";
                            }
                        }
                    }
                    if ($currency_field != "") {
                        $resources = DB::table('currency')->selectRaw(substr($currency_field, 0, -1))->where('id', $row->currency)->first();
                        $row->currency = $resources;
                    }
                }
                $row->mcc=json_decode($row->mcc,true);
                $row->phonePrefix=json_decode($row->phonePrefix,true);
            }
        }
        if($this->paginationValue['getTotal']=='true'){
            return response()->json(['_total'=>$country->count(),'data'=>$country], 200)->header('Content-Type', 'application/json');
        }
        return response()->json($country, 200)->header('Content-Type', 'application/json');
    }

    public function ReadById(Request $request,$id =0)
    {

        $result=$this->embedPagination($request,['currency']);
        if($result!="success"){
            return $result;
        }
        $country = Country::select('*')
            ->where('id','=',$id)
            ->limit($this->paginationValue['limit'])
            ->offset($this->paginationValue['offset'])
            ->orderByRaw($this->paginationValue['orderBy']==""?"id":$this->paginationValue['orderBy'])
            ->first();
        if (!is_object($country)) {
            return response()->json(["error" => "The resource not found"], 404)
                ->header('Content-Type', 'application/json');
        }
        if (($this->embed == "true" || $this->embed == "") && empty($this->embedArray)) {
            $resources = DB::table('currency')->where('id', $country->currency)->first();
            $country->currency=$resources;
            $country->mcc=json_decode($country->mcc,true);
            $country->phonePrefix=json_decode($country->phonePrefix,true);
        }
        else if($this->embed == "false"){
            unset($country->currency);
        }
        else {
            if ($this->embed != "") {
                $temp_array = explode(",", $this->embed);
                foreach ($temp_array as $tbl_name) {
                    if ($tbl_name == 'currency') {
                        $resources = DB::table('currency')->where('id', $country->currency)->first();
                        $country->currency = $resources;
                    }
                }
            }
            else if (!empty($this->embedArray)) {
                $currency_field = "";
                foreach ($this->embedArray as $parameters) {
                    foreach ($parameters as $key => $value) {
                        if ($key == 'currency') {
                            $currency_field .= $value . ",";
                        }
                    }
                }
                if ($currency_field != "") {
                    $resources = DB::table('currency')->selectRaw(substr($currency_field, 0, -1))->where('id', $country->currency)->first();
                    $country->currency = $resources;
                }
            }
            $country->mcc=json_decode($country->mcc,true);
            $country->phonePrefix=json_decode($country->phonePrefix,true);
        }
        if($this->paginationValue['getTotal']=='true'){
            return response()->json(['_total'=>1,'data'=>$country], 200)->header('Content-Type', 'application/json');
        }
        return response()->json($country, 200)->header('Content-Type', 'application/json');
    }

    public function Replace(Request $request, $id)
    {
        $rules = array(
            'name' => 'required|string|max:30',
            'iso2' => 'required|string|max:2',
            'iso3' => 'required|string|max:4',
            'mcc' => 'required|string',
            'continent' => 'required|string',
        );
        $country = Country::find($id);
        if (!is_object($country)) {
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
        if (!in_array($request->continent, $this->continent)) {
            return response()->json(['error' => "Wrong value for continent parameter"], 400)
                ->header('Content-Type', 'application/json');
        }
        $country->name = $request->name;
        $country->iso2 = $request->iso2;
        $country->iso3 = $request->iso3;
        $country->mcc = $request->mcc;
        if (!array_key_exists('id', json_decode($request->currency,true))) {
            return response()->json(['error' => "Wrong value for currency parameter"], 400)
                ->header('Content-Type', 'application/json');
        }
        $country->currency =json_decode($request->currency,true)['id'];
        $country->continent = $request->continent;
        $country->phonePrefix = $request->phonePrefix;
        $country->save();
        return response(null, 200)->header('Content-Type', 'application/json');
    }

    public function Modify(Request $request, $id)
    {
        foreach ($request->all() as $col => $key) {
            if (!Schema::hasColumn('country', $col)) {
                return response()->json(['error' => "The field " . $col . " not defined in the resource"], 400)
                    ->header('Content-Type', 'application/json');
            }
        }
        $country = Country::find($id);
        if (!is_object($country)) {
            return response()->json(["error" => "The resource not found"], 404)
                ->header('Content-Type', 'application/json');
        }
        if (count($request->all()) == 0) {
            return response()->json(["error" => "The request was not properly formed"], 400)
                ->header('Content-Type', 'application/json');
        }
        if ($request->name != "") {
            $country->name = $request->name;
        }
        if ($request->iso2 != "") {
            $country->iso2 = $request->iso2;
        }
        if ($request->iso3 != "") {
            $country->iso3 = $request->iso3;
        }
        if ($request->mcc != "") {
            $country->mcc = $request->mcc;
        }
        if ($request->currency != "") {
            if (!array_key_exists('id', json_decode($request->currency,true))) {
                return response()->json(['error' => "Wrong value for currency parameter"], 400)
                    ->header('Content-Type', 'application/json');
            }
            $country->currency =json_decode($request->currency,true)['id'];
        }
        if ($request->continent != "") {
            if (!in_array($request->continent, $this->continent)) {
                return response()->json(['error' => "Wrong value for continent parameter"], 400)
                    ->header('Content-Type', 'application/json');
            }
            $country->continent = $request->continent;
        }
        if ($request->phonePrefix != "") {
            $country->phonePrefix = $request->phonePrefix;
        }
        return response(null, 200)->header('Content-Type', 'application/json');
    }

    public function Delete($id)
    {
        $country = Country::find($id);
        if (!is_object($country)) {
            return response()->json(["error" => "The resource not found"], 404)
                ->header('Content-Type', 'application/json');
        }
        $country->delete();
        return response(null, 200)->header('Content-Type', 'application/json');
    }

    public function Search(Request $request){
        $return=$this->searchOperation($request,'country',['currency']);
        if($return!="success"){
            return response()->json(["error" => $return], 400)
                ->header('Content-Type', 'application/json');
        }
        $country=Country::select('*')
            ->whereRaw($this->termsCondition==""?"1":$this->termsCondition)
            ->limit($this->parametersValue['limit'])
            ->offset($this->parametersValue['offset'])
            ->orderByRaw($this->parametersValue['orderBy'] .'  '. $this->parametersValue['orderType'])
            ->get();
        if ($country->count()==0) {
            return response()->json(["error" => "The resource not found"], 404)
                ->header('Content-Type', 'application/json');
        }
        if(!empty($this->embedArray)){
            foreach($country as $row){
                $row->currency=json_decode($row->currency,TRUE);
                $temp_resouce=$row->currency;
                $temp_currency=array();
                foreach($this->embedArray as $parameters){
                    foreach($parameters as $key=>$value){//@value:name,id, @key:currency
                        if (!array_key_exists($value,$temp_resouce)){
                            return  response()->json(["error" => "Wrong value for ".$value." parameter"], 404)
                                ->header('Content-Type', 'application/json');
                        }
                        $temp_currency[$value]=$row[$key][$value];
                    }
                }
                $row->currency=$temp_currency;
                $row->mcc=json_decode($row->mcc,TRUE);
                $row->phonePrefix=json_decode($row->phonePrefix,TRUE);
            }
        }
        else{
            foreach($country as $row){
                $row->currency=json_decode($row->currency,TRUE);
                $row->mcc=json_decode($row->mcc,TRUE);
                $row->phonePrefix=json_decode($row->phonePrefix,TRUE);
            }
        }
        if($this->parametersValue['getTotal']=='true'){
            return response()->json(['_total'=>$country->count(),'data'=>$country], 200)->header('Content-Type', 'application/json');
        }
        return response()->json($country, 200)->header('Content-Type', 'application/json');
    }
}
