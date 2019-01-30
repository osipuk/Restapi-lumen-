<?php

use AlbertCht\Lumen\Testing\TestCase;
use Laravel\Lumen\Testing\DatabaseTransactions;
// use Laravel\Lumen\Testing\DatabaseMigrations;
// use AlbertCht\Lumen\Testing\Concerns\RefreshDatabase;

class OperatorTest extends TestCase
{
    use DatabaseTransactions;
    
    /**
     * /global/v1/operator [POST]
     */
    public function testShouldValidateParametersCreateOperator()
    {
        $parameters = [
            'namee' => 'Lykamobile',
        ];
        $this->post("/global/v1/operator", $parameters, []);
        $this->seeStatusCode(400);        
    }
    public function testShouldValidateRequiredParametersCreateOperator()
    {
        $parameters = [
            'name' => '',
        ];
        $this->post("/global/v1/operator", $parameters, []);
        $this->seeStatusCode(400);        
    }
    public function testShouldValidateCountryParameterCreateOperator()
    {
        $parameters = [
            'name' => 'USA',
            'country' => ['id' => 99999], 
        ];
        $this->post("/global/v1/operator", $parameters, []);
        $this->seeStatusCode(404);        
    }
    public function testShouldCreateOperator()
    {
        $country = factory('App\Models\Country')->create();
        $headOperator = factory('App\Models\HeadOperator')->create();
        $parameters = [
            'name' => 'Lykamobile',
            'country' => $country,
            'headOperator' => $headOperator,
        ];
        $this->post("/global/v1/operator", $parameters, [])->assertRedirect();
        $this->seeStatusCode(201);        
    }
    /**
     * /global/v1/operator [GET]
     */
    public function testShouldReturnAllOperators()
    {
        factory('App\Models\Operator',5)->create();
        $this->get("/global/v1/operator", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            [
                "id",
                "name",
                "country" => [              
                    "id",
                ],
                "headOperator" => [              
                    "id",
                ],
                // "mobileNetworks" => [
                //     [
                //         "id",
                //         "mccmnc",
                //     ],
                // ]
            ]
        ]);     
    }
}
