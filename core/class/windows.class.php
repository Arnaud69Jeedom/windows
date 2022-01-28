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

    /*
   * Permet de définir les possibilités de personnalisation du widget (en cas d'utilisation de la fonction 'toHtml' par exemple)
   * Tableau multidimensionnel - exemple: array('custom' => true, 'custom::layout' => false)
	public static $_widgetPossibility = array();
   */

    /*     * ***********************Methode static*************************** */

    /*
     * Fonction exécutée automatiquement toutes les minutes par Jeedom
     * Vérifie action & temps ouverture
     * */
    public static function cron()
    {
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

    // /*
    //  * Fonction exécutée automatiquement toutes les 5 minutes par Jeedom
    //  * Vérifie si action à réaliser
    //  * */
    // public static function cron()
    // {
    //     log::add('windows', 'debug', '*** cron5 ***');

    //     foreach (eqLogic::byType(__CLASS__, true) as $window) {
    //         if ($window->getIsEnable() == 1) {
    //             $cmd = $window->getCmd(null, 'refresh');

    //             $cmd->launchAction();
    //         }
    //     }
    // }

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
            $info->setName(__('Etat', __FILE__));
            $info->setIsVisible(1);
            $info->setIsHistorized(0);
            //$info->setTemplate('dashboard', 'line');
        }
        $info->setEqLogic_id($this->getId());
        $info->setType('info');
        $info->setSubType('boolean');
        $info->setSubType('binary');
        $info->setGeneric_type('GENERIC_INFO');

        $value = false;
        $info->setValue($value);
        $info->save();
        unset($info);

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
        unset($refresh);

        // counter
        $counter = $this->getCmd(null, 'counter');
        if (!is_object($counter)) {
            $counter = new windowsCmd();
            $counter->setLogicalId('counter');
            $counter->setIsVisible(1);
            $counter->setName(__('Compteur', __FILE__));
        }
        $counter->setEqLogic_id($this->getId());
        $counter->setType('info');
        $counter->setSubType('numeric');
        $counter->setGeneric_type('GENERIC_INFO');
        $counter->setUnite('min');
        $counter->save();
        unset($counter);

        // message
        $message = $this->getCmd(null, 'message');
        if (!is_object($message)) {
            $message = new windowsCmd();
            $message->setLogicalId('message');
            $message->setIsVisible(1);
            $message->setName(__('Message', __FILE__));
        }
        $message->setEqLogic_id($this->getId());
        $message->setType('info');
        $message->setSubType('string');
        $message->setGeneric_type('GENERIC_INFO');
        $message->setUnite('min');
        $message->save();
        unset($message);
    }

    public function preUpdate()
    {
    }

    public function postUpdate()
    {
    }

    public function preRemove()
    {
    }

    public function postRemove()
    {
    }

    /*
     * Non obligatoire : permet de modifier l'affichage du widget (également utilisable par les commandes)
      public function toHtml($_version = 'dashboard') {

      }
     */

    /*
     * Non obligatoire : permet de déclencher une action après modification de variable de configuration
    public static function postConfig_<Variable>() {
    }
     */

    /*
     * Non obligatoire : permet de déclencher une action avant modification de variable de configuration
    public static function preConfig_<Variable>() {
    }
     */

    /*     * **********************Getteur Setteur*************************** */
}

class windowsCmd extends cmd
{
    /*     * *************************Attributs****************************** */

    /*
      public static $_widgetPossibility = array();
    */

    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    /*
     * Non obligatoire permet de demander de ne pas supprimer les commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
      public function dontRemoveCmd() {
      return true;
      }
     */

