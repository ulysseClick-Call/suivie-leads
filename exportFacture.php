<?
include("inc/db.inc.php");
include("inc/fct.inc.php");

$id = intval($_POST['idHide']);
$query =  ('SELECT * FROM action WHERE idCampagne ='.$id);
$result = mysqli_query($bdd,$query);

$query2 =  ('SELECT * FROM totauxCampagne WHERE id ='.$id);
$result2 = mysqli_query($bdd,$query2);

$query3 = ('SELECT * FROM partenaire
INNER JOIN action
ON partenaire.id = action.idPartenaire WHERE action.idCampagne ='.$id);
$result3 = mysqli_query($bdd,$query3);

$queryNom = ('SELECT * FROM campagne WHERE id='.$id);
$resultNom = mysqli_query($bdd, $queryNom);

$com = utf8_encode($_POST['comFacture']);
$com = str_replace("\\", "", $com);

//$commDb = ('SELECT commentaire FROM totauxCampagne WHERE id ='.$id);

$nomCampagne = $_POST['nomCampagneHide'];
$region = $_POST['regionHide'];
$dateDebut = $_POST['dateDebutHide'];
$dateFin = $_POST['dateFinHide'];



	// PHP Excel
require_once 'inc/PHPExcel.php';
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->getProperties()
		->setCreator("Click-Call")
		->setLastModifiedBy("Click-Call")
		->setTitle("Export Bilan");

$styleArray = array(
	'fill' => array(
							'type' => PHPExcel_Style_Fill::FILL_SOLID,
							'startcolor' => array(
							'rgb' => 'DDDDDD',
						),
));

$styleArray2 = array(

		'alignment'=> array(
								'horizontal'=> PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
								'vertical'=> PHPExcel_Style_Alignment::VERTICAL_CENTER
						),
);

$styleArrayCom = array(

		'alignment'=> array(
								'horizontal'=> PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY,
								'vertical'=> PHPExcel_Style_Alignment::VERTICAL_TOP
						),
);

$styleArraySign = array(

		'alignment'=> array(
								'horizontal'=> PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
								'vertical'=> PHPExcel_Style_Alignment::VERTICAL_CENTER
						),
);

$styleArrayBase = array(

		'alignment'=> array(
								'horizontal'=> PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
								'vertical'=> PHPExcel_Style_Alignment::VERTICAL_CENTER
						),
);

$objPHPExcel->getActiveSheet()->getStyle('A6:U6')->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A6:U6')->applyFromArray($styleArray2);

	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->setCellValue('E1',$nomCampagne);
	$objPHPExcel->getActiveSheet()->mergeCells('E1:H1');
	$objPHPExcel->getActiveSheet()->getStyle('E1')->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($styleArray2);

	$objPHPExcel->getActiveSheet()->setCellValue('B2',"Région :");
	$objPHPExcel->getActiveSheet()->setCellValue('B3',"Date de début :");
	$objPHPExcel->getActiveSheet()->setCellValue('B4',"Date de fin :");

	$objPHPExcel->getActiveSheet()->setCellValue('C2',$region);
	$objPHPExcel->getActiveSheet()->setCellValue('C3',$dateDebut);
	$objPHPExcel->getActiveSheet()->setCellValue('C4',$dateFin);

	$objPHPExcel->getActiveSheet()->setCellValue('A6',"Base");
	$objPHPExcel->getActiveSheet()->setCellValue('B6',"Canal");
	$objPHPExcel->getActiveSheet()->setCellValue('C6',"Budget Initial €");
	$objPHPExcel->getActiveSheet()->setCellValue('D6',"Objectif");
	$objPHPExcel->getActiveSheet()->setCellValue('E6',"Leads Web");
	$objPHPExcel->getActiveSheet()->setCellValue('F6',"Leads Mobile");
	$objPHPExcel->getActiveSheet()->setCellValue('G6',"Leads Uniques");
	$objPHPExcel->getActiveSheet()->setCellValue('H6',"Leads Facturés");
	$objPHPExcel->getActiveSheet()->setCellValue('I6',"CPL");
	$objPHPExcel->getActiveSheet()->setCellValue('J6',"Prix d'Achat €");
	$objPHPExcel->getActiveSheet()->setCellValue('K6',"Honoraires €");
	$objPHPExcel->getActiveSheet()->setCellValue('L6',"Budget Total Facturé €");
	$objPHPExcel->getActiveSheet()->setCellValue('M6',"Aboutis");
	$objPHPExcel->getActiveSheet()->setCellValue('N6',"E-mails Ouverts");
	$objPHPExcel->getActiveSheet()->setCellValue('O6',"Taux d'Ouverture %");
	$objPHPExcel->getActiveSheet()->setCellValue('P6',"Clic");
	$objPHPExcel->getActiveSheet()->setCellValue('Q6',"Taux de Clic %");
	$objPHPExcel->getActiveSheet()->setCellValue('R6',"Taux de Réactivité %");
	$objPHPExcel->getActiveSheet()->setCellValue('S6',"Nouveaux Leads");
	$objPHPExcel->getActiveSheet()->setCellValue('T6',"Couts Nouveaux Leads €");
	$objPHPExcel->getActiveSheet()->setCellValue('U6',"RDV");



$i =7;




	while($row = mysqli_fetch_array($result3)){

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$row['nom']);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,$row['canal']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$row['budget'].' '.'€');
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$row['objectif']);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$row['leadsWeb']);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$row['leadsMob']);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$row['leadsUniques']);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,$row['leadsFactures']);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,$row['cpl'].' '.'€');
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,$row['prixAchat'].' '.'€');
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i,$row['honoraire'].' '.'€');
			$objPHPExcel->getActiveSheet()->setCellValue('L'.$i,$row['budget_total_facture'].' '.'€');
			$objPHPExcel->getActiveSheet()->setCellValue('M'.$i,$row['nb_impression']);
			$objPHPExcel->getActiveSheet()->setCellValue('N'.$i,$row['mails_ouvert']);
			$objPHPExcel->getActiveSheet()->setCellValue('O'.$i,$row['tauxOuverture'].' '.'%');
			$objPHPExcel->getActiveSheet()->setCellValue('P'.$i,$row['nb_clic']);
			$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i,$row['tauxClic'].' '.'%');
			$objPHPExcel->getActiveSheet()->setCellValue('R'.$i,$row['tauxConv'].' '.'%');
			$objPHPExcel->getActiveSheet()->setCellValue('S'.$i,$row['new_leads']);
			$objPHPExcel->getActiveSheet()->setCellValue('T'.$i,$row['nouveauxProspects'].' '.'€');
			$objPHPExcel->getActiveSheet()->setCellValue('U'.$i,$row['rdv']);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleArrayBase);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':'.'B'.$i)->applyFromArray($styleArrayBase);
