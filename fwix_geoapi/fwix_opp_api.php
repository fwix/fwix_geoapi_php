<?php
/**
 * Copyright 2011 Fwix, Inc
 *
 * Permission to use, copy, modify, distribute, and sell this software
 * and its documentation for any purpose is hereby granted without fee,
 * provided that the above copyright notice appears in all copies and that
 * both that copyright notice and this permission notice appear in
 * supporting documentation, and that the name of Fwix, Inc
 * not be used in advertising or publicity
 * pertaining to distribution of the software without specific, written
 * prior permission.  Fwix, Inc makes no representations about the 
 * suitability of this software for any
 * purpose.  It is provided "as is" without express or implied warranty.
 *
 * Fwix, Inc disclaims all warranties with regard to this software, 
 * including all implied warranties of merchantability and fitness, 
 * in no event shall Fwix, Inc be liable for any special, indirect or
 * consequential damages or any damages whatsoever resulting from loss of
 * use, data or profits, whether in an action of contract, negligence or
 * other tortious action, arising out of or in connection with the use or
 * performance of this software.
 *
 *
**/

if (!function_exists('json_decode')) {
    throw new Exception('The Fwix API requires the JSON PHP extension.');
}

class FwixApi{

    const CATEGORIES_KEY = 'categories';
    const GET_REQUEST = 'GET';
    const POST_REQUEST = 'POST';
    const API_KEY_STRING = 'api_key';
    const USER_ID_KEY = 'user_id';
    const LAT_KEY = 'lat';
    const LNG_KEY = 'lng';
    const POSTAL_CODE_KEY = 'postal_code';
    const CONTENT_TYPES_KEY ='content_types';
    protected static $PLACES_PATH = '/places.json';
    protected static $CONTENT_PATH = '/content.json';
    protected static $BASE_URL = 'http://geoapi.fwix.com';
    public static $CONTENT_TYPE_NEWS = 'news';
    public static $CONTENT_TYPE_PHOTOS = 'photos';
    public static $CONTENT_TYPE_USER_REVIEWS = 'user_reviews';
    public static $CONTENT_TYPE_CRITIC_REVIEWS = 'critic_reviews';
    public static $CONTENT_TYPE_STATUS_UPDATES = 'status_updates';
    public static $CONTENT_TYPE_EVENTS = 'events';
    public static $CONTENT_TYPE_REAL_ESTATE = 'real_estate';
    public static $CONTENT_TYPE_ALL = 'all';


    public static function get_content_types(){

        return array(self::$CONTENT_TYPE_NEWS,
                    self::$CONTENT_TYPE_PHOTOS,
                    self::$CONTENT_TYPE_USER_REVIEWS,
                    self::$CONTENT_TYPE_CRITIC_REVIEWS,
                    self::$CONTENT_TYPE_STATUS_UPDATES,
                    self::$CONTENT_TYPE_EVENTS,
                    self::$CONTENT_TYPE_REAL_ESTATE);
    }



    public function __construct($api_key, $user_id = null){
        $this->config[self::API_KEY_STRING] = $api_key;
        $this->config[self::USER_ID_KEY] = $user_id;
    }

    public function fetch_url($base_url, $params = array(), $request_type = self::GET_REQUEST, $ch=null){
        $params[self::API_KEY_STRING] = $this->config[self::API_KEY_STRING];
        if($this->config[self::USER_ID_KEY]){
            $params[self::USER_ID_KEY] = $this->user_id;}
        if (strtoupper($request_type) == self::GET_REQUEST){
            $url = $base_url . $this->build_query_string($params);
        } else if (strtoupper($request_type) == self::POST_REQUEST){
            throw new Exception('Not Supported Yet');
        } else{
            throw new Exception('Not Supported Yet');
        }

        if (function_exists('curl_init')) {
            if(!$ch){
                $ch = curl_init();
            }
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
            $response = curl_exec($ch);
		    curl_close($ch);
        } else{
            $response = $this->fetch_url_without_curl($url);
        }
        return json_decode($response, true);
    }

    public function fetch_url_without_curl($url){
        $response = file_get_contents($url);
        return $response;
    }

    /** Returns an array of category arrays **/
    public function get_categories(){

        $url = self::$BASE_URL . '/categories.json';
        $response = $this->fetch_url($url);
        $all_categories = array();

        foreach($response[self::CATEGORIES_KEY] as $category){
            self::parse_categories($category,$all_categories);
        }
        return $all_categories;
    }

    /** Returns a Location array for the given latitude and longitude **/
    public function get_location($latitude, $longitude){
        $url = self::$BASE_URL . '/location.json';
        $params = array(self::LAT_KEY => $latitude, self::LNG_KEY => $longitude);
        $location = $this->fetch_url($url,$params);
        return $location;
    }

    /** Returns a place array given a UUID**/
    public function get_place($uuid){
        $url = self::$BASE_URL . sprintf('/places/%s.json',$uuid);
        $place = $this->fetch_url($url);
        return $place['place'];
    }

    public function generic_get_places($params){
        $url = self::$BASE_URL . self::$PLACES_PATH;
        $places = $this->fetch_url($url,$params);
        return $places['places'];
    }

    /** Returns a list of places near the given lat and lng **/
    public function get_places_by_lat_lng($latitude,
                                          $longitude,
                                          $page_number = 1,
                                          $page_size = 10,
                                          $radius = 10,
                                          $categories = null){
        $params = array(self::LAT_KEY => $latitude, self::LNG_KEY => $longitude);
        $params = array_merge($params,$this->place_filters($page_number,$page_size,$radius, $categories));
        return $this->generic_get_places($params);
    }

