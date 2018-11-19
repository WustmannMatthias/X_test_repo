<link type="text/css" rel="stylesheet" href="style/mon_compte.css" />

<main class="page_content col-lg-10">
	<div class="container-fluid">
		<!--Zone formulaire-->
		<div class="row">
			<div class="container-fluid">
				<div class="mon_compte_container">
					<?php
						if (!isset($_GET['primary_key'])) {
							echo "<p class='infos'>Aucun cycle sélectionné.</p>";
						}
						else {
							$cycle = $_GET['primary_key'];
							$IDcompte = $_SESSION['ID'];
							
							$resultCycle = query($mysqli, "SELECT * FROM cycles WHERE compte = '$IDcompte' AND primary_key = '$cycle'; ");
							if (mysqli_num_rows($resultCycle) == 0) {
								echo "<p class='infos'>Le cycle demandé n'a pas été trouvé.</p>";
							}
							else {
								
								//Etape 1 : Instanciation du cycle
								$n_uplet = mysqli_fetch_assoc($resultCycle);
								$ID				= $n_uplet['ID'];
								$compte 		= $n_uplet['compte'];
								$dateAchat 		= $n_uplet['date_achat'];
								$quantiteAchat 	= $n_uplet['quantite_achat'];
								$tarif 			= $n_uplet['tarif'];
								$handicap 		= $n_uplet['handicap'];
								$commentaire 	= $n_uplet['commentaire'];
								$cycleCourant = new Cycle($ID, $compte, $dateAchat, $quantiteAchat, $tarif, $handicap, $commentaire);
								
								
								//Etape 2 : Récupération de toutes les ventes et de tous les differes et de toutes les marchandises retirées 
							 //et des remboursements de handicap de chaque cycle en cours depuis la BDD et ajouts aux objets
								$identifiant = $cycleCourant ->getIdentifiant();
								//Récupération ventes
								$ventesCycleCourant = array();
								$resultVentesCycleCourant = query($mysqli, "SELECT * FROM ventes
																	WHERE compte = '$IDcompte'
																	AND cycle = '$identifiant'; ");
								while ($n_upletVentes = mysqli_fetch_assoc($resultVentesCycleCourant)) {
									$idVente = $n_upletVentes['primary_key'];
									$compteVente = $n_upletVentes['compte'];
									$cycleVente = $n_upletVentes['cycle'];
									$clientVente = $n_upletVentes['client'];
									$quantiteVente = $n_upletVentes['quantite'];
									$tarifVente = $n_upletVentes['tarif'];
									$payementVente = $n_upletVentes['methode'];
									$dateVente = $n_upletVentes['date'];
									$vente = new Vente($idVente, $compteVente, $cycleVente, $clientVente, $quantiteVente, $tarifVente, $payementVente, $dateVente);
									array_push($ventesCycleCourant, $vente);
								}
								$cycleCourant ->setVentesTab($ventesCycleCourant);
								
								//Récupération differes
								$differesCycleCourant = array();
								$resultDifferesCycleCourant = query($mysqli, "SELECT * FROM differes
																	WHERE compte = '$IDcompte'
																	AND cycle = '$identifiant'; ");
								while ($n_upletDifferes = mysqli_fetch_assoc($resultDifferesCycleCourant)) {
									$idDiffere = $n_upletDifferes['primary_key'];
									$compteDiffere = $n_upletDifferes['compte'];
									$cycleDiffere = $n_upletDifferes['cycle'];
									$dateEmpruntDiffere = $n_upletVentes['date_emprunt'];
									$clientDiffere = $n_upletDifferes['client'];
									$montantDiffere = $n_upletDifferes['montant'];
									$montantRembourseDiffere = $n_upletDifferes['montant_rembourse'];
									$dateFinalisation = $n_upletDifferes['date_finalisation'];
									$differe = new Differe($idDiffere, $compteDiffere, $cycleDiffere, $dateEmpruntDiffere, $clientDiffere, $montantDiffere, $montantRembourseDiffere, $dateFinalisation);
									array_push($differesCycleCourant, $differe);
								}
								$cycleCourant ->setDifferesTab($differesCycleCourant);
								
								//Récupération marchandises retirées
								$resultRetraitMarchandise = query($mysqli, "SELECT sum(quantite_retiree) FROM retraits_marchandise
																			WHERE compte = '$IDcompte'
																			AND cycle = '$identifiant'; ");
								$retraitMarchandise = mysqli_fetch_row($resultRetraitMarchandise)[0];
								$cycleCourant ->setMarchandiseRetiree($retraitMarchandise);
								
								//Récupération remboursements handicap
								$resultRemboursementsHandicap = query($mysqli, "SELECT sum(quantite_remboursee) FROM remboursements_handicap
																				WHERE compte = '$IDcompte'
																				AND cycle = '$identifiant'; ");
								$handicapRembourse = mysqli_fetch_row($resultRemboursementsHandicap)[0];
								$cycleCourant ->setHandicapRembourse($handicapRembourse);
								
								unset($cycle);
								$cycle = $cycleCourant;
								

								//Etape 3 : Récupération des informations
								$identifiant 	  	= $cycle ->getIdentifiant();
								$dateAchat 		  	= $cycle ->getDateAchat();
								$argentRecupere   	= $cycle ->calculerArgentRecupere();
								$prixAchat 		  	= $cycle ->calculerPrix();
								$tarif	 		  	= $cycle ->getTarif();
								$handicap		  	= $cycle ->getHandicap();
								$handicapRestant  	= $cycle ->calculerHandicapRestant();
								$stockAchat		  	= $cycle ->getQuantiteAchat();
								$stockRestant	  	= $cycle ->calculerQuantiteRestante();
								$stockEcoule	  	= $cycle ->calculerQuantiteEcoulee();
								$stockVendu		  	= $cycle ->calculerQuantiteVendue();
								$stockRetire	  	= $cycle ->getMarchandiseRetiree();
								$ventesEffectuees 	= $cycle ->compterVentesEffectuees();
								$differes		  	= $cycle ->compterDifferes();
								$differesEnCours   	= $cycle ->compterDifferesEnCours(); 
								$argentDu		  	= $cycle ->calculerArgentDu();
								$argentVentes	  	= $cycle ->calculerArgentTotalVentes();
								$commentaire	  	= $cycle ->getCommentaire();
								$potentielNet	  	= $cycle ->calculerPotentielNet();
								$tarifVenteMoyen  	= $cycle ->calculerTarifVenteMoyen();
								$potentielBrut 	  	= $cycle ->calculerPotentielBrut();
								$valeurAjouteeNette = $cycle ->calculerValeurAjouteeNette();
								$valeurAjouteeBrute = $cycle ->calculerValeurAjouteeBrute();
								$VAPotentielle		= $cycle ->calculerValeurAjouteePotentielle();
								$sommeEnMain		= $cycle ->calculerSommeEnMain();
								
								
								//Etape 4 : Affichage des informations
								?>
								<div class="row">
									<h1>Cycle <?php echo $identifiant; ?></h1>
									<h3><?php echo $commentaire; ?></h3>
									
									<div class='col-lg-6'>
										<table class='modal_table'>
											<caption>Valeurs à l'achat</caption>
											<tbody>
												<tr>
													<td>Tarif d'achat</td>
													<td><?php echo $tarif; ?>€/pcs</td>
												</tr>
												<tr>
													<td>Stock à l'achat</td>
													<td><?php echo $stockAchat; ?>pcs</td>
												</tr>
												<tr>
													<td>Prix d'achat</td>
													<td><?php echo $prixAchat; ?>€</td>
												</tr>
												<tr>
													<td>Handicap à l'achat</td>
													<td><?php echo $handicap; ?>€</td>
												</tr>
											</tbody>
										</table>
									</div>
									
									<div class='col-lg-6'>
										<table class='modal_table'>
											<caption>Stock</caption>
											<tbody>
												<tr>
													<td>Stock à l'achat</td>
													<td><?php echo $stockAchat; ?>pcs</td>
												</tr>
												<tr>
													<td>Stock vendu</td>
													<td><?php echo $stockVendu; ?>pcs (<?php echo round((($stockVendu / $stockAchat) * 100), 2)?>%)</td>
												</tr>
												<tr>
													<td>Stock retiré</td>
													<td><?php if ($stockRetire == "") {echo 0;} else {echo $stockRetire; } ?>pcs (<?php echo round((($stockRetire / $stockAchat) * 100), 2)?>%)</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<div class='row'>
									<div class='col-lg-offset-3 col-lg-6'>
										<table class='modal_table'>
											<caption>Ventes effectuées</caption>
											<tbody>
												<tr>
													<td>Nombre de ventes</td>
													<td><?php echo $ventesEffectuees; ?></td>
												</tr>
												<tr>
													<td>Nombre de differes effectués</td>
													<td><?php echo $differes; ?></td>
												</tr>
												<tr>
													<td>Tarif de vente moyen</td>
													<td><?php if ($tarifVenteMoyen == "?") {echo $tarifVenteMoyen;} else {echo round($tarifVenteMoyen, 2)."€/pcs"; } ?></td>
												</tr>
												<tr>
													<td><strong>Total des ventes</strong></td>
													<td><?php echo $argentVentes; ?>€</td>
												</tr>
												<tr>
													<td><strong>Bénéfice</strong></td>
													<td><strong><?php echo $valeurAjouteeBrute; ?>€</strong></td>
												</tr>
											</tbody>
										</table>
									</div>
									
								</div>
									
									
								<?php
								
							}
						}
					?>
				</div>
			</div>
		</div>
	</div>
</main>
		