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
        $motifType = $eqLogic->getCmd(null, 'counter');
        if (is_object($motifType)) {
            $motifType->setName(__('durée', __FILE__));
            $motifType->setLogicalId('duration');
            
            log::add('windows', 'debug', '------ rename duration from'.$eqLogic->getHumanName().' to '.$motifType->getName()); 
            $motifType->save(true);
        }else{
            log::add('windows', 'debug', '------ motif duration not found in '.$eqLogic->getHumanName());
        }
    }
}


// Fonction exécutée automatiquement après la suppression du plugin
function windows_remove() {
    
}

?>
