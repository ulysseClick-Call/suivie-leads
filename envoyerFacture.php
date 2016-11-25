<ol class="breadcrumb">
	<li><a href="index.php?page=">Accueil</a></li>
	<li><a href="index.php?page=campagnes">Suivi des campagnes</a></li>
	<li><a href="index.php?page=campagne&id=<?= $id ?>">Campagne</a></li>
	<li class="active">Envoi facture</li>
</ol>

<?php
	include("inc/db.inc.php");
	include("inc/fct.inc.php");
	extract($_GET);
	extract($_POST);





	$campagne = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT * FROM campagne WHERE id='.$id));
	$campagne_totaux = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT * FROM totauxCampagne WHERE id='.$id));
	$actions = mysqli_query($bdd, 'SELECT * FROM action WHERE supprime = 0 AND idCampagne='.$id);
	$contact = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT * FROM contact WHERE id='.$campagne['interlocuteur']));
	$userModif = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT * FROM utilisateur WHERE id='.$campagne['userModif']));

	$totalObjectif = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT SUM(objectif) AS obj FROM action WHERE idCampagne='.$campagne['id'])); $totalObjectif = $totalObjectif['obj'];
	$totalBudget = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT SUM(budget) AS bdg FROM action WHERE supprime=0 AND idCampagne='.$campagne['id'])); $totalBudget = $totalBudget['bdg'];



	$totalLeadsWeb = 0;
	$totalLeadsMobile = 0;

	$budgetTotal = 0;
	$objectifTotal = 0;
	$leadTotal = 0;
	$prixAchatTotalModif = 0;
	$prixAchatTotal = 0;
	$prixVenteTotal = 0;

	$marge = $campagne['marge'];

//totaux par partenaires
	$cpl = $_POST['cpl'.$i];
	$honoraire = $_POST['honoraire'.$i];
	$budget_total_facture = $_POST['pv'.$i];
	$mailOuvert = $_POST['mailOuvert'.$i];
	$newLeads = $_POST['newLeads'.$i];
	$rdv = $_POST['RDV'.$i];
	$nb_clic = $_POST['clic'.$i];
	$nb_impression = $_POST['impression'.$i];
	$leadsFactures = $_POST['lf'.$i];
	$newProspects = $_POST['costNewProspect'.$i];
	$tauxOuverture = $_POST['TauxOuvertHide'.$i];
	$tauxClic = $_POST['TauxClicHide'.$i];
	$tauxConv = $_POST['TauxConvHide'.$i];
	$prixAchats = $_POST['paHide'.$i];




	//Totaux par campagnes

	$totalObjectif = $_POST['totalObjectif'];
	$totalBudgetsInitial = $_POST['totalBudgetsInitial'];
	$totalLeadsWeb = $_POST['totalLeadWeb'];
	$totalLeadsMobile = $_POST['totalLeadMobile'];
	$totalLeadsUnique = $_POST['totalLeadUnique'];
	$totalLeadsFacture = $_POST['totalLeadsFacturesHide'];
	$totalCPL = $_POST['totalCPLHide'];
	$totalAchat = $_POST['totalAchatHide'];
	$totalHonoraire = $_POST['totalHonoraireHide'];
	$totalVente = $_POST['totalVenteHide'];
	$totalAboutis = $_POST['totalAboutisHide'];
	$totalOuvert = $_POST['totalOuvertHide'];
	$totalOuverture = $_POST['totalOuvertureHide'];
	$totalClic = $_POST['totalClicHide'];
	$totalTauxClic = $_POST['totalTauxClicHide'];
	$totalTauxReac = $_POST['totalTauxReacHide'];
	$totalNewLead = $_POST['totalNewLeadHide'];
	$totalCostProspect = $_POST['totalCostProspectHide'];
	$totalRDV = $_POST['totalRDVHide'];
	$comFactures = $_POST['comFacture'];




	// Mise à jour statut facture
	mysqli_query($bdd, 'UPDATE campagne SET facture=1 WHERE id='.$id);

	// Log
	logAction($bdd, $utilisateur['id'], "La facture de la campagne <strong>".htmlentities($campagne['nom'])."</strong> a &eacute;t&eacute; envoy&eacute; à ADV", "M");

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

	$region = array();
	$regions = mysqli_query($bdd, "SELECT * FROM regions");
	while($regionTmp = mysqli_fetch_assoc($regions))
		$region[$regionTmp['id']] = $regionTmp['nomCourt'];