    /**
     * Récupérer la configuration de l'équipement & validation 
     * Récupération de la configuration du plugin
     */
    private function getMyConfiguration(): ?StdClass
    {
        $configuration = new StdClass();

        // Paramètre global
        $isOK = windowsCmd::getTemperatureOutdoor($configuration);
        $isOK &= windowsCmd::getTemperatureMaxi($configuration);
        $isOK &= windowsCmd::getTemperatureWinter($configuration);
        $isOK &= windowsCmd::getTemperatureSummer($configuration);
        $isOK &= windowsCmd::getPresence($configuration);

        // Paramètre équipement
        $eqlogic = $this->getEqLogic(); //récupère l'éqlogic de la commande $this
        // Lecture et Analyse de la configuration        
        $isOK &= $this->getTemperatureIndoor($eqlogic, $configuration);
        $isOK &= $this->getDurationWinter($eqlogic, $configuration);
        $isOK &= $this->getDurationSummer($eqlogic, $configuration);
        $isOK &= $this->getThresholdWinter($eqlogic, $configuration);
        $isOK &= $this->getThresholdSummer($eqlogic, $configuration);
        $isOK &= $this->getConsigne($eqlogic, $configuration);
        $isOK &= $this->getNotify($eqlogic, $configuration);

        if ($isOK == false) {
            return null;
        }

        // Recherche de la saison
        windowsCmd::setSeason($configuration);
        windowsCmd::setDuration($configuration);

        return $configuration;
    }

    /*** GetConfiguration ***/
    /**
     * Récupérer la valeur de la température intérieure
     */
    private function getTemperatureIndoor($eqlogic, $configuration): bool
    {
        if ($eqlogic == null) throw new ErrorException('eqlogic null');
        if ($configuration == null) throw new ErrorException('configuration null');

        $isOK = false;

        log::add('windows', 'debug', ' Analyse temperature_indoor', __FILE__);
        $temperature_indoor = $eqlogic->getConfiguration('temperature_indoor');
        $temperature_indoor = str_replace('#', '', $temperature_indoor);
        if ($temperature_indoor != '') {
            $cmd = cmd::byId($temperature_indoor);
            if ($cmd == null) {
                log::add('windows', 'error', ' Mauvaise temperature_indoor :' . $temperature_indoor, __FILE__);
                return false;
            }
            $temperature_indoor = $cmd->execCmd();
            if (is_numeric($temperature_indoor)) {
                $configuration->temperature_indoor = $temperature_indoor;
                $configuration->temperature_unit = $cmd->getunite();
                $isOK = true;
            } else {
                log::add('windows', 'error', ' Mauvaise temperature_indoor :' . $temperature_indoor, __FILE__);
                return false;
            }
        } else {
            log::add('windows', 'error', '  > Pas de temperature_indoor', __FILE__);
            return false;
        }
        unset($cmd);

        return $isOK;
    }

    /**
     * Récupérer la valeur Durée pour hiver
     */
    private function getDurationWinter($eqlogic, StdClass $configuration): bool
    {
        if ($eqlogic == null) throw new ErrorException('eqlogic null');
        if ($configuration == null) throw new ErrorException('configuration null');

        $isOK = false;

        log::add('windows', 'debug', ' Analyse durée hiver', __FILE__);
        $duration_winter = $eqlogic->getConfiguration('duration_winter');
        if (!is_numeric($duration_winter)) {
            log::add('windows', 'error', '  > Mauvaise duration_winter:' . $duration_winter, __FILE__);
        } else {
            $configuration->duration_winter = $duration_winter;
            $isOK = true;
        }

        return $isOK;
    }

    /**
     * Récupérer la valeur Durée pour été
     */
    private function getDurationSummer($eqlogic, StdClass $configuration): bool
    {
        if ($eqlogic == null) throw new ErrorException('eqlogic null');
        if ($configuration == null) throw new ErrorException('configuration null');

        $isOK = false;

        log::add('windows', 'debug', ' Analyse durée été', __FILE__);
        $duration_summer = $eqlogic->getConfiguration('duration_summer');
        if (!is_numeric($duration_summer)) {
            log::add('windows', 'error', '  > Mauvaise duration_summer:' . $duration_summer, __FILE__);
        } else {
            $configuration->duration_summer = $duration_summer;
            $isOK = true;
        }

        return $isOK;
    }

