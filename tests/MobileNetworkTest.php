<?php

use AlbertCht\Lumen\Testing\TestCase;
use Laravel\Lumen\Testing\DatabaseTransactions;
// use Laravel\Lumen\Testing\DatabaseMigrations;
// use AlbertCht\Lumen\Testing\Concerns\RefreshDatabase;

class MobileNetworkTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * /global/v1/mobileNetwork [POST]
     */
    public function testShouldValidateParametersCreateMobileNetwork()
    {
        $parameters = [
            'mccmne' => '20201',
        ];
        $this->post("/global/v1/mobileNetwork", $parameters, []);
        $this->seeStatusCode(400);        
    }
    public function testShouldValidateRequiredParametersCreateMobileNetwork()
    {
        $parameters = [
            'mccmnc' => '',
        ];
        $this->post("/global/v1/mobileNetwork", $parameters, []);
        $this->seeStatusCode(400);        
    }
    public function testShouldCreateMobileNetwork()
    {
        $operator = factory('App\Models\Operator')->create();
        $mvno = factory('App\Models\Mvno')->create();
        $parameters = [
            'mccmnc' => '101010',
            'operator' => $operator,
            'mvno' => $mvno,
        ];
        $this->post("/global/v1/mobileNetwork", $parameters, [])->assertRedirect();
        $this->seeStatusCode(201);        
    }
    /**
     * /global/v1/mobileNetwork [GET]
     */
    public function testShouldReturnAllMobileNetworks()
    {
        factory('App\Models\mobileNetwork',5)->create();

        $this->get("/global/v1/mobileNetwork", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            [
                "id",
                "mccmnc",
                "operator" => [              
                    "id",
                ],
                "mvno",
            ]
        ]);   
    }
}
