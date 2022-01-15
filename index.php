<?php
    include("modele/arrayManager.php");
    include("modele/connexion.php");
    require_once __DIR__.'/vendor/autoload.php';
    $load = new \Twig\Loader\FilesystemLoader(__DIR__."/vue");
    $twig = new \Twig\Environment($load, [
        'debug' => true,
    ]); //enlever le debug une fois finalisé !
    $twig->addExtension(new \Twig\Extension\DebugExtension()); 
    session_start();   
    
    if ( 
        isset($_GET['action']) && !empty($_GET['action'] && isset($_GET['controller'])) 
    ) { 
        $action = $_GET['action']; 
        $ctrl = $_GET['controller'] . "Controller";
    } 
    else { 
        $action = 'login';
        $ctrl = 'user' . "Controller";
    }

    require_once('controller/' . $ctrl . ".php"); 
    
    $controller = new $ctrl($twig, $load); 
    $controller->$action(); 

?>