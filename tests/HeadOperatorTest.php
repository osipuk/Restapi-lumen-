<?php

use AlbertCht\Lumen\Testing\TestCase;
use Laravel\Lumen\Testing\DatabaseTransactions;
// use Laravel\Lumen\Testing\DatabaseMigrations;
// use AlbertCht\Lumen\Testing\Concerns\RefreshDatabase;

class HeadOperatorTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * /global/v1/headOperator [POST]
     */
    public function testShouldValidateParametersCreateHeadOperator()
    {
        $parameters = [
            'namee' => 'Lykamobile',
        ];
        $this->post("/global/v1/headOperator", $parameters, []);
        $this->seeStatusCode(400);        
    }
    public function testShouldValidateRequiredParametersCreateHeadOperator()
    {
        $parameters = [
            'name' => '',
        ];
        $this->post("/global/v1/headOperator", $parameters, []);
        $this->seeStatusCode(400);        
    }
    public function testShouldCreateHeadOperator()
    {
        // $operator = factory('App\Models\Operator')->create();
        $parameters = [
            'name' => 'Lykamobile',
            // 'operators' => $operator,
        ];
        $this->post("/global/v1/headOperator", $parameters, [])->assertRedirect();
        $this->seeStatusCode(201);        
    }
    /**
     * /global/v1/headOperator [GET]
     */
    public function testShouldReturnAllHeadOperators()
    {
        factory('App\Models\HeadOperator',5)->create();
        $this->get("/global/v1/headOperator", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            [
                "id",
                "name",
                // "operators" => [              
                //     [
                //         "id",
                //     ],
                // ],
            ]
        ]); 
    }
}
