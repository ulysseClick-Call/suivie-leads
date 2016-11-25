<ol class="breadcrumb">
	<li><a href="index.php?page=">Accueil</a></li>
	<li><a href="index.php?page=campagnes">Suivi des campagnes</a></li>
	<li><a href="index.php?page=campagne&id=<?= $id ?>">Campagne</a></li>
	<li class="active">Facturation campagne</li>
</ol>

<?php
	include("inc/db.inc.php");
	include("inc/fct.inc.php");
	extract($_GET);
	extract($_POST);

	$campagne = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT * FROM campagne WHERE id='.$id));
	$actions = mysqli_query($bdd, 'SELECT * FROM action WHERE supprime = 0 AND interne = 0 AND idCampagne='.$id);
	$contact = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT * FROM contact WHERE id='.$campagne['interlocuteur']));
	$userModif = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT * FROM utilisateur WHERE id='.$campagne['userModif']));

	$totalObjectif = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT SUM(objectif) AS obj FROM action WHERE  supprime=0 and idCampagne='.$campagne['id'])); $totalObjectif = $totalObjectif['obj'];
	$totalBudget = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT SUM(budget) AS bdg FROM action WHERE supprime=0 and idCampagne='.$campagne['id'])); $totalBudget = $totalBudget['bdg'];
	//$totalLeadsWeb = mysqli_fetch_assoc(mysqli_query($bdd, 'SELECT SUM(prixAchat) AS tlw FROM action WHERE supprime=0 and idCampagne='.$campagne['id'])); $totalLeadsWeb = $totalLeadsWeb['tlw'];;
	$totalLeadsMobile = 0;
	$totalAjustes = 0;
	$totalFactures = 0;

	$totalAchat = 0;

	$marge = $campagne['marge'];

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

	// Mise à jour statut facture
	mysqli_query($bdd, 'UPDATE campagne SET facture=0 WHERE id='.$id);

	$region = array();
	$regions = mysqli_query($bdd, "SELECT * FROM regions");
	while($regionTmp = mysqli_fetch_assoc($regions))
		$region[$regionTmp['id']] = $regionTmp['nomCourt'];

//Création des canaux
$canalEmlCPM = ((strstr($action['canal'], "EML CPM"))||(strstr($action['canal'], "Newsletter"))||(strstr($action['canal'], "SMS CPM"))||(strstr($action['idPartenaire'],"247")));

$canalAffiCPM = ((strstr($action['canal'], "Affi CPM"))||(strstr($action['canal'], "Affi Mobile CPM")));

$canalSEA_FB_CPM = ((strstr($action['canal'], "SEA CPM"))||(strstr($action['canal'], "RESEAUX SOCIAUX CPM")));

$canalPERF = ((strstr($action['canal'], "EML PERF"))||(strstr($action['canal'], "Affi PERF"))||(strstr($action['canal'], "Affi Mobile PERF")));





?>

<div class="page-header">
	<h1>Facturation campagne <small><?= utf8_encode($campagne['nom']) ?></small></h1>
</div>

