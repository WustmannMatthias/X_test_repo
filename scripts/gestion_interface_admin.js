$(function() {
	
	
	$("#effacer_requete").click(function() {
			$("#requeteUtilisateur").val("");
			$("#interface_requetes form").submit();
		});
		
		
		$("#interface_requetes form").submit(function(event) {
			event.preventDefault(); //bloque la soumission du formulaire
			var base = $("#select_base").val();
			var requete = $("#requeteUtilisateur").val();
			
			while (requete.indexOf(" ") != -1) {
				requete = requete.replace(" ", "+"); //Transformer les espaces en +
			}
			$("#resultat_requete").load("programmes/traitement_requetes.php?base=X&requete=" + requete);
		});
});
	
	
	