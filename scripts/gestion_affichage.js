/**
 * Ce script gère le css des différents éléments de l'interface qui doivent être ajustés dynamiquement. 
 */


$(function() {
	
	/*
	 * Gestion des médias (écran d'accueil)
	 */
	var nbCycles = $(".cycle_media").length;
	if (nbCycles > 3) {
		$(".cycle_media:last-child").css("margin-right", "10vw");
	}
	
	
	/*
	 * 
	 */
	
});
