<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
// Déclaration des variables obligatoires
$plugin = plugin::byId('windows');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">
    <!-- Page d'accueil du plugin -->
    <div class="col-xs-12 eqLogicThumbnailDisplay">
        <legend><i class="fas fa-cog"></i> {{Gestion}}</legend>
        <!-- Boutons de gestion du plugin -->
        <div class="eqLogicThumbnailContainer">
            <div class="cursor eqLogicAction logoPrimary" data-action="add">
                <i class="fas fa-plus-circle"></i>
                <br>

                <span>{{Ajouter}}</span>
            </div>
            <div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
                <i class="fas fa-wrench"></i>
                <br>

                <span>{{Configuration}}</span>
            </div>
        </div>
        <legend><i class="fas fa-table"></i> {{Mes équipements}}</legend>
        <!-- Champ de recherche -->
        <div class="input-group" style="margin:5px;">
			<input class="form-control roundedLeft" placeholder="{{Rechercher}}" id="in_searchEqlogic"/>
			<div class="input-group-btn">
				<a id="bt_resetSearch" class="btn roundedRight" style="width:30px"><i class="fas fa-times"></i></a>
			</div>
		</div>
		<!-- Liste des équipements du plugin -->
        <div class="eqLogicThumbnailContainer">
            <?php
            foreach ($eqLogics as $eqLogic) {
                $opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
                echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
                echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
                echo '<br>';
                echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
                echo '</div>';
            }
            ?>
        </div>
    </div> <!-- /.eqLogicThumbnailDisplay -->

	<!-- Page de présentation de l'équipement -->
	<div class="col-xs-12 eqLogic" style="display: none;">
		<!-- barre de gestion de l'équipement -->
		<div class="input-group pull-right" style="display:inline-flex;">
			<span class="input-group-btn">
				<!-- Les balises <a></a> sont volontairement fermées à la ligne suivante pour éviter les espaces entre les boutons. Ne pas modifier -->
				<a class="btn btn-sm btn-default eqLogicAction roundedLeft" data-action="configure"><i class="fas fa-cogs"></i><span class="hidden-xs"> {{Configuration avancée}}</span>
				</a><a class="btn btn-sm btn-default eqLogicAction" data-action="copy"><i class="fas fa-copy"></i><span class="hidden-xs">  {{Dupliquer}}</span>
				</a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}
				</a><a class="btn btn-sm btn-danger eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}
				</a>
			</span>
		</div>
		<!-- Onglets -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fas fa-arrow-circle-left"></i></a></li>
			<li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i><span class="hidden-xs"> {{Équipement}}</span></a></li>
            <li role="presentation"><a href="#sondetab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-thermometer-empty"></i><span class="hidden-xs"> {{Sondes}}</span></a></li>
            <li role="presentation"><a href="#configureWindowstab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas jeedom-fenetre-ouverte"></i><span class="hidden-xs"> {{Ouvertures}}</span></a></li>
            <li role="presentation"><a href="#actiontab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-list-alt"></i><span class="hidden-xs"> {{Actions}}</span></a></li>
            <li role="presentation"><a href="#commandtab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-list"></i><span class="hidden-xs"> {{Commandes}}</span></a></li>
        </ul>
        <div class="tab-content">
   			<!-- Onglet de configuration de l'équipement -->
            <div role="tabpanel" class="tab-pane active" id="eqlogictab">
                <!-- Partie gauche de l'onglet "Equipements" -->
				<!-- Paramètres généraux de l'équipement -->
                <br>
                <div class="row">
                    <div class="col-lg-7">
                        <form class="form-horizontal">
                            <fieldset>                        
                                <legend><i class="fas fa-wrench"></i> {{Général}}</legend>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{Nom de l'équipement}}</label>
                                    <div class="col-xs-11 col-sm-7">
                                        <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;"/>
                                        <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement}}"/>
                                    </div>
                                </div>                       
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{Objet parent}}</label>
                                    <div class="col-xs-11 col-sm-7">
                                        <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
                                            <option value="">{{Aucun}}</option>
                                            <?php
                                            $options = '';
                                            foreach ((jeeObject::buildTree(null, false)) as $object) {
                                                $options .= '<option value="' . $object->getId() . '">' . str_repeat('&nbsp;&nbsp;', $object->getConfiguration('parentNumber')) . $object->getName() . '</option>';
                                            }
                                            echo $options;
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
                                    <label class="col-sm-3 control-label">{{Options}}</label>
                                    <div class="col-xs-11 col-sm-7">
                                        <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked />{{Activer}}</label>
                                        <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked />{{Visible}}</label>
                                    </div>
                                </div>                                                                                                                       
                            </fieldset>
                        </form>
                    </div>
                </div>
                <hr>
            </div><!-- /.tabpanel #eqlogictab-->

            <!-- Onglet Sonde -->
            <div role="tabpanel" class="tab-pane" id="sondetab">
                <br>
                <div class="row">
                    <div class="col-lg-7">
                        <form class="form-horizontal">
                            <fieldset>
                                <legend><i class="fas fa-thermometer-empty" aria-hidden="true"></i> {{Sonde de température}}</legend>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{Température extérieure}}</label>
                                    <div class="col-xs-11 col-sm-7">
                                        <div class="input-group">
                                            <input type="text" class="eqLogicAttr form-control"
                                                data-l1key="configuration" data-l2key="temperature_outdoor" data-concat="1" />
                                            <span class="input-group-btn">
                                                <a class="btn btn-default listCmdInfo">
                                                    <i class="fas fa-list-alt"></i>
                                                </a>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{Température intérieure}}</label>
                                    <div class="col-xs-11 col-sm-7">
                                        <div class="input-group">
                                            <input type="text" class="eqLogicAttr form-control"
                                                data-l1key="configuration" data-l2key="temperature_indoor" data-concat="1" />
                                            <span class="input-group-btn">
                                                <a class="btn btn-default listCmdInfo">
                                                    <i class="fas fa-list-alt"></i>
                                                </a>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{Présence (optionel)}}</label>
                                    <div class="col-xs-11 col-sm-7">
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
                                    <label class="col-sm-3 control-label">{{ Consigne Thermostat (optionel)}}</label>
                                    <div class="col-xs-11 col-sm-7">
                                        <div class="input-group">
                                            <input type="text" class="eqLogicAttr form-control tooltips"
                                                data-l1key="configuration" data-l2key="consigne" data-concat="1" />
                                            <span class="input-group-btn">
                                                <a class="btn btn-default listCmdInfo">
                                                    <i class="fa fa-list-alt"></i>
                                                </a>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{ Tempérture maxi (optionel)}}</label>
                                    <div class="col-xs-11 col-sm-7">
                                        <div class="input-group">
                                            <input type="text" class="eqLogicAttr form-control tooltips"
                                                data-l1key="configuration" data-l2key="temperature_maxi" data-concat="1" />
                                            <span class="input-group-btn">
                                                <a class="btn btn-default listCmdInfo">
                                                    <i class="fa fa-list-alt"></i>
                                                </a>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label">{{ Tempérture mini (optionel)}}</label>
                                    <div class="col-xs-11 col-sm-7">
                                        <div class="input-group">
                                            <input type="text" class="eqLogicAttr form-control tooltips"
                                                data-l1key="configuration" data-l2key="temperature_mini" data-concat="1" />
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
                                    <label class="col-sm-3 control-label">{{Température été (°C)}}</label>
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
                                    <label class="col-sm-3 control-label">{{Notifier}}</label>
                                    <div class="col-sm-2">							
                                        <input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="notifyifko"/>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
                <hr>
            </div><!-- /.tabpanel #sondetab-->

            <div role="tabpanel" class="tab-pane" id="configureWindowstab">
            <br />
                
                <a class="btn btn-success addAction pull-right" 
                    id="bt_addWindowEqLogic"
                    data-type="failureActuator" 
                    style="position: relative;top: -7px;">
                    <i class="fas fa-plus-circle"></i> {{Ajouter une ouverture}}
                </a>
                <br />
                <br />    
            
                    <form class="form-horizontal">
                        <fieldset>                                
                            <legend><i class="fas jeedom-fenetre-ouverte" aria-hidden="true"></i> {{Ouvertures}}                                
                            </legend>                            
                            <div class="row">
                                <div class="col-lg-7">
                                    <div id="div_confWindows"></div>
                                </div>
                            </div>
                        </fieldset>
                    </form>                  
                <hr>
            </div><!-- /.tabpanel #configureWindowstab-->

            <!-- Onglet Action -->
            <div role="tabpanel" class="tab-pane" id="actiontab">
            <br />
                
                <a class="btn btn-success addAction pull-right" id="bt_addActionEqLogic"
                    data-type="failureActuator" 
                    style="position: relative;top: -7px;">
                    <i class="fas fa-plus-circle"></i> {{Ajouter une action}}
                </a>
                <br />
                <br />

                <div class="alert-info bg-success">
                    A mettre dans <b>Titre</b> ou dans <b>Message</b> pour y récupérer la valeur</br>
                    <b>#name#</b> = Nom de l'objet</br>
                    <b>#message#</b> = NomMessage à afficher.</br>
                </div>
                <br />
                
                <form class="form-horizontal">
                    <div id="div_confActions"></div>
                </form>
            </div>

			<!-- Onglet des commandes de l'équipement -->
            <div role="tabpanel" class="tab-pane" id="commandtab">
                <br/>
                <div class="table-responsive">
                    <table id="table_cmd" class="table table-bordered table-condensed">
                        <thead>
                            <tr>                                
                                <th>{{Nom}}</th>
                                <th>{{Options}}</th>
                                <th>{{Action}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div><!-- /.tabpanel #commandtab-->
        </div><!-- /.tab-content -->
	</div><!-- /.eqLogic -->
</div><!-- /.row row-overflow -->

<!-- Inclusion du fichier javascript du plugin (dossier, nom_du_fichier, extension_du_fichier, id_du_plugin) -->
<?php include_file('desktop', 'windows', 'js', 'windows');?>
<!-- Inclusion du fichier javascript du core - NE PAS MODIFIER NI SUPPRIMER -->
<?php include_file('core', 'plugin.template', 'js');?>
