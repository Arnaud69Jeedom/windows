<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('windows');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">
    <div class="col-xs-12 eqLogicThumbnailDisplay" style="padding-top: 5px; height: 420.25px; overflow: hidden auto;">
        <legend>
            <i class="fa fa-cog"></i>
            {{Gestion}}
        </legend>
        <div class="eqLogicThumbnailContainer">
            <div class="cursor eqLogicAction" data-action="add"
                style="text-align: center; background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;">
                <i class="fa fa-plus-circle" style="font-size : 6em;color:#00A9EC;"></i>
                <br>
                <span
                    style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#00A9EC">{{Ajouter}}</span>
            </div>
            <div class="cursor eqLogicAction" data-action="gotoPluginConf"
                style="text-align: center; background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;">
                <i class="fa fa-wrench" style="font-size : 6em;color:#767676;"></i>
                <br>
                <span
                    style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676">{{Configuration}}</span>
            </div>
        </div>
        <legend>
            <i class="fa fa-table"></i> {{Mes équipements}}
        </legend>
        <input class="form-control" placeholder="{{Rechercher}}" id="in_searchEqlogic">
        <div class="eqLogicThumbnailContainer">
            <?php
foreach ($eqLogics as $eqLogic) {
                $opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
                echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="text-align: center; background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;' . $opacity . '" >';
                echo '<img src="' . $plugin->getPathImgIcon() . '" height="105" width="95" />';
                echo "<br>";
                echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;">' . $eqLogic->getHumanName(true, true) . '</span>';
                echo '</div>';
            }
?>
        </div>
    </div>

    <div class="col-xs-12 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
        <a class="btn btn-success eqLogicAction pull-right" data-action="save">
            <i class="fa fa-check-circle"></i>
            {{Sauvegarder}}</a>
        <a class="btn btn-danger eqLogicAction pull-right" data-action="remove">
            <i class="fa fa-minus-circle"></i>
            {{Supprimer}}</a>
        <a class="btn btn-default eqLogicAction pull-right" data-action="configure">
            <i class="fa fa-cogs"></i>
            {{Configuration avancée}}</a>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation">
                <a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab"
                    data-action="returnToThumbnailDisplay">
                    <i class="fa fa-arrow-circle-left"></i>
                </a>
            </li>
            <li role="presentation" class="active">
                <a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab">
                    <i class="fa fa-tachometer"></i> {{Equipement}}
                </a>
            </li>
            <li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i
                        class="fa fa-list-alt"></i> {{Commandes}}</a></li>
        </ul>
        <div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
            <div role="tabpanel" class="tab-pane active" id="eqlogictab">
                <br />
                <form class="form-horizontal">
                    <fieldset>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{Nom de l'équipement}}</label>
                            <div class="col-sm-3">
                                <input type="text" class="eqLogicAttr form-control" style="display: none;"
                                    data-l1key="id" />
                                <input type="text" class="eqLogicAttr form-control"
                                    placeholder="{{Nom de l'équipement template}}" data-l1key="name" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{Objet parent}}</label>
                            <div class="col-sm-3">
                                <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
                                    <option value="">{{Aucun}}</option>
                                    <?php
