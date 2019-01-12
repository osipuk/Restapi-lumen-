<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Schema;

class Controller extends BaseController
{
    //
    public $messages = ['required' => ':attribute is not defined.'];
    public $parametersTypes=['getTotal'=>'boolean','embed'=>'string','embedDepth'=>'integer','limit'=>'integer','offset'=>'integer','orderBy'=>'string','orderType'=>'string'];
    public $parameters=['getTotal','embed','embedDepth','limit','offset','orderBy','orderType'];
    public $parametersValue=['getTotal'=>false,'embed'=>"",'embedDepth'=>1,'limit'=>1000,'offset'=>0,'orderBy'=>'null','orderType'=>"ASC"];
    public $terms=['order','resource','logicalExpressionOperator','expressions','logicalTermOperation'];
    public $termsTypes=['order'=>'integer','resource'=>'string','logicalExpressionOperator'=>'string','expressions'=>'array','logicalTermOperation'=>'string'];
    public $expressions=['property','operator','value'];
    public $expressionsTypes=['property'=>'string','operator'=>'string','value'=>'string'];
    public $operator=['=' , '>', '>=' , '<', '<=', '!=', 'LIKE', 'NOT LIKE','REGEXR', 'NOT REGEXP', 'IN' , 'NOT IN','BETWEEN', 'NOT BETWEEN', 'IS NULL', 'IS','NOT NULL'];
    public $termsValue=[];
    public $logicOperator=['AND','OR'];
    public $embedPaginate=['embed','embedDepth','limit','offset','orderBy','getTotal'];
    public $paginationValue=['limit'=>1000,'offset'=>0,'orderBy'=>"",'getTotal'=>'false'];
    public $paginationTypes=['limit'=>true,'offset'=>true,'orderBy'=>false,'getTotal'=>false];
    public $termsCondition="";
    public $embed="";
    public $embedArray=array();
    public $embedDepth=1;
    /**
     * @param $request
     * @param string $table
     * @param array $resources
     * @return string
     */

    public function searchOperation($request,$table='',$resources=[]){

        $params= json_decode($request->parameters,true);
        $terms_array=json_decode($request->terms,true);

        if (is_null($params) && is_null($terms_array)) {
            return "The request was not properly formed";
        }
        if(!is_null($params)){
            foreach($params as $index=>$val){
                if(!in_array($index,$this->parameters)){
                    return "Wrong value for ".$index." parameter";
                }
                if(gettype($val)!=$this->parametersTypes[$index]){
                    return "Wrong value for ".$index." parameter";
                }
                /** embed and embedDepth */
                if($index=='embed'){
                    if($val=="true" || $val=="false"){
                        $this->embed=$val;
                    }
                    else{
                        if(strpos($val,".")===false && strpos($val,",")===false){
                            if(!in_array($val,$resources)){
                                return  response()->json(["error" => "The property ".$val." of the resource not found"], 400)
                                    ->header('Content-Type', 'application/json');
                            }
                            $this->embed=$val;
                        }
                        else{
                            $temp_array=explode(",",$val);
                            $i=0;
                            foreach($temp_array as $items){
                                $temp_params=explode(".",$items);
                                if (!in_array($temp_params[0],$resources)) {
                                    return  response()->json(["error" => "The field " . $temp_params[0] . " not defined in the resource"], 400)
                                        ->header('Content-Type', 'application/json');
                                }
                                if(count($temp_params)==1){
                                    if($i==0){
                                        $this->embed.=$items;
                                    }
                                    else {
                                        $this->embed.=",".$items;
                                    }
                                    $i++;
                                }
                                else{
                                    if (!Schema::hasColumn($temp_params[0], $temp_params[1])) {
                                        return  response()->json(["error" => "The field " . $temp_params[1] . " not defined in the ".$temp_params[0]." resource"], 400)
                                            ->header('Content-Type', 'application/json');
                                    }
                                    array_push($this->embedArray,[$temp_params[0]=>$temp_params[1]]);
                                }
                            }
                        }
                    }
                }
                else if($index=="embedDepth"){
                    $this->embedDepth=$val;
                }
                /** **/
                $this->parametersValue[$index]=$val;

            }
        }
        if(($this->parametersValue['orderBy'])!='null'){
            if (!Schema::hasColumn($table, $this->parametersValue['orderBy'])) {
                return "The field " . $this->parametersValue['orderBy'] . " not defined in the resource";
            }
        }
        if (!in_array(strtoupper($this->parametersValue['orderType']),['ASC','DESC'])) {
            return "Wrong value for orderType parameter";
        }
        if(!is_null($terms_array)){
            foreach ($terms_array as $terms){
                    foreach($terms as $index=>$val){
                        if(!in_array($index,$this->terms)){
                            return "Wrong value for ".$index." parameter";
                        }
                        if(gettype($val)!=$this->termsTypes[$index]){
                            return "Wrong value for ".$index." parameter";
                        }
                        if($index=='resource'){
                            if (!in_array($val,$resources)) {
                                return "The field " . $val . " not defined in the resource";
                            }
                        }
                        if($index=='logicalExpressionOperator' || $index=='logicalTermOperation' ){
                            if (!in_array(strtoupper($val), $this->logicOperator)) {
                                return "Wrong value for ".$index." parameter";
                            }
                        }
                        if(gettype($val)=='array'){
                          foreach($val as $val_1){
                                foreach($val_1 as $index_1=>$val_2){
                                    if(!in_array($index_1,$this->expressions)){
                                        return "Wrong value for ".$index_1." parameter";
                                    }
                                    if(gettype($val_2)!=$this->expressionsTypes[$index_1]){
                                        return "Wrong value for ".$index_1." parameter";
                                    }
                                    if($index_1=='property'){
                                        if (!Schema::hasColumn($table, $val_2)) {
                                            return "The field " . $val_2 . " not defined in the resource";
                                        }
                                    }
                                    if($index_1=='operator'){
                                        if(!in_array($val_2,$this->operator)){
                                            return "Wrong value for ".$index_1." parameter";
                                        }
                                    }
                                }
                          }
                        }
                    }
            }

            $this->termsValue=$terms_array;
            if(count($this->termsValue)>1){
                usort($this->termsValue, array($this,"orderBy"));
            }
            $whereStr="";
            foreach ($this->termsValue as $terms){
                if(array_key_exists('expressions',$terms)){

                    foreach($terms['expressions'] as $term){
                        if(in_array($term['operator'],['IN','NOT IN'])){
                            $whereStr.=$term['property']." ".$term['operator']." (".$term['value'].") ";
                        }
                        else if(in_array($term['operator'],['BETWEEN','NOT BETWEEN','IS NULL','IS NOT NULL'])){
                            $whereStr.=$term['property']." ".$term['operator']." ".$term['value']." ";
                        }
                        else{
                            $whereStr.=$term['property']." ".$term['operator']." '".$term['value']."' ";
                        }
                        if(end($terms['expressions'])!=$term){
                            if(array_key_exists('logicalExpressionOperator',$terms)){
                                $whereStr.=" ".$terms['logicalExpressionOperator']." ";
                            }
                            else{
                                $whereStr.=" AND ";
                            }
                        }
                    }
                };
                if(end($this->termsValue)!=$terms && $whereStr!=""){
                    if(array_key_exists('logicalTermOperation',$terms)){
                        $whereStr.=" ".$terms['logicalTermOperation']." ";
                    }
                    else{
                        $whereStr.=" AND ";
                    }
                }
            }
            dd($whereStr);
            $this->termsCondition=$whereStr;
        }
        return "success";
    }

