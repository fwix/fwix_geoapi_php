<html> 
	<head>
		<title>Fwix API sample code</title>	
	</head>
	<body>  

	<?php
	/**
	 * This file displays sample code that uses all the available API functions in fwix_geo_api.php
	 */
 
	require_once '../fwix_geoapi/fwix_geo_api.php';
	
	class GeoApiTest{
	
		public $fwix;
		
		public function __construct($api_key){
			$this->fwix = new FwixApi($api_key);
		}
		
		/** Prints a list of the categories available **/
		public function get_categories(){
			$categories = $this->fwix->get_categories();			
			foreach($categories as $item){
				echo $item['name']."<br/>";
			}
		}
		
		/** Prints the location at the given latitude and longitude points **/
		public function get_location($lat, $lng){	
			$location = $this->fwix->get_location($lat, $lng);
			while(list($key, $value) = each($location)){
				echo "$key => $value"."<br/>";
			}
		}
		
		/** Prints all the properties for the place given its uuid **/
		public function get_place($uuid){
			$place = $this->fwix->get_place($uuid);
			foreach ($place as $k => $v){
			
				// there might be more than one category for this place so need to handle that case
				if($k == 'categories'){
					$categories = array();
					
					// add each category to the array
					foreach($v as $key => $value){
						array_push($categories, $value['name']);
					}
					
					// get the array values and make them a string
					$str = implode(', ', $categories);
					
					// print categories
					echo "$k = ".$str."<br/>";
					
				}else{
					echo "$k = $v"."<br/>";
				}	
			}
		}
		
		/** Prints a list of the places within the given latitude and longitude points **/
		public function get_places_by_lat_lng($lat, $lng){
			$places = $this->fwix->get_places_by_lat_lng($lat, $lng);
			foreach($places as $item){
				echo $item['name']."<br/>";
			}
		}
		
		/** Prints a list of the places within the given postal code **/
		public function get_places_by_postal_code($postal_code){
			$places = $this->fwix->get_places_by_postal_code($postal_code);
			foreach($places as $item){
				echo $item['name']."<br/>";
			}
		}
		
		/** Prints a list of the places within the given locaiton (latitude and longitude points) **/
		public function get_places_by_location($lat, $lng){
			$location = $this->fwix->get_location($lat, $lng);
			$places = $this->fwix->get_places_by_location($location);
			foreach($places as $item){
				echo $item['name']."<br/>";
			}
		}
		
		/** Prints the body and source for the given content type in the given latotude and longitude **/
		public function get_specific_content_by_lat_lng($lat, $lng, $content_types){
			$content = $this->fwix->get_content_by_lat_lng($lat, $lng, $content_types);
			foreach($content as $item){
				echo $item['author']."  ==>  ";
				echo $item['source']."<br/>";
			}
		}
		
		/** Prints the author and source for the given content type in the given postal code **/
		public function get_content_by_postal_code($postal_code, $content_types){
			$content = $this->fwix->get_content_by_postal_code($postal_code, $content_types);
			foreach($content as $item){
				echo $item['author']."  ==>  ";
				echo $item['source']."<br/>";
			}
		}
		
		/** Prints the author and source for the given content type in the given location **/
		public function get_specific_content_by_location($lat, $lng, $content_types){
			$content = $this->fwix->get_content_by_location($this->fwix->get_location($lat, $lng), $content_types);
			foreach($content as $item){
				echo $item['author']."  ==>  ";
				echo $item['source']."<br/>";
			}
		}
		
		/** Prints the author and source for the given content type in the given place uuid **/
		public function get_specific_content_by_place($place_uuid, $content_types){
			$content = $this->fwix->get_content_by_place($place_uuid, $content_types);
			foreach($content as $item){
				echo $item['author']."  ==>  ";
				echo $item['source']."<br/>";
			}
		}
	}
	
	// set up variables to use
	$api_key = '42a97fb59252';
	$lat = 37.787462;
	$lng = -122.399223;
	$random_place_uuid = '304f36b-70c6-68ac-245f-11c9b17eafdfa';
	$random_postcode = 94117;
	$random_content_type = 'status_updates';
	$random_content_type2 = 'news';
	
	// initialize the GeoApiTest by using your API key
	$myGeoApiTest = new GeoApiTest($api_key);
	
	$myGeoApiTest->get_location($lat, $lng);
	
	$myGeoApiTest->get_categories();
	
	$myGeoApiTest->get_place($random_place_uuid);
	
	$myGeoApiTest->get_specific_content_by_lat_lng($lat, $lng, $random_content_type);
	
	$myGeoApiTest->get_content_by_postal_code($random_postcode, $random_content_type);
	
	$myGeoApiTest->get_specific_content_by_location($lat, $lng, $random_content_type);
	
	$myGeoApiTest->get_specific_content_by_place($random_place_uuid, $random_content_type2);
	
	$myGeoApiTest->get_places_by_lat_lng($lat, $lng);
	
	$myGeoApiTest->get_places_by_postal_code($random_postcode);
	
	$myGeoApiTest->get_places_by_location($lat, $lng);
	
	?>
	</body> 
</html>
