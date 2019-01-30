<?php

use AlbertCht\Lumen\Testing\TestCase;
use Laravel\Lumen\Testing\DatabaseTransactions;
// use Laravel\Lumen\Testing\DatabaseMigrations;
// use AlbertCht\Lumen\Testing\Concerns\RefreshDatabase;

class CountryTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * /global/v1/country [POST]
     */
    public function testShouldValidateParametersCreateCountry()
    {
        $parameters = [
            'namee' => 'USA',
        ];
        $this->post("/global/v1/country", $parameters, []);
        $this->seeStatusCode(400);        
    }
    public function testShouldValidateRequiredParametersCreateCountry()
    {
        $parameters = [
            'name' => '',
        ];
        $this->post("/global/v1/country", $parameters, []);
        $this->seeStatusCode(400);        
    }
    public function testShouldValidateContinentParameterCreateCountry()
    {
        $parameters = [
            'name' => 'USA',
            'iso2' => 'US',
            'iso3' => 'USA',
            'mcc' => ['202'],
            'continent' => 'America', 
        ];
        $this->post("/global/v1/country", $parameters, []);
        $this->seeStatusCode(400);        
    }
    public function testShouldCreateCountry()
    {
        $currency = factory('App\Models\Currency')->create();
        $parameters = [
            'name' => 'USA',
            'iso2' => 'US',
            'iso3' => 'USA',
            'mcc' => ['205'],
            'continent' => 'North America',
            'currency' => $currency,
            'phonePrefix' => ['30'],
        ];    
        $this->post("/global/v1/country", $parameters, [])->assertRedirect();
        $this->seeStatusCode(201);        
    }
    /**
     * /global/v1/country [GET]
     */
    public function testShouldReturnAllCountries()
    {
        factory('App\Models\Country',5)->create();
        $this->get("/global/v1/country", []);
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            [
                "id",
                "name",
                "iso2",
                "iso3",
                "mcc",
                "phonePrefix",
                "currency" => [              
                    "id",
                    "name",
                    "symbol",
                    "euroRelation",
                    "usdRelation"
                ],
                "continent",
            ]
        ]);      
    }
}
