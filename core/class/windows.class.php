<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';

class windows extends eqLogic
{
    /*     * *************************Attributs****************************** */



    /*     * ***********************Methode static*************************** */

    /*
     * Fonction exécutée automatiquement toutes les minutes par Jeedom
     * */
      public static function cron() {
        log::add('windows', 'debug', '*** cron ***');

        foreach (eqLogic::byType(__CLASS__, true) as $window) {
            if ($window->getIsEnable() == 1) {
				$cmd = $window->getCmd(null, 'refresh');
				if (!is_object($cmd)) {
					continue; 
				}
				$cmd->execCmd();
			}
          }
      }

    /*     * *********************Méthodes d'instance************************* */

    public function preInsert()
    {
    }

    public function postInsert()
    {
    }

    public function preSave()
    {
    }

    public function postSave()
    {        
        // window_action
        
        log::add('windows', 'debug', 'postSave');

        $info = $this->getCmd(null, 'window_action');
        if (!is_object($info)) {
            $info = new windowsCmd();
            $info->setLogicalId('window_action');
            $info->setName(__('Action', __FILE__));
            $info->setIsVisible(1);
            $info->setIsHistorized(0);
            //$info->setTemplate('dashboard', 'line');
        }
        $info->setEqLogic_id($this->getId());
        $info->setType('info');
        $info->setSubType('boolean');
        $info->setSubType('binary');
        $info->setDisplay('generic_type', 'GENERIC_INFO');

        $value = false;        
        $info->setValue($value);
        $info->save();

        // refresh
        $refresh = $this->getCmd(null, 'refresh');
        if (!is_object($refresh)) {
            $refresh = new windowsCmd();
            $refresh->setLogicalId('refresh');
            $refresh->setIsVisible(1);
            $refresh->setName(__('Rafraichir', __FILE__));
            $refresh->setOrder(0);
        }
        $refresh->setEqLogic_id($this->getId());
        $refresh->setType('action');
        $refresh->setSubType('other');
        $refresh->save();
    }

    public function preUpdate()
    {
    }

    public function postUpdate()
    {
        // $cmd = $this->getCmd(null, 'refresh'); // On recherche la commande refresh de l’équipement
        // if (is_object($cmd)) { //elle existe et on lance la commande
        //      $cmd->execCmd();
        // }
    }

    public function preRemove()
    {
    }

    public function postRemove()
    {
    }

    /*
     * Non obligatoire mais permet de modifier l'affichage du widget si vous en avez besoin
      public function toHtml($_version = 'dashboard') {

      }
     */

    /*
     * Non obligatoire mais ca permet de déclencher une action après modification de variable de configuration
    public static function postConfig_<Variable>() {
    }
     */

    /*
     * Non obligatoire mais ca permet de déclencher une action avant modification de variable de configuration
    public static function preConfig_<Variable>() {
    }
     */

    /*     * **********************Getteur Setteur*************************** */
}

class windowsCmd extends cmd
{
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */

