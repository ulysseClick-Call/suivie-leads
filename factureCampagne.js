$('.listUcci div').tooltip({
  animation: true,
  html: true,
  placement: 'top',
  trigger: 'hover'
});

function EditLeadsPerf (id, marge){
  var leadsFacturé = $('#lf'+id).val();
  var coutLeadsPerf = $('#cpl'+id).val();

  var prixAchatPerf = parseFloat(leadsFacturé)*parseFloat(coutLeadsPerf);
prixAchatPerf =parseFloat(prixAchatPerf);
  prixAchatPerf = prixAchatPerf.toFixed(2);
  $('#pa'+id).attr('value',prixAchatPerf);
  $('#pa'+id).html(prixAchatPerf);

  var honorairePerf = (parseFloat(prixAchatPerf)*(1+marge))-parseFloat(prixAchatPerf);
  honorairePerf =parseFloat(honorairePerf);
  honorairePerf = honorairePerf.toFixed(2);
  $('#honoraire'+id).attr('value',honorairePerf);
  $('#honoraire'+id).html(honorairePerf);

  var budgetTotalPerf = (parseFloat(prixAchatPerf)+parseFloat(honorairePerf));
  budgetTotalPerf = budgetTotalPerf.toFixed(2);
  $('#pv'+id).attr('value',budgetTotalPerf);
  $('#pv'+id).html(budgetTotalPerf);




  totalLF();
};

function calculTaux(compteur){
  var aboutis = $('#impression'+compteur).val();
  var mailOuvert = $('#mailOuvert'+compteur).val();
  var clic = $('#clic'+compteur).val();

  var tauxOuverture = (mailOuvert/aboutis)*100;
  tauxOuverture = parseFloat(tauxOuverture);
  tauxOuverture = tauxOuverture.toFixed(2);
  if ($('#TauxOuvert'+compteur).val() == "***"){
    $('#TauxOuvert'+compteur).attr('value',"***");
    $('#TauxOuvert'+compteur).html("***");
  }else{
  $('#TauxOuvert'+compteur).attr('value',tauxOuverture);
  $('#TauxOuvert'+compteur).html(tauxOuverture);
};

  var tauxClic = (clic/aboutis)*100;
  tauxClic = parseFloat(tauxClic);
  tauxClic = tauxClic.toFixed(2);
  $('#TauxClic'+compteur).attr('value',tauxClic);
  $('#TauxClic'+compteur).html(tauxClic);

  var tauxReac = (clic/mailOuvert)*100;
  tauxReac = parseFloat(tauxReac);
  tauxReac = tauxReac.toFixed(2);
  if ($('#TauxConv'+compteur).val() == "***"){
    $('#TauxConv'+compteur).attr('value',"***");
    $('#TauxConv'+compteur).html("***");
  }else{
    $('#TauxConv'+compteur).attr('value',tauxReac);
    $('#TauxConv'+compteur).html(tauxReac);
}};

function Prospect(id, compteur){
  var newLeads = $('#newLeads'+compteur).val();
  var prixAchat = $('#pa'+id).val();

  var costProspect = (parseFloat(prixAchat)/parseFloat(newLeads));
  costProspect = parseFloat(costProspect);
  costProspect = costProspect.toFixed(2);
    $('#costNewProspect'+compteur).attr('value',costProspect);
    $('#costNewProspect'+compteur).html(costProspect);
};
function totalLF(){
if(('input.facture')!= 0){
		var totalLeadsFactures = 0;
		$('input.facture').each(function(index, elem) {
			var tmpLead = $(this).val();
			var tmpLead = parseInt(tmpLead)
			totalLeadsFactures += parseInt(tmpLead);

      $('#totalLeadsFactures').html(totalLeadsFactures);
      $('#totalLeadsFacturesHide').attr('value',totalLeadsFactures);
})}

if (('input.achat')!= 0){
		var totalAchat = 0;
		$('input.achat').each(function(index, elem) {
			var tmpAchat = $(this).val();
			var tmpAchat = tmpAchat.replace(",", ".");
			totalAchat = totalAchat + parseFloat(tmpAchat, 10);

      $('#totalAchat').html(totalAchat);
      $('#totalAchatHide').attr('value',totalAchat);
})};
      var leadsGenerer = $('.TotalUniques').html();
      leadsGenerer = parseFloat(leadsGenerer);
      var totalCoutsLeads = (parseFloat(totalAchat)/parseFloat(leadsGenerer));
      totalCoutsLeads = parseFloat(totalCoutsLeads);
      totalCoutsLeads = totalCoutsLeads.toFixed(2);
      $('#totalCPLHide').attr('value',totalCoutsLeads);
      $('#totalCPL').html(totalCoutsLeads);

      if (('input.honoraire')!= 0){
            var totalHonoraire = 0;
            $('input.honoraire').each(function(index, elem) {
              var tmpHonoraire = $(this).val();
              var tmpHonoraire = tmpHonoraire.replace(",", ".");
              totalHonoraire += parseFloat(tmpHonoraire, 10);
              totalHonoraire = parseFloat(totalHonoraire);
              totalHonoraire = totalHonoraire.toFixed(2);

              $('#totalHonoraire').html(totalHonoraire);
              $('#totalHonoraireHide').attr('value',totalHonoraire);
      })}


      var totalHonoraire = $('#totalHonoraireHide').val();
        var totalVente = ((parseFloat(totalAchat)) + (parseFloat(totalHonoraire)));
        totalVente = parseFloat(totalVente);
        totalVente = totalVente.toFixed(2);
        $('#totalVente').html(totalVente);
        $('#totalVenteHide').attr('value',totalVente);
  };

