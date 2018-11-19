<?php
	//inclusion des modals
	require "frames/modals.php";
	
	
	
	
	$ID = $_SESSION['ID'];
	$resultat = query($mysqli, "SELECT quantite_possedee, argent_possede, dette, commentaire
								FROM comptes
								WHERE ID = '$ID'; 
					 ");
	$n_uplet = mysqli_fetch_assoc($resultat);
	$stock = $n_uplet['quantite_possedee'];
	$portefeuille = $n_uplet['argent_possede'];
	$dette = $n_uplet['dette'];
	$commentaire = $n_uplet['commentaire'];
?>


<link type="text/css" rel="stylesheet" href="style/mon_compte.css">

<main class="page_content col-lg-10">
	<div class="container-fluid">
		<div class="row">
			<div class="container-fluid">
				<div class="mon_compte_container">
					<h1>Mon compte</h1>
					
					<p class="mon_compte_infos"><?php echo $commentaire; ?></p>
					
					<div class="col-lg-6">
						<?php
							/*
							 * Calcul des la valeur ajoutées du compte
							 */
							
							//1) Valeur ajoutée sans compter le stock
							$sommeDue;
							
							$resultMontantDifferes = query($mysqli, "SELECT sum(montant) montant, sum(montant_rembourse) montant_rembourse
																	FROM differes
																	WHERE compte = '$ID'
																	AND montant != montant_rembourse;
														 ");
							if (mysqli_num_rows($resultMontantDifferes) == 0) {
								$sommeDue = 0;
							}
							else {
								$montantDifferes = mysqli_fetch_assoc($resultMontantDifferes);
								$sommeDue = $montantDifferes['montant'] - $montantDifferes['montant_rembourse'];
							}
							
							$valeurAjouteeActuelle = round($portefeuille + $sommeDue - $dette, 2);
							echo "<h4>Valeur ajoutée de l'entreprise sans compter le stock : <strong>$valeurAjouteeActuelle</strong> €</h4>";
							
							//2) Valeur ajoutée en comptant le stock
							$tarifMoyen;
							
							$resultTarif = query($mysqli, "SELECT avg(ventes.tarif) tarif
															FROM ventes
															INNER JOIN cycles ON ventes.cycle = cycles.ID
															WHERE ventes.compte = '$ID'
															AND cycles.compte = '$ID'
															AND cycles.actif = 'true'");
							if (mysqli_num_rows($resultTarif) == 0) {
								$tarifMoyen = 0;
							}
							else {
								$tarifMoyen = mysqli_fetch_assoc($resultTarif)['tarif'];
								if ($tarifMoyen == "") {
									echo "<p class='infos'>Pas assez d'informations pour calculer la valeur ajoutée en comptant le stock.</p>";
								}
								else {
									$valeurAjouteePotentielle = round($valeurAjouteeActuelle + ($stock * $tarifMoyen), 2);
									echo "<h4>Valeur ajoutée de l'entreprise en comptant le stock : <strong>$valeurAjouteePotentielle</strong> €</h4>";
								}
							}
							
							
							
						?>
					</div>
					
					<div class="col-lg-6">
						<a class="lien_page_mon_compte" href="#" data-toggle="modal" data-target="#modal_modification_informations">
							Modifier mes informations...
						</a>
						<br />
						<br />
						<a class="lien_page_mon_compte" href="#" data-toggle="modal" data-target="#modal_retrait_argent">
							Enregistrer un retrait d'argent...
						</a>
						<br />
						<a class="lien_page_mon_compte" href="#" data-toggle="modal" data-target="#modal_depot_argent">
							Enregistrer un dépot d'argent...
						</a>
						<br />	
						
						<?php
							if (isset($_SESSION['messageConfirmationModification'])) {
								$msg = $_SESSION['messageConfirmationModification'];
								echo "<p class='messageConfirmationModification'>$msg</p>";
								unset($_SESSION['messageConfirmationModification']);
							}
							elseif (isset($_SESSION['messageErreurModification'])) {
								$msg = $_SESSION['messageErreurModification'];
								echo "<br /><p class='messageErreurModification'>$msg</p>";
								unset($_SESSION['messageErreurModification']);
							}
						?>
					</div>
				</div>
			</div>
		</div>
		
		<!-- ZONE HISTORIQUE -->
		<div class="row">
			<div class="container-fluid">
				<div class="mon_compte_container little_down">
					<h2>Historique des modifications</h2>
					
					<?php
						$historiqueResult = query($mysqli, "SELECT date, attribut_modifie, valeur_modification, motif, operation
															FROM modifications_compte 
															WHERE compte = '$ID'
															ORDER BY date DESC; ");
						
						//Cas ou l'historique est vide
						if (mysqli_num_rows($historiqueResult) == 0) {
							echo "<p class='mon_compte_infos'>Aucune modification n'a été apportée à ce compte</p>";
						}
						else {
							//Sinon, affichage d'un tableau récapitulatif
							echo "<table class='table table-bordered table-striped my_table'>";
							echo "<thead><tr>";
							echo "<th>Date</th>
								  <th>Opération</th>
								  <th>Valeur</th>
								  <th>Motif</th>
								  <th>Modification</th>
								  ";
							echo "</tr></thead>";
							
							echo "<tbody>";
							while ($n_uplet = mysqli_fetch_assoc($historiqueResult)) {
								$date = $n_uplet['date'];
								$modification = $n_uplet['attribut_modifie'];
								$valeur = $n_uplet['valeur_modification'];
								$motif = $n_uplet['motif'];
								$operation = $n_uplet['operation'];
								
								echo "<tr>
										<td>$date</td>
										<td>$operation</td>
										<td>$valeur</td>
										<td>$motif</td>
										<td>$modification</td>
									  </tr>";
							}
							
							echo "</tbody></table>";
						}
					?>
					
					
					
				</div>
			</div>
		</div>
	</div>
</main>