     /**
      * Récupérer la configuration
      */
    private function getMyConfiguration() {    
        $configuration = new StdClass();

        $eqlogic = $this->getEqLogic(); //récupère l'éqlogic de la commande $this

        // Lecture et Analyse de la configuration

        // température exterieure
        log::add('windows', 'debug', ' Analyse temperature_outdoor', __FILE__);
        $temperature_outdoor = $eqlogic->getConfiguration('temperature_outdoor');                               
        $temperature_outdoor = str_replace('#', '', $temperature_outdoor);
        if ($temperature_outdoor != '') {                        
            $cmd = cmd::byId($temperature_outdoor);
            if ($cmd == null) {
                log::add('windows', 'error', ' Mauvaise temperature_outdoor :'. $temperature_outdoor, __FILE__);
                return;
            }
            $configuration->temperature_outdoor = $cmd->execCmd();    
            log::add('windows', 'debug', ' temperature_outdoor: '. $configuration->temperature_outdoor, __FILE__);
        } else {
            log::add('windows', 'error', ' Pas de temperature_outdoor', __FILE__);
            return;  
        }                
        unset($cmd);

        // température interieure
        log::add('windows', 'debug', ' Analyse temperature_indoor', __FILE__);
        $temperature_indoor = $eqlogic->getConfiguration('temperature_indoor');                       
        $temperature_indoor = str_replace('#', '', $temperature_indoor);
        if ($temperature_indoor != '') {
            $cmd = cmd::byId($temperature_indoor);
            if ($cmd == null) {
                log::add('windows', 'error', ' Mauvaise temperature_indoor :'.$temperature_indoor, __FILE__);
                return;
            }
            $configuration->temperature_indoor = $cmd->execCmd();
            log::add('windows', 'debug', ' temperature_indoor: '. $configuration->temperature_indoor, __FILE__);
        } else {
            log::add('windows', 'error', ' Pas de temperature_indoor', __FILE__);
            return;
        }
        unset($cmd);

        // presence
        log::add('windows', 'debug', ' Analyse presence', __FILE__);
        $presence = $eqlogic->getConfiguration('presence');                            
        $presence = str_replace('#', '', $presence);
        if ($presence != '') {
            $cmd = cmd::byId($presence);
            if ($cmd == null) {
                log::add('windows', 'error', ' Mauvaise presence :'.$presence, __FILE__);
                return;
            }
            $configuration->presence = $cmd->execCmd();
            log::add('windows', 'debug', ' presence: '. $presence, __FILE__);
        } else {
            log::add('windows', 'debug', ' Pas de presence : valeur par défaut prise = 1', __FILE__);
            // Valeur par défaut
            $configuration->presence = 1;                    
        }
        unset($cmd);

        // température hiver
        $temperature_winter  = $eqlogic->getConfiguration('temperature_winter');                               
        if (!is_numeric($temperature_winter)) {
            log::add('windows', 'error', ' Mauvaise temperature_winter: '.$temperature_winter, __FILE__);
            return;
        } else {
            $configuration->temperature_winter = $temperature_winter;
        }
        
        // température été
        $temperature_summer  = $eqlogic->getConfiguration('temperature_summer');                               
        if (!is_numeric($temperature_summer)) {
            log::add('windows', 'error', ' Mauvaise temperature_summer:'.$temperature_summer, __FILE__);
            return;
        } else {
            $configuration->temperature_summer = $temperature_summer;
        }

        // durée hiver
        $duration_winter = $eqlogic->getConfiguration('duration_winter');
        if (!is_numeric($duration_winter)) {
            log::add('windows', 'error', ' Mauvaise duration_winter:'. $duration_winter, __FILE__);
            return;
        } else {
            $duration_winter = $duration_winter;
        }

        // durée été
        $duration_summer = $eqlogic->getConfiguration('duration_summer'); 
        if (!is_numeric($duration_summer)) {
            log::add('windows', 'error', ' Mauvaise duration_summer:'.$duration_summer, __FILE__);
            return;                
        } else {
            $duration_summer = $duration_summer;
        }

        // Seuil hiver
        $threshold_winter = $eqlogic->getConfiguration('threshold_winter'); 
        if (!is_numeric($threshold_winter)) {
            log::add('windows', 'error', ' Mauvaise threshold_winter:'.$threshold_winter, __FILE__);
            return;
        } else {
            $configuration->threshold_winter = $threshold_winter;
        }

        // Seuil été
        $threshold_summer = $eqlogic->getConfiguration('threshold_summer'); 
        if (!is_numeric($threshold_summer)) {
            log::add('windows', 'error', ' Mauvaise threshold_summer:'.$threshold_summer, __FILE__);
            return;
        } else {
            $configuration->threshold_summer = $threshold_summer;
        }

        // Consigne thermostat
        log::add('windows', 'debug', ' Analyse consigne', __FILE__);
        $consigne = $eqlogic->getConfiguration('consigne');                       
        $consigne = str_replace('#', '', $consigne);
        if ($consigne != '') {
            $cmd = cmd::byId($consigne);
            if ($cmd == null) {
                log::add('windows', 'error', ' Mauvaise consigne :'.$consigne, __FILE__);
                return;
            }
            $configuration->consigne = $cmd->execCmd();
            log::add('windows', 'debug', ' consigne: '. $consigne, __FILE__);
        } else {
            log::add('windows', 'debug', ' Pas de consigne', __FILE__);                    
        }
        unset($cmd);

        // Notification
        $configuration->notifyko = $eqlogic->getConfiguration('notifyifko');


        // Recherche de la durée à prendre en compte
        $dateTime = new DateTime('NOW');
        $dayOfTheYear = $dateTime->format('z');
        if($dayOfTheYear < 80 || $dayOfTheYear > 280){
            $configuration->duration = $duration_winter;
            $configuration->isWinter = true;
        } else {
            $configuration->duration = $duration_summer;
            $configuration->isWinter = false;
        }
        unset($dateTime);
        unset($duration_winter);
        unset($duration_summer); 

        return $configuration;
    }

