<?php
/****************************************************************************
 *   Copyright Michel Begoc
 *   Auteur: Michel Begoc
 *   Date: 29 mars 2010
 *   Version: 1.0.0
 *
 *   Description: Cette classe a pour but de faciliter le debuggage. Elle se
 *                charge de récolter les messages de debuggage pour les
 *                afficher de manière formattée et lisible dans différent
 *                format convenant au programmeur.
 *
 ****************************************************************************/
namespace helpers;


class Debug {
    /*ce paramètre doit être modifié directement dans la classe si on veut le modifier.
     C'est le seul moyen de s'assurer que la config d'un utilisateur ne soit pas écrasée*/
//    private static $configFilePath = "/var/www/debug.xml";
    private static $configFilePath = "pmf/config/debug.xml";


    //la liste des messages
    private static $messages = array();
    //la liste des instances du debugger
    private static $instances = array();

    //veut on être redirigé à la fin de l'exécution du debugger ?
    private static $redirect = false;
    //est on en mode debuggage ou non ?
    private static $debug = true;
    //veut on qu'un rapport soit créé ?
    private static $report = false;
    //veut on un rapport dans un frame
    private static $frameReport = false;
    //le chemin auquel le rapport de debuggage sera créé - apparement ce chemin doit être complet, sinon l'écriture plante lorsqu'elle a lieu à partir du destructeur
    //le chemin dans le repertoire www
    private static $reportPath = "/LIB/framework/debug.html";
    //la liste des section affichées par defaut
    private static $sections = NULL;
    //le chemin dans lequel on veut le rapport dans un environnement de script (i.e. pas dans une page web)
    private static $scriptPath = "/home/michel/Developpement/";
//    private static $scriptPath = "/var/www/www.lespac.com";
    //est ce qu'on a utilisé un fichier de config pour configurer l'environnement de debuggage
    private static $isFileConfigured = false;
    //gestion des exceptions
    private static $autoDisplayExceptions = false;
    private static $stopExceptions = false;
    private static $defaultSection = 'Exceptions "catchées" automatiquement';
    private static $errorLevel;
    private static $autoConvertErrors = false;

    //la section d'une instance du debugger
    private $curSection;
    //l'indice pour les titres de messages générés automatiquement
    private $i;
    //le temps pour le profiling
    private $time = NULL;


    //le html du rapport
    private static $header = "
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' lang='fr'>
<head>
<title>Debuggage</title>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8' />
<meta http-equiv='expires' content='0'>
<style type='text/css'>
        table{
            border:1px solid #000;
            border-right:0;
            border-collapse:collapse;
        }
        thead{
            background-color:#99a;
            border-bottom:1px solid #000;
        }
        td{
            border-right:1px solid #000;
        }
        .highlight{
            background-color:#bbc;
        }
</style>
</head>
<body>
<h1>Debuggage</h1>";

    private static $footer = "
</body>
</html>";

    private static $frame = "
<script type='text/javascript'>
    var divDebug = document.createElement('div');
    divDebug.setAttribute('class', 'debug');
    divDebug.style.position = 'fixed';
    divDebug.style.top = 0;
    divDebug.style.left = 0;
    divDebug.style.height = '10px';
    divDebug.style.width = '10px';
    divDebug.style.maxHeight = '95%';
    divDebug.style.maxWidth = '95%';
    divDebug.style.backgroundColor = '#ff0';
    divDebug.style.border = '1px solid #f00';
    divDebug.style.overflow = 'hidden';
    divDebug.style.zIndex = 10000;
    divDebug.innerHTML = \"<style type='text/css'>.debug table{border:1px solid #000;background-color:#fff;border-right:0;border-collapse:collapse;}.debug thead{background-color:#99a;border-bottom:1px solid #000;}.debug td{border-right:1px solid #000;}.debug .highlight{background-color:#bbc;}</style>{debugInfo}\";
    divDebug.onmouseover = function(){divDebug.style.width = 'auto';divDebug.style.height = 'auto';divDebug.style.padding = '10px';divDebug.style.overflow = 'auto';};
    divDebug.onmouseout = function(){divDebug.style.width = '10px';divDebug.style.height = '10px';divDebug.style.padding = '0';divDebug.style.overflow = 'hidden';};
    document.body.appendChild(divDebug);
</script>";


