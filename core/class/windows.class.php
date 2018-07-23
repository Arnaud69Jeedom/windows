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
        // internal
        $info = $this->getCmd(null, 'temperature_indoor');
        if (!is_object($info)) {
            $info = new windowsCmd();
            $info->setName(__('Température', __FILE__));
            $info->setIsVisible(1);
            $info->setIsHistorized(1);
        }
        $info->setLogicalId('temperature_indoor');
        $info->setEqLogic_id($this->getId());
        $info->setType('info');
        $info->setSubType('numeric');
        $info->setUnite('°C');
        $value = '';
        preg_match_all("/#([0-9]*)#/", $this->getConfiguration('temperature_indoor'), $matches);
        foreach ($matches[1] as $cmd_id) {
            if (is_numeric($cmd_id)) {
                $cmd = cmd::byId($cmd_id);
                if (is_object($cmd) && $cmd->getType() == 'info') {
                    $value .= '#' . $cmd_id . '#';
                    break;
                }
            }
        }
        $info->setValue($value);
        $info->save();
        $info->event($info->execute());


        // external
        $info = $this->getCmd(null, 'temperature_outdoor');
        if (!is_object($info)) {
            $info = new windowsCmd();
            $info->setIsVisible(1);
            $info->setIsHistorized(1);
            $info->setName(__('Temperature extérieure', __FILE__));
        }
        $info->setLogicalId('temperature_outdoor');
        $info->setEqLogic_id($this->getId());
        $info->setType('info');
        $info->setSubType('numeric');
        $info->setUnite('°C');
        $value = '';
        preg_match_all("/#([0-9]*)#/", $this->getConfiguration('temperature_outdoor'), $matches);
        foreach ($matches[1] as $cmd_id) {
            if (is_numeric($cmd_id)) {
                $cmd = cmd::byId($cmd_id);
                if (is_object($cmd) && $cmd->getType() == 'info') {
                    $value .= '#' . $cmd_id . '#';
                    break;
                }
            }
        }
        $info->setValue($value);
        $info->setDisplay('generic_type', 'THERMOSTAT_TEMPERATURE_OUTDOOR');
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
    }

    /*     * **********************Getteur Setteur*************************** */
}
