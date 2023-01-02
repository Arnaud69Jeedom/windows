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
include_file('core', 'authentification', 'php');
if (!isConnect()) {
  include_file('desktop', '404', 'php');
  die();
}
?>


<form class="form-horizontal parametre_app">

  <fieldset>
    <legend><i class="fas fa-thermometer-empty" aria-hidden="true"></i> {{Sonde de température}}</legend>
    <div class="form-group">
      <label class="col-sm-3 control-label">{{Température extérieure}}</label>
      <div class="col-xs-11 col-sm-7">
        <div class="input-group">
          <input type="text" class="eqLogicAttr configKey form-control" data-l1key="temperature_outdoor" />
          <span class="input-group-btn">
            <a class="btn btn-default listCmdInfo">
              <i class="fas fa-list-alt"></i>
            </a>
          </span>
        </div>
      </div>
    </div>
  </fieldset>

  <fieldset>
    <legend><i class="fas jeedom-mouvement"></i> {{Sonde de présence}} </legend>
    <div>
      <u>Gestion de la présence</u> :
      <ul>
        <li>Si non renseigné : Toujours vérifié l'état des ouvertures</li>
        <li>Si renseigné : Vérification uniquement si une présence est indiquée</li>
      </ul>
    </div>

    <div class="form-group">
      <label class="col-sm-3 control-label">{{Présence (optionnel)}}

      </label>
      <div class="col-xs-11 col-sm-7">
        <div class="input-group">
          <input type="text" class="eqLogicAttr configKey form-control tooltips" data-l1key="presence" />
          <span class="input-group-btn">
            <a class="btn btn-default listCmdInfo">
              <i class="fa fa-list-alt"></i>
            </a>
          </span>
        </div>
      </div>
    </div>
  </fieldset>

  <fieldset>
    <legend><i class="fas fa-sun" aria-hidden="true"></i> {{Température Saison}}</legend>

    <u>Optionnel</u> : Rechercher la saison par rapport à la température minimum et maximum prévue dans la journée <br />
    Comparer Température maxi en hiver, et Température maxi en été <br />
    Autrement les saisons seront calculées par rapport aux dates

    <div class="form-group">
      <label class="col-sm-3 control-label">{{ Température maxi}}</label>
      <div class="col-xs-11 col-sm-7">
        <div class="input-group">
          <input type="text" class="eqLogicAttr configKey form-control tooltips" data-l1key="temperature_maxi" />
          <span class="input-group-btn">
            <a class="btn btn-default listCmdInfo">
              <i class="fa fa-list-alt"></i>
            </a>
          </span>
        </div>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 control-label">{{Température hiver (°C)}}</label>
      13°C recommandée
      <div class="col-sm-2">
        <div class="input-group">
          <input type="text" class="eqLogicAttr configKey form-control tooltips" placeholder="13" data-l1key="temperature_winter" />
        </div>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 control-label">{{Température été (°C)}}</label>
      25°C recommandée
      <div class="col-sm-2">
        <div class="input-group">
          <input type="text" class="eqLogicAttr configKey form-control tooltips" placeholder="25" data-l1key="temperature_summer" />
        </div>
      </div>
    </div>

  </fieldset>
</form>


<script>
  $(".parametre_app").delegate(".listCmdInfo", 'click', function() {
    console.log('eqLgic listCmdInfo');
    var el = $(this).closest('.form-group').find('.eqLogicAttr');

    console.log('el=', el);

    jeedom.cmd.getSelectModal({
      cmd: {
        type: 'info'
      }
    }, function(result) {
      console.log('result:', result);

      if (el.attr('data-concat') == 1) {
        el.atCaret('insert', result.human);
      } else {
        el.value(result.human);
      }
    });
  });
</script>