    /****************************************************************************
     *   CONSTRUCTEUR: il est privé, appeler la fonction getInstance pour créer une
     *   nouvelle instance.
     */
    private function __construct($section){
        //on ne veut pas que le debuggage soit actif en production, quoiqu'il arrive
        //il serait bon que ce code s'execute seulement quand on a accés à la classe config -> try ?
//            $objCnf = VoConfig::getInstance();
//            $objCnf->getConfigSite();
//            if((string)$objCnf->cfgXml->siteConfig->ONL_SRV == "TRUE"){
//                Debug::$debug = false;
//            }

            //initialisation du tableau de messages
            $this->curSection = $section;
            self::$messages[$section] = array();
            $this->i = array("message" => 1, "var" => 1, "exception" => 1, "trace" => 1, "profiling" => 1);

            //auto-conversion des erreurs en Exception
            if(count(self::$instances)==0 && self::$autoConvertErrors){
                $this->convertErrorToException();
            }
    	}

        /****************************************************************************
         *   on veut un fonctionnement sur le principe du singleton: chaque instance
         *   du debugger gère une section de debuggage dont elle a la responsabilité,
         *   pour toutes les pages du site.
         */
        public static function getInstance($section = "default"){
            if(count(self::$instances) == 0){
                if(file_exists(self::$configFilePath)){
                    self::loadConfig();
                    self::$isFileConfigured = true;
                }else{
                    //on ne peut pas affecter cette valeur par defaut à la déclaration de la variable, on le fait ici
                    self::$errorLevel = E_ALL & ~E_NOTICE;
                }
            }
            if(isset(self::$instances[$section])){
                return self::$instances[$section];
            }else{
                self::$instances[$section] = new Debug($section);
                return self::$instances[$section];
            }
        }


        /****************************************************************************
         *   DESTRUCTEUR: si on l'a demandé et qu'il n'y a plus d'instances du debugger,
         *   on génére un rapport et/ou on redirige l'utilisateur vers ce rapport
         */
        public function __destruct(){
            if(count(self::$instances) == 1 && self::$debug){
                if(self::$report){
                    $this->generateReport(self::$sections);
                    if(self::$redirect && isset($_SERVER['HTTP_HOST'])){
                        echo("<script type='text/javascript'>location.assign('http://".$_SERVER['HTTP_HOST'].self::$reportPath."');</script>");
                    }
                }
                if(self::$frameReport){
                    $this->displayFrameReport(self::$sections);
                }
            }
            //cette ligne m'a d'abord parue douteuse (unsetter un objet référencé dans un tableau dans son propre constructeur...)
            //mais ça n'a jamais posé de problème et même ça parrait nécessaire, puisque visiblement le comportement n'est pas celui
            //attendu si on ne fait pas ça
            unset(self::$instances[$this->curSection]);
        }


        /****************************************************************************
         *   AJOUT DE DONNÉES DE DEBUGGAGE
         */
        private function addMessage($type, $title, $content, array $location = NULL){
            if(is_null($location)){
                $trace = debug_backtrace();
                //$trace[0] est l'appel interne à cette méthode par une methode publique de l'objet
                $location = array("file" => $trace[1]["file"], "line" => $trace[1]["line"]);
            }
            self::$messages[$this->curSection][] = array("type" => $type, "title" => $title, "content" => $content, "file" => $location["file"], "line" => $location["line"]);
        }

        public function log($message, $title = NULL){
            if(self::$debug){
                if(is_null($title)){
                    $title = "Message ".$this->i["message"]++;
                }
                $this->addMessage("Message", $title, $message);
            }
        }

        public function dump($variable, $title = NULL){
            if(self::$debug){
                if(is_null($title)){
                    $title = "Variable ".$this->i["var"]++;
                }
                $this->addMessage("Variable", $title, "<pre>".print_r($variable, true)."</pre>");
            }
        }

        public function handle($exception){
            if(self::$debug){
                $this->addMessage("Exception",
                                  "Exception: ".$exception->getCode(),
                                  "<pre>".$exception->getMessage()."\nTrace: ".$exception->getTraceAsString()."</pre>",
                				  array("file" => $exception->getFile(), "line" => $exception->getLine())
                );
            }
        }

        public function trace($title = NULL){
            if(is_null($title)){
                $title = "Trace ".$this->i["trace"]++;
            }
            $backtrace = debug_backtrace();
            foreach($backtrace as $trace){
                if($trace["class"] !== "Debug"){
                    $this->addMessage("Trace",
				                      $title,
				                      $trace["class"].$trace["type"].$trace["function"],
				                      array("file" => $trace["file"], "line" => $trace["line"])
                    );
                }
            }
        }

        public function startProfiling($title = NULL){
            if(is_null($title)){
                $title = $title = "Start profiling";
            }
            $this->i["profiling"] = 1;
            $this->addMessage("Profiling", $title, "0 s");
            $this->time = microtime(true);
        }