    /**
     * Récupérer le seuil hiver
     */
    private function getThresholdWinter($eqlogic, StdClass $configuration): bool
    {
        if ($eqlogic == null) throw new ErrorException('eqlogic null');
        if ($configuration == null) throw new ErrorException('configuration null');

        $isOK = false;

        log::add('windows', 'debug', ' Analyse seuil hiver', __FILE__);
        $threshold_winter = $eqlogic->getConfiguration('threshold_winter');
        if ($threshold_winter == '') {
            log::add('windows', 'debug', '  > Pas de threshold_winter : valeur par défaut = 0', __FILE__);
            $configuration->threshold_winter = 0;
            $isOK = true;
        } else if (!is_numeric($threshold_winter)) {
            log::add('windows', 'error', '  > Mauvaise threshold_winter:' . $threshold_winter, __FILE__);
        } else {
            $configuration->threshold_winter = $threshold_winter;
            $isOK = true;
        }

        return $isOK;
    }

    /**
     * Récupérer le seuil été
     */
    private function getThresholdSummer($eqlogic, StdClass $configuration): bool
    {
        if ($eqlogic == null) throw new ErrorException('eqlogic null');
        if ($configuration == null) throw new ErrorException('configuration null');

        $isOK = false;

        // Seuil été
        log::add('windows', 'debug', ' Analyse seuil été', __FILE__);
        $threshold_summer = $eqlogic->getConfiguration('threshold_summer');
        if ($threshold_summer == '') {
            log::add('windows', 'debug', '  > Pas de threshold_summer : valeur par défaut = 0', __FILE__);
            $configuration->threshold_summer = 0;
            $isOK = true;
        } else if (!is_numeric($threshold_summer)) {
            log::add('windows', 'error', '  > Mauvaise threshold_summer:' . $threshold_summer, __FILE__);
        } else {
            $configuration->threshold_summer = $threshold_summer;
            $isOK = true;
        }

        return $isOK;
    }

    /**
     * Récupérer la consigne
     */
    private function getConsigne($eqlogic, StdClass $configuration): bool
    {
        if ($eqlogic == null) throw new ErrorException('eqlogic null');
        if ($configuration == null) throw new ErrorException('configuration null');

        $isOK = false;

        log::add('windows', 'debug', ' Analyse consigne', __FILE__);
        $consigne = $eqlogic->getConfiguration('consigne');
        $consigne = str_replace('#', '', $consigne);
        if ($consigne != '') {
            $cmd = cmd::byId($consigne);
            if ($cmd == null) {
                log::add('windows', 'error', '  > Mauvaise consigne :' . $consigne, __FILE__);
                return false;
            }
            $consigne = $cmd->execCmd();
            if (!is_numeric($consigne)) {
                log::add('windows', 'error', '  > Mauvaise consigne:' . $consigne, __FILE__);
                return false;
            } else {
                $configuration->consigne = $consigne;
                $isOK = true;
            }
        } else {
            log::add('windows', 'debug', '  > Pas de consigne', __FILE__);
            return false;
        }
        unset($cmd);

        return $isOK;
    }

    /**
     * Récupérer la Notification
     */
    private function getNotify($eqlogic, stdClass $configuration): bool
    {
        if ($eqlogic == null) throw new ErrorException('eqlogic null');
        if ($configuration == null) throw new ErrorException('configuration null');

        log::add('windows', 'debug', ' Analyse notification', __FILE__);
        $configuration->notifyko = $eqlogic->getConfiguration('notifyifko');

        return true;
    }