    /**
     * Récupérer la configuration sur les fenêtres
     * Récupère l'état des fenêtres (et la durée si ouverte)
     */
    private function getWindowsInformation($configuration) {
        if ($configuration == null) return;

        $configuration->isOpened = false;
        $configuration->durationOpened = 0;

        $eqlogic = $this->getEqLogic(); //récupère l'éqlogic de la commande $this

        log::add('windows', 'debug', ' Liste des ouvertures :');
        $windows = $eqlogic->getConfiguration('window');
        foreach ($windows as $window) {
            $window = str_replace('#', '', $window['cmd']);
            if ($window != '') {
                $cmd = cmd::byId($window);                    
            } else {
                log::add('windows', 'error', ' Pas de window', __FILE__);
                return; 
            }
            
            if ($cmd == null) {
                log::add('windows', 'error', ' Mauvaise window :'.$window, __FILE__);
                return;
            }
            $windowState = $cmd->execCmd();
            log::add('windows', 'debug', '    '.$cmd->getEqLogic()->getHumanName().'['.$cmd->getName().'] : '.$windowState);

            // 1 = fermé
            $isWindowOpened = ($windowState == 0);
           
            if ($isWindowOpened) {
                // si ouvert
                
                // Vérification de la durée
                $lastDateValue = $cmd->getValueDate();
                $time = strtotime($lastDateValue);
                $interval = (time() - $time) / 60; // en minutes
                log::add('windows', 'debug', '       lastDateValue:'.$lastDateValue.' windowState:'.$windowState.', timediff:'.$interval.', duration:'.$configuration->duration);
                
                $configuration->isOpened = true;
                $configuration->durationOpened = max($configuration->durationOpened, $interval);
            }
        }
    }

    /**
     * Vérifie l'action à réaliser et le message à afficher associé
     */
    private function checkAction($configuration) {
        if ($configuration == null) return;

        $eqlogic = $this->getEqLogic(); //récupère l'éqlogic de la commande $this

        $result = new stdClass();
        $result->actionToExecute = false;

        // Vérifier s'il faut fermer
        log::add('windows', 'debug', ' Analyse métier');        
        if ($configuration->isOpened) {
            // si ouvert
                                
            // Vérification sur durée
            log::add('windows', 'debug', '    calcul sur durée');
            if ($configuration->durationOpened >=  $configuration->duration) {                
                log::add('windows', 'debug', '    il faudra fermer sur durée');
                $result->actionToExecute = true;
            }

            // Vérification du seuil
            if (isset($configuration->consigne) && $configuration->consigne != '') {
                log::add('windows', 'debug', '    calcul sur consigne: '.$configuration->consigne);

                // Hiver
                if ($configuration->isWinter) {
                    $temp_mini = $configuration->consigne - $configuration->threshold_winter;
                    log::add('windows', 'debug', '    température mini :'.$temp_mini.', température:'.$configuration->temperature_indoor);

                    if ($configuration->temperature_indoor <= $temp_mini) {
                        log::add('windows', 'debug', '    il faudra fermer sur température');
                        $result->actionToExecute = true;
                    }
                }
            }
        }

        // Log de résumé        
        log::add('windows', 'info', 
            '     '
            .'ext:'.$configuration->temperature_outdoor
            .', int:'.$configuration->temperature_indoor
            .', seuil hiver:'.$configuration->temperature_winter
            .', presence:'.$configuration->presence
            .', isOpened:'. ($configuration->isOpened ? 'true' : 'false')
            .', actionToExecute:'. ($result->actionToExecute ? 'true' : 'false')
        );
        unset($value);

       

        // Hiver, fenetre fermée
        // mais il fait plus chaud dehors tout de même
        // il faut donc ouvrir
        if ($configuration->temperature_outdoor < $configuration->temperature_winter 
            && !$configuration->isOpened
            && $configuration->presence
            && $configuration->temperature_outdoor > $configuration->temperature_indoor)
        {
            $result->messageWindows = 'il faut ouvrir';
            log::add('windows', 'info', $result->messageWindows);
            $result->actionToExecute = true;
        } 

        // Hiver
        if ($result->actionToExecute
            && $configuration->presence)
        {
            $result->messageWindows = 'il faut fermer';
            log::add('windows', 'info', $result->messageWindows);
            $result->actionToExecute = true;
        }

        // // Hiver, fenetre ouverte
        // // La température va continuer à descendre
        // // il faut fermer
        // if ($temperature_outdoor < $temperature_winter
        //     && $isOpened
        //     && $presence
        //     && $temperature_outdoor < $temperature_indoor)
        // {
        //     $result->messageWindows = 'il faut fermer (sur temps)';
        //     log::add('windows', 'info', $result->messageWindows);
        //     $result->actionToExecute = true;

        //     $window_action->event(0);
        // }

        // // Avec consigne
        // if (isset($consigne) && $consigne != '' && $isOpened) {
        //     // Hiver
        //     if ($isWinter) {
        //         $result->messageWindows = 'il faut fermer (sur consigne)';
        //         log::add('windows', 'info', $result->messageWindows);
        //         $result->actionToExecute = true;

        //         $window_action->event(0);
        //     }
        // }

        return $result;
    }