        public function profilingCP($title = NULL){
            $endTime = microtime(true);
            if(is_null($title)){
                $title = $title = "Profiling CheckPoint".$this->i["profiling"]++;
            }

            $this->addMessage("Profiling", $title, ($endTime - $this->time)." s");
            $this->time = microtime(true);
        }

        public function endProfiling($title = NULL){
            $endTime = microtime(true);
            if(is_null($title)){
                $title = $title = "End profiling";
            }

            $this->addMessage("Profiling", $title, ($endTime - $this->time)." s");
        }


        /****************************************************************************
         *   FONCTIONS D'AFFICHAGE
         */
        public function getFormattedMessages($sections = NULL){
            if(self::$debug){
                if(is_null($sections)){
                    $sections = array_keys(self::$messages);
                }
                $html = "";
                foreach($sections as $section){
                    if(isset(self::$messages[$section]) && count(self::$messages[$section]) != 0){
                        $html .= "<h2>$section</h2>";
                        $html .= "<table>";
                        $html .= "<thead><tr><td>Type</td><td>Titre</td><td>Contenu</td><td>Fichier</td><td>Ligne</td></tr></thead><tbody>";
                        $highlighted = false;
                        foreach(self::$messages[$section] as $message){
                            if($highlighted){
                                $class = " class='highlight'";
                            }else{
                                $class = "";
                            }
                            $html .= "<tr$class><td>".$message["type"]."</td>";
                            $html .= "<td>".$message["title"]."</td>";
                            $html .= "<td>".$message["content"]."</td>";
                            $html .= "<td>".$message["file"]."</td>";
                            $html .= "<td>".$message["line"]."</td></tr>";
                            $highlighted = !$highlighted;
                        }
                        $html .= "</tbody></table>";
                    }
                }
                return $html;
            }else{
                return NULL;
            }
        }

        public function displayFrameReport($sections = NULL){
            //les retours à la ligne et les " provoquent la coupure des chaines javascript et des plantages
            $formattedMessages = preg_replace('/"/', '\"', $this->getFormattedMessages($sections));
            $formattedMessages = preg_replace("/\n/", "\\n", $formattedMessages);
            $frame = preg_replace("/{debugInfo}/", $formattedMessages, self::$frame);
            echo($frame);
        }

        public function getHtmlPage($sections = NULL){
            if(self::$debug){
                $html = self::$header;
                $html .= "<p>Heure d'execution: ".date("d-m-Y H:i:s", time())."</p>";
                if(self::$redirect && isset($_SERVER['HTTP_HOST']) && isset($_SERVER['REQUEST_URI'])){
                    $html .= "<p>Origine: http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']." - ";
                    $html .= "<a href='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."'>Retester</a></p>";

                }
                $html .= $this->getFormattedMessages($sections);
                $html .= self::$footer;
                return $html;
            }else{
                return NULL;
            }
        }

        public function generateReport($sections = NULL){
            if(self::$debug){
                if(isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'] !== ''){
                    $path = $_SERVER['DOCUMENT_ROOT'].self::$reportPath;
                }else{
                    $path = self::$scriptPath.self::$reportPath;
                }
                $file = fopen($path, "w");
                fwrite($file, $this->getHtmlPage($sections));
                fclose($file);
            }
        }


        /****************************************************************************
         *   SETTERS / GETTERS
         */
        public function reportPath($path = NULL){
            if(!is_null($path) && !self::$isFileConfigured){
                self::$reportPath = $path;
            }

            return self::$reportPath;
        }

        public function isRedirected($redirect = NULL){
            if(!is_null($redirect) && !self::$isFileConfigured){
                self::$redirect = (boolean)$redirect;
            }

            return self::$redirect;
        }

        public function isDebugging($debug = NULL){
            if(!is_null($debug) && !self::$isFileConfigured){
                self::$debug = (boolean)$debug;
            }

            return self::$debug;
        }

        public function isReported($report = NULL){
            if(!is_null($report) && !self::$isFileConfigured){
                self::$report = (boolean)$report;
            }

            return self::$report;
        }

        public function autoConvertError($autoConvertErrors = NULL){
            if(!is_null($autoConvertErrors) && !self::$isFileConfigured){
                self::$autoConvertErrors = (boolean)$autoConvertErrors;
            }

            return self::$autoConvertErrors;
        }

        public function autoDisplayExceptions($autoDisplayExceptions = NULL){
        	if(!is_null($autoDisplayExceptions) && !self::$isFileConfigured){
                self::$autoDisplayExceptions = (boolean)$autoDisplayExceptions;
            }

            return self::$autoDisplayExceptions;
        }