    /**
     * Récupérer la température extérieure
     */
    private static function getTemperatureOutdoor(StdClass $configuration): bool
    {
        if ($configuration == null) throw new ErrorException('configuration null');

        $isOK = true;

        log::add('windows', 'debug', ' Analyse temperature_outdoor', __FILE__);
        $temperature_outdoor = config::byKey('temperature_outdoor', 'windows');
        $temperature_outdoor = str_replace('#', '', $temperature_outdoor);
        if ($temperature_outdoor != '') {
            $cmd = cmd::byId($temperature_outdoor);
            if ($cmd == null) {
                log::add('windows', 'error', '  > Mauvaise temperature_outdoor :' . $temperature_outdoor, __FILE__);
                return false;
            }
            $temperature_outdoor = $cmd->execCmd();
            if (is_numeric($temperature_outdoor)) {
                $configuration->temperature_outdoor = $temperature_outdoor;
                $isOK = true;
            } else {
                log::add('windows', 'error', '  > Mauvaise temperature_outdoor :' . $temperature_outdoor, __FILE__);
                return false;
            }
        } else {
            log::add('windows', 'error', '  > Pas de temperature_outdoor', __FILE__);
            return false;
        }
        unset($cmd);

        return $isOK;
    }

    /**
     * Récupérer la température maximum
     */
    private static function getTemperatureMaxi(StdClass $configuration): bool
    {
        if ($configuration == null) throw new ErrorException('configuration null');

        $isOK = false;

        log::add('windows', 'debug', ' Analyse température maxi', __FILE__);
        $temperature_maxi = config::byKey('temperature_maxi', 'windows');
        $temperature_maxi = str_replace('#', '', $temperature_maxi);
        if ($temperature_maxi != '') {
            $cmd = cmd::byId($temperature_maxi);
            if ($cmd == null) {
                log::add('windows', 'error', '  > Mauvaise temperature_maxi :' . $temperature_maxi, __FILE__);
                return false;
            }
            $temperature_maxi = $cmd->execCmd();
            if (!is_numeric($temperature_maxi)) {
                log::add('windows', 'error', '  > Mauvaise temperature_maxi:' . $temperature_maxi, __FILE__);
                return false;
            } else {
                $configuration->temperature_maxi = $temperature_maxi;
                $isOK = true;
            }
        } else {
            log::add('windows', 'debug', '  > Pas de temperature_maxi', __FILE__);
        }
        unset($cmd);

        return $isOK;
    }

    /**
     * Récupérer la température Hiver
     */
    private static function getTemperatureWinter(StdClass $configuration): bool
    {
        if ($configuration == null) throw new ErrorException('configuration null');

        $isOK = false;

        log::add('windows', 'debug', ' Analyse température hivers', __FILE__);
        $temperature_winter = config::byKey('temperature_winter', 'windows');
        if ($temperature_winter != '') {
            if (!is_numeric($temperature_winter)) {
                log::add('windows', 'error', '  > Mauvaise temperature_winter: ' . $temperature_winter, __FILE__);
            } else {
                $configuration->temperature_winter = $temperature_winter;
                $isOK = true;
            }
        } else {
            log::add('windows', 'debug', '  > Pas de temperature_winter', __FILE__);
        }

        return $isOK;
    }

    /**
     * Récupérer la température été
     */
    private static function getTemperatureSummer(stdClass $configuration): bool
    {
        if ($configuration == null) throw new ErrorException('configuration null');

        $isOK = false;

        log::add('windows', 'debug', ' Analyse température été', __FILE__);
        $temperature_summer = config::byKey('temperature_summer', 'windows');
        if ($temperature_summer != '') {
            if (!is_numeric($temperature_summer)) {
                log::add('windows', 'error', '  > Mauvaise temperature_summer:' . $temperature_summer, __FILE__);
                return false;
            } else {
                $configuration->temperature_summer = $temperature_summer;
                $isOK = true;
            }
        } else {
            log::add('windows', 'debug', '  > Pas de temperature_summer', __FILE__);
        }

        return $isOK;
    }

