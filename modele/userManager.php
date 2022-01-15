<?php 

class userManager{

    function verifyUser(user $searchedUser){
        try { 

            $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); 
            $filter = []; 
            $option = []; 
            $read = new MongoDB\Driver\Query($filter, $option); 
            $personnes = $manager->executeQuery('planning.personneCorvee', $read); 
            $verify = false;

            foreach ($personnes as $user) { 
                $passwordUser =  $user->password;
                $emailUser = $user->mail;

                // à voir pour les mots de passe cryptés
                // password_verify($searchedUser->getPassword(), $passwordUser)
                if($emailUser === $searchedUser->getEmail() && password_verify($searchedUser->getPassword(), $passwordUser)){
                    $verify = true;
                }
            } 
            return $verify;
        } 
        catch ( MongoDB\Driver\ConnectionException $e ) 
        { 
                echo $e->getMessage(); 
        } 
    }

    function createUser(user $user){
        $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017"); 
        $personneObject = array(
            'nom' => $user->getLastName(),
            'prenom' => $user->getFirstName(),
            'mail'=> $user->getEmail(),
            'password'=>$user->getPassword(),
            'age'=>$user->getAge(),
            'genre'=>$user->getGender()
        );
        $insert = new MongoDB\Driver\BulkWrite();
        $insert->insert($personneObject);
        $manager->executeBulkWrite('planning.personneCorvee', $insert); //insertion
    }

}

?>