    /**
     * @param $request
     * @param object $request,array $resources
     * @return string|array
     */

    public function embedPagination($request,$resources=[]){
        foreach ($request->all() as $index=>$value){
            if(!in_array($index,$this->embedPaginate)){
                return  response()->json(["error" => "Wrong value for ".$index." parameter"], 404)
                ->header('Content-Type', 'application/json');
            }
            if($index=='embed'){
                if($value=="true" || $value=="false"){
                    $this->embed=$value;
                }
                else{
                    if(strpos($value,".")===false && strpos($value,",")===false){
                        if(!in_array($value,$resources)){
                            return  response()->json(["error" => "The property ".$value." of the resource not found"], 405)
                                ->header('Content-Type', 'application/json');
                        }
                        $this->embed=$value;
                    }
                    else{
                        $temp_array=explode(",",$value);
                        $i=0;
                        foreach($temp_array as $items){
                            $temp_params=explode(".",$items);
                            if (!in_array($temp_params[0],$resources)) {
                                return  response()->json(["error" => "The field " . $temp_params[0] . " not defined in the resource"], 400)
                                    ->header('Content-Type', 'application/json');
                            }
                            if(count($temp_params)==1){
                                if($i==0){
                                    $this->embed.=$items;
                                }
                                else {
                                    $this->embed.=",".$items;
                                }
                                $i++;
                            }
                            else{
                                if (!Schema::hasColumn($temp_params[0]=="mobileNetworks"?"mobilenetwork":$temp_params[0], $temp_params[1])) {
                                    return  response()->json(["error" => "The field " . $temp_params[1] . " not defined in the ".$temp_params[0]." resource"], 400)
                                        ->header('Content-Type', 'application/json');
                                }
                                array_push($this->embedArray,[$temp_params[0]=>$temp_params[1]]);
                            }
                        }
                    }
                }
            }
            else if($index=="embedDepth"){
                $this->embedDepth=$value;
            }
            else{
                if(is_numeric($value)!=$this->paginationTypes[$index]){
                    return  response()->json(["error" => "Wrong value for ".$index." parameter"], 400)
                        ->header('Content-Type', 'application/json');
                }
                $this->paginationValue[$index]=$value;

            }
        }
        return "success";
    }

    public function orderBy($a, $b)
    {
        if(!array_key_exists('order',$b)){
            return true;
        };
        if(!array_key_exists('order',$a)){
            return true;
        };
        return strnatcmp($a['order'], $b['order']);
    }
}