$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':'.'U'.$i)->applyFromArray($styleArraySign);
		$i++;

}
 mysqli_free_result($result);



while($row = mysqli_fetch_array($result2)){

 	$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,"Totaux");
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$i,'');
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$row['objectif'].' '.'€');
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$i,$row['budget_initial']);
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,$row['leadsWeb']);
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$i,$row['leadsMobiles']);
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$i,$row['leadsUniques']);
	$objPHPExcel->getActiveSheet()->setCellValue('H'.$i,$row['leadsFactures']);
	$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,$row['CPL'].' '.'€');
	$objPHPExcel->getActiveSheet()->setCellValue('J'.$i,$row['achats'].' '.'€');
	$objPHPExcel->getActiveSheet()->setCellValue('K'.$i,$row['honoraires'].' '.'€');
	$objPHPExcel->getActiveSheet()->setCellValue('L'.$i,$row['ventes'].' '.'€');
	$objPHPExcel->getActiveSheet()->setCellValue('M'.$i,$row['aboutis']);
	$objPHPExcel->getActiveSheet()->setCellValue('N'.$i,$row['ouvert']);
	$objPHPExcel->getActiveSheet()->setCellValue('O'.$i,$row['ouverture'].' '.'%');
	$objPHPExcel->getActiveSheet()->setCellValue('P'.$i,$row['clic']);
	$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i,$row['tauxClic'].' '.'%');
	$objPHPExcel->getActiveSheet()->setCellValue('R'.$i,$row['tauxReactivite'].' '.'%');
	$objPHPExcel->getActiveSheet()->setCellValue('S'.$i,$row['newLeads']);
	$objPHPExcel->getActiveSheet()->setCellValue('T'.$i,$row['coutProspect'].' '.'€');
	$objPHPExcel->getActiveSheet()->setCellValue('U'.$i,$row['RDV']);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.($i+2),"Commentaires :");
	$objPHPExcel->getActiveSheet()->mergeCells('B'.($i+2).':'.'E'.($i+8));
	$objPHPExcel->getActiveSheet()->setCellValue('B'.($i+2),utf8_encode(str_replace("\\", "", $row['commentaire'])));
	$objPHPExcel->getActiveSheet()->getStyle('B'.($i+2))->getAlignment()->setWrapText(true);
	$objPHPExcel->getActiveSheet()->getStyle('B'.($i+2))->applyFromArray($styleArrayCom);

	$styleArray = array(
		'fill' => array(
								'type' => PHPExcel_Style_Fill::FILL_SOLID,
								'startcolor' => array(
								'rgb' => 'D9EDF7',
							)),

			'alignment'=> array(
					      	'horizontal'=> PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					        'vertical'=> PHPExcel_Style_Alignment::VERTICAL_CENTER
					    ),
	);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i,"Totaux")->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i,'')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('C'.$i,$row['objectif'])->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('D'.$i,$row['budget_initial'])->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('E'.$i,$row['leadsWeb'])->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i,$row['leadsMobiles'])->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('G'.$i,$row['leadsUniques'])->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('H'.$i,$row['leadsFactures'])->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('I'.$i,$row['CPL'])->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('J'.$i,$row['achats'])->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('K'.$i,$row['honoraires'])->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('L'.$i,$row['ventes'])->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('M'.$i,$row['aboutis'])->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('N'.$i,$row['ouvert'])->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('O'.$i,$row['ouverture'])->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('P'.$i,$row['clic'])->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('Q'.$i,$row['tauxClic'])->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('R'.$i,$row['tauxReactivite'])->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('S'.$i,$row['newLeads'])->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('T'.$i,$row['coutProspect'])->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('U'.$i,$row['RDV'])->applyFromArray($styleArray);


$i++;


}






	//Ecriture Excel
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
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);




	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition:inline;filename=Fichier.xlsx ');
	$objWriter->save('php://output');


?>
