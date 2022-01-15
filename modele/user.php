<?php
class user {
        private  $id;  
        private  $email; 
        private  $password; 
        private  $firstName; 
        private  $lastName; 
        private  $gender; 
        private  $age; 

        function __construct(array $donnees) 
        {  
            $this->hydrate($donnees); 
        } 

        private function hydrate($donnees){
            foreach($donnees as $key => $value)
            {
                $this->$key = $value;
            }
        }

        public function getId(){
            return $this->id;
        }
    
        public final function setId($id1) { 
            $this->id=$id1; 
        }

        public function getGender(){
            return $this->gender;
        }

        public function setGender($gender1){
            $this->gender=$gender1;
        }

        public function getAge(){
            return $this->age;
        }

        public function setAge($age1){
            $this->$age=$age1;
        }

        public function getEmail(){
            return $this->email;
        }
    
        public final function setEmail($email1) { 
            $this->email=$email1; 
        }

        public function getPassword(){
            return $this->password;
        }

        public final function setPassword($password1) { 
            $this->password=$password1; 
        }

        public function getLastName(){
            return $this->lastName;
        }
    
        public final function setLastName($lastName1) { 
                $this->lastName=$lastName1; 
        }

        public function getFirstName(){
            return $this->firstName;
        }
    
        public final function setFirstName($firstName1) { 
                $this->firstName=$firstName1; 
        }

    }

?>