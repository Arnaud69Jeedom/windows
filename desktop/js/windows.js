
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

 
/* Permet la réorganisation des commandes dans l'équipement */
$("#table_cmd").sortable({
  axis: "y",
  cursor: "move",
  items: ".cmd",
  placeholder: "ui-state-highlight",
  tolerance: "intersect",
  forcePlaceholderSize: true
});

/* Fonction permettant l'affichage des commandes dans l'équipement */
function addCmdToTable(_cmd) {
  if (!isset(_cmd)) {
    var _cmd = {configuration: {}};
  }
  if (!isset(_cmd.configuration)) {
    _cmd.configuration = {};
  }
  var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
  tr += '<td>';
 
  tr += '<input class="cmdAttr form-control input-sm" data-l1key="id" style="display : none;">';
  tr += '<input class="cmdAttr form-control input-sm" data-l1key="type" style="display : none;">';
  tr += '<input class="cmdAttr form-control input-sm" data-l1key="subType" style="display : none;">';

  tr += '<input class="cmdAttr form-control input-sm" data-l1key="name" placeholder="{{Nom de la commande}}">';
  tr += '</td>';
  tr += '<td>';
  tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isVisible" checked/>{{Afficher}}</label></span>';
  tr += '<span><label class="checkbox-inline"><input type="checkbox" class="cmdAttr checkbox-inline" data-l1key="isHistorized" checked/>{{Historiser}}</label></span>';
  tr += '</td>';
  tr += '<td>';
  if (is_numeric(_cmd.id)) {
     tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fas fa-cogs"></i></a> ';
     tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fas fa-rss"></i> Tester</a>';
  }
  tr += '</tr>';
  $('#table_cmd tbody').append(tr);
  var tr = $('#table_cmd tbody tr').last();
  jeedom.eqLogic.builSelectCmd({
    id:  $('.eqLogicAttr[data-l1key=id]').value(),
    filter: {type: 'info'},
    error: function (error) {
      $('#div_alert').showAlert({message: error.message, level: 'danger'});
    },
    success: function (result) {
      tr.find('.cmdAttr[data-l1key=value]').append(result);
      tr.setValues(_cmd, '.cmdAttr');
      jeedom.cmd.changeType(tr, init(_cmd.subType));
    }
  });
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
  var div = '<div class="confWindow">';

  div += '<div class="form-group">';
  div += '<label class="col-sm-3 control-label">{{Ouverture}}</label>';
  div += '<div class="col-sm-7">';
  div += '<div class="input-group">';
  div += '<span class="input-group-btn">';
  div += '<a class="btn btn-default bt_removeConfWindow roundedLeft" data-type=""><i class="fas fa-minus-circle"></i></a>';
  div += '</span>';
  div += '<input class="eqLogicAttr form-control expressionAttr tooltips" data-l1key="cmd" data-type="window"/>';
  div += '<span class="input-group-btn">';
  div += '<a class="btn btn-default listCmdInfo"><i class="fa fa-list-alt"></i></a>';
  div += '</span>';
  div += '</div>';
  div += '</div>';
  div += '<div class="col-sm-1">';
  div += '</div>';
  div += '</div>';

  div += '</div>';
  $('#div_confWindows').append(div);
  $('#div_confWindows').find('.confWindow:last').setValues(_window, '.expressionAttr');
}


// **** Action ************
/**
 * Bouton Ajout d'une action
 */
$('#bt_addActionEqLogic').on('click', function () {
  addConfActions({});
});

$("#div_confActions").delegate('.bt_removeConfAction', 'click', function () {
  $(this).closest('.confAction').remove();
});

function addConfActions(_action) {
  if (!isset(_action)) {
    _action = {};
  }
  if (!isset(_action.options)) {
    _action.options = {}
  }
  // console.log("addConfActions", _action);
  var div = '<div class="confAction">';
  div += '<div class="form-group">';
  div += '<label class="col-sm-1 control-label">{{Action}}</label>';
  div += '<div class="col-sm-4">';
  div += '<div class="input-group">';
  div += '<span class="input-group-btn">';
  div += '<a class="btn btn-default bt_removeConfAction btn-sm roundedLeft" data-type=""><i class="fas fa-minus-circle"></i></a>';
  div += '</span>';
  div += '<input class="expressionAttr form-control input-sm cmdAction" data-l1key="cmd" data-type="action" />';
  div += '<span class="input-group-btn">';
  div += '<a class="btn btn-default btn-sm listAction" data-type="confAction" title="{{Sélectionner un mot-clé}}"><i class="fas fa-tasks"></i></a>';
  div += '<a class="btn btn-default btn-sm listCmdAction roundedRight" data-type="confAction" title="{{Sélectionner la commande}}"><i class="fas fa-list-alt"></i></a>';
  div += '</span>';
  div += '</div>';
  div += '</div>';
  div += '<div class="col-sm-7 actionOptions">';
  div += jeedom.cmd.displayActionOption(init(_action.cmd, ''), _action.options);
  div += '</div>';
  div += '</div>';
  $('#div_confActions').append(div);
  $('#div_confActions').find('.confAction:last').setValues(_action, '.expressionAttr');
}

$(".eqLogic").delegate(".listCmdAction", 'click', function () {
    //console.log("--------- listCmdAction");
    var type = $(this).attr('data-type');
    var el = $(this).closest('.' + type).find('.expressionAttr[data-l1key=cmd]');

    jeedom.cmd.getSelectModal({cmd: {type: 'action'}}, function (result) {
        el.value(result.human);
        jeedom.cmd.displayActionOption(el.value(), '', function (html) {
          el.closest('.' + type).find('.actionOptions').html(html);
          taAutosize();
        });
    });
  });

$(".eqLogic").delegate(".listAction", 'click', function () {
	//console.log("--------- listAction");
  var type = $(this).attr('data-type');
  var el = $(this).closest('.' + type).find('.expressionAttr[data-l1key=cmd]');

  jeedom.getSelectActionModal({}, function (result) {
    el.value(result.human);
    jeedom.cmd.displayActionOption(el.value(), '', function (html) {
      el.closest('.' + type).find('.actionOptions').html(html);
      taAutosize();
    });
  });
});


/*** Save ***/
function saveEqLogic(_eqLogic) {
  if (!isset(_eqLogic.configuration)) {
    _eqLogic.configuration = {};
  }
  _eqLogic.configuration.window = $('#div_confWindows .confWindow').getValues('.expressionAttr');
  _eqLogic.configuration.action = $('#div_confActions .confAction').getValues('.expressionAttr');

  console.log('saveEqLogic:', _eqLogic);
  return _eqLogic;
}

function printEqLogic(_eqLogic) {
  console.log('printEqLogic:', _eqLogic);

  $('#div_confWindows').empty();
  if (isset(_eqLogic.configuration)) {
    if (isset(_eqLogic.configuration.window)) {
      for (var i in _eqLogic.configuration.window) {
        console.log("printEqLogic.addConfWindows", _eqLogic.configuration.window[i]);
        addConfWindows(_eqLogic.configuration.window[i]);
      }
    }
  }

  $('#div_confActions').empty();
  if (isset(_eqLogic.configuration)) {
    if (isset(_eqLogic.configuration.action)) {
      for (var i in _eqLogic.configuration.action) {
        console.log("printEqLogic.addConfActions", _eqLogic.configuration.action[i]);
        addConfActions(_eqLogic.configuration.action[i]);
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