        public function stopExceptions($stopExceptions = NULL){
            if(!is_null($stopExceptions) && !self::$isFileConfigured){
                self::$stopExceptions = (boolean)$stopExceptions;
            }

            return self::$stopExceptions;
        }

        public function exceptionDefaultSection($defaultSection = NULL){
            if(!is_null($defaultSection) && !self::$isFileConfigured){
                self::$defaultSection = $defaultSection;
            }

            return self::$defaultSection;
        }

        public function errorLevel($errorLevel = NULL){
            if(!is_null($errorLevel) && !self::$isFileConfigured){
                self::$errorLevel = (int)$errorLevel;
            }

            return self::$errorLevel;
        }


        /****************************************************************************
         *   DIVERS
         */
        public static function error_handler($errno, $errstr, $errfile, $errline ) {
            if(($errno & self::$errorLevel) != 0){
                $error = new ErrorException($errstr, 0, $errno, $errfile, $errline);
                if(self::$autoDisplayExceptions){
                    $debug = self::getInstance(self::$defaultSection);
                    $debug->handle($error);
                }

                if(!self::$stopExceptions){
                    throw $error;
                }
            }
        }

        public function convertErrorToException($autoDisplay = false, $stopExceptions = false, $defaultSection = NULL, $errorLevel = NULL){
            if(!self::$isFileConfigured){
                self::$autoDisplayExceptions = $autoDisplay;
                self::$stopExceptions = $stopExceptions;
                if(!is_null($defaultSection)){
                    self::$defaultSection = $defaultSection;
                }
                if(!is_null($errorLevel)){
                    self::$errorLevel = $errorLevel;
                }
            }

            if(self::$debug){
                set_error_handler(array("Debug", "error_handler"));
            }
        }

        public function restoreError(){
            restore_error_handler();
        }

        public function writeConfig(){
            //on ne veut pas pouvoir écraser la config définie par l'utilisateur: il faut supprimer le fichier manuellement pour pouvoir le changer
            if(!self::$isFileConfigured){

                $xml = "<?xml version='1.0' encoding='utf-8'?>
<Debug>
<options redirect='".(int)self::$redirect."' debug='".(int)self::$debug."' report='".(int)self::$report."' frameReport='".(int)self::$frameReport."' reportPath='".self::$reportPath."' scriptPath='".self::$scriptPath."'>
<exceptions autoDisplay='".(int)self::$autoDisplayExceptions."' autoConvert='".(int)self::$autoConvertErrors."' stoped='".(int)self::$stopExceptions."' defaultSection='".self::$defaultSection."' errorLevel='".self::$errorLevel."' />
<sections>";
                if(!is_null(self::$sections)){
                    foreach(self::$sections as $section){
                        $xml .=
" <section>$section</section>";
                    }
                }
                $xml .=
" </sections>
</options>
<html>
<header><![CDATA[".self::$header."]]></header>
<footer><![CDATA[".self::$footer."]]></footer>
<frame><![CDATA[".self::$frame."]]></frame>
</html>
</Debug>";
                $file = fopen(self::$configFilePath, "w");
                fwrite($file, $xml);
                fclose($file);
            }else{
                throw new Exception("Le fichier de configuration est déjà défini.");

            }
        }

        private static function loadConfig(){
            $xml = new SimpleXmlElement(self::$configFilePath, 0, true);
            self::$redirect = (boolean)(int)$xml->options["redirect"];
            self::$debug = (boolean)(int)$xml->options["debug"];
            self::$report = (boolean)(int)$xml->options["report"];
            self::$frameReport = (boolean)(int)$xml->options["frameReport"];
            self::$reportPath = (string)$xml->options["reportPath"];
            self::$scriptPath = (string)$xml->options["scriptPath"];
            self::$header = (string)$xml->html->header;
            self::$footer = (string)$xml->html->footer;
            self::$frame = (string)$xml->html->frame;
            self::$autoConvertErrors= (boolean)(int)$xml->options->exceptions["autoConvert"];
            self::$autoDisplayExceptions = (boolean)(int)$xml->options->exceptions["autoDisplay"];
            self::$stopExceptions = (boolean)(int)$xml->options->exceptions["stoped"];
            self::$defaultSection = (string)$xml->options->exceptions["defaultSection"];
            self::$errorLevel = (int)$xml->options->exceptions["errorLevel"];

            foreach($xml->options->sections->children() as $section){
                if(is_null(self::$sections)){
                    self::$sections = array();
                }
                self::$sections[] = (string)$section;
            }
        }
}
/****************************************************************************
 *   End of file Debug.class.php
 */