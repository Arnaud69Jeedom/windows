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
abstract class Seasons
{
    const Winter = 1;
    const Summer = 2;
    const interSeason = 3;
}

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
        // log::add('windows', 'debug', 'postSave');

        // windows_action
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

        // // counter
        // // Renommer counter en duration
        // $counter = $this->getCmd(null, 'counter');
        // if (is_object($counter)) {
        //     $counter->setLogicalId('duration');
        //     $counter->setName(__('Durée', __FILE__));
        //     $counter->save();
        // }
        // unset($counter);

        // duration
        $duration = $this->getCmd(null, 'duration');
        if (!is_object($duration)) {
            $duration = new windowsCmd();
            $duration->setLogicalId('duration');
            $duration->setIsVisible(1);
            $duration->setName(__('Durée', __FILE__));
            $duration->setOrder(1);
        }
        $duration->setName(__('Durée', __FILE__));
        $duration->setEqLogic_id($this->getId());
        $duration->setType('info');
        $duration->setSubType('numeric');
        $duration->setGeneric_type('GENERIC_INFO');
        $duration->setUnite('min');
        $duration->save();
        unset($duration);

        // durationDaily
        $durationDaily = $this->getCmd(null, 'durationDaily');
        if (!is_object($durationDaily)) {
            $durationDaily = new windowsCmd();
            $durationDaily->setLogicalId('durationDaily');
            $durationDaily->setIsVisible(1);
            $durationDaily->setName(__('Durée du jour', __FILE__));
            $durationDaily->setOrder(2);
        }
        $durationDaily->setEqLogic_id($this->getId());
        $durationDaily->setType('info');
        $durationDaily->setSubType('numeric');
        $durationDaily->setGeneric_type('GENERIC_INFO');
        $durationDaily->setUnite('min');
        $durationDaily->save();
        unset($durationDaily);

        // message
        $message = $this->getCmd(null, 'message');
        if (!is_object($message)) {
            $message = new windowsCmd();
            $message->setLogicalId('message');
            $message->setIsVisible(1);
            $message->setName(__('Message', __FILE__));
            $message->setOrder(3);
        }
        $message->setEqLogic_id($this->getId());
        $message->setType('info');
        $message->setSubType('string');
        $message->setGeneric_type('GENERIC_INFO');
        $message->setUnite(null);
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
    private function getMyConfiguration(): ?stdClass
    {
        $configuration = new stdClass();

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
        $isOK &= $this->getFrequency($eqlogic, $configuration);
        $isOK &= $this->getCo2($eqlogic, $configuration);
        if (isset($configuration->co2)) {
            $isOK &= $this->getThresholdMaxiCo2($eqlogic, $configuration);
            $isOK &= $this->getThresholdNormalCo2($eqlogic, $configuration);
        }

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
    private function getDurationWinter($eqlogic, stdClass $configuration): bool
    {
        if ($eqlogic == null) throw new ErrorException('eqlogic null');
        if ($configuration == null) throw new ErrorException('configuration null');

        $isOK = false;

        $duration_winter = $eqlogic->getConfiguration('duration_winter');
        if ($duration_winter != '') {
            if (!is_numeric($duration_winter)) {
                log::add('windows', 'error', '  > Mauvaise duration_winter:' . $duration_winter, __FILE__);
            } else {
                $configuration->duration_winter = $duration_winter;
                $isOK = true;
            }
        } else {
            log::add('windows', 'debug', '  > Pas de duration_winter : valeur par défaut = 0', __FILE__);
            // Valeur par défaut
            $configuration->duration_winter = 0;
            $isOK = true;
        }

        return $isOK;
    }

    /**
     * Récupérer la valeur Durée pour été
     */
    private function getDurationSummer($eqlogic, stdClass $configuration): bool
    {
        if ($eqlogic == null) throw new ErrorException('eqlogic null');
        if ($configuration == null) throw new ErrorException('configuration null');

        $isOK = false;

        $duration_summer = $eqlogic->getConfiguration('duration_summer');
        if ($duration_summer != '') {
            if (!is_numeric($duration_summer)) {
                log::add('windows', 'error', '  > Mauvaise duration_summer:' . $duration_summer, __FILE__);
            } else {
                $configuration->duration_summer = $duration_summer;
                $isOK = true;
            }
        } else {
            log::add('windows', 'debug', '  > Pas de duration_summer : valeur par défaut = 0', __FILE__);
            // Valeur par défaut
            $configuration->duration_summer = 0;
            $isOK = true;
        }

        return $isOK;
    }

    /**
     * Récupérer le seuil hiver
     */
    private function getThresholdWinter($eqlogic, stdClass $configuration): bool
    {
        if ($eqlogic == null) throw new ErrorException('eqlogic null');
        if ($configuration == null) throw new ErrorException('configuration null');

        $isOK = false;

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
    private function getThresholdSummer($eqlogic, stdClass $configuration): bool
    {
        if ($eqlogic == null) throw new ErrorException('eqlogic null');
        if ($configuration == null) throw new ErrorException('configuration null');

        $isOK = false;

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
    private function getConsigne($eqlogic, stdClass $configuration): bool
    {
        if ($eqlogic == null) throw new ErrorException('eqlogic null');
        if ($configuration == null) throw new ErrorException('configuration null');

        $isOK = false;

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
            $isOK = true;
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

        $configuration->notifyko = $eqlogic->getConfiguration('notifyifko');

        return true;
    }

    /**
     * Récupérer la température extérieure
     */
    private static function getTemperatureOutdoor(stdClass $configuration): bool
    {
        if ($configuration == null) throw new ErrorException('configuration null');

        $isOK = true;

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
    private static function getTemperatureMaxi(stdClass $configuration): bool
    {
        if ($configuration == null) throw new ErrorException('configuration null');

        $isOK = false;

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
    private static function getTemperatureWinter(stdClass $configuration): bool
    {
        if ($configuration == null) throw new ErrorException('configuration null');

        $isOK = false;

        $temperature_winter = config::byKey('temperature_winter', 'windows');
        if ($temperature_winter == '') {
            log::add('windows', 'debug', '  > Pas de temperature_winter : valeur par défaut = 13', __FILE__);
            $configuration->temperature_winter = 13;
            $isOK = true;
        } else if (!is_numeric($temperature_winter)) {
            log::add('windows', 'error', '  > Mauvaise temperature_winter:' . $temperature_winter, __FILE__);
        } else {
            $configuration->temperature_winter = $temperature_winter;
            $isOK = true;
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

        $temperature_summer = config::byKey('temperature_summer', 'windows');
        if ($temperature_summer == '') {
            log::add('windows', 'debug', '  > Pas de temperature_summer : valeur par défaut = 25', __FILE__);
            $configuration->temperature_summer = 25;
            $isOK = true;
        } else if (!is_numeric($temperature_summer)) {
            log::add('windows', 'error', '  > Mauvaise temperature_summer:' . $temperature_summer, __FILE__);
        } else {
            $configuration->temperature_summer = $temperature_summer;
            $isOK = true;
        }

        return $isOK;
    }

    /**
     * Récupérer la sonde de CO2
     */
    private static function getCo2($eqlogic, stdClass $configuration): bool
    {
        if ($eqlogic == null) throw new ErrorException('eqlogic null');
        if ($configuration == null) throw new ErrorException('configuration null');

        $isOK = true;

        $co2 = $eqlogic->getConfiguration('co2');
        $co2 = str_replace('#', '', $co2);
        if ($co2 != '') {
            $cmd = cmd::byId($co2);
            if ($cmd == null) {
                log::add('windows', 'error', '  > Mauvaise co2 :' . $co2, __FILE__);
                return false;
            }
            $co2 = $cmd->execCmd();
            if (is_numeric($co2)) {
                $configuration->co2 = $co2;
                $isOK = true;
            } else {
                log::add('windows', 'error', '  > Mauvaise co2 :' . $co2, __FILE__);
                return false;
            }
        } else {
            log::add('windows', 'debug', '  > Pas de co2', __FILE__);
            $isOK = true;
        }
        unset($cmd);

        return $isOK;
    }

    /**
     * Récupérer le seuil maxi co2
     */
    private function getThresholdMaxiCo2($eqlogic, stdClass $configuration): bool
    {
        if ($eqlogic == null) throw new ErrorException('eqlogic null');
        if ($configuration == null) throw new ErrorException('configuration null');

        $isOK = false;

        $threshold_maxi_co2 = $eqlogic->getConfiguration('threshold_maxi_co2');
        if ($threshold_maxi_co2 == '') {
            log::add('windows', 'debug', '  > Pas de threshold_maxi_co2 : valeur par défaut = 1000', __FILE__);
            $configuration->threshold_maxi_co2 = 1000;
            $isOK = true;
        } else if (!is_numeric($threshold_maxi_co2)) {
            log::add('windows', 'error', '  > Mauvaise threshold_maxi_co2:' . $threshold_maxi_co2, __FILE__);
        } else {
            $configuration->threshold_maxi_co2 = $threshold_maxi_co2;
            $isOK = true;
        }

        return $isOK;
    }

    /**
     * Récupérer le seuil maxi co2
     */
    private function getThresholdNormalCo2($eqlogic, stdClass $configuration): bool
    {
        if ($eqlogic == null) throw new ErrorException('eqlogic null');
        if ($configuration == null) throw new ErrorException('configuration null');

        $isOK = false;

        $threshold_normal_co2 = $eqlogic->getConfiguration('threshold_normal_co2');
        if ($threshold_normal_co2 == '') {
            log::add('windows', 'debug', '  > Pas de threshold_normal_co2 : valeur par défaut = 800', __FILE__);
            $configuration->threshold_normal_co2 = 800;
            $isOK = true;
        } else if (!is_numeric($threshold_normal_co2)) {
            log::add('windows', 'error', '  > Mauvaise threshold_normal_co2:' . $threshold_normal_co2, __FILE__);
        } else {
            $configuration->threshold_normal_co2 = $threshold_normal_co2;
            $isOK = true;
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

    /**
     * Récupérer le seuil maxi co2
     */
    private function getFrequency($eqlogic, stdClass $configuration): bool
    {
        if ($eqlogic == null) throw new ErrorException('eqlogic null');
        if ($configuration == null) throw new ErrorException('configuration null');

        $isOK = false;

        $frequency = $eqlogic->getConfiguration('frequency');
        if ($frequency == '') {
            log::add('windows', 'debug', '  > Pas de frequency : valeur par défaut = 5', __FILE__);
            $configuration->frequency = 5;
            $isOK = true;
        } else if (!is_numeric($frequency)) {
            log::add('windows', 'error', '  > Mauvaise frequency:' . $frequency, __FILE__);
        } else {
            $configuration->frequency = $frequency;
            $isOK = true;
        }

        return $isOK;
    }

    /*** Calcul sur Configuration ***/
    /**
     * Choix de la saison
     */
    private static function setSeason(stdClass $configuration)
    {
        if ($configuration == null) throw new ErrorException('configuration null');

        log::add('windows', 'debug', ' Recherche de la saison', __FILE__);
        if (
            isset($configuration->temperature_maxi)
            && isset($configuration->temperature_summer)
            && isset($configuration->temperature_winter)
        ) {
            // Type de saison par température
            log::add('windows', 'debug', ' Saison par température', __FILE__);

            if ($configuration->temperature_maxi <= $configuration->temperature_winter) {
                log::add('windows', 'debug', ' Saison : Hiver', __FILE__);
                $configuration->Season = Seasons::Winter;
            } else if ($configuration->temperature_maxi >= $configuration->temperature_summer) {
                log::add('windows', 'debug', ' Saison : Eté', __FILE__);
                $configuration->Season = Seasons::Summer;
            } else {
                log::add('windows', 'debug', ' Saison : Intersaison', __FILE__);
                $configuration->Season = Seasons::interSeason;
            }
        } else {
            // Type de saison par date
            log::add('windows', 'debug', ' Saison par date', __FILE__);

            $dateTime = new DateTime('NOW');
            $dayOfTheYear = $dateTime->format('z');

            if ($dayOfTheYear < 80 || $dayOfTheYear > 264) {
                // du 21 septembre au 21 mars : automne et hivers
                log::add('windows', 'debug', ' Saison : Hiver', __FILE__);
                $configuration->Season = Seasons::Winter;
            } else if ($dayOfTheYear > 172 && $dayOfTheYear < 264) {
                // du 21 juin au 21 septebmre : été
                log::add('windows', 'debug', ' Saison : Eté', __FILE__);
                $configuration->Season = Seasons::Summer;
            } else {
                log::add('windows', 'debug', ' Saison : Intersaison', __FILE__);
                $configuration->Season = Seasons::interSeason;
            }
        }
    }

    /**
     * Mise à jour de la durée retenu
     */
    private static function setDuration(stdClass $configuration)
    {
        if ($configuration == null) throw new ErrorException('configuration null');

        log::add('windows', 'debug', ' Récupération de la durée selon la saison', __FILE__);
        if ($configuration->Season == Seasons::Winter) {
            $configuration->duration = $configuration->duration_winter;
        } else {
            $configuration->duration = $configuration->duration_summer;
        }
    }

    /**
     * Récupérer la configuration sur les fenêtres
     * Récupère l'état des fenêtres (et la durée si ouverte)
     */
    private function getWindowsInformation(stdClass $configuration)
    {
        if ($configuration == null) throw new ErrorException('configuration null');

        $configuration->isOpened = false;
        $configuration->durationOpened = 0;
        $configuration->durationDailyOpened = 0;

        $eqlogic = $this->getEqLogic(); //récupère l'eqlogic de la commande $this

        log::add('windows', 'info', ' Liste des ouvertures :');
        $windows = $eqlogic->getConfiguration('window');

        foreach ($windows as $window) {
            $this->computeByWindow($window, $configuration);
        }
    }

    /**
     * Calcul le temps ouverture le plus grand
     */
    private function computeByWindow(array $window, stdClass $configuration)
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
        log::add('windows', 'info', '    ' . $cmd->getEqLogic()->getHumanName() . '[' . $cmd->getName() . '] : ' . $windowState);

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
            log::add('windows', 'info', '       fenêtre ouverte');

            // Vérification de la durée
            $lastDateValue = $cmd->getValueDate();  // Date de l'ouverture de la fenêtre
            $time = strtotime($lastDateValue);
            $interval = round((time() - $time) / 60); // en minutes

            $configuration->isOpened = true;
            $configuration->durationOpened = max($configuration->durationOpened, $interval);
        } else {
            log::add('windows', 'info', '      fenêtre fermée');
        }

        try {
            // duration daily Opened
            $valueOpen = ($window['invert'] == 1) ? 0 : 1;

            $windowName = '#' . $cmd->getEqLogic()->getHumanName() . '[' . $cmd->getName() . ']#';
            $durationDaily = intval(scenarioExpression::durationbetween($windowName, $valueOpen, 'today 00:00', 'today 23:59', 60));

            // Max de Daily et fenetre open
            $durationDaily = max($durationDaily, $configuration->durationOpened);
            $configuration->durationDailyOpened = max($configuration->durationDailyOpened, $durationDaily);
        } catch (Exception $e) {
            log::add('windows', 'debug', '       Exception reçue : ',  $e->getMessage());
        }
    }

    /**
     * Vérifie l'action à réaliser en hiver
     * et le message à afficher associé
     */
    private function checkActionWinter(stdClass $configuration, stdClass $result): stdClass
    {
        if ($configuration == null) throw new ErrorException('configuration null');
        if ($result == null) throw new ErrorException('result null');

        log::add('windows', 'debug', ' > Analyse hiver');

        // Hiver, fenetre fermée
        // Température
        // mais il fait plus chaud dehors tout de même
        // il faut donc ouvrir
        if (
            !$configuration->isOpened
            && $configuration->temperature_outdoor > $configuration->temperature_indoor
        ) {
            log::add('windows', 'debug', '    test hiver sur température');

            $result->actionToExecute = true;
            $result->messageWindows = __('il faut ouvrir', __FILE__);
            $result->reason = __('température', __FILE__);
            log::add('windows', 'info', '     > il faudra ouvrir sur température');
        }

        // Hiver et fenêtre ouverte
        // Durée
        // mais le temps d'ouverture mini est atteinte
        // Vérifier s'il faut fermer
        if ($configuration->isOpened) {
            log::add('windows', 'debug', '    test hiver sur durée');

            // Vérification sur durée
            log::add('windows', 'debug', '    calcul sur durée');
            // Hiver et trop longtemps
            if ($configuration->duration != 0) {
                // Durée non illimitée
                if ($configuration->durationOpened >=  $configuration->duration) {
                    $result->actionToExecute = true;
                    $result->messageWindows =  __('il faut fermer', __FILE__);
                    $result->reason = __('durée', __FILE__);
                    log::add('windows', 'info', '     > il faudra fermer sur durée');
                }
            } else {
                log::add('windows', 'debug', '     > pas de limite sur durée');
            }
        }

        // Hiver (ou intersaison?) et fenêtre ouverte
        // Consigne
        // il fait bon dedans : pas la peine de fermer sur durée
        // il fait froid dedans : il faut fermer
        if ($configuration->isOpened) {
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
                    $result->reason = '';
                    log::add('windows', 'info', '     > plus la peine de fermer sur durée');
                }

                // Si température plus froide que le mini autorisé
                if ($configuration->temperature_indoor <= $temp_mini) {
                    $result->actionToExecute = true;
                    $result->messageWindows = __('il faut fermer', __FILE__);
                    $result->reason = __('température', __FILE__);
                    log::add('windows', 'info', '     > il faudra fermer sur température');
                }
            }
        }

        return $result;
    }

    /**
     * Vérifie l'action à réaliser en été
     * et le message à afficher associé
     */
    private function checkActionSummer(stdClass $configuration, stdClass $result): stdClass
    {
        if ($configuration == null) throw new ErrorException('configuration null');
        if ($result == null) throw new ErrorException('result null');

        log::add('windows', 'debug', ' > Analyse été');

        // Eté et fenetre fermée
        // Température
        // mais il fait plus frais dehors tout de même : il faut ouvrir
        if (
            $configuration->Season == Seasons::Summer
            && !$configuration->isOpened
            && $configuration->temperature_outdoor < $configuration->temperature_indoor
        ) {
            log::add('windows', 'debug', '    test été sur température');

            $result->actionToExecute = true;
            $result->messageWindows = __('il faut ouvrir', __FILE__);
            $result->reason = __('température', __FILE__);
            log::add('windows', 'info', '     > il faudra ouvrir sur température');
        }

        // Eté et fenetre ouverte
        // Durée
        // Vérifier s'il faut fermer      
        if (
            $configuration->Season == Seasons::Summer
            && $configuration->isOpened
        ) {
            log::add('windows', 'debug', '    test été sur température et durée');

            // Vérification sur durée
            log::add('windows', 'debug', '    calcul sur durée');
            // Hiver et trop longtemps
            if ($configuration->duration != 0) {
                if ($configuration->durationOpened >= $configuration->duration) {
                    $result->actionToExecute = true;
                    $result->messageWindows = __('il faut fermer', __FILE__);
                    $result->reason = __('durée', __FILE__);
                    log::add('windows', 'info', '    il faudra fermer sur durée');
                }
            } else {
                log::add('windows', 'info', '     > pas de limite sur durée');
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
            //         $result->reason = __('température', __FILE__);
            //         log::add('windows', 'info', '    il faudra fermer sur température');
            //     }
            // }
        }

        return $result;
    }

    /**
     * Vérifie l'action à réaliser en intersaison
     * et le message à afficher associé
     */
    private function checkActionInterseason(stdClass $configuration, stdClass $result): stdClass
    {
        if ($configuration == null) throw new ErrorException('configuration null');
        if ($result == null) throw new ErrorException('result null');

        log::add('windows', 'debug', ' > Analyse intersaison');

        // Vérification sur consigne
        if (isset($configuration->consigne) && $configuration->consigne != '') {
            log::add('windows', 'debug', '    calcul sur consigne: ' . $configuration->consigne);

            $temp_mini = $configuration->consigne - $configuration->threshold_winter;
            $temp_maxi = $configuration->consigne + $configuration->threshold_summer;
            log::add('windows', 'debug', '    température mini :' . $temp_mini . ', température:' . $configuration->temperature_indoor . ', température maxi :' . $temp_maxi);

            // // ARRIVERA la nuit, s'il pleut, toutes les 5 minutes...
            // // Température exterieure idéale
            // if (!$configuration->isOpened
            //     && $configuration->temperature_outdoor <= $temp_maxi
            //     && $configuration->temperature_outdoor >= $temp_mini) {
            //         $result->actionToExecute = true;
            //         $result->messageWindows = __('il faut ouvrir', __FILE__);
            //         $result->reason = __('température', __FILE__);
            //         log::add('windows', 'info', '     > il faudra ouvrir sur température');
            // }

            // Intersaison et fenêtre ouverte
            // Température
            // il fait trop chaud dedans et encore plus chaud dehors
            // ou trop froid dedans et aussi dehors
            // : il faut fermer
            // sinon ouvrir devrait stabiliser la température
            if (
                $configuration->isOpened
                && (
                    ($configuration->temperature_indoor > $temp_maxi && $configuration->temperature_outdoor > $temp_maxi)
                    ||  ($configuration->temperature_indoor < $temp_mini && $configuration->temperature_outdoor < $temp_mini)
                )
            ) {
                $result->actionToExecute = true;
                $result->messageWindows = __('il faut fermer', __FILE__);
                $result->reason = __('température', __FILE__);
                log::add('windows', 'info', '     > il faudra fermer sur température');
            }
        }

        return $result;
    }

    /**
     * Vérifie l'action à réaliser 
     * et le message à afficher associé
     */
    private function checkAction(stdClass $configuration): stdClass
    {
        if ($configuration == null) throw new ErrorException('configuration null');

        log::add('windows', 'debug', ' Analyse métier');

        // Initialisation
        $result = new stdClass();
        $result->actionToExecute = false;
        $result->messageWindows = '';
        $result->reason = '';
        $result->durationOpened = $configuration->durationOpened;
        $result->durationDailyOpened = $configuration->durationDailyOpened;

        // Vérification sur Présence
        if (!$configuration->presence) {
            log::add('windows', 'debug', '    Pas présent : rien à faire');
            return $result;
        }

        // Hiver
        if ($configuration->Season == Seasons::Winter) {
            $result = $this->checkActionWinter($configuration, $result);
        }

        // Intersaison
        if ($configuration->Season == Seasons::interSeason) {
            $result = $this->checkActionInterseason($configuration, $result);
        }

        // ETE
        if ($configuration->Season == Seasons::Summer) {
            $result = $this->checkActionSummer($configuration, $result);
        }

        // CO2
        if (isset($configuration->co2) && $configuration->co2 != '') {
            log::add('windows', 'debug', '    test sur co2: ' . $configuration->co2);

            // Fenêtre fermée et niveau CO2 trop important
            // il faut ouvrir
            if (
                !$configuration->isOpened
                && $configuration->co2 >= $configuration->threshold_maxi_co2
            ) {
                $result->actionToExecute = true;
                $result->messageWindows = __('il faut ouvrir', __FILE__);
                $result->reason = __('co2', __FILE__);
                log::add('windows', 'info', '    il faudra ouvrir sur co2');
            }

            // Fenêtre ouverte et action de fermeture 
            // Mais niveau de CO2 trop éleve
            // Il faut laisser ouvert
            if (
                $configuration->isOpened
                && $result->actionToExecute == true
                && $configuration->co2 >= $configuration->threshold_normal_co2
            ) {
                $result->actionToExecute = false;
                $result->messageWindows = '';
                $result->reason = '';
                log::add('windows', 'info', '    il faudra continuer à laisser ouvert cause co2');
            }
        }

        // Log de résumé        
        // log::add(
        //     'windows',
        //     'debug',
        //     '     ==> '
        //         . 'ext:' . $configuration->temperature_outdoor
        //         . ', int:' . $configuration->temperature_indoor
        //         . ', seuil hiver:' . $configuration->temperature_winter
        //         . ', presence:' . $configuration->presence
        //         . ', isOpened:' . ($configuration->isOpened ? 'true' : 'false')
        //         . ', actionToExecute:' . ($result->actionToExecute ? 'true' : 'false')
        //         . ', messageWindows:' . $result->messageWindows
        //         . ', reason:' . $result->reason
        //         . ', durationOpened:' . $result->durationOpened
        // );

        unset($value);

        return $result;
    }

    /**
     * Mise à jour des commandes
     * * @param stdClass result Action et message
     */
    private function updateCommands(stdClass $result)
    {
        $eqlogic = $this->getEqLogic(); //récupère l'éqlogic de la commande $this

        // Icone sur le widget (actionToExecute)
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
        $cmdDuration = $eqlogic->getCmd(null, 'duration');
        $durationOpen = intval($result->durationOpened);
        log::add('windows', 'debug', '       durationOpen:' . $durationOpen);
        $cmdDuration->event($durationOpen);

        // duration daily
        $cmdDurationDaily = $eqlogic->getCmd(null, 'durationDaily');
        $durationDailyOpened = intval($result->durationDailyOpened);
        log::add('windows', 'debug', '       $durationDailyOpened:' . $durationDailyOpened);
        $cmdDurationDaily->event($durationDailyOpened);
    }

    /**
     * Réaliser les actions :
     *  - Icone sur le widget
     *  - Notification
     *  - Actions diverses
     */
    private function action(stdClass $configuration, stdClass $result)
    {
        if ($result->actionToExecute == false) {
            log::add('windows', 'info', 'rien à faire');
            return;
        }

        $eqlogic = $this->getEqLogic(); //récupère l'éqlogic de la commande $this

        // Notification
        if ($configuration->notifyko == 1) {
            log::add('windows', 'debug', ' Notification:' . $configuration->notifyko);

            $messageToSend = "$result->messageWindows : #parent# (#temperature_indoor#) (#reason#)";
            $messageToSend = str_replace('#name#', $eqlogic->getName(), $messageToSend);
            $messageToSend = str_replace('#message#', $result->messageWindows, $messageToSend);
            $messageToSend = str_replace('#reason#', $result->reason, $messageToSend);
            $messageToSend = str_replace('#temperature_indoor#', "$configuration->temperature_indoor $configuration->temperature_unit", $messageToSend);
            $messageToSend = str_replace('#parent#', $eqlogic->getObject()->getName(), $messageToSend);

            message::add('windows', $messageToSend, '', '' . $this->getId());
        } else {
            log::add('windows', 'debug', ' Notification désactivée');
        }

        // Actions
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
                    $option = str_replace('#reason#', $result->reason, $option);
                    $option = str_replace('#temperature_indoor#', "$configuration->temperature_indoor $configuration->temperature_unit", $option);
                    $option = str_replace('#parent#', $eqlogic->getObject()->getName(), $option);

                    $options[$key] = $option;
                }

                if ($option['title'] == '' || $option['message'] == '') {
                    log::add('windows', 'error', 'Action sans titre ou message');
                    break;
                }
            }

            // Gestion des tags
            // $tags = scenarioExpression::getTags();
            // log::add('windows', 'debug', '$tags:'.json_decode($tags));

            // $tags = array();
            // if (isset($options['tags'])) {
            //     $options['tags'] = arg2array($options['tags']);
            //     foreach ($options['tags'] as $key => $value) {
            //         $tags['#' . trim(trim($key), '#') . '#'] = scenarioExpression::setTags(trim($value));
            //     }
            // }
            // $options['tags'] = $tags;

            scenarioExpression::createAndExec('action', $action['cmd'], $options);
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

                    // Limiter les actions toutes les 5 minutes
                    $minutes = date('i');
                    if ($configuration->isOpened && ($result->durationOpened % $configuration->frequency) == 0) {
                        $this->action($configuration, $result);
                    } elseif (!$configuration->isOpened && ($minutes % $configuration->frequency == 0)) {
                        // Pas ouvert, time % 300 ?
                        // A TESTER
                        log::add('windows', 'info', ' Action sur fenêtre fermée', __FILE__);
                        $this->action($configuration, $result);
                    } else {
                        log::add('windows', 'info', ' pas action : ' . ($result->durationOpened % $configuration->frequency), __FILE__);
                    }
                } else {
                    log::add('windows', 'error', ' >>> Vérifier le paramétrage');
                }

                break;
        }
    }


    /*     * **********************Getteur Setteur*************************** */
}