    /**
     * Réaliser les actions :
     *  - Icone sur le widget
     *  - Notification
     *  - Actions diverses
     */
    private function action($configuration, $result) {
        $eqlogic = $this->getEqLogic(); //récupère l'éqlogic de la commande $this

        // Icone sur le widget
        $window_action = $eqlogic->getCmd(null, 'window_action');
        if ($result->actionToExecute) {
            $window_action->event(0);
        } else {
            $window_action->event(1);
        }

        // Notification
        log::add('windows', 'debug', '    notification:'.$configuration->notifyko);
        if ($configuration->notifyko == 1 && $result->actionToExecute) {
            message::add('windows', $result->messageWindows, '', '' . $this->getId());
        }

        // Actions
        if ($result->actionToExecute) {
            $actions = $eqlogic->getConfiguration('action');                    
            log::add('windows', 'debug', ' Lancement des actions :');
            foreach ($actions as $action) {
                log::add('windows', 'debug', $action['cmd']);

                $options = array();
                if (isset($action['options'])) {
                    $options = $action['options'];

                    foreach ($options as $key => $option) {
                        $option = str_replace('#name#', $eqlogic->getName(), $option);
                        $option = str_replace('#message#', $result->messageWindows, $option);
                        $options[$key] = $option;
                    }

                    if ($option['title'] == ''
                    || $option['message'] == '') {
                        log::add('windows', 'error', 'Action sans titre ou message');
                        break;
                    }
                }
                scenarioExpression::createAndExec('action', $action['cmd'], $options);
            }
        } else {
            log::add('windows', 'info', 'rien à faire');
        }
    }

    public function execute($_options = array())
    {
        log::add('windows', 'info', ' **** execute ****', __FILE__);
        
        switch ($this->getLogicalId()) {				
            case 'refresh': // LogicalId de la commande rafraîchir que l’on a créé dans la méthode Postsave de la classe vdm .                                 
                $eqlogic = $this->getEqLogic(); //récupère l'éqlogic de la commande $this
                log::add('windows', 'info', ' Objet : '.$eqlogic->getName(), __FILE__);

                // Lecture et Analyse de la configuration
                $configuration = $this->getMyConfiguration();
                $this->getWindowsInformation($configuration);
                log::add('windows', 'debug', ' configuration :' .json_encode((array)$configuration));

                $result = $this->checkAction($configuration);
                $this->action($configuration, $result);
                
            break;
        }
        
    }

    /*     * **********************Getteur Setteur*************************** */
}