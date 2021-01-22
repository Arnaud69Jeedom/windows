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
      public static function cron() {

      }
     */


    /*
     * Fonction exécutée automatiquement toutes les heures par Jeedom
      public static function cronHourly() {

      }
     */

    /*
     * Fonction exécutée automatiquement tous les jours par Jeedom
      public static function cronDaily() {

      }
     */



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
            $refresh->setName(__('Rafraichir', __FILE__));
        }
        $refresh->setEqLogic_id($this->getId());
        $refresh->setLogicalId('refresh');
        $refresh->setType('action');
        $refresh->setSubType('other');
        $refresh->save();
    }

    public function preUpdate()
    {
    }

    public function postUpdate()
    {
        $cmd = $this->getCmd(null, 'refresh'); // On recherche la commande refresh de l’équipement
        if (is_object($cmd)) { //elle existe et on lance la commande
             $cmd->execCmd();
        }
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

    public function execute($_options = array())
    {
        switch ($this->getLogicalId()) {				
            case 'refresh': // LogicalId de la commande rafraîchir que l’on a créé dans la méthode Postsave de la classe vdm . 
         
               
                $eqlogic = $this->getEqLogic(); //récupère l'éqlogic de la commande $this
        
                log::add('windows', 'debug', __('temperature_indoor', __FILE__));
                                
                // température interieure
                $temperature_indoor = $eqlogic->getConfiguration('temperature_indoor');                               
                $temperature_indoor = str_replace('#', '', $temperature_indoor);
                $temperature_indoor = cmd::byId($temperature_indoor)->execCmd();
                
                // température exterieure
                $temperature_outdoor = $eqlogic->getConfiguration('temperature_outdoor');                               
                $temperature_outdoor = str_replace('#', '', $temperature_outdoor);
                $temperature_outdoor = cmd::byId($temperature_outdoor)->execCmd();

                // température hiver
                $temperature_winter  = $eqlogic->getConfiguration('temperature_winter');                               
                
                // presence
                $presence = $eqlogic->getConfiguration('presence');                               
                $presence = str_replace('#', '', $presence);
                $presence = cmd::byId($presence)->execCmd();

                // fenetre
                $windows = $eqlogic->getConfiguration('window');                
                $isOpened = false;
                
			    foreach ($windows as $window) {
                    $window = str_replace('#', '', $window);
                    $cmd = cmd::byId($window);
                    $window = cmd::byId($window)->execCmd();
                    $isOpened = $isOpened || $window;

                    if ($window) {
                        $lastDateValue = $cmd->getCollectDate();
                        log::add('windows', 'debug', 'lastDateValue:'.$lastDateValue);
                    }
                }
                
                // window_action                
                $window_action = $eqlogic->getCmd(null, 'window_action');
                $window_action->setValue(true);

                log::add('windows', 'debug', 
                    'ext:'.$temperature_outdoor
                    .', int:'.$temperature_indoor
                    .', seuil hiver:'.$temperature_winter
                    .', presence:'.$presence
                    .', isOpened:'.$isOpened);

                // Hiver, fenetre fermée
                if ($temperature_outdoor < $temperature_winter 
                    && !$isOpened
                    && $presence
                    && $temperature_outdoor > $temperature_indoor)
                {
                    $window_action->setValue(false);
                    log::add('windows', 'debug', 'il faut ouvrir');
                } 
                
                // Hiver, fenetre ouverte
                if ($temperature_outdoor < $temperature_winter
                    && $isOpened
                    && $presence
                    && $temperature_outdoor < $temperature_indoor)
                {
                    $window_action->setValue(false);
                    log::add('windows', 'debug', 'c\'est bon, faut fermer');
                }
                
        
        
        //       if ($temperature_indoor == "19") {
        //            $eqlogic->checkAndUpdateCmd('window_action', 1);
        //        } else {
        //            $eqlogic->checkAndUpdateCmd('window_action', 0);
        //        }

            break;
        }
        
    }

    /*     * **********************Getteur Setteur*************************** */
}