foreach (jeeObject::all() as $object) {
    echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
}
?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{Catégorie}}</label>
                            <div class="col-sm-9">
                                <?php
                    foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                        echo '<label class="checkbox-inline">';
                        echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
                        echo '</label>';
                    }
                  ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-9">
                                <label class="checkbox-inline">
                                    <input type="checkbox" class="eqLogicAttr" data-l1key="isEnable"
                                        checked />{{Activer}}
                                </label>
                                <label class="checkbox-inline">
                                    <input type="checkbox" class="eqLogicAttr" data-l1key="isVisible"
                                        checked />{{Visible}}
                                </label>
                            </div>
                        </div>
                    </fieldset>
                </form>

                <form class="form-horizontal">
                    <fieldset>
                        <legend>
                            <i class="fa fa-thermometer-empty" aria-hidden="true"></i> {{Sonde de température}}
                        </legend>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">{{Température extérieure}}</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="text" class="eqLogicAttr form-control tooltips"
                                        data-l1key="configuration" data-l2key="temperature_outdoor" data-concat="1" />
                                    <span class="input-group-btn">
                                        <a class="btn btn-default listCmdInfo">
                                            <i class="fa fa-list-alt"></i>
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">{{Température intérieure}}</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="text" class="eqLogicAttr form-control tooltips"
                                        data-l1key="configuration" data-l2key="temperature_indoor" data-concat="1" />
                                    <span class="input-group-btn">
                                        <a class="btn btn-default listCmdInfo">
                                            <i class="fa fa-list-alt"></i>
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">{{Présence}}</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="text" class="eqLogicAttr form-control tooltips"
                                        data-l1key="configuration" data-l2key="presence" data-concat="1" />
                                    <span class="input-group-btn">
                                        <a class="btn btn-default listCmdInfo">
                                            <i class="fa fa-list-alt"></i>
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">{{Thermostat}}</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="text" class="eqLogicAttr form-control tooltips"
                                        data-l1key="configuration" data-l2key="thermostat" data-concat="1" />
                                    <span class="input-group-btn">
                                        <a class="btn btn-default listCmdInfo">
                                            <i class="fa fa-list-alt"></i>
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-sm-2 control-label">{{Température hiver}}</label>
                            <div class="col-sm-2">
                                <div class="input-group">
                                    <input type="text" class="eqLogicAttr form-control tooltips"
                                        data-l1key="configuration" data-l2key="temperature_winter" data-concat="1" />
                                </div>
                            </div>

                            <label class="col-sm-1 control-label">{{Durée}}</label>
                            <div class="col-sm-2">
                                <div class="input-group">
                                    <input type="text" class="eqLogicAttr form-control tooltips"
                                        data-l1key="configuration" data-l2key="duration_winter" data-concat="1" />
                                </div>
                            </div>

                            <label class="col-sm-1 control-label">{{Seuil}}</label>
                            <div class="col-sm-2">
                                <div class="input-group">
                                    <input type="text" class="eqLogicAttr form-control tooltips"
                                        data-l1key="configuration" data-l2key="threshold_winter" data-concat="1" />
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">{{Température été}}</label>
                            <div class="col-sm-2">
                                <div class="input-group">
                                    <input type="text" class="eqLogicAttr form-control tooltips"
                                        data-l1key="configuration" data-l2key="temperature_summer" data-concat="1" />
                                </div>
                            </div>

                            <label class="col-sm-1 control-label">{{Durée}}</label>
                            <div class="col-sm-2">
                                <div class="input-group">
                                    <input type="text" class="eqLogicAttr form-control tooltips"
                                        data-l1key="configuration" data-l2key="duration_summer" data-concat="1" />
                                </div>
                            </div>

                            <label class="col-sm-1 control-label">{{Seuil}}</label>
                            <div class="col-sm-2">
                                <div class="input-group">
                                    <input type="text" class="eqLogicAttr form-control tooltips"
                                        data-l1key="configuration" data-l2key="threshold_summer" data-concat="1" />
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-2 control-label">{{Notifier}}</label>
                            <div class="col-sm-2">							
                                <input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="notifyifko"/>
                            </div>                            
                        </div>

                    </fieldset>
                </form>

                <legend>
                    <i class="icon jeedom-fenetre-ferme"></i> {{Sonde fenêtre}}
                    <a class="btn btn-default btn-xs pull-right" id="bt_addWindowEqLogic">
                        <i class="fa fa-plus">{{Ajouter}}</i>
                    </a>
                </legend>
                
                <form class="form-horizontal">
                    <div id="div_confWindows"></div>
                </form>
            </div>

            <div role="tabpanel" class="tab-pane" id="commandtab">
                <br>
                <table id="table_cmd" class="table table-bordered table-condensed">
                    <thead>
                        <tr>
                            <th>{{Nom}}</th>
                            <th>{{Configuration}}</th>
                            <th>{{Action}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_file('desktop', 'windows', 'js', 'windows');?>
<?php include_file('core', 'plugin.template', 'js');?>