    /**
     * Récupérer la Présence
     */
    private static function getPresence(stdClass $configuration): bool
    {
        if ($configuration == null) throw new ErrorException('configuration null');

        $isOK = true;

        log::add('windows', 'debug', ' Analyse presence', __FILE__);
        $presence = config::byKey('presence', 'windows');
        $presence = str_replace('#', '', $presence);
        if ($presence != '') {
            $cmd = cmd::byId($presence);
            if ($cmd == null) {
                log::add('windows', 'error', '  > Mauvaise presence :' . $presence, __FILE__);
                return false;
            }
            $presence = $cmd->execCmd();
            if (is_numeric($presence)) {
                $configuration->presence = $presence;
                $isOK = true;
            } else {
                log::add('windows', 'error', '  > Mauvaise presence :' . $presence, __FILE__);
                return false;
            }
        } else {
            log::add('windows', 'debug', '  > Pas de presence : valeur par défaut = 1', __FILE__);
            // Valeur par défaut
            $configuration->presence = 1;
            $isOK = true;
        }
        unset($cmd);

        return $isOK;
    }

    /*** Calcul sur Configuration ***/
    /**
     * Choix de la saison
     */
    private static function setSeason(StdClass $configuration)
    {
        if ($configuration == null) throw new ErrorException('configuration null');

        log::add('windows', 'debug', ' Recherche de la saison', __FILE__);
        if (
            isset($configuration->temperature_maxi)
            // && isset($configuration->temperature_mini)
            && isset($configuration->temperature_summer)
            && isset($configuration->temperature_winter)
        ) {
            // Type de saison par température
            log::add('windows', 'debug', ' Saison par température', __FILE__);

            if ($configuration->temperature_maxi <= $configuration->temperature_winter) {
                log::add('windows', 'debug', ' Saison : Hiver', __FILE__);
                $configuration->isWinter = true;
                $configuration->isSummer = false;
            } else if ($configuration->temperature_maxi >= $configuration->temperature_summer) {
                log::add('windows', 'debug', ' Saison : Eté', __FILE__);
                $configuration->isWinter = false;
                $configuration->isSummer = true;
            } else {
                log::add('windows', 'debug', ' Saison : Intérmédiaire', __FILE__);
                $configuration->isWinter = false;
                $configuration->isSummer = false;
            }
        } else {
            // Type de saison par date
            log::add('windows', 'debug', ' Saison par date', __FILE__);

            $dateTime = new DateTime('NOW');
            $dayOfTheYear = $dateTime->format('z');

            if ($dayOfTheYear < 80 || $dayOfTheYear > 264) {
                // du 21 septembre au 21 mars : automne et hivers
                log::add('windows', 'debug', ' Saison : Hiver', __FILE__);
                $configuration->isSummer = false;
                $configuration->isWinter = true;
            } else if ($dayOfTheYear > 172 && $dayOfTheYear < 264) {
                // du 21 juin au 21 septebmre : été
                log::add('windows', 'debug', ' Saison : Eté', __FILE__);
                $configuration->isSummer = true;
                $configuration->isWinter = false;
            } else {
                log::add('windows', 'debug', ' Saison : Intérmédiaire', __FILE__);
                $configuration->isSummer = false;
                $configuration->isWinter = false;
            }
        }
    }

    /**
     * Mise à jour de la durée retenu
     */
    private static function setDuration(StdClass $configuration)
    {
        if ($configuration == null) throw new ErrorException('configuration null');

        // Récupération de la durée
        log::add('windows', 'debug', ' Récupération de la durée selon la saison', __FILE__);
        if ($configuration->isWinter) {
            $configuration->duration = $configuration->duration_winter;
        } else {
            $configuration->duration = $configuration->duration_summer;
        }
    }

    /**
     * Récupérer la configuration sur les fenêtres
     * Récupère l'état des fenêtres (et la durée si ouverte)
     */
    private function getWindowsInformation(StdClass $configuration)
    {
        if ($configuration == null) throw new ErrorException('configuration null');

        $configuration->isOpened = false;
        $configuration->durationOpened = 0;

        $eqlogic = $this->getEqLogic(); //récupère l'eqlogic de la commande $this

        log::add('windows', 'debug', ' Liste des ouvertures :');
        $windows = $eqlogic->getConfiguration('window');
        foreach ($windows as $window) {
            $this->computeByWindow($window, $configuration);
        }
    }

