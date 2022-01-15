<?php 

    class userController { 
    
        public function __construct($twig, $load) {
            require_once('./modele/user.php'); 
            require_once('./modele/userManager.php');
            require_once('arrayController.php');
            require_once('./vendor/autoload.php');
            $this->userManager = new userManager();
            $this->arrayController = new arrayController($twig, $load);
            $this->load = $load;
            $this->twig = $twig;
        } 

        public function returnLinks($page){

            if (isset($_SESSION['user']) && $_SESSION['user'] === true) {
                $titreTableau = "tableau";
                $titreTableauControllerName = "array"; 
                $titreConnexion = "Se déconnecter";
                $menuConnexion = "logout";
            }
            else {
                $titreTableau = "login"; 
                $titreTableauControllerName = "user"; 
                $titreConnexion = "Se connecter";
                $menuConnexion = "login"; 
            }

            $links = [
                "create"=>["name"=>"Créer" , "ctrl"=>"user"], 
                "tableau"=>["name"=>"Tableau","ctrl"=>"array"], // à enlever quand les fonctions de login seront réalisées
                $menuConnexion=> ["name"=>$titreConnexion, "ctrl"=>"user"]
            ];
            unset($links[$page]);
            return ($links);
        }

        public function login() {
            $page = "login";
            $links = $this->returnLinks($page);
            $nom_page = "Se connecter";
            
            echo $this->twig->render("login.twig" , array(
            "links" => $links,
            "page" => $page,
            "nom_page" => $nom_page,
            ));
        }

        public function create(){
            $page = "create";
            $links =  $this->returnLinks($page);
            $nom_page = "Créer un compte";

            echo $this->twig->render("create.twig" , array(
            "links" => $links,
            "page" => $page,
            "nom_page" => $nom_page,
            ));
        }

        public function formCreate(){
            if(isset($_POST['user']))
            {
                $regUser = $_POST['user'];
                $regUser['password'] = password_hash($regUser['password'], PASSWORD_DEFAULT);
                $user = new user($regUser);
                $test = $this->userManager->verifyUser($user);
                if($test){
                    //exit("Cet utilisateur existe déjà !");
                }
                else {
                    $this->userManager->createUser($user);
                }
            }

            $this->login();
        }

        public function formLogin(){
            if(isset($_POST['user']))
            {
                $user = new user($_POST["user"]);
                $authentification = $this->userManager->verifyUser($user);
                // var_dump($authentification);
                if(isset($authentification)){
                    if ($authentification === true) {
                        // echo "Connexion réussie!";
                        // on crée la session user
                        $_SESSION['user'] = true;

                        // redirection vers la page tableau
                        $this->arrayController->tableau();
                    }
                    else {
                        echo "Vous n'avez pas entré les bons identifiants ! Veuillez contacter les administrateurs du site. ";
                        // redirection vers le formulaire de login
                        $this->login();
                    }
                }
                else {
                    // on renvoie un message d'erreur
                    echo "Il y a eu une erreur. Veuillez réessayer de vous connecter à votre compte.";
                }
            }
        }

        public function ReturnNames(){
            $names = $this->arrayManager->getAllNames();
            $_POST['options[]'] = $names;
        }

        public function logout(){
            unset($_SESSION["user"]);
            // echo "Déconnexion réussie";
            // var_dump($_SESSION['user']);
            // redirection vers le formulaire de login
            $this->login();
        }

    }

?>
 
