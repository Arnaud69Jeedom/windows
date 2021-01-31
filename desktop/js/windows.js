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

$("#table_cmd").sortable({
  axis: "y",
  cursor: "move",
  items: ".cmd",
  placeholder: "ui-state-highlight",
  tolerance: "intersect",
  forcePlaceholderSize: true
});
/*
 * Fonction pour l'ajout de commande, appellé automatiquement par plugin.template
 */
function addCmdToTable(_cmd) {
  if (!isset(_cmd)) {
    var _cmd = {
      configuration: {}
    };
  }
  if (!isset(_cmd.configuration)) {
    _cmd.configuration = {};
  }
  var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
  tr += '<td>';
  tr += '<span class="cmdAttr" data-l1key="id" style="display:none;"></span>';
  tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" style="width : 140px;" placeholder="{{Nom}}">';
  tr += '</td>';

  tr += '<td>';
  tr += '<span><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isHistorized" /> {{Historiser}}<br/></span>';
  tr += '<span><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" /> {{Affichage}}<br/></span>';
  tr += '</td>';
  tr += '<td>';

  if (is_numeric(_cmd.id)) {
    tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fa fa-cogs"></i></a> ';
    tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
  }
  tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i>';
  tr += '</td>';
  tr += '</tr>';
  $('#table_cmd tbody').append(tr);
  $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
  if (isset(_cmd.type)) {
    $('#table_cmd tbody tr:last .cmdAttr[data-l1key=type]').value(init(_cmd.type));
  }
  jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType));
}


$(".eqLogic").delegate(".listCmdInfo", 'click', function () {
  var el = $(this).closest('.form-group').find('.eqLogicAttr');
  jeedom.cmd.getSelectModal({
    cmd: {
      type: 'info'
    }
  }, function (result) {
    if (el.attr('data-concat') == 1) {
      el.atCaret('insert', result.human);
    } else {
      el.value(result.human);
    }
  });
});

// ***** Windows ****************
/**
 * Bouton Ajout d'une ouverture
 */
$('#bt_addWindowEqLogic').on('click', function () {
  addConfWindows({});
});

$('#bt_addWindowCmd').on('click', function() {
  addCmdToTable({
    configuration: {
      period: 1
    }
  });
});

$("#div_confWindows").delegate('.bt_removeConfWindow', 'click', function () {
  $(this).closest('.confWindow').remove();
});

function addConfWindows(_window) {
  if (!isset(_window)) {
    _window = {};
  }
  console.log("addConfWindows", _window);
  var div = '<div class="confWindow ' + $('.eqLogicAttr[data-l1key=configuration][data-l2key=window]').value() + '">';

  div += '<div class="form-group">';
  div += '<label class="col-sm-2 control-label">{{Ouverture}}</label>';
  div += '<div class="col-sm-9">';
  div += '<div class="input-group">';
  div += '<input type="text" class="eqLogicAttr form-control confWindowAttr tooltips" data-l1key="configuration" data-l2key="window"  data-concat="1"/>';
  div += '<span class="input-group-btn">';
  div += '<a class="btn btn-default listCmdInfo"><i class="fa fa-list-alt"></i></a>';
  div += '</span>';
  div += '</div>';
  div += '</div>';
  div += '<div class="col-sm-1">';
  div += '<i class="fa fa-minus-circle pull-right cursor bt_removeConfWindow"></i>';
  div += '</div>';
  div += '</div>';

  div += '</div>';
  $('#div_confWindows').append(div);
  $('#div_confWindows').find('.confWindow:last').setValues(_window, '.confWindowAttr');
}


// **** Action ************
/**
 * Bouton Ajout d'une action
 */
$('#bt_addActionEqLogic').on('click', function () {
  addConfActions({});
});

$("#div_confActions").delegate('.bt_removeConAction', 'click', function () {
  $(this).closest('.confAction').remove();
});

function addConfActions(_action) {
  if (!isset(_action)) {
    _action = {};
  }
  console.log("addConfActions", _action);
  var div = '<div class="confAction ' + $('.eqLogicAttr[data-l1key=configuration][data-l2key=window]').value() + '">';

  div += '<div class="form-group">';
  div += '<label class="col-sm-2 control-label">{{Action}}</label>';
  div += '<div class="col-sm-9">';
  div += '<div class="input-group">';
  div += '<input type="text" class="eqLogicAttr form-control confActionAttr tooltips" data-l1key="configuration" data-l2key="action"  data-concat="1"/>';
  div += '<span class="input-group-btn">';
  div += '<a class="btn btn-default listCmdInfo"><i class="fa fa-list-alt"></i></a>';
  div += '</span>';
  div += '</div>';
  div += '</div>';
  div += '<div class="col-sm-1">';
  div += '<i class="fa fa-minus-circle pull-right cursor bt_removeConfAction"></i>';
  div += '</div>';
  div += '</div>';

  div += '</div>';
  $('#div_confActions').append(div);
  $('#div_confActions').find('.confAtion:last').setValues(_action, '.confActionAttr');
}




function saveEqLogic(_eqLogic) {
  if (!isset(_eqLogic.configuration)) {
    _eqLogic.configuration = {};
  }
  _eqLogic.configuration.confWindow = $('#div_confWindows .confWindow').getValues('.confWindowAttr');

  console.log('saveEqLogic:', _eqLogic);
  return _eqLogic;
}

function printEqLogic(_eqLogic) {
  console.log('printEqLogic:', _eqLogic);

  $('#div_confWindows').empty();
  if (isset(_eqLogic.configuration)) {
    if (isset(_eqLogic.configuration.confWindow)) {
      for (var i in _eqLogic.configuration.confWindow) {
        console.log("printEqLogic.addConfWindows", _eqLogic.configuration.confWindow[i]);
        addConfWindows(_eqLogic.configuration.confWindow[i]);
      }
    }
  }
}

// Sondes triables
$("#div_confWindows").sortable({
  axis: "y",
  cursor: "move",
  items: ".confWindow",
  placeholder: "ui-state-highlight",
  tolerance: "intersect",
  forcePlaceholderSize: true
});