$( document ).ready(function(id, marge, leads, budget, objectif,compteur){
  if(('input.facture')!= 0){
		var totalLeadsFactures = 0;
		$('input.facture').each(function(index, elem) {
			var tmpLead = $(this).val();
			var tmpLead = parseInt(tmpLead);
			totalLeadsFactures += parseInt(tmpLead);

      $('#totalLeadsFactures').html(totalLeadsFactures);
      $('#totalLeadsFacturesHide').attr('value',totalLeadsFactures);
})}


      if (('input.achat')!= 0){
      		var totalAchat = 0;
  		$('input.achat').each(function(index, elem) {
  			var tmpAchat = $(this).val();
  			var tmpAchat = tmpAchat.replace(",", ".");
  			totalAchat += parseFloat(tmpAchat, 10);

        $('#totalAchat').html(totalAchat);
        $('#totalAchatHide').attr('value',totalAchat);
})}


  var totalAchat = 0;
  $('input.achat').each(function(index, elem) {
    var tmpAchat = $(this).val();
    var tmpAchat = tmpAchat.replace(",", ".");
    totalAchat = totalAchat + parseFloat(tmpAchat, 10);
})
        var leadsGenerer = $('.TotalUniques').html();
        leadsGenerer = parseFloat(leadsGenerer);
        var totalCoutsLeads = (parseFloat(totalAchat)/parseFloat(leadsGenerer));
        totalCoutsLeads = parseFloat(totalCoutsLeads);
        totalCoutsLeads = totalCoutsLeads.toFixed(2);
        $('#totalCPLHide').attr('value',totalCoutsLeads);
        $('#totalCPL').html(totalCoutsLeads);



if (('input.honoraire')!= 0){
      var totalHonoraire = 0;
      $('input.honoraire').each(function(index, elem) {
        var tmpHonoraire = $(this).val();
        var tmpHonoraire = tmpHonoraire.replace(",", ".");
        totalHonoraire += parseFloat(tmpHonoraire, 10);
        totalHonoraire = parseFloat(totalHonoraire);
        totalHonoraire = totalHonoraire.toFixed(2);

        $('#totalHonoraire').html(totalHonoraire);
        $('#totalHonoraireHide').attr('value',totalHonoraire);
})}

    var totalVente = ((parseFloat(totalAchat)) + (parseFloat(totalHonoraire)));
    totalVente = totalVente.toFixed(2);

    $('#totalVente').html(totalVente);
    $('#totalVenteHide').attr('value',totalVente);
});

function newLeads (id, compteur){
  if(('input.newLead')!= 0){
        var totalNewLeads = 0;
        $('input.newLead').each(function(index, elem) {
          var tmpLeads = $(this).val();
          var tmpLeads = tmpLeads.replace(",", ".");
          tmpLeads = parseFloat(tmpLeads);
          totalNewLeads += parseFloat(tmpLeads);

          $('#totalNewLeadHide').attr('value',parseFloat(totalNewLeads));
          $('#totalNewLead').html(totalNewLeads);
        })}

        var prixAchat = $('#pa'+id).val();
        var newLeads = $('#newLeads'+compteur).val();
        var coutsProspect = ((parseFloat(prixAchat))/(parseFloat(newLeads)));
        coutsProspect = parseFloat(coutsProspect);
        coutsProspect = coutsProspect.toFixed(2);

        $('#costNewProspect'+compteur).attr('value',coutsProspect);
        $('#costNewProspect'+compteur).html(coutsProspect);

        var totalAchat = $('#totalAchatHide').val();
        var coutTotalProspect = ((parseFloat(totalAchat)/parseFloat(totalNewLeads)));
        coutTotalProspect = parseFloat(coutTotalProspect);
        coutTotalProspect = coutTotalProspect.toFixed(2);

        $('#totalCostProspectHide').attr('value',coutTotalProspect);
        $('#totalCostProspect').html(coutTotalProspect);
};

function totalRDV (){
  if(('input.RDV')!= 0){
        var totalRDV = 0;
        $('input.RDV').each(function(index, elem) {
          var tmpRDV = $(this).val();
          var tmpRDV = tmpRDV.replace(",", ".");
              tmpRDV = parseInt(tmpRDV);
              totalRDV += parseInt(tmpRDV);

          $('#totalRDVHide').attr('value',totalRDV);
          $('#totalRDV').html(totalRDV);
        })}
};
