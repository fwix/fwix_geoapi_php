<?php

require_once 'fwix_opp_api.php';

/** These tests could use some love, they aren't exactly complete **/

class FwixTest extends PHPUnit_Framework_TestCase{

    public function setUp(){
        $this->kFWIX_API_KEY = ''; # YOUR API KEY
        $this->kFWIX_LAT = 37.787462;
        $this->kFWIX_LON = -122.399223;
        $this->kRANDOM_PLACE_UUID = '304f36b-70c6-68ac-245f-11c9b17eafdfa';
        $this->kRANDOM_POSTCODE = 94117;
        $this->fxapi = new FwixApi($this->kFWIX_API_KEY);
    }

    public function test_get_categories(){
        $response = $this->fxapi->get_categories();
    }

    public function test_get_location(){
        $location = $this->fxapi->get_location($this->kFWIX_LAT, $this->kFWIX_LON); # Fwix's location
        $this->assertTrue($location['province'] == 'CA' && $location['city'] == 'San Francisco');
    }

    public function test_get_place(){
        $place = $this->fxapi->get_place($this->kRANDOM_PLACE_UUID);
        $this->assertTrue($place['uuid'] == $this->kRANDOM_PLACE_UUID);
    }

    public function test_get_places_by_lat_lng(){
        $places = $this->fxapi->get_places_by_lat_lng($this->kFWIX_LAT,$this->kFWIX_LON);
    }

    public function test_get_places_by_postal_code(){
        $places = $this->fxapi->get_places_by_postal_code($this->kRANDOM_POSTCODE);
        foreach($places as $place){
            $this->assertTrue($place['postal_code'] == $this->kRANDOM_POSTCODE);
        }
    }

    public function test_get_places_by_location(){
        $location = $this->fxapi->get_location($this->kFWIX_LAT, $this->kFWIX_LON);
        $places = $this->fxapi->get_places_by_location($location, $radius=3,$page_size = 5);
        $this->assertTrue(count($places) == 5);
        foreach ($places as $place){
            $this->assertTrue($place['city']=='San Francisco');
        }
    }

    public function test_get_content_by_lat_lng(){
        $content = $this->fxapi->get_content_by_lat_lng($this->kFWIX_LAT,$this->kFWIX_LON,'all');
        foreach ($content as $item){
            $this->assertTrue(array_key_exists('body',$item));
        }
    }

    public function test_get_content_by_location(){
        $content = $this->fxapi->get_content_by_location($this->fxapi->get_location($this->kFWIX_LAT, $this->kFWIX_LON),'all');
        foreach ($content as $item){
            $this->assertTrue(array_key_exists('body',$item));
        }
    }
    
    public function test_get_content_by_place(){
        //taqueria cancun
        $content = $this->fxapi->get_content_by_place('cd9df19-a725-1faa-8d7c-9629d588e9eec','all');
        foreach ($content as $item){
            $this->assertTrue(array_key_exists('body',$item));
        }
    }

    public function test_get_content_by_postal_code(){
        $content = $this->fxapi->get_content_by_postal_code($this->kRANDOM_POSTCODE,'all');
        foreach ($content as $item){
            $this->assertTrue(array_key_exists('body',$item));
        }
    }

    public function test_places_query(){
        $params = array('query'=>'Taqueria Cancun','city'=>'San Francisco','province'=>'CA');
        $places = $this->fxapi->generic_get_places($params);
        $this->assertTrue(count($places) > 0);
        foreach ($places as $place){
            $this->assertTrue($place['city']=='San Francisco');
        }
    }

}