    /**
     * Calcul le temps ouverture le plus grand
     */
    private function computeByWindow(array $window, StdClass $configuration)
    {
        if ($configuration == null) throw new ErrorException('configuration null');

        $window_cmd = str_replace('#', '', $window['cmd']);
        if ($window_cmd != '') {
            $cmd = cmd::byId($window_cmd);
        } else {
            log::add('windows', 'error', ' Pas de window', __FILE__);
            return;
        }

        if ($cmd == null) {
            log::add('windows', 'error', ' Mauvaise window :' . $window, __FILE__);
            return;
        }
        $windowState = $cmd->execCmd();
        log::add('windows', 'debug', '    ' . $cmd->getEqLogic()->getHumanName() . '[' . $cmd->getName() . '] : ' . $windowState);

        // 0 = fermé
        // 1 = ouvert
        // inverser
        if (isset($window['invert']) && $window['invert'] == 1) {
            $windowState = ($windowState == 0) ? 1 : 0;
            log::add('windows', 'debug', '     inversion de l\'état de l\'ouverture');
        }
        $isWindowOpened = ($windowState == 1);

        if ($isWindowOpened) {
            // si ouvert
            // Vérification de la durée
            $lastDateValue = $cmd->getValueDate();
            $time = strtotime($lastDateValue);
            $interval = (time() - $time) / 60; // en minutes
            log::add('windows', 'debug', '       lastDateValue:' . $lastDateValue . ' isWindowOpened:' . $isWindowOpened . ', timediff:' . $interval . ', duration:' . $configuration->duration);

            $configuration->isOpened = true;
            $configuration->durationOpened = max($configuration->durationOpened, $interval);
        }
    }





