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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

// Fonction exécutée automatiquement après l'installation du plugin
function windows_install() {

}

// Fonction exécutée automatiquement après la mise à jour du plugin
function windows_update() {
    log::add('windows','debug','=============  mise à jour des equipements suite à update plugin =============');

    foreach (eqLogic::byType('windows', true) as $eqLogic) {
        log::add('windows','debug', 'mise à jour de '.$eqLogic->getHumanName());
        
        // modif des commandes déjà renseignée
        // modif counter => duration
        $counter = $eqLogic->getCmd(null, 'counter');
        if (is_object($counter)) {
            $counter->setName(__('durée', __FILE__));
            $counter->setLogicalId('duration');
            
            log::add('windows', 'debug', '------ rename counter from'.$eqLogic->getHumanName().' to '.$counter->getName()); 
            $counter->save(true);
        } else {
            log::add('windows', 'debug', '------ modif counter not found in '.$eqLogic->getHumanName());
        }
        unset($counter);

        // message => suppression de l'unité
        $message = $eqLogic->getCmd(null, 'message');
        if (!is_object($message)) {
            log::add('windows', 'debug', '------ modif message from'.$eqLogic->getHumanName().' to '.$message->getName()); 

            $message->setUnite(null);
            $message->save();
        }
        else {
            log::add('windows', 'debug', '------ modif message not found in '.$eqLogic->getHumanName());
        }
        unset($message);

        // création de nouvelle commande
        // durationDaily
        $durationDaily = $eqLogic->getCmd(null, 'durationDaily');
        if (!is_object($durationDaily)) {
            log::add('windows', 'debug', '------ creation durationDaily from'.$eqLogic->getHumanName().' to '.$durationDaily->getName()); 

            $durationDaily = new windowsCmd();
            $durationDaily->setLogicalId('durationDaily');
            $durationDaily->setIsVisible(1);
            $durationDaily->setName(__('durée du jour', __FILE__));
            $durationDaily->setOrder(2);

            $durationDaily->setEqLogic_id($this->getId());
            $durationDaily->setType('info');
            $durationDaily->setSubType('numeric');
            $durationDaily->setGeneric_type('GENERIC_INFO');
            $durationDaily->setUnite('min');
            $durationDaily->save();
            unset($durationDaily);
        }
    }
}


// Fonction exécutée automatiquement après la suppression du plugin
function windows_remove() {
    
}

?>