?>

<div class="page-header">
	<h1>Facturation campagne <small><?= utf8_encode($campagne['nom']) ?></small></h1>
</div>

<div class="alert alert-info">
	<strong>EN ATTENTE</strong> La facture est en attente de vérification. <a class="btn btn-primary btn-xs pull-right" href="index.php?page=facturationCampagne&id=<?= $id ?>"><span class="glyphicon glyphicon-chevron-left"></span> Modifier la facture</a>
</div>

<br/>
<form  action="exportFacture.php" method="POST">
	<input type="hidden" name="idHide" value="<?= $id ?>">
	<input type="hidden" name="nomCampagneHide" value="<?= $campagne['nom'] ?>">
	<input type="hidden" name="regionHide" value="<?= ($region[$campagne['regionBI']]) ?>">
	<input type="hidden" name="dateDebutHide" value="<?= ($campagne['dateDebut'])?(formaterDate($campagne['dateDebut'])):('-') ?>">
	<input type="hidden" name="dateFinHide" value="<?= ($campagne['dateFin'])?(formaterDate($campagne['dateFin'])):('-') ?>">
	<input type="hidden" name="comFacture" id="comFacture" value="<?=$_POST['comFacture']?>" ></input>
	<input type="submit" class="btn btn-default"  style="margin:0 -160px 5px -160px;" value="Exporter le Bilan"></input>
	</input>