    /**
     * Vérifie l'action à réaliser et le message à afficher associé
     */
    private function checkAction($configuration)
    {
        if ($configuration == null) return;

        $result = new stdClass();
        $result->actionToExecute = false;
        $result->messageWindows = '';
        $result->durationOpened = $configuration->durationOpened;

        log::add('windows', 'debug', ' Analyse métier');

        // Vérification sur Présence
        if (!$configuration->presence) {
            log::add('windows', 'debug', '    Pas présent : rien à faire');
            return $result;
        }

        /*** HIVER ***/
        // Hiver, fenetre fermée
        // mais il fait plus chaud dehors tout de même
        // il faut donc ouvrir
        if (
            $configuration->isWinter
            && !$configuration->isOpened
            && $configuration->temperature_outdoor > $configuration->temperature_indoor
        ) {
            log::add('windows', 'debug', '    test hiver sur température');

            $result->messageWindows = 'il faut ouvrir';
            $result->actionToExecute = true;
            log::add('windows', 'info', $result->messageWindows);
        }

        // Vérifier s'il faut fermer      
        // si hiver et ouvert
        // if ($configuration->isWinter && $configuration->isOpened) {
        if (!$configuration->isSummer && $configuration->isOpened) {
            log::add('windows', 'debug', '    test hiver sur température et durée');

            // Vérification sur durée
            log::add('windows', 'debug', '    calcul sur durée');
            // Hiver et trop longtemps
            if ($configuration->durationOpened >=  $configuration->duration) {
                $result->actionToExecute = true;
                $result->messageWindows = 'il faut fermer';
                log::add('windows', 'info', '     > il faudra fermer sur durée');
            }

            // Vérification sur consigne
            if (isset($configuration->consigne) && $configuration->consigne != '') {
                log::add('windows', 'debug', '    calcul sur consigne: ' . $configuration->consigne);

                $temp_mini = $configuration->consigne - $configuration->threshold_winter;
                log::add('windows', 'debug', '    température mini :' . $temp_mini . ', température:' . $configuration->temperature_indoor);

                // Si durée longue mais tout de même chaude dedans
                if (
                    $result->actionToExecute
                    && $configuration->temperature_indoor >= $configuration->consigne
                ) {
                    $result->actionToExecute = false;
                    $result->messageWindows = '';
                    log::add('windows', 'info', '     > plus la peine de fermer sur durée');
                }

                // Si température plus froide que le mini autorisé
                if ($configuration->temperature_indoor <= $temp_mini) {
                    $result->actionToExecute = true;
                    $result->messageWindows = 'il faut fermer';
                    log::add('windows', 'info', '     > il faudra fermer sur température');
                }
            }
        }

        /*** ETE***/
        // Eté, fenetre fermée
        // mais il fait plus frais dehors tout de même
        // il faut donc ouvrir
        if (
            $configuration->isSummer
            && !$configuration->isOpened
            && $configuration->temperature_outdoor < $configuration->temperature_indoor
        ) {
            log::add('windows', 'debug', '    test été sur température');

            $result->messageWindows = 'il faut ouvrir';
            $result->actionToExecute = true;
            log::add('windows', 'info', $result->messageWindows);
        }

        // Vérifier s'il faut fermer      
        // si été et ouvert
        if ($configuration->isSummer && $configuration->isOpened) {
            log::add('windows', 'debug', '    test été sur température et durée');

            // Vérification sur durée
            log::add('windows', 'debug', '    calcul sur durée');
            // Hiver et trop longtemps
            if ($configuration->durationOpened >=  $configuration->duration) {
                $result->actionToExecute = true;
                $result->messageWindows = 'il faut fermer';
                log::add('windows', 'info', '    il faudra fermer sur durée');
            }

            // // Vérification sur consigne
            // if (isset($configuration->consigne) && $configuration->consigne != '') {
            //     log::add('windows', 'debug', '    calcul sur consigne: '.$configuration->consigne);

            //     // Hiver                
            //     $temp_mini = $configuration->consigne - $configuration->threshold_summer;
            //     log::add('windows', 'debug', '    température mini :'.$temp_mini.', température:'.$configuration->temperature_indoor);

            //     if ($configuration->temperature_indoor >= $temp_mini) {
            //         $result->actionToExecute = true;
            //         $result->messageWindows = 'il faut fermer';
            //         log::add('windows', 'info', '    il faudra fermer sur température');
            //     }
            // }
        }


        // Log de résumé        
        log::add(
            'windows',
            'debug',
            '     ==> '
                . 'ext:' . $configuration->temperature_outdoor
                . ', int:' . $configuration->temperature_indoor
                . ', seuil hiver:' . $configuration->temperature_winter
                . ', presence:' . $configuration->presence
                . ', isOpened:' . ($configuration->isOpened ? 'true' : 'false')
                . ', actionToExecute:' . ($result->actionToExecute ? 'true' : 'false')
                . ', messageWindows:' . $result->messageWindows
                . ', durationOpened:' . $result->durationOpened
        );

        unset($value);

        return $result;
    }

    /**
     * Mise à jour des commandes
     * * @param StdClass result Action et message
     */
    private function updateCommands($result)
    {
        log::add('windows', 'debug', ' updateCommands(): result :' . json_encode((array)$result));

        $eqlogic = $this->getEqLogic(); //récupère l'éqlogic de la commande $this

        // Icone sur le widget
        $window_action = $eqlogic->getCmd(null, 'window_action');
        if ($result->actionToExecute === true) {
            log::add('windows', 'debug', '       window_action: action !');

            $window_action->event(1);
        } else {
            log::add('windows', 'debug', '       window_action: rien à faire');

            $window_action->event(0);
        }

        // Message sur le widget
        $message = $eqlogic->getCmd(null, 'message');
        log::add('windows', 'debug', '       message : ' . $result->messageWindows);
        $message->event($result->messageWindows);

        // duree
        $cmdCounter = $eqlogic->getCmd(null, 'counter');
        $durationOpen = intval($result->durationOpened);
        log::add('windows', 'debug', '       durationOpen:' . $durationOpen);
        $cmdCounter->event($durationOpen);
    }

