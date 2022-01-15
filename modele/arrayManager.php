<?php 

class arrayManager{

    function getAllNames(){
        try { 

            $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); 
            $filter = []; 
            $option = [
                'sort' => 
                [
                'nom' => 1
                ],
            ]; 
            $read = new MongoDB\Driver\Query($filter, $option); 
            $personnes = $manager->executeQuery('planning.personneCorvee', $read); 
            $i = 0;

            
            foreach ($personnes as $user) { 
                $user = $user->prenom.' '.$user->nom; 
                $arrayP[$i] = $user;
                $i++;
            } 

            return $arrayP;
        } 
        catch ( MongoDB\Driver\ConnectionException $e ) 
        { 
                echo $e->getMessage(); 
        } 
    }

    function getGender(){
        $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); 
        $cmd = new \MongoDB\Driver\Command([
            'aggregate' => 'personneCorvee',
            'pipeline' => 
            [[
                '$group' =>
                [
                    '_id' => '$genre',
                    'ids' => 
                    [
                      '$push' => '$_id',
                    ],
                ]
            ]],
            'cursor' => new stdClass, 
        ]);

        $cursor = $manager->executeCommand('planning',  $cmd);
        $list = $cursor->toArray();

        foreach($list as $gender) {
            $name = $gender->{'_id'};
            $array[$name] = $gender->{'ids'};
            $count[$name] = [0];
           
            foreach($array[$name] as $id){
                $id = strval($id);
                $count[$name][0] += $this->getNbCorvees($id); //on récupère le nombre de semaines épluchées par personne et on ajoute à la somme dans le tableau genre correspondant
            }

            $divideBy = sizeof($array[$name]); //on récupère le nombre de personnes dans chaque tableau contenant les personnes du même genre
            $count[$name] = $count[$name][0]/$divideBy; 
        }
       
        $number = max($count);
        $result = array_search($number, $count);
        return $result;
    }
    
   



    function getUserInfo($prenom){
        try { 

            $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); 
            $filter = ['prenom' => $prenom]; 
            $option = [
                'sort' => 
                [
                'nom' => 1
                ],
            ]; 
            $read = new MongoDB\Driver\Query($filter, $option); 
            $personnes = $manager->executeQuery('planning.personneCorvee', $read); 
            $i = 0;

            foreach ($personnes as $user) { 
                $userGenre = $user->genre;
                $userAge = $user->age;
                $arrayInfoUser["genre"] = $userGenre; 
                $arrayInfoUser["age"] = $userAge;
            } 

            //echo $arrayInfoUser[genre]; 
            return $arrayInfoUser;
        } 
        catch ( MongoDB\Driver\ConnectionException $e ) 
        { 
                echo $e->getMessage(); 
        } 
    }

    function getDates(){
        try { 

            $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); 
            $filter = []; 
            $option = []; 
            $read = new MongoDB\Driver\Query($filter, $option); 
            $dates = $manager->executeQuery('planning.dateCorvee', $read); 
            $i = 0;
            
            foreach ($dates as $date) { 
                $date = $date->dateCorvee; 
                $arrayP[$i] = $date;
                $i++;
            } 
            return $arrayP;
        } 
        catch ( MongoDB\Driver\ConnectionException $e ) 
        { 
                echo $e->getMessage(); 
        } 
    }

    function getDatesofTheYear($yearChoosed){
        try { 

            $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); 
            $filter = []; 
            $option = []; 
            $read = new MongoDB\Driver\Query($filter, $option); 
            $dates = $manager->executeQuery('planning.dateCorvee', $read); 
            $i = 0;
            $semainesCorvees = null;
            
            foreach ($dates as $date) { 
                $date = $date->dateCorvee; 
                $arrayP[$i] = $date;
                $i++;

                $year = substr($date, -4);
                //echo $year. " ";
                if ($year === $yearChoosed) {
                    $semainesCorvees++;
                }
            } 
            return $semainesCorvees;
        } 
        catch ( MongoDB\Driver\ConnectionException $e ) 
        { 
                echo $e->getMessage(); 
        } 
    }

    
    function getUsersDates(){
        try { 

            $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); 
            $filter = []; 
            $option = []; 
            $read = new MongoDB\Driver\Query($filter, $option); 
            $dates = $manager->executeQuery('planning.dateCorvee', $read)->toArray(); 
            $i = 0;
            
            foreach ($dates as $date) { 
                $nameUser = $date->idPersonneCorvee;
                $date = $date->dateCorvee; 
                $arrayP[$date] = $nameUser;
                $i++;
            } 
            return $arrayP;
        } 
        catch ( MongoDB\Driver\ConnectionException $e ) 
        { 
                echo $e->getMessage(); 
        } 
    }

    function getId($names, $personne){
        $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); 
        for ($i = 0; $i < count($names); $i++){
            $prenom = explode(" ", $names[$i]);
            if($names[$i] === $personne) {
                
                //requete pour trouver l'id de la personne
                $filter = ['prenom' => $prenom[0] ];
                $option = [
                    'projection' => [
                        'id' => 1
                    ],
                ]; 
                $read = new MongoDB\Driver\Query($filter, $option); 
                $idPersonneCorvees = $manager->executeQuery('planning.personneCorvee', $read)->toArray();
                
                return $idPersonneCorvees[0];
            }
        }
    }

    function getUserById($id){
        try { 
            $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); 
            $id = new MongoDB\BSON\ObjectId($id);
                $filter = ['_id' => $id];
                $option = [];  
                $read = new MongoDB\Driver\Query($filter, $option); 
                $users = $manager->executeQuery('planning.personneCorvee', $read)->toArray();
                foreach ($users as $user) { 
                    $name = $user->prenom.' '.$user->nom;
                } 
            if(isset($name)){
                return $name;
            }
        }

        catch ( MongoDB\Driver\ConnectionException $e ) 
        { 
                echo $e->getMessage(); 
        } 
    }

    function validateDates($date, $idPersonneCorvees){
        $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); 

            $id = strval($idPersonneCorvees->{'_id'}); //get the object id
            $dateObject = array(
                'idPersonneCorvee' => $id,
                'dateCorvee' => $date,
            );
            $insert = new MongoDB\Driver\BulkWrite();
            $insert->insert($dateObject);
            $manager->executeBulkWrite('planning.dateCorvee', $insert); //insertion
        
    }

    function deleteAllDates(){
        $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); 
        $delete = new MongoDB\Driver\BulkWrite();
        $delete->delete([]);
      
        $manager->executeBulkWrite('planning.dateCorvee', $delete); //deleteAll
        
    }

    function getNbCorvees($id) {
        try { 
            $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); 
                if(!is_string($id)){
                    $id = strval($id->{'_id'});
                }
                $filter = ['idPersonneCorvee' => $id];
                $option = [];  
                $read = new MongoDB\Driver\Query($filter, $option); 
                $idsDates = $manager->executeQuery('planning.dateCorvee', $read)->toArray();
                $nbCorvees = count($idsDates);
                $nbCorveesPersonne = $nbCorvees;
               
            return $nbCorveesPersonne;
        }

        catch ( MongoDB\Driver\ConnectionException $e ) 
        { 
                echo $e->getMessage(); 
        } 
              
        // requete nombre de corvees fait par personnes
        /* > db.dateCorvee.find(
            {
                idPersonneCorvee: "61af7b4a0f07b57db30a8c1b"
            }
            ).count()
            foreach($idPersonneCorvees as $idPersonneCorvee) {
            $id = strval($idPersonneCorvee->{'_id'}); //get the object id
            //echo $id;
            $filter = ['idPersonneCorvee' => $id];
            $option = []; 
        $read = new MongoDB\Driver\Query($filter, $option); 
        $nombreCorveesParPersonne = $manager->executeQuery('planning.dateCorvee', $read);
        var_dump($nombreCorveesParPersonne);
        }*/

    }

    function findPersonWhoPeeledTheMost(){
        try { 
            $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); 
            $cmd = new \MongoDB\Driver\Command([
                'aggregate' => 'dateCorvee',
                'pipeline' => 
                [ [
                    '$group' => ["_id" => '$idPersonneCorvee', "compte" => array('$sum' => 1)]
               ],
               [
                    '$sort' => ["compte" => -1]
               ], 
               [
                    '$limit' => 1
               ], 
                ],
                'cursor' => new stdClass, 
            ]);
    
            $cursor = $manager->executeCommand('planning',  $cmd);
            $list = $cursor->toArray();
            //echo $list[0]->_id;
            return $list[0]->_id;
        } 
        catch ( MongoDB\Driver\ConnectionException $e ) 
        { 
                echo $e->getMessage(); 
        }
    }

    function findPersonWhoPeeledTheLeast(){
        try { 
            $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); 
            $cmd = new \MongoDB\Driver\Command([
                'aggregate' => 'dateCorvee',
                'pipeline' => 
                [ [
                    '$group' => ["_id" => '$idPersonneCorvee', "compte" => array('$sum' => 1)]
               ],
               [
                    '$sort' => ["compte" => 1]
               ], 
               [
                    '$limit' => 1
               ], 
                ],
                'cursor' => new stdClass, 
            ]);
    
            $cursor = $manager->executeCommand('planning',  $cmd);
            $list = $cursor->toArray();
            //echo $list[0]->_id;
            return $list[0]->_id;
        } 
        catch ( MongoDB\Driver\ConnectionException $e ) 
        { 
                echo $e->getMessage(); 
        }
    }


    // stats pour savoir il y a combien de semaines ou on a quelquun pour la corvee
    function findNumberWeeksWhereThereIsSomeoneToPeel($annee){
        $anneeString = strval($annee);
        try { 
            $anneeString = strval($annee);
            $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); 
            $cmd = new \MongoDB\Driver\Command([
                'aggregate' => 'dateCorvee',
                'pipeline' => 
                [ [
                    '$match' => ['dateCorvee' => array('$regex' => $anneeString)]
               ],
               [
                    '$count' => "countSemainesCorveesNonVides"
               ],  
                ],
                'cursor' => new stdClass, 
            ]);
    
            $cursor = $manager->executeCommand('planning',  $cmd);
            $list = $cursor->toArray();
            if(isset($list[0]->countSemainesCorveesNonVides)){
                return $list[0]->countSemainesCorveesNonVides;
            }
            else {
                return null;
            }
        } 
        catch ( MongoDB\Driver\ConnectionException $e ) 
        { 
                echo $e->getMessage(); 
        }


    }

}


?>