<?php 

    class arrayController { 
    
        public function __construct($twig, $load) { 
            require_once('./modele/arrayManager.php'); 
            require_once('./modele/userManager.php');
            $this->arrayManager = new arrayManager(); 
            $this->userManager = new userManager($twig, $load);
            $this->load = $load;
            $this->twig = $twig;
        } 

        public function returnLinks($page){
            //var_dump($_SESSION['user']);

            if ($_SESSION['user'] === true) {
                $titreConnexion = "Se déconnecter";
                $menuConnexion = "logout";
            }
            else {
                $titreConnexion = "Se connecter";
                $menuConnexion = "login"; 
            }

            $links = [
                "create"=>["name"=>"Créer" , "ctrl"=>"user"], 
                "tableau"=>["name"=>"Tableau","ctrl"=>"array"],
                $menuConnexion=> ["name"=>$titreConnexion, "ctrl"=>"user"],
                "stats"=>["name"=>"Statistiques", "ctrl"=>"array"]
            ];
            unset($links[$page]);
            return ($links);
        }

        public function tableau() { 
            if (isset($_SESSION['user']) && $_SESSION['user'] === true) {
            
            $page = 'tableau'; 
            $this->ReturnNames();
            $dates = null;
            $links = $this->returnLinks($page);
            $users = $_POST["options[]"];
            $nom_page = "Remplissez le tableau hebdomadaire";

            if(isset($_POST['date'])){

                $choosenYear = $_POST['date'];

                $date = date("d.m.Y", strtotime("first Monday $choosenYear-01"));

                $year = date("Y", strtotime($date));

              

                ////////// A améliorer si possible : 
                $datesUsers = $this->arrayManager->getUsersDates();
                

                foreach ($datesUsers as $dateUser){ //pour chaque id utilisateur compris dans le tableau date -> id
                    
                    $dateKeys = array_keys($datesUsers, $dateUser); //on cherche les clés date correspondant à l'id utilisateur

                    foreach ($dateKeys as $dateKey){ //Pour chaque date
                        $dateKey = str_replace("/", ".", $dateKey);
                        $names[$dateKey] = $this->arrayManager->getUserById($dateUser); //On récupère le nom d'utilisateur correspondant à l'id et on l'insere dans le tableau date -> nom utilisateur
                    }
                    
                }

                //////////
            

                while($year == $choosenYear){

                    for($i=0; $i <=4 && $year == $choosenYear ; $i++)
                    {
                        $dates[$date] = $date;
                        $date = date("d.m.Y", strtotime("$date +1 week"));
                        $year = date("Y", strtotime($date));
                    }
                }
            }

            echo $this->twig->render("tableau.twig" , array(
                "dates" => $dates,
                "users" => $users,
                "links" => $links,
                "page" => $page,
                "nom_page" => $nom_page,
                'names'=> $names,
            ));
        }
        else {
            $page = "unauthorized";
            $links = $this->returnLinks($page);
            $nom_page = "Accès refusé";
            
            echo $this->twig->render("unauthorized.twig" , array(
            "links" => $links,
            "page" => $page,
            "nom_page" => $nom_page,
            ));
        }

        }

        public function validate() {
            $page = 'validate'; 
            $dates = [];
            $names = $this->arrayManager->getAllNames();

            if(isset($_POST["dates"])){

                foreach($_POST["dates"] as $date => $personne){
                    $date = str_replace(".", "/", $date);
                    $dates[$date] = $personne;
                    $idPersonneCorvees = $this->arrayManager->getId($names, $personne);
                    $this->arrayManager->validateDates($date, $idPersonneCorvees);
                }
            }
            else {
                $dates["Erreur"] = "Vous n'avez choisi aucune date !";
            }

            $registeredDates = $this->arrayManager->getDates(); //get datas of planning.dateCorvee 
            
            $links = $this->returnLinks($page);
            $nom_page = "Validation";

            echo $this->twig->render("validation.twig" , array(
                "dates" => $dates,
                "links" => $links,
                "page" => $page,
                "nom_page" => $nom_page,
            ));

        }



        public function stats(){

            if (isset($_SESSION['user']) && $_SESSION['user'] === true) {

            $page = "statsResults";
            $names = $this->arrayManager->getAllNames();
            $nom_page = "Statistiques";
            $nbCorveesMax = 0;
            // sert à savoir qui a épluché le plus
            //requête MongoDB pour trouver l'id de la personne ayant épluche le plus 
            $idPersonnePeeledMost = $this->arrayManager->findPersonWhoPeeledTheMost();
            //requête MongoDB pour trouver le nom de cette personne
            $nomPersonneAyantEplucheLePlus = $this->arrayManager->getUserById($idPersonnePeeledMost);
            // sert à savoir qui a épluché le moins
            //requête MongoDB pour trouver l'id de la personne ayant épluche le plus 
            $idPersonnePeeledLeast = $this->arrayManager->findPersonWhoPeeledTheLeast();
            //requête MongoDB pour trouver le nom de cette personne
            $nomPersonneAyantEplucheLeMoins = $this->arrayManager->getUserById($idPersonnePeeledLeast);
            $genreAyantLePlusEpluche = $this->arrayManager->getGender(); 
            $trancheAgeAyantLePlusEpluche = "Indéfini pour le moment !";
            $corveesHomme = 0; 
            $corveesFemme = 0;   
            $nbPersonneEnfants = 0; 
            $nbPersonneAdos = 0;
            $nbPersonneAdultes = 0;
            $nbPersonneAines = 0;
            $tableauAnnee = [];


            foreach($names as $name){
                $id = $this->arrayManager->getId($names, $name);
                $nbCorveesPersonnes = $this->arrayManager->getNbCorvees($id); //sert à récupérer le nombre de semaines d'épluchages d'une personne
                $CorveesPersonnes[$name] = $nbCorveesPersonnes;
                //echo $CorveesPersonnes[$name];  

                // connaître le genre d'une personne 
                $prenom = [];
                $prenom = explode(" ", $name);
                // echo $prenom[0];

                $userInfos = $this->arrayManager->getUserInfo($prenom[0]);

               // savoir à quelle tranche d'âge la personne appartient 
               if ($userInfos["age"] >= 0 && $userInfos["age"] <= 14) {
                    $nbPersonneEnfants++; 
               }
               else if ($userInfos["age"] <= 24) {
                    $nbPersonneAdos++; 
               }
               else if ($userInfos["age"] <= 64) {
                    $nbPersonneAdultes++; 
               }
               else {
                    $nbPersonneAines++; 
               }
            }

            // savoir quel est la tranche d'âge qu'on retrouve le plus 
            if ($nbPersonneEnfants > $nbPersonneAdos && $nbPersonneEnfants > $nbPersonneAdultes && $nbPersonneEnfants > $nbPersonneAines){
                $trancheAgeAyantLePlusEpluche = "Enfants"; 
            }
            else if ($nbPersonneAdos > $nbPersonneEnfants && $nbPersonneAdos > $nbPersonneAdultes && $nbPersonneAdos > $nbPersonneAines) {
                $trancheAgeAyantLePlusEpluche = "Adolescents"; 
            }
            else if ($nbPersonneAdultes > $nbPersonneEnfants && $nbPersonneAdultes > $nbPersonneAdos && $nbPersonneAdultes > $nbPersonneAines) {
                $trancheAgeAyantLePlusEpluche = "Adultes"; 
            }
            else if ($nbPersonneAines > $nbPersonneEnfants && $nbPersonneAines > $nbPersonneAdos && $nbPersonneAines > $nbPersonneAine) {
                $trancheAgeAyantLePlusEpluche = "Ainés"; 
            }
            else {
                $trancheAgeAyantLePlusEpluche = "Il y a égalité !"; 
            }
            

            // Semaines vides (au total -> 52 semaines)
            for ($annee = 2021; $annee < 2045; $annee++) {
                $anneeChoisi = strval($annee);
                $nbDateDansAnnee = 0;
                $semainesRestants = 52; 
                $semainesRestantsAnnee = ""; 
                // on récupère le nombre de semaines où quelqu'un est chargé de faire la corvée durant l'année en question       
                $nbSemainesOccupes = $this->arrayManager->findNumberWeeksWhereThereIsSomeoneToPeel($anneeChoisi);
                // on trouve le nombre de semaine vides par année grâce à un calcul 
                $semainesRestants -= $nbSemainesOccupes; 
                //echo " Semaine restants : ".$semainesRestants; 
                if ($semainesRestants < 0) {
                    $semainesRestantsAnnee = "Il y a une erreur !"; 
                }
                else {
                    $semainesRestantsAnnee = $semainesRestants." semaine(s)";
                }
                $tableauAnnee[$anneeChoisi] = $semainesRestantsAnnee; 
            }

            // Résultats finaux des stats
            //echo $genreAyantLePlusEpluche; 
            //echo $nomPersonneAyantEplucheLePlus;
            //echo $nomPersonneAyantEplucheLeMoins;
            //var_dump($tableauAnnee);
            //echo $trancheAgeAyantLePlusEpluche;


            $links = $this->returnLinks($page);
            echo $this->twig->render("statsResults.twig" , array(
                "corveeMaxStats" => $nomPersonneAyantEplucheLePlus,
                "corveesMoinsStats" => $nomPersonneAyantEplucheLeMoins,
                "genreStat" => $genreAyantLePlusEpluche,
                "trancheAgeStat" => $trancheAgeAyantLePlusEpluche,
                "semainesVidesStat" => $tableauAnnee,
                "links" => $links,
                "page" => $page,
                "nom_page" => $nom_page,
                "statsCorvees" => $CorveesPersonnes,
            ));

        }
        else {
            $page = "unauthorized";
            $links = $this->returnLinks($page);
            $nom_page = "Accès refusé";
            
            echo $this->twig->render("unauthorized.twig" , array(
            "links" => $links,
            "page" => $page,
            "nom_page" => $nom_page,
            ));
        }

        }


        public function ReturnNames(){
            $names = $this->arrayManager->getAllNames();
            $_POST['options[]'] = $names;
        }
    }

?>
 
