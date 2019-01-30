<?php

use AlbertCht\Lumen\Testing\TestCase;
use Laravel\Lumen\Testing\DatabaseTransactions;
// use Laravel\Lumen\Testing\DatabaseMigrations;
// use AlbertCht\Lumen\Testing\Concerns\RefreshDatabase;

class CurrencyTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * /global/v1/currency [POST]
     */
    public function testShouldValidateParametersCreateCurrency()
    {
        $parameters = [
            'namee' => 'USD',
        ];
        $this->post("/global/v1/currency", $parameters, []);
        $this->seeStatusCode(400);        
    }
    public function testShouldValidateRequiredParametersCreateCurrency()
    {
        $parameters = [
            'name' => '',
        ];
        $this->post("/global/v1/currency", $parameters, []);
        $this->seeStatusCode(400);        
    }
    public function testShouldCreateCurrency()
    {
        $parameters = [
            'name' => 'USD',
            'symbol' => '$',
            'usdRelation' => '1',
            'euroRelation' => '0.7'
        ];
        $this->post("/global/v1/currency", $parameters, [])->assertRedirect();
        $this->seeStatusCode(201);        
    }
    /**
     * /global/v1/currency [GET]
     */
    public function testShouldReturnAllCurrencies()
    {
        factory('App\Models\Currency',5)->create();
        $this->get("/global/v1/currency", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            [
                "id",
                "name",
                "symbol",
                "usdRelation",
                "euroRelation",
            ]
        ]); 
    }
}
