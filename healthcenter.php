<?php
//-------------------------------------------------------------
// * Name: PHP2GeoJSON  
// * Purpose: GIST@NU (www.cgistln.nu.ac.th)
// * Date: 2016/10/13
// * Author: Chingchai Humhong (chingchaih@nu.ac.th)
// * Acknowledgement: 
//-------------------------------------------------------------
   // Database connection settings
	   define("PG_DB"  , "database");
	   define("PG_HOST", "localhost"); 
	   define("PG_USER", "postgres");
	   define("PG_PORT", "port"); 
	   define("PG_PASS", "password"); 
	   define("TABLE",   "table");
	   
    // Retrieve start point
    // Connect to database
    $con = pg_connect("dbname=".PG_DB." host=".PG_HOST." port=".PG_PORT." password=".PG_PASS." user=".PG_USER);
    $sql = "select  gid, provcode, maincode, bed, name, lat, lon, typecode, ST_AsGeoJSON(geom) AS geojson from ".TABLE." WHERE provcode = 65; ";
    
   // Perform database query
   $query = pg_query($con,$sql);   
   //echo $sql;

    // Return route as GeoJSON
   $geojson = array(
      'type'      => 'FeatureCollection',
      'features'  => array()
   ); 
  
   // Add geom to GeoJSON array
   while($edge=pg_fetch_assoc($query)) {  

      $feature = array(
         'type' => 'Feature',
         'geometry' => json_decode($edge['geojson'], true),
         'crs' => array(
            'type' => 'EPSG',
            'properties' => array('code' => '4326')
         ),
            'properties' => array(
			'gid' => $edge['gid'],
            'provcode' => $edge['provcode'],
            'maincode' => $edge['maincode'],
            'bed' => $edge['bed'],
            'name' => $edge['name'],
            'lat' => $edge['lat'],
            'lon' => $edge['lon'],
            'typecode' => $edge['typecode']		
         )
      );
      
      // Add feature array to feature collection array
      array_push($geojson['features'], $feature);
   }

	
   // Close database connection
   pg_close($con);

   // Return routing result
   // header('Content-type: application/json',true);
  echo json_encode($geojson);
?>