<br />
<form class="form-horizontal" style="margin-left:-14%;margin-right:-14%;" role="form" method="POST"   action="index.php?page=envoyerFacture&id=<?= $id ?>">
	<div class="panel panel-default">
		<div class="panel-heading"><strong>Détails de la campagne</strong></div>
		<div class="panel-body">

			<dl class="dl-horizontal">
				<dt>Nom</dt>
				<dd><?= utf8_encode($campagne['nom']) ?></dd>

				<dt>Région</dt>
				<dd><?= utf8_encode($region[$campagne['regionBI']]) ?></dd>

				<dt>Date de début</dt>
				<dd><?= ($campagne['dateDebut'])?(formaterDate($campagne['dateDebut'])):('-') ?></dd>

				<dt>Date de fin</dt>
				<dd><?= ($campagne['dateFin'])?(formaterDate($campagne['dateFin'])):('-') ?></dd>

				<dt>Marge</dt>
				<dd><?= $campagne['marge']*100 ?> <strong>%</strong></dd>
			</dl>
		</div>

		<table class="table table-bordered table-striped special">
			<thead>
				<tr>
					<th>Partenaire</th>
					<th>Canal</th>
					<th title="Objectifs" >Obj</th>
					<th>Budget<br />initial</th>
					<th colspan="3" style="text-align: center;" data-sorter="false">Leads</th>
					<th>Leads<br />Facturés</th>
					<th>Coût <br />Lead</th>
					<th>Prix<br />achat <br />(HT)</th>
					<th>Honoraires</th>
					<th>Budget total<br />facturé</th>
					<th title="Impression">Aboutis<br>/Imp</th>
					<th>e-mails<br />ouverts</th>
					<th>Taux<br />d'ouverture</th>
					<th>&nbsp;&nbsp;Clic&nbsp;&nbsp;</th>
					<th>Taux de</br>clic/CTR</th>
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
					$totalAjustes += $action['leadsAjustes'];
					$totalFactures += $leadsUniques;
					$leadsFactures = $leadsUniques;
					$prixAchat = ($action['budget']/(1+$marge))/$action['objectif']*($leadsFactures);


					$CplPerf = (($action['budget']/(1+$marge))/$action['objectif']);
					$PrixAchatPerf = $CplPerf*$leadsFactures;
					$PrixAchatCPM = $action['budget']/(1+$marge);
					$HonorairePerf = (($PrixAchatPerf*(1+$marge))-$PrixAchatPerf);
					$HonoraireCPM = (($PrixAchatCPM*(1+$marge))-$PrixAchatCPM);
					?>

					<tr>
					  <td>
					    <?= utf8_encode($partenaire['nom']) ?>
					  </td>

					  <td>
					    <?= utf8_encode($action['canal']) ?>
					  </td>

					  <td style="width: 40px;text-align:center;" class="text-right">
					    <?= $action['objectif'] ?>
					  </td>

					  <td style="width: 100px;text-align:center;" class="text-right">
					    <?= str_replace(" ", "&nbsp;", number_format($action['budget'], 2, ",", "")) ?>&nbsp;&euro;
					  </td>

					  <td style="width: 100px;text-align:center;" class="text-right leadsWeb"  >
					    <input  type="text"  name="leadsWeb<?=$i?>"   id="leadsWeb<?=$i?>"  disabled class="form-control text-right factures"   value="<?=$leadsWeb?>" >
					  </td>

					  <td style="width: 100px;text-align:center;" class="text-right"  >
					    <input  type="text"  name="leadsMobile<?=$i?>"   id="leadsMobile<?=$i?>"  disabled class="form-control text-right factures"   value="<?=$leadsMobile?>" >
					  </td>

					  <td style="width: 80px;text-align:center;" class="text-right"  >
					    <input  type="text"  name="leadsUniques<?=$i?>"   id="leadsUniques<?=$i?>"  disabled class="form-control text-right factures"   value="<?=$leadsUniques?>" >
					  </td>

						<td style="width: 80px;" class="text-right">
							<?if (((strstr($action['canal'], "EML PERF"))||(strstr($action['canal'], "Affi PERF"))||(strstr($action['canal'], "Affi Mobile PERF")))){?>
							<input type="text" name="lf<?= $i ?>" id="lf<?= $action['id'] ?>" class="form-control text-center facture" value="<?=$leadsFactures?>" onkeyup="EditLeadsPerf(<?=$action['id']?>,<?=$marge?>)" >
							<?}else{?>
								<input type="text" name="lf<?= $i ?>" id="lf<?= $action['id'] ?>" class="form-control text-center facture" value="<?=$leadsFactures?>" >
							<?}?>
							</td>

							<td style="width: 150px;text-align:center;" class="text-right">
								<?if (((strstr($action['canal'], "EML PERF"))||(strstr($action['canal'], "Affi PERF"))||(strstr($action['canal'], "Affi Mobile PERF")))) {
									$CplPerf = (($action['budget']/(1+$marge))/$action['objectif']);?>
									<input type="text" name="cpl<?=$i?>" id="cpl<?=$action['id']?>" class="form-control text-center CPL" value="<?echo str_replace(" ", "&nbsp;", number_format($CplPerf, 2, ",", ""))." &euro;";?>"   >
						<?}else{?>
									<input type="text" name="cpl<?=$i?>" id="cpl<?=$action['id']?>" class="form-control text-center CPL" value="<?echo str_replace(" ", "&nbsp;", number_format($prixAchat/$leadsFactures, 2, ",", ""))." &euro;";?>"   >
								<?}?>
							</td>

							<td style="width:200px;" >
								<?if (((strstr($action['canal'], "EML PERF"))||(strstr($action['canal'], "Affi PERF"))||(strstr($action['canal'], "Affi Mobile PERF")))) {?>
									<input type="text" name="pa<?=$i?>"  id="pa<?=$action['id']?>"  class="form-control text-right achat"   value="<?echo str_replace(" ", "&nbsp;", number_format($PrixAchatPerf, 2, ",", ""))." &euro;";?>"  style="font-size:14px!important;padding:0!important;text-align:center;">
								<?}elseif (((strstr($action['canal'], "SEA CPM"))||(strstr($action['canal'], "RESEAUX SOCIAUX CPM")))){?>
									<input type="text" name="pa<?=$i?>"  id="pa<?=$action['id']?>"  class="form-control text-right achat"   value="<?echo str_replace(" ", "&nbsp;", number_format($action['budget'], 2, ",", ""))." &euro;";?>"  style="font-size:14px!important;padding:0!important;text-align:center;">
								<?}else{?>
									<input type="text" name="pa<?=$i?>"  id="pa<?=$action['id']?>"  class="form-control text-right achat"   value="<?echo str_replace(" ", "&nbsp;", number_format($PrixAchatCPM, 2, ",", ""))." &euro;";?>"  style="font-size:14px!important;padding:0!important;text-align:center;">
								<?}?>
							</td>

							<td style="width: 75px;text-align:center;" class="text-right">
								<?if (((strstr($action['canal'], "SEA CPM"))||(strstr($action['canal'], "RESEAUX SOCIAUX CPM")))){?>
										<input type="text" name="honoraire<?=$i?>"  id="honoraire<?=$action['id']?>"  class="form-control text-right honoraire" value="0" style="font-size:14px!important;padding:0!important;text-align:center!important;">
								<?}elseif (((strstr($action['canal'], "EML PERF"))||(strstr($action['canal'], "Affi PERF"))||(strstr($action['canal'], "Affi Mobile PERF")))){?>
								<input type="text" name="honoraire<?=$i?>"  id="honoraire<?=$action['id']?>"  class="form-control text-right honoraire" value="<?echo str_replace(" ", "&nbsp;", number_format($HonorairePerf, 2, ",", ""))." &euro;";?>" style="font-size:14px!important;padding:0!important;text-align:center!important;">
								<?}else {?>
									<input type="text" name="honoraire<?=$i?>"  id="honoraire<?=$action['id']?>"  class="form-control text-right honoraire" value="<?echo str_replace(" ", "&nbsp;", number_format($HonoraireCPM, 2, ",", ""))." &euro;";?>" style="font-size:14px!important;padding:0!important;text-align:center!important;">
								<?}?>
							</td>

							<td  style="width: 125px;text-align:center;" class="text-right"  >
								<? if (((strstr($action['canal'], "EML PERF"))||(strstr($action['canal'], "Affi PERF"))||(strstr($action['canal'], "Affi Mobile PERF")))){?>
								<input type="text" id="pv<?= $action['id'] ?>" name="pv<?=$i?>" class="form-control text-right" value="<?echo str_replace(" ", "&nbsp;", number_format($PrixAchatPerf+$HonorairePerf, 2, ",", ""))." &euro;";?>" style="font-size:14px!important;padding:0!important;text-align:center;">
								<?}else{?>
									<input type="text" id="pv<?= $action['id'] ?>" name="pv<?=$i?>" class="form-control text-right" value="<?echo str_replace(" ", "&nbsp;", number_format($PrixAchatCPM+$HonoraireCPM, 2, ",", ""))." &euro;";?>" style="font-size:14px!important;padding:0!important;text-align:center;">
									<?}?>
							</td>

							<td style="width: 98px;" class="text-left">
								<? if (((strstr($action['canal'], "EML CPM"))||(strstr($action['canal'], "Newsletter"))||(strstr($action['canal'], "SMS CPM"))||(strstr($action['idPartenaire'],"247")))){?>
								<input type="text" name="impression<?=$i?>"   id="impression<?=$i?>" onkeyup="calculTaux(<?=$i?>)" class="form-control text-right impression" value="">
								<?}else{?>
									<input type="text" name="impression<?=$i?>"   id="impression<?=$i?>" class="form-control text-right impression" value="">
									<?}?>
							</td>

							<td style="width: 90px;" class="text-left">
								<? if (((strstr($action['canal'], "EML CPM"))||(strstr($action['canal'], "Newsletter"))||(strstr($action['canal'], "SMS CPM"))||(strstr($action['idPartenaire'],"247")))){?>
									<input  type="text"  name="mailOuvert<?=$i?>"    id="mailOuvert<?=$i?>" onkeyup="calculTaux(<?=$i?>)" class="form-control text-right ouverture" value="">
								<?}else{?>
									<input  type="text"  name="mailOuvert<?=$i?>"    id="mailOuvert<?=$i?>" class="form-control text-right ouverture" value="">
									<?}?>
							</td>

							<td style="width: 200px;" class="text-left">
									<div class="input-group">
										<? if (((strstr($action['canal'], "EML CPM"))||(strstr($action['canal'], "Newsletter"))||(strstr($action['canal'], "SMS CPM"))||(strstr($action['idPartenaire'],"247")))){?>
									<input type="text" readonly name="TauxOuvert<?=$i?>"  id="TauxOuvert<?=$i?>"  class="form-control text-right tauxOuvert" value="" ><span class="input-group-addon" >%</span>
									<?}else{?>
									<input type="text" readonly name="TauxOuvert<?=$i?>"  id="TauxOuvert<?=$i?>"  class="form-control text-right tauxOuvert" disabled value="***" ><span class="input-group-addon" >%</span>
										<?}?>
								</div>
							</td>

							<td style="width: 90px;" class="text-left">
								<? if (((strstr($action['canal'], "EML CPM"))||(strstr($action['canal'], "Newsletter"))||(strstr($action['canal'], "SMS CPM"))||(strstr($action['idPartenaire'],"247")))){?>
								<input  type="text"  name="clic<?=$i?>"    id="clic<?=$i?>" onkeyup="calculTaux(<?=$i?>)"  class="form-control text-right clic" value="">
								<?}elseif(((strstr($action['canal'], "Affi CPM"))||(strstr($action['canal'], "Affi Mobile CPM")))){?>
									<input  type="text"  name="clic<?=$i?>"    id="clic<?=$i?>" onkeyup="calculTaux(<?=$i?>)"  class="form-control text-right clic" value="">
								<?}else{?>
									<input  type="text" readonly  name="clic<?=$i?>"    id="clic<?=$i?>" class="form-control text-right clic" value="***">
								<?}?>
							</td>

							<td style="width: 200px;" class="text-left">
						    <div class="input-group">
									<? if (((strstr($action['canal'], "EML CPM"))||(strstr($action['canal'], "Newsletter"))||(strstr($action['canal'], "SMS CPM"))||(strstr($action['idPartenaire'],"247")))){?>
						      <input type="text" name="TauxClic<?=$i?>"   id="TauxClic<?=$i?>"  class="form-control text-right tauxClic" readonly value="" ><span class="input-group-addon" >%</span>
								<?}elseif(((strstr($action['canal'], "Affi CPM"))||(strstr($action['canal'], "Affi Mobile CPM")))){?>
									<input type="text" name="TauxClic<?=$i?>"   id="TauxClic<?=$i?>"  class="form-control text-right tauxClic" readonly value="" ><span class="input-group-addon" >%</span>
								<?}else{?>
									<input type="text" name="TauxClic<?=$i?>"   id="TauxClic<?=$i?>" class="form-control text-right tauxClic" readonly value="***" ><span class="input-group-addon" >%</span>
								<?}?>
						    </div>
						  </td>

						  <td style="width: 200px;" class="text-left">
						      <div class="input-group">
										<? if (((strstr($action['canal'], "EML CPM"))||(strstr($action['canal'], "Newsletter"))||(strstr($action['idPartenaire'],"247")))){?>
						      <input type="text" name="TauxConv<?=$i?>"  id="TauxConv<?=$i?>"  class="form-control text-right tauxConv" readonly value="" ><span class="input-group-addon" >%</span>
									<?}else{?>
										<input type="text" name="TauxConv<?=$i?>"  id="TauxConv<?=$i?>"  class="form-control text-right tauxConv" readonly value="***" ><span class="input-group-addon" >%</span>
									<?}?>
						    </div>
						  </td>

							<td style="width: 70px;" class="text-left">
						      <input type="text" name="newLeads<?=$i?>" id="newLeads<?=$i?>"  class="form-control text-right newLead" onkeyup="newLeads(<?=$action['id']?>,<?=$i?>)" value="0" >
								</td>


						<td style="width: 200px;" class="text-left">
								<div class="input-group">
								<input type="text" name="costNewProspect<?=$i?>"  id="costNewProspect<?=$i?>"  class="form-control text-right costProspect"  readonly value="0" >
							</div>
						</td>

						<td style="width: 90px;" class="text-left">
							<input  type="text"  name="RDV<?=$i?>"    id="RDV<?=$i?>"  class="form-control text-right factures RDV" onkeyup="totalRDV()" value="0">
						</td>
		</tr>
							<?php $i++;} mysqli_free_result($actions); ?>

							<tr class="info">
								<td colspan="2"><strong>Total</strong></td>
								<td class="text-center"><strong><?= $totalObjectif ?></strong>
									<input type="hidden" name="totalObjectif" id="totalObjectif" value=""></td>
								<td class="text-center"><strong><?= str_replace(" ", "&nbsp;", number_format($totalBudget, 2, ",", "")) ?>&nbsp;&euro;</strong>
									<input type="hidden" name="totalBudgetsInitial" id="totalBudgetsInitial" value="<?=$totalBudget?>"></td>
								<td class="text-center"><strong><?= str_replace(" ", "&nbsp;", number_format($totalLeadsWeb, 0, ",", "")) ?></strong>
									<input type="hidden" name="totalLeadWeb" id="totalLeadWeb" value="<?= str_replace(" ", "&nbsp;", number_format($totalLeadsWeb, 0, ",", "")) ?>"></td>
								<td class="text-center"><strong><?= str_replace(" ", "&nbsp;", number_format($totalLeadsMobile, 0, ",", "")) ?></strong></td>
									<input type="hidden" name="totalLeadMobile" id="totalLeadMobile" value="<?= str_replace(" ", "&nbsp;", number_format($totalLeadsMobile, 0, ",", "")) ?>"></td>
								<td class="text-center"><strong class="TotalUniques"><?= str_replace(" ", "&nbsp;", number_format($totalFactures, 0, ",", "")) ?></strong>
									<input type="hidden" name="totalLeadUnique" id="totalLeadUnique" value="<?= str_replace(" ", "&nbsp;", number_format($totalFactures, 0, ",", "")) ?>"></td>
								<td class="text-center"><strong name ="totalLeadsFactures" id="totalLeadsFactures"></strong>
									<input type="hidden" name="totalLeadsFacturesHide" id="totalLeadsFacturesHide" value=""></td>
								<td class="text-center"><strong name ="totalCPL" id="totalCPL"></strong>
									<input type="hidden" name="totalCPLHide" id="totalCPLHide" value=""></td>
								<td class="text-center"><strong name ="totalAchat" id="totalAchat"></strong>
									<input type="hidden" name="totalAchatHide" id="totalAchatHide" value=""></td>
								<td class="text-center"><strong name ="totalHonoraire" id="totalHonoraire"></strong>
									<input type="hidden" name="totalHonoraireHide" id="totalHonoraireHide" value=""></td>
								<td class="text-center"><strong name ="totalVente" id="totalVente"></strong>
									<input type="hidden" name="totalVenteHide" id="totalVenteHide" value=""></td>
								<td class="text-center"><strong name ="totalAboutis" id="totalAboutis">***</strong>
									<input type="hidden" name="totalAboutisHide" id="totalAboutisHide" value=""></td>
								<td class="text-center"><strong name ="totalOuvert" id="totalOuvert">***</strong>
									<input type="hidden" name="totalOuvertHide" id="totalOuvertHide" value=""></td>
								<td class="text-center"><strong name ="totalOuverture" id="totalOuverture">***</strong>
									<input type="hidden" name="totalOuvertureHide" id="totalOuvertureHide" value=""></td>
								<td class="text-center"><strong name ="totalClic" id="totalClic">***</strong>
									<input type="hidden" name="totalClicHide" id="totalClicHide" value=""></td>
								<td class="text-center"><strong name ="totalTauxClic" id="totalTauxClic">***</strong>
									<input type="hidden" name="totalTauxClicHide" id="totalTauxClicHide" value=""></td>
								<td class="text-center"><strong name ="totalTauxReac" id="totalTauxReac">***</strong>
									<input type="hidden" name="totalTauxReacHide" id="totalTauxReacHide" value=""></td>
								<td class="text-center"><strong name ="totalNewLead" id="totalNewLead"></strong>
									<input type="hidden" name="totalNewLeadHide" id="totalNewLeadHide" value=""></td>
								<td class="text-center"><strong name ="totalCostProspect" id="totalCostProspect"></strong>
									<input type="hidden" name="totalCostProspectHide" id="totalCostProspectHide" value=""></td>
								<td class="text-center"><strong name ="totalRDV" id="totalRDV"></strong>
									<input type="hidden" name="totalRDVHide" id="totalRDVHide" value=""></td>
							</tr>
							</tbody>
							</table>
							</div>
							<p style="font-weight:bolder;width:7%;">Commentaire:</p>

							<textarea name="comFacture" rows="5" cols="40"></textarea>
							<button type="submit" class="btn btn-primary btn-lg pull-right">Envoyer facture <span class="glyphicon glyphicon-ok"></span></button>
							<br /><br />
							<br /><br />
							</form>
							</table>

							<script type="text/javascript" src="factureCampagne.js"></script>