    /**
     * Réaliser les actions :
     *  - Icone sur le widget
     *  - Notification
     *  - Actions diverses
     */
    private function action($configuration, $result)
    {
        log::add('windows', 'debug', ' action(): result :' . json_encode((array)$result));

        $eqlogic = $this->getEqLogic(); //récupère l'éqlogic de la commande $this

        // // Icone sur le widget
        // $window_action = $eqlogic->getCmd(null, 'window_action');
        // if ($result->actionToExecute === true) {
        //     log::add('windows', 'debug', '       window_action: action !');

        //     $window_action->event(1);
        // } else {
        //     log::add('windows', 'debug', '       window_action: rien à faire');

        //     $window_action->event(0);
        // }

        // // Message sur le widget
        // $message = $eqlogic->getCmd(null, 'message');
        // log::add('windows', 'debug', '       message');
        // $message->event($result->messageWindows);

        log::add('windows', 'debug', ' avant Notification');
        // Notification
        if ($configuration->notifyko == 1 && $result->actionToExecute) {
            log::add('windows', 'debug', ' Notification:' . $configuration->notifyko);

            $messageToSend = "$result->messageWindows : #parent# (#temperature_indoor#)";
            $messageToSend = str_replace('#name#', $eqlogic->getName(), $messageToSend);
            $messageToSend = str_replace('#message#', $result->messageWindows, $messageToSend);
            $messageToSend = str_replace('#temperature_indoor#', "$configuration->temperature_indoor $configuration->temperature_unit", $messageToSend);
            $messageToSend = str_replace('#parent#', $eqlogic->getObject()->getName(), $messageToSend);

            message::add('windows', $messageToSend, '', '' . $this->getId());
        } else {
            log::add('windows', 'debug', ' Notification désactivée');
        }

        // Actions
        log::add('windows', 'debug', ' avant Execute');
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
                        $option = str_replace('#temperature_indoor#', "$configuration->temperature_indoor $configuration->temperature_unit", $option);
                        $option = str_replace('#parent#', $eqlogic->getObject()->getName(), $option);

                        $options[$key] = $option;
                    }

                    if ($option['title'] == '' || $option['message'] == '') {
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

    // Exécution d'une commande  
    public function execute($_options = array())
    {
        log::add('windows', 'info', ' **** execute ****', __FILE__);

        switch ($this->getLogicalId()) {
            case 'refresh': // LogicalId de la commande rafraîchir que l’on a créé dans la méthode Postsave de la classe vdm .                                 
                $eqlogic = $this->getEqLogic(); //récupère l'éqlogic de la commande $this
                log::add('windows', 'info', ' Objet : ' . $eqlogic->getName(), __FILE__);

                // Lecture et Analyse de la configuration
                $configuration = $this->getMyConfiguration();

                if ($configuration != null) {
                    $this->getWindowsInformation($configuration);
                    log::add('windows', 'debug', ' configuration :' . json_encode((array)$configuration));

                    $result = $this->checkAction($configuration);
                    $this->updateCommands($result);
                    //$this->action($configuration, $result);
                } else {
                    log::add('windows', 'error', ' >>> Vérifier le paramétrage');
                }

                break;
        }
    }

    public function launchAction()
    {
        log::add('windows', 'debug', ' launchAction', __FILE__);

        // window_action
        $eqlogic = $this->getEqLogic(); //récupère l'éqlogic de la commande $this
        log::add('windows', 'info', ' Objet : ' . $eqlogic->getName(), __FILE__);

        $result = new stdClass();
        $cmd = $eqlogic->getCmd(null, 'window_action');
        $result->actionToExecute = $cmd->execCmd();

        $cmd = $eqlogic->getCmd(null, 'message');
        $result->messageWindows = $cmd->execCmd();
        log::add('windows', 'debug', ' result :' . json_encode((array)$result));


        // Lecture et Analyse de la configuration
        //$configuration = $this->getMyConfiguration();
        $configuration = new StdClass();
        //$this->getWindowsInformation($configuration);
        log::add('windows', 'debug', ' configuration :' . json_encode((array)$configuration));

        //$this->action($configuration, $result);
    }

    /*     * **********************Getteur Setteur*************************** */
}
