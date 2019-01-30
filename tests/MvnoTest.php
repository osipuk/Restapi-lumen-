<?php

use AlbertCht\Lumen\Testing\TestCase;
use Laravel\Lumen\Testing\DatabaseTransactions;
// use Laravel\Lumen\Testing\DatabaseMigrations;
// use AlbertCht\Lumen\Testing\Concerns\RefreshDatabase;

class MvnoTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * /global/v1/mvno [POST]
     */
    public function testShouldValidateParametersCreateMvno()
    {
        $parameters = [
            'namee' => 'Lykamobile',
        ];
        $this->post("/global/v1/mvno", $parameters, []);
        $this->seeStatusCode(400);        
    }
    public function testShouldValidateRequiredParametersCreateMvno()
    {
        $parameters = [
            'name' => '',
        ];
        $this->post("/global/v1/mvno", $parameters, []);
        $this->seeStatusCode(400);        
    }
    public function testShouldCreateMvno()
    {
        // $mobileNetwork = factory('App\Models\mobileNetwork')->create();
        $parameters = [
            'name' => 'Lykamobile',
            // 'mobileNetworks' => $mobileNetwork,
        ];
        $this->post("/global/v1/mvno", $parameters, [])->assertRedirect();
        $this->seeStatusCode(201);        
    }
    /**
     * /global/v1/mvno [GET]
     */
    public function testShouldReturnAllMvnos()
    {
        factory('App\Models\Mvno',5)->create();
        $this->get("/global/v1/mvno", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            [
                "id",
                "name",
                // "mobileNetworks" => [
                //     [              
                //         "id",
                //     ],
                // ],
            ]
        ]); 
    }
}
