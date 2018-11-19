/**
 * Ce script gère les interactions avec l'utilisateur (vérification des données des formulaires, etc) 
 */

$(function() {
	
	/*
	 * Opérations depuis la page mon Compte
	 */
	$("#modal_modification_informations .validation_modal").click(function() {
		$("#modal_modification_informations form").submit();
	});
	$("#modal_retrait_argent .validation_modal").click(function() {
		$("#modal_retrait_argent form").submit();
	});
	$("#modal_depot_argent .validation_modal").click(function() {
		$("#modal_depot_argent form").submit();
	});
	$("#modal_retrait_marchandise .validation_modal").click(function() {
		$("#modal_retrait_marchandise form").submit();
	});
	
	//Validation du modal ajout_client
	$("#modal_ajout_client .validation_modal").click(function() {
		$("#modal_ajout_client form").submit();
	});
	
	
	
	/*
	 * Création d'un nouveau cycle
	 */
	$(".methodePayement").click(function() {
		var val = $(".methodePayement").val();
		if (val == "Accompte") {
			$(".champs_optionnel").show();
		}
		else if (val != "Accompte") {
			$(".champs_optionnel").hide();
		}
	});
	
	$("#modal_ajout_cycle .validation_modal").click(function() {
		$("#modal_ajout_cycle form").submit();
	});
	
	
	
	
	
	/*
	 * Enregistrements ventes
	 * Les data-identifier servent à repérer le modal dans lequel l'utilisateur travaille, afin de ne pas
	 * 	voir les valeurs faussées par celles des autres modals
	 */
	$('.methodePayementVente').click(function() { //Afficher ou non le champs Montant Payé
		var dataIdentifier = $(this).data("identifier");
		var val = $('.methodePayementVente[data-identifier="'+dataIdentifier+'"]').val();
		if (val == "Accompte") {
			$('.champs_optionnel_vente[data-identifier="'+dataIdentifier+'"]').show();
		}
		else if (val != "Accompte") {
			$('.champs_optionnel_vente[data-identifier="'+dataIdentifier+'"]').hide();
		}
	});


	
	function echecVente(data) { //Si jamais la requete échoue
		alert("Echec de la requete AJAX pour l'enregistrement de la vente.");
	}
	
	$(".validation_vente").click(function() { //Récupération des valeurs (!!! ajouter des tests sur les formats !!!)
		var dataIdentifier = $(this).data("identifier");		
		var dateVente = $('.modal_vente form[data-identifier="' + dataIdentifier + '"] input[name="dateVente"]').val(); 
		var client = $('.modal_vente form[data-identifier="' + dataIdentifier + '"] select[name="client"]').val();
		var quantiteVendue = $('.modal_vente form[data-identifier="' + dataIdentifier + '"] input[name="quantiteVendue"]').val();
		var tarif = $('.modal_vente form[data-identifier="' + dataIdentifier + '"] input[name="tarif"]').val();
		var methodePayement = $('.modal_vente form[data-identifier="' + dataIdentifier + '"] select[name="payement"]').val();
		var montantPaye = false;
		if (methodePayement == "Accompte") {
			var montantPaye = $('.modal_vente form[data-identifier="' + dataIdentifier + '"] input[name="montantPaye"]').val();
		}
		$.post("programmes/enregistrement_vente.php", {
			dateVente: dateVente, 
			quantiteVendue: quantiteVendue, 
			tarif: tarif, 
			methodePayement: methodePayement, 
			montantPaye: montantPaye,
			cycle: dataIdentifier,
			client: client
		}).done(function(data) {
			window.location.href = "index.php";
			//$("#resultatAJAX").html(data);
		});
	});
	
	
	
	
	
	
	/*
	 * Validation d'un retrait de marchandise
	 */
	$(".validation_retrait_marchandise").click(function() {
		var dataIdentifier = $(this).data("identifier");
		var motif = $('.modal_retrait_marchandise form[data-identifier="' + dataIdentifier + '"] input[name="motif"]').val();
		var montant = $('.modal_retrait_marchandise form[data-identifier="' + dataIdentifier + '"] input[name="montant"]').val(); 
		var dateRetrait = $('.modal_retrait_marchandise form[data-identifier="' + dataIdentifier + '"] input[name="dateRetrait"]').val();
		
		$.post("programmes/enregistrement_retrait_marchandise.php", {
			dateRetrait: dateRetrait,
			montant: montant,
			motif: motif,
			cycle: dataIdentifier
		}).done(function(data) {
			//$("#resultatAJAX").html(data);
			window.location.href = "index.php";
		});
		
	});
	
	
	/*
	 * Validation d'un remboursement de handicap
	 */
	$(".validation_remboursement_handicap").click(function() {
		var dataIdentifier = $(this).data("identifier");
		var montant = $('.modal_remboursement_handicap form[data-identifier="' + dataIdentifier + '"] input[name="montant"]').val(); 
		var dateRemboursement = $('.modal_remboursement_handicap form[data-identifier="' + dataIdentifier + '"] input[name="dateRemboursement"]').val();
		
		$.post("programmes/enregistrement_remboursement_handicap.php", {
			dateRemboursement: dateRemboursement,
			montant: montant,
			cycle: dataIdentifier
		}).done(function(data) {
			//$("#resultatAJAX").html(data);
			window.location.href = "index.php";
		});
	});
	
	
	
	
	
	
	/*
	 * Validation d'un remboursement de differe
	 */
	$(".remboursement_differe").submit(function(event) {
		event.preventDefault();
		
		var identifiant = $(this).data("identifier");
		var cycle = $(this).data("cycle");
		var montant = $('.remboursement_differe[data-identifier="' + identifiant + '"] input[name="valeur"]').val();
		
		$.post("programmes/enregistrement_remboursement_differe.php", {
			id: identifiant,
			montant: montant,
			cycle: cycle
		}).done(function(data) {
			//$("#resultatAJAX").html(data);
			window.location.href = "index.php";
		});
	});
	
	
	
	
	
	
	/**
	 * Lorsqu'on agit sur le TARIF
	 */
	$('.modal_vente form input[name="tarif"]').blur(function() {
		id = $(this).data("identifier");
		var champsQte = $('.modal_vente form[data-identifier="' + id + '"] input[name="quantiteVendue"]');
		var champsTarif = $('.modal_vente form[data-identifier="' + id + '"] input[name="tarif"]');
		var champsPrix = $('.modal_vente form[data-identifier="' + id + '"] input[name="prix"]');

		//Calcul du prix si la quantite et le tarif sont remplis
		if (champsQte.val() != "" && champsTarif.val() != "") {
			champsPrix.val(parseFloat(champsQte.val()) * parseFloat(champsTarif.val()));
		}
		//Calcul de la quantite si le prix et le tarif sont remplis
		if (champsPrix.val() != "" && champsTarif.val() != "") {
			champsQte.val(parseFloat(champsPrix.val()) / parseFloat(champsTarif.val()));
		}
	});

	/**
 	 * Lorsqu'on agit sur la QUANTITE
 	 */
	$('.modal_vente form input[name="quantiteVendue"]').blur(function() {
		id = $(this).data("identifier");
		var champsQte = $('.modal_vente form[data-identifier="' + id + '"] input[name="quantiteVendue"]');
		var champsTarif = $('.modal_vente form[data-identifier="' + id + '"] input[name="tarif"]');
		var champsPrix = $('.modal_vente form[data-identifier="' + id + '"] input[name="prix"]');

		//Calcul du prix si la quantite et le tarif sont remplis
		if (champsQte.val() != "" && champsTarif.val() != "") {
			champsPrix.val(parseFloat(champsQte.val()) * parseFloat(champsTarif.val()));
		}
		//Calcul du tarif si la quantite et le prix sont remplis
		else if (champsQte.val() != "" && champsPrix.val() != "") {
			champsTarif.val(parseFloat(champsPrix.val()) / parseFloat(champsQte.val()));
		}
	});

	/**
 	 * Lorsqu'on agit sur le PRIX
 	 */
	$('.modal_vente form input[name="prix"]').blur(function() {
		id = $(this).data("identifier");
		var champsQte = $('.modal_vente form[data-identifier="' + id + '"] input[name="quantiteVendue"]');
		var champsTarif = $('.modal_vente form[data-identifier="' + id + '"] input[name="tarif"]');
		var champsPrix = $('.modal_vente form[data-identifier="' + id + '"] input[name="prix"]');

		//Calcul du tarif si la quantite et le prix sont remplis
		if (champsQte.val() != "" && champsPrix.val() != "") {
			champsTarif.val(parseFloat(champsPrix.val()) / parseFloat(champsQte.val()));
		}
		//Calcul de la quantite si le prix et le tarif sont remplis
		else if (champsPrix.val() != "" && champsTarif.val() != "") {
			champsQte.val(parseFloat(champsPrix.val()) / parseFloat(champsTarif.val()));
		}
		
	});


	
	
	
	
});
