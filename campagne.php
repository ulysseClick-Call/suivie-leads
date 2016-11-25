<ol class="breadcrumb">
	<li><a href="index.php?page=">Accueil</a></li>
	<li><a href="index.php?page=campagnes">Suivi des campagnes</a></li>
	<li class="active">Campagne</li>
</ol>

<?php
	include("inc/db.inc.php");
	include("inc/fct.inc.php");
	extract($_GET);
	extract($_POST);

	if(isset($modification))
	{
		// Log
		$action = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT * FROM action WHERE id='.$idAction));
		$partenaireTmp = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT * FROM partenaire WHERE id='.$action['idPartenaire']));
		$nouveauPartenaireTmp = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT * FROM partenaire WHERE id='.$partenaire));
		$campagneTmp = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT * FROM campagne WHERE id='.$action['idCampagne']));
		$messageLog = "Modification de l'action <strong>".htmlentities(utf8_decode($partenaireTmp['nom']))."</strong> sur la campagne <strong>".htmlentities($campagneTmp['nom'])."</strong> : <br />";
		$messageLogDetail = "";

		if($action['idPartenaire'] != $partenaire) $messageLogDetail .= "partenaire : <em>".htmlentities(utf8_decode($partenaireTmp['nom']))." <span class='glyphicon glyphicon-chevron-right'></span> ".htmlentities(utf8_decode($nouveauPartenaireTmp['nom']))."</em><br />";
		if($action['canal'] != $canal) $messageLogDetail .= "canal : <em>".$action['canal']." <span class='glyphicon glyphicon-chevron-right'></span> ".$canal."</em><br />";
		if($action['codeCampagne'] != $codeCampagne) $messageLogDetail .= "codeCampagne : <em>".$action['codeCampagne']." <span class='glyphicon glyphicon-chevron-right'></span> ".$codeCampagne."</em><br />";
		if($action['objectif'] != $objectif) $messageLogDetail .= "objectif : <em>".$action['objectif']." <span class='glyphicon glyphicon-chevron-right'></span> ".$objectif."</em><br />";
		if($action['volume'] != $volume) $messageLogDetail .= "volume : <em>".$action['volume']." <span class='glyphicon glyphicon-chevron-right'></span> ".$volume."</em><br />";
		if($action['budget'] != $budget) $messageLogDetail .= "budget : <em>".$action['budget']." <span class='glyphicon glyphicon-chevron-right'></span> ".$budget."</em><br />";
		if($action['commentaires'] != utf8_decode($commentaires)) $messageLogDetail .= "commentaire : <em>".htmlentities($action['commentaires'])." <span class='glyphicon glyphicon-chevron-right'></span> ".htmlentities(utf8_decode($commentaires))."</em><br />";

		if($messageLogDetail == '') $messageLogDetail = "Aucune valeur modifi&eacute;e.";

		logAction($bdd, $utilisateur['id'], $messageLog.$messageLogDetail, "M");

		mysqli_query($bdd, 'UPDATE action SET
			idPartenaire = "'.utf8_decode($partenaire).'",
			interne = 0,
			canal = "'.utf8_decode($canal).'",
			codeCampagne = "'.trim(utf8_decode($codeCampagne)).'",
			dateDebut = "'.$dateDebut.'",
			dateFin = "'.$dateFin.'",
			objectif = "'.utf8_decode($objectif).'",
			volume = "'.utf8_decode($volume).'",
			budget = "'.str_replace(",", ".", $budget).'",
			batValide = "'.utf8_decode($batValide).'",
			commentaires = "'.utf8_decode($commentaires).'",
			dateModif = "'.date("Y-m-d H-i-s").'",
			userModif = '.$utilisateur['id'].'
			WHERE id='.$idAction);

		mysqli_query($bdd, 'UPDATE campagne SET
			dateModif = "'.date("Y-m-d H-i-s").'",
			userModif = '.$utilisateur['id'].'
			WHERE id='.$id);

		echo "<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>&times;</button><strong>OK</strong> L'action a bien été modifié.</div>";
	}
	else if(isset($nouveau) && $codeCampagne != "" && $partenaire != "")
	{
		mysqli_query($bdd, 'INSERT INTO action (idCampagne,idPartenaire,canal,codeCampagne,dateDebut,dateFin,objectif,volume,budget,batValide,commentaires,dateModif,userModif) VALUES (
			"'.utf8_decode($id).'",
			"'.utf8_decode($partenaire).'",
			"'.utf8_decode($canal).'",
			"'.utf8_decode(trim($codeCampagne)).'",
			"'.utf8_decode($dateDebut).'",
			"'.utf8_decode($dateFin).'",
			"'.utf8_decode($objectif).'",
			"'.utf8_decode($volume).'",
			"'.utf8_decode(str_replace(",", ".", $budget)).'",
			"'.utf8_decode($batValide).'",
			"'.utf8_decode($commentaires).'",
			"'.date("Y-m-d H-i-s").'",
			"'.$utilisateur['id'].'")');

		mysqli_query($bdd, 'UPDATE campagne SET
			dateModif = "'.date("Y-m-d H-i-s").'",
			userModif = '.$utilisateur['id'].'
			WHERE id='.$id);

		// Log
		$partenaire = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT * FROM partenaire WHERE id='.$partenaire));
		$campagne = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT * FROM campagne WHERE id='.$id));
		logAction($bdd, $utilisateur['id'], "Ajout d'une action pour la campagne <strong>".$campagne['nom']."</strong> : <em>".$partenaire['nom']."</em> (".$codeCampagne.", objectif : ".$objectif.", budget: ".str_replace(",", ".", $budget)."&euro;)", "A");

		echo "<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>&times;</button><strong>OK</strong> L'action a bien été créée.</div>";
	}
	else if (isset($suppression))
	{
		// Log

		$action = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT * FROM action WHERE id='.$idAction));
		$partenaire = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT * FROM partenaire WHERE id='.$action['idPartenaire']));
		$campagne = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT * FROM campagne WHERE id='.$id));

		logAction($bdd, $utilisateur['id'], "Suppression d'une action pour la campagne <strong>".htmlentities($campagne['nom'])."</strong> : <em>".htmlentities($partenaire['nom'])."</em>", "S");

		mysqli_query($bdd, 'UPDATE action SET supprime =1 WHERE id='.$idAction);

		mysqli_query($bdd, 'UPDATE campagne SET
			dateModif = "'.date("Y-m-d H-i-s").'",
			userModif = '.$utilisateur['id'].'
			WHERE id='.$id);

		echo "<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>&times;</button><strong>OK</strong> L'action a bien été supprimé. <a class='btn btn-default btn-mini' href='index.php?page=retablirAction&id=".$idAction."'><span class='glyphicon glyphicon-chevron-left'></span> Annuler</a></div>";
	}
	else if (isset($suppressionDefinitif))
	{
		// Log
		$action = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT * FROM action WHERE id='.$idAction));
		$partenaire = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT * FROM partenaire WHERE id='.$action['idPartenaire']));
		$campagne = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT * FROM campagne WHERE id='.$id));
		logAction($bdd, $utilisateur['id'], "Suppression d&eacute;finitive d'une action pour la campagne <strong>".htmlentities($campagne['nom'])."</strong> : <em>".htmlentities($partenaire['nom'])."</em>", "S");

		mysqli_query($bdd, 'DELETE FROM action WHERE id='.$idAction);

		mysqli_query($bdd, 'UPDATE campagne SET
			dateModif = "'.date("Y-m-d H-i-s").'",
			userModif = '.$utilisateur['id'].'
			WHERE id='.$id);

		echo "<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>&times;</button><strong>OK</strong> L'action a bien été supprimé définitivement.</div>";
	}
	else if (isset($retablir))
	{
		// Log
		$action = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT * FROM action WHERE id='.$idAction));
		$partenaire = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT * FROM partenaire WHERE id='.$action['idPartenaire']));
		$messageLog = "R&eacute;tablissement d'action du partenaire ".$partenaire['nom']."<br />";

		logAction($bdd, $utilisateur['id'], $messageLog, "R");

		mysqli_query($bdd, 'UPDATE action SET supprime = 0 WHERE id='.$idAction);

		echo "<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>&times;</button><strong>OK</strong> L'action a été rétablie.</div>";
	}
	else if (isset($demarrer))
	{
		// Log
		$campagneTemp = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT * FROM campagne WHERE id='.$id));

		logAction($bdd, $utilisateur['id'], "Demarrage de la campagne : ".htmlentities($campagneTemp['nom']), "U");

		mysqli_query($bdd, 'UPDATE campagne SET planMedia = 0 WHERE id='.$id);

		echo "<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>&times;</button><strong>OK</strong> La campagne a bien démarré.</div>";
	}
	else if (isset($rafraichir))
	{
		// DEBUT MODIFICATION AD 2014-09-03
		$campagne = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT * FROM campagne WHERE id='.$id));

		// Emails à retirer
		if($campagne['emailTest'])
		{
			$emailsTest = explode(",", $campagne['emailTest']);

			$reqEmail = "";
			foreach($emailsTest as $emailTest)
			{
				$reqEmail .= " AND LEADS_Email NOT LIKE '%".$emailTest."%' ";
				$reqEmailN .= " OR LEADS_Email LIKE '%".$emailTest."%' ";
			}
		}
		// FIN MODIFICATION AD 2014-09-03

		$leadsCampagne = 0;
		$leadsInternesCampagne = 0;
		$actions = mysqli_query($bdd, "SELECT * FROM action WHERE idCampagne=".$id." AND supprime=0");

		while($action = mysqli_fetch_assoc($actions))
		{
			if($action['codeCampagne'] != '')
			{
				$nbLeadsWeb = mysqli_fetch_assoc(mysqli_query($bdd, "SELECT COUNT(DISTINCT LEADS_Email) AS leads FROM lead WHERE LEADS_Campagne ='". $action['codeCampagne'] ."' ".$reqEmail." AND ".$reqTest));
				$nbLeadsWeb = $nbLeadsWeb['leads'];


                $nbLeadsMob = mysqli_fetch_assoc(mysqli_query($bdd, "SELECT COUNT(DISTINCT LEADS_Email) AS leads FROM lead WHERE ( LOCATE('LP','".$action['codeCampagne']."') > 0 AND LEADS_Campagne ='". str_replace(" LP ", " MOB ", $action['codeCampagne']) ."')".$reqEmail." AND ".$reqTest));
				$nbLeadsMob = $nbLeadsMob['leads'];

				$nbLeadsTest = mysqli_fetch_assoc(mysqli_query($bdd, "SELECT COUNT(DISTINCT LEADS_Email) AS test FROM lead WHERE (LEADS_Campagne ='". $action['codeCampagne'] ."' OR LEADS_Campagne = '".str_replace(" LP ", " MOB ", $action['codeCampagne'])."') AND (".$reqTestN." ".$reqEmailN.")")); $nbLeadsTest = $nbLeadsTest['test'];

				$leadsUniques = mysqli_fetch_assoc(mysqli_query($bdd, "SELECT COUNT(DISTINCT LEADS_Email)  AS uniques FROM lead WHERE (LEADS_Campagne ='". $action['codeCampagne'] ."' OR LEADS_Campagne = '".str_replace(" LP ", " MOB ", $action['codeCampagne'])."') ".$reqEmail." AND ".$reqTest)); $leadsUniques = $leadsUniques['uniques'];

				mysqli_query($bdd, 'UPDATE action SET leadsWeb = '.$nbLeadsWeb.' WHERE id='.$action['id']);
				mysqli_query($bdd, 'UPDATE action SET leadsMob = '.$nbLeadsMob.' WHERE id='.$action['id']);
				mysqli_query($bdd, 'UPDATE action SET leadsTest = '.$nbLeadsTest.' WHERE id='.$action['id']);
				mysqli_query($bdd, 'UPDATE action SET leadsUniques = '.$leadsUniques.' WHERE id='.$action['id']);

				if($campagne['interne'] == 1)
					$leadsInternesCampagne += ($nbLeadsWeb + $nbLeadsMob);
				else
					$leadsCampagne += ($nbLeadsWeb + $nbLeadsMob);
			}
		}

		mysqli_query($bdd, 'UPDATE campagne SET nbLeadsTmp = '.$leadsCampagne.' WHERE id='.$campagne['id']);
		mysqli_query($bdd, 'UPDATE campagne SET nbLeadsInternesTmp = '.$leadsInternesCampagne.' WHERE id='.$campagne['id']);
	}

	// Ajout d'une période
	if(isset($periode))
	{
		mysqli_query($bdd, 'INSERT INTO periode SET nom="'.utf8_decode($nomPeriode).'", commentaire="'.utf8_decode($commentairePeriode).'", debut="'.$debutPeriode.'", fin="'.$finPeriode.'", campagne="'.$id.'"');
		echo "<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>&times;</button><strong>OK</strong> La période a bien été ajoutée</div>";
	}

	// Suppression d'une période
	if(isset($supprimerPeriode))
	{
		mysqli_query($bdd, 'DELETE FROM periode WHERE id='.$supprimerPeriode);
		echo "<div class='alert alert-success'><button type='button' class='close' data-dismiss='alert'>&times;</button><strong>OK</strong> La période a bien été supprimée</div>";
	}

	$campagne = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT * FROM campagne WHERE id='.$id));
	$actions = mysqli_query($bdd, 'SELECT * FROM action WHERE supprime=0 AND interne=0 AND idCampagne='.$id);
	$actionsInternes = mysqli_query($bdd, 'SELECT * FROM action WHERE supprime=0 AND interne=1 AND idCampagne='.$id);
	$contact = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT * FROM contact WHERE id='.$campagne['interlocuteur']));
	$client = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT * FROM client WHERE id='.$campagne['client']));
	$userModif = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT * FROM utilisateur WHERE id='.$campagne['userModif']));
	$clientInfo = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT * FROM client WHERE id = '.$campagne['client']));

	$totalObjectif = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT SUM(objectif) AS obj FROM action WHERE idCampagne='.$campagne['id'].' AND supprime=0')); $totalObjectif = $totalObjectif['obj'];
	$totalBudget = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT SUM(budget) AS bdg FROM action WHERE idCampagne='.$campagne['id'].' AND supprime=0')); $totalBudget = $totalBudget['bdg'];

	$region = array();
	$regions = mysqli_query($bdd, "SELECT * FROM regions");
	while($regionTmp = mysqli_fetch_assoc($regions))
		$region[$regionTmp['id']] = $regionTmp['nomCourt'];

	$interne = array();
	$interne[0] = "<strong>Plan media</strong> le plan media est externalisé";
	$interne[1] = "<strong>Interne</strong> la campagne est gérée exclusivement en interne";
	$interne[2] = "<strong>Mixte</strong> plan media + opérations internes";

	$nomFichier = 'export/'.time().'.xlsx';

	// Emails à retirer
	if($campagne['emailTest'])
	{
		$emailsTest = explode(",", $campagne['emailTest']);

		$reqEmail = "";
		foreach($emailsTest as $emailTest)
		{
			$reqEmail .= " AND LEADS_Email NOT LIKE '%".$emailTest."%' ";
			$reqEmailN .= " OR LEADS_Email LIKE '%".$emailTest."%' ";
		}
	}
?>

<div class="page-header">
	<h1>
		Campagne <small><?= utf8_encode($campagne['nom']) ?></small>
		<? if($campagne['image']) { ?>
		<img class="bordered pull-right" src="http://www.bouygues-immobilier.net/report/suivibi2/inc/img.php?id=<?= $id ?>" height="110" />
		<? } ?>
	</h1>

	<a class="btn btn-primary col-sm-offset-10 text-left " href="index.php?page=campagnes&filtre=1&submit=1"><span class="glyphicon glyphicon-chevron-left"></span>Retour</a>


</div>
<small>Les leads affichés sur cette page sont mis à jour toutes les <strong>30</strong> minutes. Pour actualiser manuellement les leads, cliquez sur le le bouton <a class="btn btn-default btn-xs" href="index.php?page=campagne&id=<?= $campagne['id'] ?>&rafraichir"><span class="glyphicon glyphicon-refresh"></span> Rafraichir</a></small>
<br />
<br />

<? if($campagne['facture'] >= 1 && $campagne['facture'] < 5) { ?>
	<div class="alert alert-warning"><strong>ATTENTION</strong> La facture est en cours de traitement à l'adminstration des ventes, vous ne pouvez plus modifier la campagne.<br />Pour débloquer la campagne, contactez l'administration des ventes.</div>
<? } ?>

<div class="btn-toolbar">
	<div class="btn-group">
		<a class="btn btn-default" href="index.php?page=campagne&id=<?= $campagne['id'] ?>&rafraichir"><span class="glyphicon glyphicon-refresh"></span> Rafraichir</a>
		<a class="btn btn-default" href="exportCampagne.php?id=<?=$campagne['id']?>"><span class="glyphicon glyphicon-download-alt"></span> Exporter les leads</a>
		<? if($campagne['interne'] == 0 || $campagne['interne'] == 2) { ?>
			<a class="btn btn-default" href="index.php?page=formCampagne&modification&id=<?= $campagne['id'] ?>" <?= ($campagne['facture'] > 1)?('disabled="disabled"'):('') ?>><span class="glyphicon glyphicon-edit"></span> Modifier la campagne</a>
			<a class="btn btn-default" href="index.php?page=supprCampagne&id=<?= $campagne['id'] ?>" <?= ($campagne['facture'] >= 1)?('disabled="disabled"'):('') ?>><span class="glyphicon glyphicon-remove"></span> Supprimer la campagne</a>
		<? } ?>
		<? if ($campagne['planMedia'] == 1 && $campagne['brief'] == 0) { ?>
			<a class="btn btn-primary" href="index.php?page=campagne&demarrer&id=<?= $campagne['id'] ?>"><span class="glyphicon glyphicon-play"></span> Démarrer la campagne</a>
		<? } else if ($campagne['planMedia'] == 0 && $campagne['brief'] == 0 && $campagne['interne'] != 1) { ?>
			<a class="btn btn-primary" <?= ($campagne['facture'] >= 1 || $campagne['interne'] == 1)?('disabled="disabled"'):('') ?> href="index.php?page=facturationCampagne&id=<?= $campagne['id'] ?>"><span class="glyphicon glyphicon-list-alt"></span> <?= ($campagne['facture'] == 1)?('Modifier la facture'):('Générer la facture') ?></a>
		<? } ?>
	</div>
</div>

<br />

<div class="panel panel-default">
	<div class="panel-heading"><strong>Détails de la campagne</strong></div>
	<div class="panel-body">

		<dl class="dl-horizontal">
			<dt>Client</dt>
			<dd>
				<?= ($client['image'])?('<img src="http://www.bouygues-immobilier.net/report/suivibi2/inc/img.php?id='.$client['id'].'&client" height="36" />'):(utf8_encode($client['nom'])) ?>
			</dd>

			<br />

			<dt>Nom</dt>
			<dd><?= utf8_encode($campagne['nom']) ?></dd>

			<dt>Descriptif</dt>
			<dd><?= utf8_encode(($campagne['description'])?($campagne['description']):('-')) ?></dd>

			<dt>Type</dt>
			<dd><?= $interne[$campagne['interne']] ?></dd>

			<dt>Fil rouge</dt>
			<dd><?= ($campagne['filRouge'] == 1)?('Oui'):('Non') ?></dd>

			<dt>Cible principale</dt>
			<dd><?= utf8_encode(($campagne['cible'])?($campagne['cible']):('-')) ?></dd>

			<dt>Région</dt>
			<dd><?= utf8_encode($region[$campagne['regionBI']]) ?></dd>

			<dt>Complément région</dt>
			<dd><?= utf8_encode(($campagne['region'])?($campagne['region']):('-')) ?></dd>

			<br />

			<dt>UCCI</dt>
			<dd class="listUcci well">
				<?
					if($campagne['ucci'])
					{
						$uccis = explode(",", $campagne['ucci']);

						include("inc/db3.inc.php");

						foreach($uccis as $ucci)
						{
							$ucciResult = mysqli_query($bdd, 'SELECT * FROM ucci WHERE ucci LIKE "%'.$ucci.'%"');

							if(mysqli_num_rows($ucciResult) > 0)
							{
								$ucciTmp = mysqli_fetch_assoc($ucciResult);
								echo "<div class='thumb' data-toggle='tooltip' title=\"".htmlentities($ucciTmp['nom'])."\"><img src='".$ucciTmp['image']."' /><br /><span class='titleUcci'>".$ucciTmp['ucci']."</span></div>";
							}
							else
							{
								echo "<div class='thumb'><span class='titleUcci'>".$ucci."</span></div>";
							}
						}
						include("inc/db.inc.php");
					}
					else
					{
						echo '-';
					}
				?>
			</dd>

			<dt>Emails de test</dt>
			<dd>
				<?
					if($campagne['emailTest'])
					{
						$emailsTest = explode(",", $campagne['emailTest']);

						foreach($emailsTest as $emailTest)
							echo '<span class="label label-default">'.$emailTest.'</span>&nbsp;';
					}
					else
					{
						echo '-';
					}
				?>
			</dd>

			<dt>URL</dt>
			<dd><?= utf8_encode(($campagne['url'])?("<a href='".$campagne['url']."' target='_blank'>".$campagne['url']."</a>"):('-')) ?></dd>

			<dt>Date de début</dt>
			<dd><?= ($campagne['dateDebut'])?(formaterDate($campagne['dateDebut'])):('-') ?></dd>

			<dt>Date de fin</dt>
			<dd><?= ($campagne['dateFin'])?(formaterDate($campagne['dateFin'])):('-') ?></dd>

			<dt>Statut</dt>
			<dd>
				<?
					$timeFin = mktime(23,59,59, substr($campagne['dateFin'],5,2), substr($campagne['dateFin'],8,2), substr($campagne['dateFin'],0,4));

					if($campagne['brief'] == 1 && $campagne['planMedia'] == 0)
						echo '<span class="label label-purple">Préparation</span>';
					else if($campagne['brief'] == 0 && $campagne['planMedia'] == 1)
						echo '<span class="label label-info">Plan Media</span>';
					else if($campagne['bdc'] == 1)
						echo '<span class="label label-default">Terminée</span>';
					else if($campagne['facture'] == 1)
						echo '<span class="label label-info">Facturation</span>';
					else if($timeFin < time() && $campagne['bdc'] == 0)
						echo '<span class="label label-warning">BDC à envoyer</span>';
					else
						echo '<span class="label label-success">En cours</span>';
				?>
			</dd>


			<dt>Bon de commande</dt>
			<dd><?= ($campagne['bdc'] == 1)?('Oui'):('Non') ?></dd>

			<dt>Objectif</dt>
			<dd><strong><?= number_format($campagne['objectif'], 0, ",", " ").(($campagne['objectif'] != $totalObjectif)?(' | <span class="text-warning">'.$totalObjectif.'</span>'):('')) ?></strong></dd>

			<dt>Budget</dt>
			<dd><strong><?= number_format($campagne['budget'], 2, ",", " ").((round($campagne['budget'],2) != round($totalBudget,2))?(' | <span class="text-warning">'.number_format($totalBudget, 2, ",", " ").'</strong></span>'):('')) ?> &euro;</strong></dd>

			<dt>Marge</dt>
			<dd><?= number_format($campagne['marge']*100, 0, ",", " ")?> <strong>%</strong></dd>

			<dt>Interlocuteur</dt>
			<dd><?= utf8_encode($contact['prenom']." ".$contact['nom']) ?></dd>

			<dt>Commentaires</dt>
			<dd><?= utf8_encode($campagne['commentaires']) ?></dd>
		</dl>
	</div>
</div>

<p class="text-info"><em>Dernière modification : <?= utf8_encode($userModif['identifiant'])." le ".utf8_encode($campagne['dateModif']) ?></em></p>

<div class="panel panel-default">
	<div class="panel-heading" onmouseover="$('#periodes').slideDown();">
		<strong>Périodes</strong>
	</div>
	<div class="panel-body" style="<?=(!isset($filtreDateDebut) && !isset($filtrePeriode))?('display: none;'):('')?>" id="periodes">
		<h5><strong>Filtrer sur une période</strong></h5>
		<div class="row">
			<form id="filtre" method="POST" class="form-inline" action="index.php?page=campagne&id=<?= $campagne['id'] ?>&filtre=1">
				<div class="form-group col-xs-3">
					<div class="input-group date">
						<span class="input-group-addon"><strong>Du</strong></span>
						<input type="text" name="filtreDateDebut" value="<?= $filtreDateDebut ?>" class="form-control input-sm">
						<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
					</div>
				</div>
				<div class="form-group col-xs-3">
					<div class="input-group date">
						<span class="input-group-addon"><strong>au</strong></span>
						<input type="text" name="filtreDateFin" value="<?= $filtreDateFin ?>" class="form-control input-sm">
						<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
					</div>
				</div>
				<div class="form-group col-xs-3">
					<div class="btn-group">
						<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-filter"></span> Filtrer</button>
						<a href="index.php?page=campagne&id=<?=$id?>" class="btn btn-default"><span class="glyphicon glyphicon-refresh"></span> Reset</a>
					</div>
				</div>
			</form>
		</div>
		<hr />
		<h5><strong>Gestion des périodes</strong></h5>
		<div class="row">
			<form id="filtre" method="POST" action="index.php?page=campagne&id=<?= $campagne['id'] ?>&periode">
				<div class="form-group col-xs-3">
					<div class="input-group date">
						<span class="input-group-addon"><strong>Du</strong></span>
						<input type="text" name="debutPeriode" class="form-control input-sm" required>
						<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
					</div>
					<br />
					<input type="text" name="nomPeriode" class="form-control input-sm" maxlength="100" placeholder="Nom" required>
				</div>
				<div class="form-group col-xs-3">
					<div class="input-group date">
						<span class="input-group-addon"><strong>au</strong></span>
						<input type="text" name="finPeriode" class="form-control input-sm" required>
						<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
					</div>
					<br />
					<textarea name="commentairePeriode" class="form-control input-sm" placeholder="Commentaire..."></textarea>
				</div>
				<div class="form-group col-xs-3">
					<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-plus-sign"></span> Ajouter période</button>
				</div>
			</form>
		</div>
		<br />
		<div class="well">
			<div class="row">
				<? if (isset($filtreDateDebut) || isset($filtrePeriode)) { ?>
					<a href="index.php?page=campagne&id=<?=$id?>" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-remove-circle"></span> Reset</a>
				<?	}
					$periodes = mysqli_query($bdd, 'SELECT * FROM periode WHERE campagne='.$id.' ORDER BY debut ASC');
					while($periode = mysqli_fetch_assoc($periodes)) {
				?>
					<div class="btn-group">
						<a href="index.php?page=campagne&id=<?= $campagne['id'] ?>&filtrePeriode=<?=$periode['id']?>" class="btn btn-default btn-sm <?=($filtrePeriode==$periode['id'])?('active'):('')?> text-left" data-toggle="tooltip" data-placement="top" title="<?=$periode['commentaire']?>">
							<strong><?=utf8_encode($periode['nom'])?></strong>
							&nbsp;
							<span class="label label-date"><?=formaterDate($periode['debut'])?></span>&nbsp;<span class="glyphicon glyphicon-arrow-right"></span>&nbsp;<span class="label label-date"><?=formaterDate($periode['fin'])?></span>
						</a>
						<a class="btn btn-default btn-sm" href="index.php?page=campagne&id=<?= $campagne['id'] ?>&supprimerPeriode=<?=$periode['id']?>">&times;</a>
					</div>
				<? } ?>
			</div>
		</div>
	</div>
</div>

<?
	// Requete filtre leads
	$reqFiltreLeads = '';
	if(!empty($filtreDateDebut)) $reqFiltreLeads .= " AND LEADS_Date >= '".$filtreDateDebut." 00:00:00' ";
	if(!empty($filtreDateFin)) $reqFiltreLeads .= " AND LEADS_Date <= '".$filtreDateFin." 23:59:59' ";

	// Requete filtre leads période
	if(isset($filtrePeriode)) {
		$filtrePeriode = mysqli_fetch_assoc(mysqli_query($bdd, "SELECT * FROM periode WHERE id=".$filtrePeriode));
		$reqFiltreLeads .= " AND LEADS_Date >= '".$filtrePeriode['debut']." 00:00:00' ";
		$reqFiltreLeads .= " AND LEADS_Date <= '".$filtrePeriode['fin']." 23:59:59' ";
	}
	$_SESSION['recherche']['reqFiltreLeads'] = $reqFiltreLeads;
?>


<? if ($campagne['interne'] == 0 || $campagne['interne'] == 2) { ?>
<br />

<h4>Plan média</h4>

<div class="btn-group" style="display:inline;">
	<a class="btn btn-default " href="index.php?page=campagne&id=<?= $campagne['id'] ?>"><span class="glyphicon glyphicon-refresh"></span> Rafraichir</a>
	<form  action="exportFacture.php" method="POST">
		<input type="hidden" name="idHide" value="<?= $id ?>">
		<input type="hidden" name="nomCampagneHide" value="<?= $campagne['nom'] ?>">
		<input type="hidden" name="regionHide" value="<?= ($region[$campagne['regionBI']]) ?>">
		<input type="hidden" name="dateDebutHide" value="<?= ($campagne['dateDebut'])?(formaterDate($campagne['dateDebut'])):('-') ?>">
		<input type="hidden" name="dateFinHide" value="<?= ($campagne['dateFin'])?(formaterDate($campagne['dateFin'])):('-') ?>">
		<input type="hidden" name="comFacture" id="comFacture" value="<?=$_POST['comFacture']?>" ></input>
		<input type="submit" class="btn btn-default" value="Exporter le Bilan" style="width:150px;text-align:right;"><span class="glyphicon glyphicon-download-alt" style="margin-left: -135px;" ></span></input>
		</input>
	<!--<a class="btn btn-default" href="<?= $nomFichier?>"><span class="glyphicon glyphicon-download-alt"></span> Exporter le plan media</a>-->
	<a class="btn btn-primary" style="margin-left:120px;" href="index.php?page=formAction&id=<?= $campagne['id'] ?>" <?= ($campagne['termine'] == 1 || $campagne['facture'] >= 1 || $campagne['interne'] == 1)?('disabled="disabled"'):('') ?>><span class="glyphicon glyphicon-plus"></span> Ajouter une action</a>
</form>
</div>

<br /><br />

<table class="table table-bordered table-hover table-condensed special">
	<thead>
		<tr>
			<th rowspan="2" colspan="2">Base partenaire</th>
			<th rowspan="2">Canal</th>
			<th rowspan="2" data-sorter="false">Début</th>
			<th rowspan="2" data-sorter="false">Fin</th>
			<th rowspan="2" data-sorter="false">Obj.</th>
			<th colspan="8" style="text-align: center;" data-sorter="false">Leads</th>
			<th rowspan="2">Avancement</th>
			<th rowspan="2" data-sorter="false">Budget</th>
			<th rowspan="2" data-sorter="false"></th>
		</tr>
		<tr>
			<th title="Web" class="text-center" data-sorter="false"><span class="glyphicon glyphicon-globe"></span></th>
			<th title="Mobile" class="text-center" data-sorter="false"><span class="glyphicon glyphicon-phone"></span></th>
			<th title="Tests" class="text-center" data-sorter="false"><span class="glyphicon glyphicon-thumbs-up"></span></th>
			<!--<th class="text-center" data-sorter="false">Total</th>-->
			<th title="Leads uniques" class="text-center" data-sorter="false">Uniques</th>
			<th title="Leads ajustes" class="text-center" data-sorter="false"><span class="glyphicon glyphicon-saved"></span></th>
			<th title="Nouveaux prospects" class="text-center" data-sorter="false"><span class="glyphicon glyphicon-user"></span></th>
			<th title="Coût au lead" class="text-center" data-sorter="false" nowrap><span class="glyphicon glyphicon-euro">/<span class="glyphicon glyphicon-saved"></span></th>
			<th title="Coût au prospect" class="text-center" data-sorter="false" nowrap><span class="glyphicon glyphicon-euro">/<span class="glyphicon glyphicon-user"></span></th>
		</tr>
	</thead>
	<tbody>
		<?
			// PHP Excel
			require_once 'inc/PHPExcel.php';
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()
				->setCreator("Click-Call")
				->setLastModifiedBy("Click-Call")
				->setTitle("Plan média");

			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle('Plan média');

			$objPHPExcel->getActiveSheet()->setCellValue('A1',"Base partenaire");
			$objPHPExcel->getActiveSheet()->setCellValue('B1',"Canal");
			$objPHPExcel->getActiveSheet()->setCellValue('C1',"Objectif");
			$objPHPExcel->getActiveSheet()->setCellValue('D1',"Leads Web");
			$objPHPExcel->getActiveSheet()->setCellValue('E1',"Leads Mobiles");
			$objPHPExcel->getActiveSheet()->setCellValue('F1',"Leads Tests");
			$objPHPExcel->getActiveSheet()->setCellValue('G1',"Total leads");
			$objPHPExcel->getActiveSheet()->setCellValue('H1',"Leads ajustés");
			$objPHPExcel->getActiveSheet()->setCellValue('I1',"Nouveaux prospects");
			$objPHPExcel->getActiveSheet()->setCellValue('J1',"Coût/Lead");
			$objPHPExcel->getActiveSheet()->setCellValue('K1',"Coût/Prospect");
			$objPHPExcel->getActiveSheet()->setCellValue('L1',"Budget");
			$objPHPExcel->getActiveSheet()->setCellValue('M1',"Commentaire");

			$i = 2;
		?>

		<?php
			$totalLeadsWeb = 0;
			$totalLeadsMobile = 0;
			$totalLeadsTest = 0;
			$totalLeadsAjustes = 0;
			$totalNouveauxProspects = 0;
			$totalUniques = 0;

			$k = 0;
			while($action = mysqli_fetch_assoc($actions)) { $k++;
				$partenaire = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT * FROM partenaire WHERE id='.$action['idPartenaire']));

				$alertes = mysqli_query($bdd, 'SELECT * FROM action a WHERE TO_DAYS(NOW()) - TO_DAYS(a.dateDebut) >= 2 AND a.leadsWeb = 0 AND a.leadsMob = 0 AND NOW() <= a.dateFin AND a.objectif > 0 AND a.id='.$action['id']);
				$alerte = mysqli_num_rows($alertes);


				// Si filtrage on recalcule à la volée
				if($action['codeCampagne'] != '' && (isset($filtreDateDebut) || isset($filtrePeriode)))
				{
					// echo '<br>Je suis là 1<br>';
					$nbLeadsWeb = mysqli_fetch_assoc(mysqli_query($bdd, "SELECT COUNT(DISTINCT LEADS_Email) AS leads FROM lead WHERE LEADS_Campagne ='". $action['codeCampagne'] ."' ".$reqEmail." AND ".$reqTest.$reqFiltreLeads));
					$action['leadsWeb'] = $nbLeadsWeb['leads'];

					$nbLeadsMob = mysqli_fetch_assoc(mysqli_query($bdd, "SELECT COUNT(DISTINCT LEADS_Email) AS leads FROM lead WHERE ( LOCATE('LP','".$action['codeCampagne']."') > 0 AND LEADS_Campagne ='". str_replace(" LP ", " MOB ", $action['codeCampagne']) ."')".$reqEmail." AND ".$reqTest.$reqFiltreLeads));
					$action['leadsMob'] = $nbLeadsMob['leads'];

					$nbLeadsTest = mysqli_fetch_assoc(mysqli_query($bdd, "SELECT COUNT(DISTINCT LEADS_Email) AS test FROM lead WHERE (LEADS_Campagne ='". $action['codeCampagne'] ."' OR LEADS_Campagne = '".str_replace(" LP ", " MOB ", $action['codeCampagne'])."') AND (".$reqTestN." ".$reqEmailN.")".$reqFiltreLeads));
					$action['leadsTest'] = $nbLeadsTest['test'];
				}
				else
				{
					// echo '<br>';
					// echo "SELECT COUNT(DISTINCT LEADS_Email) AS leads FROM lead WHERE LEADS_Campagne ='". $action['codeCampagne'] ."' ".$reqEmail." AND ".$reqTest.$reqFiltreLeads;
					// echo '<br>';

					$nbLeadsWeb = mysqli_fetch_assoc(mysqli_query($bdd, "SELECT COUNT(DISTINCT LEADS_Email) AS leads FROM lead WHERE LEADS_Campagne ='". $action['codeCampagne'] ."' ".$reqEmail." AND ".$reqTest.$reqFiltreLeads));
					$action['leadsWeb'] = $nbLeadsWeb['leads'];

					$nbLeadsMob = mysqli_fetch_assoc(mysqli_query($bdd, "SELECT COUNT(DISTINCT LEADS_Email) AS leads FROM lead WHERE ( LOCATE('LP','".$action['codeCampagne']."') > 0 AND LEADS_Campagne ='". str_replace(" LP ", " MOB ", $action['codeCampagne']) ."')".$reqEmail." AND ".$reqTest));
					$action['leadsMob'] = $nbLeadsMob['leads'];

					$nbLeadsTest = mysqli_fetch_assoc(mysqli_query($bdd, "SELECT COUNT(DISTINCT LEADS_Email) AS test FROM lead WHERE (LEADS_Campagne ='". $action['codeCampagne'] ."' OR LEADS_Campagne = '".str_replace(" LP ", " MOB ", $action['codeCampagne'])."') AND (".$reqTestN." ".$reqEmailN.")".$reqFiltreLeads));
					$action['leadsTest'] = $nbLeadsTest['test'];
				}
		?>

		<tr <?=($alerte > 0)?('class="danger"'):('')?>>
			<?php
				$totalLeadsTest += $action['leadsTest'];
				$totalLeadsWeb += $action['leadsWeb'];
				$totalLeadsMobile += $action['leadsMob'];
				$totalLeadsAjustes += $action['leadsAjustes'];
				$totalNouveauxProspects += $action['nouveauxProspects'];
				$totalUniques += $action['leadsUniques'];
			?>
			<td><?=($alerte > 0)?('<span class="label label-danger"><span class="glyphicon glyphicon-warning-sign"></span></span>'):('')?></td>
			<td>
				<?= utf8_encode($partenaire['nom']) ?>
				<?=($action['commentaires']!='')?('<span class="badge pull-right commentaire" data-toggle="tooltip" title="'.utf8_encode($action['commentaires']).'"><span class="glyphicon glyphicon-comment"></span></span>'):('')?>
			</td>
			<td><?= utf8_encode($action['canal']) ?></td>
			<td><?= formaterDate($action['dateDebut']) ?></td>
			<td><?= formaterDate($action['dateFin']) ?></td>
			<td><?= $action['objectif'] ?></td>
			<td><?= $action['leadsWeb'] ?></td>
			<td><?= $action['leadsMob'] ?></td>
			<td><?= $action['leadsTest'] ?></td>
			<!--<td <?/*=($alerte > 0)?('class="rouge"'):('')?>><?= $action['leadsWeb'] + $action['leadsMob'] */?></td>-->
			<!--<td><?//= $action['leadsWeb']+$action['leadsMob']?></td>-->
			<td><?= $action['leadsUniques']?></td>
			<td><?= $action['leadsAjustes'] ?></td>
			<td><?= $action['nouveauxProspects'] ?></td>
			<td class="text-right"><?= ($action['leadsAjustes'] == '0')?(''):(number_format($action['budget']/$action['leadsAjustes'], 2, ",", " ")."&nbsp;&euro;") ?></td>
			<td class="text-right"><?= ($action['nouveauxProspects'] == '0')?(''):(number_format($action['budget']/$action['nouveauxProspects'], 2, ",", " ")."&nbsp;&euro;") ?></td>
			<td>
				<? if ( $action['objectif'] > 0 ) { ?>
				<div class="progress">
					<div class="progress-bar" role="progressbar" style="width: <?= (100 * ($action['leadsWeb']+$action['leadsMob']) / $action['objectif']) ?>%;"></div>
					<div class="info"><?= round((100 * ($action['leadsWeb']+$action['leadsMob']) / $action['objectif'])) ?> %</div>
				</div>
				<? } ?>
			</td>
			<td><?= str_replace(" ", "&nbsp;", number_format($action['budget'], 2, ",", " ")) ?> &euro;</td>
			<td>
				<div class="btn-group">
					<button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
						Actions <span class="caret"></span>
					</button>
					<ul class="dropdown-menu text-left">
						<li><a href="index.php?page=action&id=<?= $action['id'] ?>"><span class="glyphicon glyphicon-eye-open"></span> Afficher détail</a></li>
						<? if (!($campagne['termine'] == 1 || $campagne['facture'] >= 1 || $campagne['interne'] == 1)) { ?>
						<li><a href="index.php?page=formAction&modification&id=<?= $campagne['id'] ?>&idAction=<?= $action['id'] ?>" <?= ($campagne['termine'] == 1 || $campagne['facture'] >= 1)?('disabled="disabled"'):('') ?>><span class="glyphicon glyphicon-edit"></span> Modifier</a></li>
						<li><a href="index.php?page=supprAction&id=<?= $action['id'] ?>" <?= ($campagne['termine'] == 1 || $campagne['facture'] >= 1)?('disabled="disabled"'):('') ?>><span class="glyphicon glyphicon-remove"></span> Supprimer</a></li>
						<? } ?>
					</ul>
				</div>
			</td>
		</tr>
		<?

					$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,utf8_encode($partenaire['nom']));
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,utf8_encode($action['canal']));
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$action['objectif']);
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$action['leadsWeb']);
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$action['leadsMob']);
					$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$action['leadsTest']);
					$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,($action['leadsWeb']+$action['leadsMob']));
					$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,$action['leadsAjustes']);
					$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,$action['nouveauxProspects']);
					$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,($action['leadsAjustes'] == '0')?(''):($action['budget']/$action['leadsAjustes']));
					$objPHPExcel->getActiveSheet()->setCellValue('K'.$i,($action['nouveauxProspects'] == '0')?(''):($action['budget']/$action['nouveauxProspects']));
					$objPHPExcel->getActiveSheet()->setCellValue('L'.$i,$action['budget']);
					$objPHPExcel->getActiveSheet()->setCellValue('M'.$i,utf8_encode($action['commentaires']));

					$i++;

			} mysqli_free_result($actions);
		?>
	</tbody>
	<tbody class="tablesorter-no-sort">
		<tr class="info">
			<td colspan="5"><strong>Total</strong></td>
			<td><strong><?= $totalObjectif ?></strong></td>
			<td><strong><?= $totalLeadsWeb ?></strong></td>
			<td><strong><?= $totalLeadsMobile ?></strong></td>
			<td><strong><?= $totalLeadsTest ?></strong></td>
			<td><strong><?= $totalLeadsWeb+$totalLeadsMobile ?></strong></td>
			<td><strong><?= $totalLeadsAjustes ?></strong></td>
			<td><strong><?= $totalNouveauxProspects ?></strong></td>
			<td class="text-right"><strong><?= number_format($totalLeadsAjustes/$k, 2, ",", " ")."&nbsp;&euro;" ?></strong></td>
			<td class="text-right"><strong><?= number_format($totalNouveauxProspects/$k, 2, ",", " ")."&nbsp;&euro;" ?></strong></td>
			<td>
				<? if ($totalObjectif > 0) { ?>
				<div class="progress">
					<div class="progress-bar" role="progressbar" style="width: <?= (100 * ($totalLeadsWeb+$totalLeadsMobile) / $totalObjectif) ?>%;"></div>
					<div class="info"><?= round(100 * ($totalLeadsWeb+$totalLeadsMobile) / $totalObjectif) ?> %</div>
				</div>
				<? } ?>
			</td>
			<td><strong><?= str_replace(" ", "&nbsp;", number_format($totalBudget, 2, ",", " ")) ?>&nbsp;&euro;</strong></td>
			<td colspan="3"></td>
		</tr>
	</tbody>
