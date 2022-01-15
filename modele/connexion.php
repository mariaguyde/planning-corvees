<?php 
try { 
        $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); 
      //var_dump($manager); 
}  
catch ( MongoDB\Driver\Exception\InvalidArgumentException $e ) 
{ 
        echo $e->getMessage(); 
} 
?>
