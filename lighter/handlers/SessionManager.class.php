<?php
/*****************************************************************************
 * Copyright (c) 2010 Michel Begoc
 * Author: Michel Begoc
 * Date: 2010-06-15
 * Description: gère les sessions
 *
 *****************************************************************************/
require_once("dao/DaoSessions.class.php");
require_once("dao/DaoUsers.class.php");

require_once("vo/VoSession.class.php");
require_once("vo/VoUser.class.php");

require_once("forms/Form.class.php");

require_once("helpers/Get.class.php");


class SessionManager {
    //le VoSession
    private $sessionVo = NULL;
    private $sessionDao;
    private $loginForm;


    /**
     * Constructeur: initialise la session. Doit être exécuté le plus tôt possible, ET AVANT TOUT AFFICHAGE
     * car sinon l'envoi du token dans le cookie sera impossible, car l'entête HTTP sera parti.
     */
    public function __construct(){
        $config = Config::getInstance();

        $this->sessionDao = new DaoSessions();

        if((int)$config->xml->global->session["autoclean"] == 1){
            $this->sessionDao->clean();
        }

        //on récupère la session précédente
        if(isset($_COOKIE["sessionId"]) && $_COOKIE["sessionId"]!= ""){
            $this->sessionVo = $this->sessionDao->search($_COOKIE["sessionId"]);
        }

        //si il n'y a pas de session valide, on en crée une nouvelle
        if(!isset($this->sessionVo)){
            $this->sessionVo = new VoSession();
            $this->sessionVo->timeout = (int)$config->xml->global->session["timeout"];
        }

        /* on crée un token, quoi qu'il arrive. Cela rendra plus difficile le vol de session
         * car à chaque nouvel appel de la page, le token change. De plus, la prédiction du token
         * est presque impossible et la fixation de session est impossible
         */
        $this->sessionVo->token = md5(rand().time());

        //on crée le formulaire dans tous les cas: il peut servir autant à la lecture qu'à l'affichage
        $this->createLoginForm();

        //on gère l'identification des usagers
        $this->authenticate();

        //il faut propager le token
        setcookie("sessionId", $this->sessionVo->token, time() + $this->sessionVo->timeout);
    }


    /**
     * destructeur: sauvegarde la session tout à la fin seulement, comme ça, toutes les modifications faites
     * à la session seront sauvegardées en une seule fois
     */
    public function __destruct(){
        $this->sessionDao->save($this->sessionVo);
    }


    /**
     * gère l'authentification des usagers: log et delog
     */
    private function authenticate(){
        if($this->loginForm->isPosted()){
            $userDao = new DaoUsers();
            $values = $this->loginForm->getValues();
            $userVo = $userDao->identify($values["username"], $values["password"]);
            if(isset($userVo)){
                $this->sessionVo->setUserVo($userVo);
            }
        }elseif(Get::getString("deco") != NULL){
            $this->sessionVo->user = NULL;
            $this->sessionDao->update($this->sessionVo);
        }
    }


    /**
     * création du formulaire permettant de se logger
     */
    private function createLoginForm(){
        $this->loginForm = new Form("loginForm");
        $this->loginForm->addElement(new InputText("username", "Nom d'usager", 32));
        $this->loginForm->addElement(new Password("password", "Mot de passe", 32));
        $this->loginForm->setFieldset("Identification");
    }


    /**
     * retourne le formulaire d'authentification
     * @return Form
     */
    public function getAuthentificationForm(){
        return $this->loginForm;
    }


    /**
     * @return boolean est ce que l'usager est identifié
     */
    public function isLogged(){
        if(isset($this->sessionVo->user)){
            return true;
        }else{
            return false;
        }
    }
}
/*****************************************************************************
 * End of file SessionManager.class.php
 */