</table>

<?
	// Ecriture Excel
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save($nomFichier);
?>

<? } ?>

<? if ($campagne['interne'] == 1 || $campagne['interne'] == 2) { ?>


<div class="btn-group">
	<a class="btn btn-primary" href="index.php?page=formAction&id=<?= $campagne['id'] ?>" <?= ($campagne['termine'] == 1 || $campagne['facture'] >= 1 || $campagne['interne'] == 1)?('disabled="disabled"'):('') ?>><span class="glyphicon glyphicon-plus"></span> Ajouter une action</a>


</div>


<hr />
<h4>Actions internes</h4>
<table class="table table-bordered table-hover table-condensed table-striped special">
	<thead>
		<tr>
			<th rowspan="2">Base partenaire</th>
			<th rowspan="2">Canal</th>
			<th rowspan="2" data-sorter="false">Début</th>
			<th rowspan="2" data-sorter="false">Fin</th>
			<th colspan="6" style="text-align: center;" data-sorter="false">Leads</th>
			<th rowspan="2" data-sorter="false"></th>
		</tr>
		<tr>
			<th title="Web" data-sorter="false"><span class="glyphicon glyphicon-globe"></span></th>
			<th title="Mobile" data-sorter="false"><span class="glyphicon glyphicon-phone"></span></th>
			<th title="Tests" data-sorter="false"><span class="glyphicon glyphicon-thumbs-up"></span></th>
			<th>Total</th>
			<th title="Leads ajustes" data-sorter="false"><span class="glyphicon glyphicon-saved"></span></th>
			<th title="Nouveaux prospects" data-sorter="false"><span class="glyphicon glyphicon-user"></span></th>
		</tr>
	</thead>
	<tbody>
		<?php
			$totalLeadsWeb = 0;
			$totalLeadsMobile = 0;
			$totalLeadsTest = 0;
			$totalLeadsAjustes = 0;
			$totalNouveauxProspects = 0;
			$k = 0;
			while($action = mysqli_fetch_assoc($actionsInternes)) { $k++;
				$partenaire = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT * FROM partenaire WHERE id='.$action['idPartenaire']));

				// Si filtrage on recalcule à la volée
				if($action['codeCampagne'] != '' && isset($filtreDateDebut))
				{
					$nbLeadsWeb = mysqli_fetch_assoc(mysqli_query($bdd, "SELECT COUNT(DISTINCT LEADS_Email) AS leads FROM lead WHERE LEADS_Campagne ='". $action['codeCampagne'] ."' ".$reqEmail." AND ".$reqTest.$reqFiltreLeads));
					$action['leadsWeb'] = $nbLeadsWeb['leads'];



					$nbLeadsMob = mysqli_fetch_assoc(mysqli_query($bdd, "SELECT COUNT(DISTINCT LEADS_Email) AS leads FROM lead WHERE ( LOCATE('LP','".$action['codeCampagne']."') > 0 AND LEADS_Campagne ='". str_replace(" LP ", " MOB ", $action['codeCampagne']) ."')".$reqEmail." AND ".$reqTest.$reqFiltreLeads));
					$action['leadsMob'] = $nbLeadsMob['leads'];



					$nbLeadsTest = mysqli_fetch_assoc(mysqli_query($bdd, "SELECT COUNT(DISTINCT LEADS_Email) AS test FROM lead WHERE (LEADS_Campagne ='". $action['codeCampagne'] ."' OR LEADS_Campagne = '".str_replace(" LP ", " MOB ", $action['codeCampagne'])."') AND (".$reqTestN." ".$reqEmailN.")".$reqFiltreLeads));
					$action['leadsTest'] = $nbLeadsTest['test'];
				}
		?>

		<tr>
			<?php
				$totalLeadsTest += $action['leadsTest'];
				$totalLeadsWeb += $action['leadsWeb'];
				$totalLeadsMobile += $action['leadsMob'];
				$totalLeadsAjustes += $action['leadsAjustes'];
				$totalNouveauxProspects += $action['nouveauxProspects'];
			?>
			<td>
				<?= ($partenaire['nom'] != '')?(utf8_encode($partenaire['nom'])):($clientInfo['nom']) ?>
				<?=($action['commentaires']!='')?('<span class="badge pull-right commentaire" data-toggle="tooltip" title="'.utf8_encode($action['commentaires']).'"><span class="glyphicon glyphicon-comment"></span></span>'):('')?>
			</td>
			<td><?= utf8_encode($action['canal']) ?></td>
			<td><?= formaterDate($action['dateDebut']) ?></td>
			<td><?= formaterDate($action['dateFin']) ?></td>
			<td><?= $action['leadsWeb'] ?></td>
			<td><?= $action['leadsMob'] ?></td>
			<td><?= $action['leadsTest'] ?></td>
			<td><?= $action['leadsWeb'] + $action['leadsMob'] ?></td>
			<td><?= $action['leadsAjustes'] ?></td>
			<td><?= $action['nouveauxProspects'] ?></td>
			<td><a class="btn btn-xs btn-default" href="index.php?page=actionInterne&id=<?= $action['id'] ?>"><span class="glyphicon glyphicon-eye-open"></span> Détail</a></li></td>
		</tr>
		<? } mysqli_free_result($actionsInternes); ?>
	</tbody>
	<tbody class="tablesorter-no-sort">
		<tr class="info">
			<td colspan="4"><strong>Total</strong></td>
			<td><strong><?= $totalLeadsWeb ?></strong></td>
			<td><strong><?= $totalLeadsMobile ?></strong></td>
			<td><strong><?= $totalLeadsTest ?></strong></td>
			<td><strong><?= $totalLeadsWeb + $totalLeadsMobile ?></strong></td>
			<td><strong><?= $totalLeadsAjustes ?></strong></td>
			<td><strong><?= $totalNouveauxProspects ?></strong></td>
			<td colspan="3"></td>
		</tr>
	</tbody>
</table>
<? } ?>


<script type="text/javascript">
	$('.commentaire, .btn-sm').tooltip({
		animation: true,
		html: true,
		placement: 'top',
		trigger: 'hover'
	});

	$('.input-group.date').datepicker({
		format: "yyyy-mm-dd",
		todayBtn: true,
		language: "fr",
		todayHighlight: true
	});

	$(function() {
		$.extend($.tablesorter.themes.bootstrap, {
			table      : 'table table-bordered',
			caption    : 'caption',
			header     : 'bootstrap-header',
			footerRow  : '',
			footerCells: '',
			icons      : '',
			sortNone   : 'bootstrap-icon-unsorted',
			sortAsc    : 'icon-chevron-up glyphicon glyphicon-chevron-up',
			sortDesc   : 'icon-chevron-down glyphicon glyphicon-chevron-down',
			active     : '',
			hover      : '',
			filterRow  : '',
			even       : '',
			odd        : ''
		});

		// call the tablesorter plugin and apply the uitheme widget
		$("table").tablesorter({
			theme : "bootstrap",
			widthFixed: false,
			headerTemplate : '{content} {icon}',
			widgets : [ "uitheme" ]
		});
	});
</script>