</form>
<div class="panel panel-default" style="margin:0 -160px 0 -160px">
	<div class="panel-heading"><strong>Détails de la campagne</strong></div>
	<div class="panel-body" >

		<dl class="dl-horizontal">
			<dt>Nom</dt>
			<dd><?= utf8_encode($campagne['nom']) ?></dd>

			<dt>Région</dt>
			<dd><?= utf8_encode($region[$campagne['regionBI']]) ?></dd>

			<dt>Date de début</dt>
			<dd><?= ($campagne['dateDebut'])?(formaterDate($campagne['dateDebut'])):('-') ?></dd>

			<dt>Date de fin</dt>
			<dd><?= ($campagne['dateFin'])?(formaterDate($campagne['dateFin'])):('-') ?></dd>
		</dl>
	</div>

	<table  class="table table-bordered table-striped special">
		<thead>
			<tr>
				<th>Partenaire</th>
				<th>Canal</th>
				<th>Budget<br />initial</th>
				<th title="Objectifs" >Obj</th>
				<th colspan="3" style="text-align: center;" data-sorter="false">Leads</th>
				<th>Leads<br />Facturés</th>
				<th>Coût<br />Lead</th>
				<th>Prix<br />achat <br />(HT)</th>
				<th>Honoraires</th>
				<th>Budget total<br />facturé</th>
				<th title="Impression">Aboutis<br>/Imp</th>
				<th>e-mails<br />ouverts</th>
				<th>Taux<br />d'ouverture</th>
				<th>&nbsp;&nbsp;Clic&nbsp;&nbsp;</th>
				<th>Taux<br />de clic</th>
				<th>Taux<br />de reactivité</th>
				<th>Nouveaux<br />Leads</th>
				<th>Couts<br />nouveau<br />prospect</th>
				<th>RDV</th>
			</tr>
			<tr>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
					<th title="Web" class="text-center" data-sorter="false"><span class="glyphicon glyphicon-globe"></span></th>
					<th title="Mobile" class="text-center" data-sorter="false"><span class="glyphicon glyphicon-phone"></span></th>
					<th title="Leads uniques" class="text-center" data-sorter="false">Uniques</th>
				</tr>
		</thead>
		<tbody>

		    <? $i = 0; ?>

			<?php while($action = mysqli_fetch_assoc($actions)) { ?>

			<?php $partenaire = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT * FROM partenaire WHERE id='.$action['idPartenaire']));

					$leadsWeb = mysqli_fetch_assoc(
						mysqli_query($bdd, "SELECT COUNT(DISTINCT LEADS_Email) AS web FROM lead
						WHERE LEADS_Campagne ='".$action['codeCampagne'] ."' AND ".$reqTest." ".$reqEmail)); $leadsWeb = $leadsWeb['web'];

					$leadsMobile = mysqli_fetch_assoc(
						mysqli_query($bdd, "SELECT COUNT(DISTINCT LEADS_Email) AS mob FROM lead
						WHERE LEADS_Campagne ='".str_replace(" LP ", " MOB ", $action['codeCampagne']) ."' AND ".$reqTest." ".$reqEmail)); $leadsMobile = $leadsMobile['mob'];

						$leadsUniques = mysqli_fetch_assoc(
							mysqli_query($bdd, "SELECT COUNT(DISTINCT LEADS_Email)  AS uniques FROM lead WHERE (LEADS_Campagne ='". $action['codeCampagne'] ."' OR LEADS_Campagne = '".str_replace(" LP ", " MOB ", $action['codeCampagne'])."') ".$reqEmail." AND ".$reqTest)); $leadsUniques = $leadsUniques['uniques'];

					$totalLeadsWeb += $leadsWeb;
					$totalLeadsMobile += $leadsMobile;

					$budgetTotal += $action['budget'];
					$objectifTotal += $action['objectif'];
					$leadTotal += ($leadsWeb + $leadsMobile);
						$leadsFactures = $leadsUniques;

			?>

			<tr>
				<td>
					<?= utf8_encode($partenaire['nom']) ?>
				</td>

				<td>
					<?= utf8_encode($action['canal']) ?>
				</td>

				<td style="width: 100px;text-align:center;" class="text-right">
					<?= str_replace(" ", "&nbsp;", number_format($action['budget'], 2, ",", "")) ?>&nbsp;&euro;
				</td>

				<td style="width: 40px;text-align:center;" class="text-right">
					<?= $action['objectif'] ?>
				</td>

				<td style="width: 100px;text-align:center;" class="text-right"  >
					<?	echo str_replace(" ", "&nbsp;", number_format($leadsWeb, 0, ",", ""));?>
				</td>

				<td style="width: 100px;text-align:center;" class="text-right"  >
					<?	echo str_replace(" ", "&nbsp;", number_format($leadsMobile, 0, ",", ""));?>
				</td>

				<td style="width: 80px;text-align:center;" class="text-right"  >
				<?	echo str_replace(" ", "&nbsp;", number_format($leadsUniques, 0, ",", ""));?>
				</td>

				<td style="width: 80px;" class="text-center">
						<? if(strstr($action['canal'], "CPM")) { ?>
					<?	echo str_replace(" ", "&nbsp;", number_format($_POST['lf'.$i], 0, ",", ""));?>
						<? } else { ?>
							<?	echo str_replace(" ", "&nbsp;", number_format($_POST['lf'.$i], 0, ",", ""));?>
						<? } ?>
					</td>

				<td style="width: 150px;text-align:center;" class="text-right" >
					<?
						if(strstr($action['idPartenaire'], "304")){
							echo 0;
						}elseif(strstr($action['idPartenaire'],"56")){
								echo 0;
							}elseif(strstr($action['idPartenaire'],"247")){
								echo 0;
							}elseif(strstr($action['idPartenaire'],"275")){
								echo 0;
							}elseif(strstr($action['idPartenaire'],"305")){
								echo 0;
							}else
						{
					echo str_replace(" ", "&nbsp;", number_format($_POST['cpl'.$i], 0, ",", ""));?>&nbsp;&euro;

					<? } ?>
				</td>


					<td  id="pa<?= $action['id'] ?>"  style="width:150px;text-align:center;" class="text-right ">
							<?
								echo str_replace(" ", "&nbsp;",$_POST['pa'.$i]);
							?>

					</td>

					<td id="honoraire<?=$action['id']?>" style="width: 200px;text-align:center;" class="text-right">
								<?
									echo str_replace(" ", "&nbsp;", number_format($_POST['honoraire'.$i], 2, ",", ""))." &euro;";
								?>
					</td>


				<td style="width: 100px;text-align:center;" class="text-right" id="pv<?= $i ?>">
					<?
						echo str_replace(" ", "&nbsp;", number_format($_POST['pv'.$i], 2, ",", ""))." &euro;";
					?>
				</td>




				<td style="width: 98px;" class="text-center" id="impression<?=$i?>">
					<?	echo str_replace(" ", "&nbsp;", number_format($_POST['impression'.$i]));?>
				</td>

				<td style="width: 90px;" class="text-center" id="mailOuvert<?=$i?>">
					<?	echo str_replace(" ", "&nbsp;", number_format($_POST['mailOuvert'.$i]));?>
				</td>

				<td style="width: 200px;" class="text-center" id="TauxOuvert<?=$i?>">
						<?	echo str_replace(" ", "&nbsp;", number_format($_POST['TauxOuvertHide'.$i], 2, ",", ""))." %";?>
				</td>

				<td style="width: 90px;" class="text-center" id="clic<?=$i?>">
					<?	echo str_replace(" ", "&nbsp;", number_format($_POST['clic'.$i]));?>
				</td>

				<td style="width: 200px;" class="text-center" id="TauxClic<?=$i?>">
					<?	echo str_replace(" ", "&nbsp;", number_format($_POST['TauxClicHide'.$i], 2, ",", ""))." %";?>
				</td>

				<td style="width: 200px;" class="text-center" id="TauxConv<?=$i?>">
						<?	echo str_replace(" ", "&nbsp;", number_format($_POST['TauxConvHide'.$i], 2, ",", ""))." %";?>
				</td>

				<td style="width: 70px;" class="text-center" id="newLeads<?=$i?>">
					<?	echo str_replace(" ", "&nbsp;", number_format($_POST['newLeads'.$i]));?>
					</td>


			<td style="width: 200px;" class="text-center" id="costNewProspect<?=$i?>">
					<?	echo str_replace(" ", "&nbsp;", number_format($_POST['costNewProspect'.$i], 2, ",", ""))." €";?>
			</td>

			<td style="width: 90px;" class="text-center" id="RDV<?=$i?>">
				<?	echo str_replace(" ", "&nbsp;", number_format($_POST['RDV'.$i]));?>
			</td>
			</tr>
			<?php

//Post valeurs par partenaires

			if (isset ($_POST['clic'.$i])) {
			$nb_clic = $_POST['clic'.$i];
			}
			else { $nb_clic = ''; }

			if (isset ($_POST['impression'.$i])) {
			$nb_impression = $_POST['impression'.$i];
			}
			else { $nb_impression = ''; }

			if (isset ($_POST['TauxClicHide'.$i])) {
			$tauxClic = $_POST['TauxClicHide'.$i];
			}
			else { $tauxClic = '';}

			if (isset ($_POST['TauxConvHide'.$i])) {
			$tauxConv = intval($_POST['TauxConvHide'.$i]);
			}
			else { $tauxConv = ''; }

			if (isset ($_POST['cpl'.$i])) {
			$cpl = $_POST['cpl'.$i];
			}
			else { $cpl = ''; }

			if (isset ($_POST['honoraire'.$i])) {
			$honoraire = $_POST['honoraire'.$i];
			}
			else { $honoraire = ''; }

			if (isset ($_POST['pv'.$i])) {
			$budget_total_facture = $_POST['pv'.$i];
			}
			else { $budget_total_facture = ''; }

			if (isset ($_POST['mailOuvert'.$i])) {
			$mailOuvert = $_POST['mailOuvert'.$i];
			}
			else { $mailOuvert = ''; }

			if (isset ($_POST['newLeads'.$i])) {
			$newLeads = $_POST['newLeads'.$i];
			}
			else { $newLeads = ''; }

			if (isset ($_POST['RDV'.$i])) {
			$rdv = $_POST['RDV'.$i];
			}
			else { $rdv = ''; }

			if (isset ($_POST['lf'.$i])) {
			$leadsFactures = $_POST['lf'.$i];
			}
			else { $leadsFactures = ''; }

			if (isset ($_POST['costNewProspect'.$i])) {
			$newProspects = $_POST['costNewProspect'.$i];
			}
			else { $newProspects = ''; }

			if (isset ($_POST['TauxOuvertHide'.$i])) {
			$tauxOuverture = $_POST['TauxOuvertHide'.$i];
			}
			else { $tauxOuverture = ''; }

			if (isset ($_POST['paHide'.$i])) {
			$prixAchats = $_POST['paHide'.$i];
			}
			else { $prixAchats = ''; }


//post valeurs par campagnes

if (isset ($_POST['totalCPLHide'])) {
$totalCPL = $_POST['totalCPLHide'];
}
else { $totalCPL = ''; }

if (isset ($_POST['totalAchatHide'])) {
$totalAchat = $_POST['totalAchatHide'];
}
else { $totalAchat = ''; }

if (isset ($_POST['totalHonoraireHide'])) {
$totalHonoraire = $_POST['totalHonoraireHide'];
}
else { $totalHonoraire = ''; }

if (isset ($_POST['totalVenteHide'])) {
$totalVente = $_POST['totalVenteHide'];
}
else { $totalVente = ''; }

if (isset ($_POST['totalAboutisHide'])) {
$totalAboutis = $_POST['totalAboutisHide'];
}
else { $totalAboutis = ''; }

if (isset ($_POST['totalOuvertHide'])) {
$totalOuvert = $_POST['totalOuvertHide'];
}
else { $totalOuvert = ''; }

if (isset ($_POST['totalOuvertureHide'])) {
$totalOuverture = $_POST['totalOuvertureHide'];
}
else { $totalOuverture = ''; }

if (isset ($_POST['totalClicHide'])) {
$totalClic = $_POST['totalClicHide'];
}
else { $totalClic = ''; }

if (isset ($_POST['totalTauxClicHide'])) {
$totalTauxClic = $_POST['totalTauxClicHide'];
}
else { $totalTauxClic = ''; }

if (isset ($_POST['totalTauxReacHide'])) {
$totalTauxReac = $_POST['totalTauxReacHide'];
}
else { $totalTauxReac = ''; }

if (isset ($_POST['totalNewLeadHide'])) {
$totalNewLead = $_POST['totalNewLeadHide'];
}
else { $totalNewLead = ''; }

if (isset ($_POST['totalCostProspectHide'])) {
$totalCostProspect = $_POST['totalCostProspectHide'];
}
else { $totalCostProspect = ''; }

if (isset ($_POST['totalRDVHide'])) {
$totalRDV = $_POST['totalRDVHide'];
}
else { $totalRDV = ''; }

if (isset ($_POST['totalObjectif'])) {
$totalObjectif = $_POST['totalObjectif'];
}
else {$totalObjectif  = ''; }

if (isset ($_POST['totalBudgetsInitial'])) {
$totalBudgetsInitial = $_POST['totalBudgetsInitial'];
}
else {$totalBudgetsInitial  = ''; }

if (isset ($_POST['totalLeadWeb'])) {
$totalLeadsWeb = $_POST['totalLeadWeb'];
}
else {$totalLeadsWeb  = ''; }

if (isset ($_POST['totalLeadMobile'])) {
$totalLeadsMobile = $_POST['totalLeadMobile'];
}
else {$totalLeadsMobile  = ''; }

if (isset ($_POST['totalLeadUnique'])) {
$totalLeadsUnique = $_POST['totalLeadUnique'];
}
else {$totalLeadsUnique  = ''; }

if (isset ($_POST['totalLeadsFacturesHide'])) {
$totalLeadsFacture = $_POST['totalLeadsFacturesHide'];
}
else {$totalLeadsFacture  = ''; }

if (isset ($_POST['comFacture'])) {
$comFactures = utf8_decode($_POST['comFacture']);
}
else { $comFactures = ''; }


//ecriture BDD par partenaires
			mysqli_query($bdd, 'UPDATE action SET cpl ='.round($cpl, 2).' WHERE id='.$action['id']);
			mysqli_query($bdd, 'UPDATE action SET nb_clic ='.round($nb_clic, 2).' WHERE id='.$action['id']);
			mysqli_query($bdd, 'UPDATE action SET nb_impression ='.round($nb_impression, 2).' WHERE id='.$action['id']);
			mysqli_query($bdd, 'UPDATE action SET tauxClic ='.round($tauxClic, 2).' WHERE id='.$action['id']);
			mysqli_query($bdd, 'UPDATE action SET tauxConv ='.round($tauxConv, 2).' WHERE id='.$action['id']);
			mysqli_query($bdd, 'UPDATE action SET leadsFactures ='.round($leadsFactures, 2).' WHERE id='.$action['id']);
			mysqli_query($bdd, 'UPDATE action SET tauxOuverture ='.round($tauxOuverture, 2).' WHERE id='.$action['id']);
			mysqli_query($bdd, 'UPDATE action SET prixAchat ='.round($prixAchats, 2).' WHERE id='.$action['id']);
			mysqli_query($bdd, 'UPDATE action SET honoraire ='.round($honoraire, 2).' WHERE id='.$action['id']);
			mysqli_query($bdd, 'UPDATE action SET budget_total_facture ='.round($budget_total_facture, 2).' WHERE id='.$action['id']);
			mysqli_query($bdd, 'UPDATE action SET mails_ouvert ='.round($mailOuvert, 2).' WHERE id='.$action['id']);
			mysqli_query($bdd, 'UPDATE action SET new_leads ='.round($newLeads, 2).' WHERE id='.$action['id']);
			mysqli_query($bdd, 'UPDATE action SET rdv ='.round($rdv, 2).' WHERE id='.$action['id']);
			mysqli_query($bdd, 'UPDATE action SET nouveauxProspects ='.round($newProspects, 2).' WHERE id='.$action['id']);




//ecriture BDD par campagnes



			mysqli_query($bdd, 'UPDATE totauxCampagne SET commentaire ="'. utf8_decode($_POST['comFacture']).'" WHERE id='.$campagne_totaux['id']);
			mysqli_query($bdd, 'UPDATE totauxCampagne SET objectif ='.round($totalBudgetsInitial, 2).' WHERE id='.$campagne_totaux['id']);
			mysqli_query($bdd, 'UPDATE totauxCampagne SET leadsWeb ='.round($totalLeadsWeb, 2).' WHERE id='.$campagne_totaux['id']);
			mysqli_query($bdd, 'UPDATE totauxCampagne SET leadsMobiles ='.round($totalLeadsMobile, 2).' WHERE id='.$campagne_totaux['id']);
			mysqli_query($bdd, 'UPDATE totauxCampagne SET leadsUniques ='.round($totalLeadsUnique, 2).' WHERE id='.$campagne_totaux['id']);
			mysqli_query($bdd, 'UPDATE totauxCampagne SET leadsFactures ='.round($totalLeadsFacture, 2).' WHERE id='.$campagne_totaux['id']);
			mysqli_query($bdd, 'UPDATE totauxCampagne SET CPL ='.round($totalCPL, 2).' WHERE id='.$campagne_totaux['id']);
			mysqli_query($bdd, 'UPDATE totauxCampagne SET achats ='.round($totalAchat, 2).' WHERE id='.$campagne_totaux['id']);
			mysqli_query($bdd, 'UPDATE totauxCampagne SET honoraires ='.round($totalHonoraire, 2).' WHERE id='.$campagne_totaux['id']);
			mysqli_query($bdd, 'UPDATE totauxCampagne SET ventes ='.round($totalVente, 2).' WHERE id='.$campagne_totaux['id']);
			mysqli_query($bdd, 'UPDATE totauxCampagne SET aboutis ='.round($totalAboutis, 2).' WHERE id='.$campagne_totaux['id']);
			mysqli_query($bdd, 'UPDATE totauxCampagne SET ouvert ='.round($totalOuvert, 2).' WHERE id='.$campagne_totaux['id']);
			mysqli_query($bdd, 'UPDATE totauxCampagne SET ouverture ='.round($totalOuverture, 2).' WHERE id='.$campagne_totaux['id']);
			mysqli_query($bdd, 'UPDATE totauxCampagne SET clic ='.round($totalClic, 2).' WHERE id='.$campagne_totaux['id']);
			mysqli_query($bdd, 'UPDATE totauxCampagne SET tauxClic ='.round($totalTauxClic, 2).' WHERE id='.$campagne_totaux['id']);
			mysqli_query($bdd, 'UPDATE totauxCampagne SET tauxReactivite ='.round($totalTauxReac, 2).' WHERE id='.$campagne_totaux['id']);
			mysqli_query($bdd, 'UPDATE totauxCampagne SET newLeads ='.round($totalNewLead, 2).' WHERE id='.$campagne_totaux['id']);
			mysqli_query($bdd, 'UPDATE totauxCampagne SET coutProspect ='.round($totalCostProspect, 2).' WHERE id='.$campagne_totaux['id']);
			mysqli_query($bdd, 'UPDATE totauxCampagne SET RDV ='.round($totalRDV, 2).' WHERE id='.$campagne_totaux['id']);




			$i++;}mysqli_free_result($actions);?>

			<tr>
				<td colspan="2" style="background-color:rgb(217, 237, 247)"><strong>Total</strong></td>
				<td class="text-center" style="background-color:rgb(217, 237, 247)"><strong><?= str_replace(" ", "&nbsp;", number_format($totalBudget, 0, ",", "")) ?>&nbsp;&euro;</strong></td>
				<td class="text-center" style="background-color:rgb(217, 237, 247)"><strong><?= $totalObjectif ?></strong></td>
				<td class="text-center" style="background-color:rgb(217, 237, 247)"><strong><?= $totalLeadsWeb ?></strong></td>
				<td class="text-center" style="background-color:rgb(217, 237, 247)"><strong><?= $totalLeadsMobile ?></strong></td></td>
				<td class="text-center" style="background-color:rgb(217, 237, 247)"><strong><?= $totalLeadsWeb + $totalLeadsMobile ?></strong></td>
				<td class="text-center" style="background-color:rgb(217, 237, 247)"><strong name ="totalLeadsFactures" id="totalLeadsFactures"><?= str_replace(" ", "&nbsp;", number_format($_POST['totalLeadsFacturesHide']))?></strong></td>
				<td class="text-center" style="background-color:rgb(217, 237, 247)"><strong name ="totalCPL" id="totalCPL"><?= str_replace(" ", "&nbsp;", number_format($_POST['totalCPLHide'], 2, ",", "")) ?>&nbsp;&euro;</strong></td>
				<td class="text-center" style="background-color:rgb(217, 237, 247)"><strong name ="totalAchat" id="totalAchat"><?= str_replace(" ", "&nbsp;", number_format($_POST['totalAchatHide'], 2, ",", "")) ?>&nbsp;&euro;</strong></td>
				<td class="text-center" style="background-color:rgb(217, 237, 247)"><strong name ="totalHonoraire" id="totalHonoraire"><?= str_replace(" ", "&nbsp;", number_format($_POST['totalHonoraireHide'], 2, ",", "")) ?>&nbsp;&euro;</strong></td>
				<td class="text-center" style="background-color:rgb(217, 237, 247)"><strong name ="totalVente" id="totalVente"><?= str_replace(" ", "&nbsp;", number_format($_POST['totalVenteHide'], 2, ",", "")) ?>&nbsp;&euro;</strong></td>
				<td class="text-center" style="background-color:rgb(217, 237, 247)"><strong name ="totalAboutis" id="totalAboutis">***</strong></td>
				<td class="text-center" style="background-color:rgb(217, 237, 247)"><strong name ="totalOuvert" id="totalOuvert">***</strong></td>
				<td class="text-center" style="background-color:rgb(217, 237, 247)"><strong name ="totalOuverture" id="totalOuverture">***</strong></td>
				<td class="text-center" style="background-color:rgb(217, 237, 247)"><strong name ="totalClic" id="totalClic">***</strong></td>
				<td class="text-center" style="background-color:rgb(217, 237, 247)"><strong name ="totalTauxClic" id="totalTauxClic">***</strong></td>
				<td class="text-center" style="background-color:rgb(217, 237, 247)"><strong name ="totalTauxReac" id="totalTauxReac">***</strong></td>
				<td class="text-center" style="background-color:rgb(217, 237, 247)"><strong name ="totalNewLead" id="totalNewLead"><?= str_replace(" ", "&nbsp;", number_format($_POST['totalNewLeadHide'])) ?></strong></td>
				<td class="text-center" style="background-color:rgb(217, 237, 247)"><strong name ="totalCostProspect" id="totalCostProspect"><?= str_replace(" ", "&nbsp;", number_format($_POST['totalCostProspectHide'], 2, ",", "")) ?>&nbsp;&euro;</strong></td>
				<td class="text-center" style="background-color:rgb(217, 237, 247)"><strong name ="totalRDV" id="totalRDV"><?= str_replace(" ", "&nbsp;", number_format($_POST['totalRDVHide'])) ?></strong></td>
			</tr>
		</tbody>
	</table>
</div>

<p style="font-weight:bolder;margin:1% 1% 0 -160px;width:7%;">Commentaire:</p>

<textarea class="commentaire" type="text" name="comFacture" id="comFacture"  style="width:25%;height:5em;margin:1% 0 0 -160px;" disabled value=""><? echo(stripslashes(htmlspecialchars($_POST['comFacture'])))?></textarea>

<input type="hidden" name="comFacture" id="comFacture" value="<?=$_POST['comFacture']?>" ></input>


<script type="text/javascript">
	$('.listUcci div').tooltip({
		animation: true,
		html: true,
		placement: 'top',
		trigger: 'hover'
	});


</script>