    /** Returns an array of places near the given postal code**/
    public function get_places_by_postal_code($postal_code,
                                              $page_number = 1,
                                              $page_size = 10,
                                              $radius = 10,
                                              $categories = null){    
        $params = array(self::POSTAL_CODE_KEY => $postal_code);
        $params = array_merge($params,$this->place_filters($page_number,$page_size,$radius, $categories));
        return $this->generic_get_places($params);
    }

    /** Given a location array, returns associated places **/
    public function get_places_by_location($location,
                                           $page_number = 1,
                                           $page_size = 10,
                                           $radius = 10,
                                           $categories = null){    
        $params = array_merge($location,$this->place_filters($page_number,$page_size,$radius,$categories));
        return $this->generic_get_places($params);
    }

    /** Updates information about a place, returns a boolean of whether the request succeeded or not **/
    public function update_place($place){
        throw new Exception('Not Implemented');
    }

    /** Deletes a place, returs a boolean of wheter or not the request was succesful **/
    public function delete_place($place){
        throw new Exception('Not Implemented');
    }

    /** Returns a list of content objects based on the given criteria **/
    public function generic_get_content($params,
                                        $content_types,
                                        $page_number,
                                        $page_size,
                                        $start_date,
                                        $end_date,
                                        $sort_by, 
                                        $search_query){
        $url = self::$BASE_URL . self::$CONTENT_PATH;
        if (is_array($content_types))
            $params[self::CONTENT_TYPES_KEY] = implode(',',$content_types);
        else
            $params[self::CONTENT_TYPES_KEY] = $content_types;
        if($page_number !== null)
            $params['page'] = $page_number;
        if($page_size !== null)
            $params['page_size'] = $page_size;
        if($start_date !== null)
            $params['start_date'] = $start_date;
        if($end_date !== null)
            $params['end_date'] = $end_date;
        if($sort_by !== null)
            $params['sort_by'] = $sort_by;
        if($search_query)
            $params['query'] = $search_query;
        $raw_content = $this->fetch_url($url,$params);
        $content = array();
        foreach($this->get_content_types() as $CTYPE){
            if(array_key_exists($CTYPE,$raw_content)){
                foreach($raw_content[$CTYPE] as $single_content){
                    $single_content['type'] = $CTYPE;
                    array_push($content,$single_content);
                }
            }
        }                  
        return $content;
    }


    /** Returns an array of content arrays based on latitude and longitude **/
    public function get_content_by_lat_lng($latitude,
                                           $longitude,
                                           $content_types,
                                           $page_number=1,
                                           $page_size=10,
                                           $start_date=null,
                                           $end_date=null,
                                           $sort_by=null, 
                                           $search_query=null){
        $params = array(self::LAT_KEY => $latitude, self::LNG_KEY => $longitude);
        return $this->generic_get_content( $params,
                                    $content_types,
                                    $page_number,
                                    $page_size,
                                    $start_date,
                                    $end_date,
                                    $sort_by, 
                                    $search_query);
    }

    /** Returns a list of content objects near a given a postal code **/
    public function get_content_by_postal_code($postal_code,
                                               $content_types,
                                               $page_number=1,
                                               $page_size=10,
                                               $start_date=null,
                                               $end_date=null,
                                               $sort_by=null, 
                                               $search_query=null){
        $params = array(self::POSTAL_CODE_KEY => $postal_code);
        return $this->generic_get_content( $params,
                                    $content_types,
                                    $page_number,
                                    $page_size,
                                    $start_date,
                                    $end_date,
                                    $sort_by, 
                                    $search_query);
    }

    /** Returns a list of content objects near a given location object **/
    public function get_content_by_location($location,
                                            $content_types,
                                            $page_number=1,
                                            $page_size=10,
                                            $start_date=null,
                                            $end_date=null,
                                            $sort_by=null, 
                                            $search_query=null){
        return $this->generic_get_content( $location,
                                    $content_types,
                                    $page_number,
                                    $page_size,
                                    $start_date,
                                    $end_date,
                                    $sort_by, 
                                    $search_query);
    }


    /** Returns a list of content arrays associated with a given place uuid **/
    public function get_content_by_place($place_uuid,
                                         $content_types,
                                         $page_number=1,
                                         $page_size=10,
                                         $start_date=null,
                                         $end_date=null,
                                         $sort_by=null, 
                                         $search_query=null){
        $params = array('place_id' => $place_uuid);
        return $this->generic_get_content( $params,
                                    $content_types,
                                    $page_number,
                                    $page_size,
                                    $start_date,
                                    $end_date,
                                    $sort_by, 
                                    $search_query);
    }

    protected function place_filters($page_number,$page_size,$radius, $categories){
        $filters = array();
        if ($page_number !== null)
            $filters['page'] = $page_number;
        if($page_size !== null)
            $filters['page_size'] = $page_size;
        if($radius !== null)
            $filters['radius'] = $radius;
        if($categories !== null){
            $filters[self::CATEGORIES_KEY] = implode(',',$categories);
        }
        return $filters;
    }

    protected static function parse_categories($category,&$categories){
        //recursively parses all categories
        $next_categories = array();
        if(array_key_exists(self::CATEGORIES_KEY,$category) && $category[self::CATEGORIES_KEY]){
            $next_categories = $category[self::CATEGORIES_KEY];
            unset($category[self::CATEGORIES_KEY]);
        }
        array_push($categories,$category);
        foreach ($next_categories as $next_category){
            self::parse_categories($next_category, $categories);
        }
    }

    protected function build_query_string($params){
        $query_string = '?';
        foreach ($params as $key=>$value){
            $query_string .= urlencode($key) . '=' .urlencode($value) .'&';
        }
        return substr($query_string,0,-1);
    }

}

