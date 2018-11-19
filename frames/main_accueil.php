<?php
	
	//inclusion des modals
	require_once "frames/modals.php";
	$cyclesCourants;
	$cyclesString;
	$IDcompte = $_SESSION['ID'];
	$clientsTab = getClientsTab();
?>

<main class="page_content col-lg-10">
	<div class="container-fluid">
		<!--ZONE CYCLES-->
		<div class="row">
			<div class="container-fluid zone_cycles">
				<div class="row">
					<div class="col-lg-3">
						<a class="lien_ajout_cycle" href="#" data-toggle="modal" data-target="#modal_ajout_cycle">
							Ajouter un cycle...
						</a>
					</div>
					<div class="col-lg-7 col-lg-offset 2">
						<?php
							if (isset($_SESSION["messageErreurCreationCycle"])) {
								$msg = $_SESSION['messageErreurCreationCycle'];
								echo "<p class='messageErreurCreationCycle'>$msg</p>";
								unset($_SESSION['messageErreurCreationCycle']);
							}
							elseif (isset($_SESSION["messageConfirmationCreationCycle"])) {
								$msg = $_SESSION['messageConfirmationCreationCycle'];
								echo "<p class='messageConfirmationCreationCycle'>$msg</p>";
								unset($_SESSION['messageConfirmationCreationCycle']);
							}
							elseif (isset($_SESSION["messageConfirmationEnregistrementVente"])) {
								$msg = $_SESSION['messageConfirmationEnregistrementVente'];
								echo "<p class='messageConfirmationEnregistrementVente'>$msg</p>";
								unset($_SESSION['messageConfirmationEnregistrementVente']);
							}
							elseif (isset($_SESSION["messageErreurEnregistrementVente"])) {
								$msg = $_SESSION['messageErreurEnregistrementVente'];
								echo "<p class='messageErreurEnregistrementVente'>$msg</p>";
								unset($_SESSION['messageErreurEnregistrementVente']);
							}
							elseif (isset($_SESSION["messageConfirmationEnregistrementRetraitMarchandise"])) {
								$msg = $_SESSION['messageConfirmationEnregistrementRetraitMarchandise'];
								echo "<p class='messageConfirmationEnregistrementRetraitMarchandise'>$msg</p>";
								unset($_SESSION['messageConfirmationEnregistrementRetraitMarchandise']);
							}
							elseif (isset($_SESSION["messageErreurEnregistrementRetraitMarchandise"])) {
								$msg = $_SESSION['messageErreurEnregistrementRetraitMarchandise'];
								echo "<p class='messageErreurEnregistrementRetraitMarchandise'>$msg</p>";
								unset($_SESSION['messageErreurEnregistrementRetraitMarchandise']);
							}
							elseif (isset($_SESSION["messageConfirmationRemboursementHandicap"])) {
								$msg = $_SESSION['messageConfirmationRemboursementHandicap'];
								echo "<p class='messageConfirmationRemboursementHandicap'>$msg</p>";
								unset($_SESSION['messageConfirmationRemboursementHandicap']);
							}
							elseif (isset($_SESSION["messageErreurRemboursementHandicap"])) {
								$msg = $_SESSION['messageErreurRemboursementHandicap'];
								echo "<p class='messageErreurRemboursementHandicap'>$msg</p>";
								unset($_SESSION['messageErreurRemboursementHandicap']);
							}
							elseif (isset($_SESSION["messageConfirmationRemboursementDiffere"])) {
								$msg = $_SESSION['messageConfirmationRemboursementDiffere'];
								echo "<p class='messageConfirmationRemboursementDiffere'>$msg</p>";
								unset($_SESSION['messageConfirmationRemboursementDiffere']);
							}
							elseif (isset($_SESSION["messageErreurRemboursementDiffere"])) {
								$msg = $_SESSION['messageErreurRemboursementDiffere'];
								echo "<p class='messageErreurRemboursementDiffere'>$msg</p>";
								unset($_SESSION['messageErreurRemboursementDiffere']);
							}
						?>
					</div>
				</div>
				
				
				<div class="row zone_medias">
					
					<?php
						/**
						 * Affichage des medias correspondants aux cycles en cours du compte courant
						 */
						
						//Etape 1 : Récupérer tous les cycles actifs du compte
						$resultCyclesCourants = query($mysqli, "SELECT * FROM cycles WHERE actif = 'true' AND compte = '$IDcompte' ORDER BY date_achat DESC; ");
						if (mysqli_num_rows($resultCyclesCourants) == 0) {
							echo "<p class='infos'>Aucun cycle actif sur ce compte.</p>";
						}
						else {
							$cyclesCourants = array();
							while ($n_uplet = mysqli_fetch_assoc($resultCyclesCourants)) {
								$ID 			= $n_uplet['ID'];
								$compte 		= $n_uplet['compte'];
								$dateAchat 		= $n_uplet['date_achat'];
								$quantiteAchat 	= $n_uplet['quantite_achat'];
								$tarif 			= $n_uplet['tarif'];
								$handicap 		= $n_uplet['handicap'];
								$commentaire 	= $n_uplet['commentaire'];
								$cycle = new Cycle($ID, $compte, $dateAchat, $quantiteAchat, $tarif, $handicap, $commentaire);
								array_push($cyclesCourants, $cycle);
							}
							
							//Etape 2 : Récupération de toutes les ventes et de tous les differes et de toutes les marchandises retirées 
							 //et des remboursements de handicap de chaque cycle en cours depuis la BDD et ajouts aux objets
							foreach ($cyclesCourants as $cycleCourant) {
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
								
								//Récupération marchandises retirées
								$resultRemboursementsHandicap = query($mysqli, "SELECT sum(quantite_remboursee) FROM remboursements_handicap
																				WHERE compte = '$IDcompte'
																				AND cycle = '$identifiant'; ");
								$handicapRembourse = mysqli_fetch_row($resultRemboursementsHandicap)[0];
								$cycleCourant ->setHandicapRembourse($handicapRembourse);
							}
							
							
							//Etape 3 : Vérification que les cycles ayant l'attribut actif = 'true' soient bien actifs
							//Rappel : cycle actif : Stock >= 0 OU handicap restant >= 0 OU differes en cours >= 0
							$cycleDesactive = false;
							foreach ($cyclesCourants as $cycle) {
								if (!($cycle ->estActif())) {
									$id = $cycle ->getIdentifiant();
									query($mysqli, "UPDATE cycles
													SET actif = 'false'
													WHERE compte = '$IDcompte' AND ID = '$id';
										  ");
									$cycleDesactive = true;
								}
							}
							if ($cycleDesactive) {
								mysqli_close($mysqli);
								echo "<script>window.location.href = 'index.php';</script>";
								exit;
							}
							
							
							//Etape 4 : Affichage d'un media + modal pour chaque cycle
							foreach ($cyclesCourants as $cycle) {
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
								
								?>
								<!-- Création du média de chaque cycle -->
								<a class="" href="#" data-toggle="modal" data-target="#modal_cycle_<?php echo $identifiant; ?>">
									<div class="media cycle_media">
										<div class="media-body">
											<h3 class="media-heading center">Cycle <?php echo $identifiant; ?></h3>
											<p class='media_date_cycle'>Début :  <?php echo $dateAchat; ?></p>
											<ul>
												<li>Prix d'achat : <?php echo $prixAchat; ?>€</li>
												<li>Stock restant : <?php echo $stockRestant; ?>pcs</li>
												<li>Ventes effectuées : <?php echo $ventesEffectuees; ?></li>
												<li>Montant récupéré : <?php echo $argentRecupere; ?>€</li>
												<li>Differes en cours : <?php echo $differesEnCours; ?></li>
												<li>Bénéfice estimé : <?php echo round($VAPotentielle, 2); ?>€</li>
											</ul>
										</div>
									</div>
								</a>
								
								<!-- Création du modal associé à chaque média -->
								<div class="modal fade" id="modal_cycle_<?php echo $identifiant; ?>" tabindex="-1" role="dialog" aria-labelledby="modal_cycle_label" aria-hidden="true">
									<div class="modal-dialog modal-dialog-centered" role="document">
										<div class="modal-content modal_content modal_cycle">
											<div class="modal-header modal_header">
												<h3 class="modal-title">Cycle <?php echo $identifiant; ?></h3>
												<button class="close close_button" data-dismiss="modal" aria-label="Close"> <!--Croix pour fermer-->
										        	<span aria-hidden="true">&times;</span>
										        </button>
											</div>
											
											<div class="modal-header modal_button_zone">
								        		<button type="submit" name="submit" class="btn btn-primary" data-toggle="modal" 
								        			data-target="#modal_cycle_<?php echo $identifiant; ?>_vente">Enregistrer une vente</button>
								        		<button type="submit" name="submit" class="btn btn-primary" data-toggle="modal"
								        			data-target="#modal_cycle_<?php echo $identifiant; ?>_retrait_marchandise">Enregistrer un retrait de marchandise</button>
								        		<button type="submit" name="submit" class="btn btn-primary" data-toggle="modal"
								        			data-target="#modal_cycle_<?php echo $identifiant; ?>_remboursement_handicap">Rembourser le handicap</button>
											</div>
											
											<div class="modal-body">
												<div class="container-fluid">
													<div class="row">
														<div class="col-lg-8 col-lg-offset-2">
															<p class="modal_cyle_commentaire"><?php echo $commentaire; ?></p>
														</div>
													</div>
													<div class="row">
														<div class="col-lg-6">
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

														<div class="col-lg-6">
															<table class='modal_table'>
																<caption>Stock</caption>
																<tbody>
																	<tr>
																		<td>Stock vendu</td>
																		<td><?php echo $stockVendu; ?>pcs</td>
																	</tr>
																	<tr>
																		<td>Stock retiré</td>
																		<td><?php if ($stockRetire == "") {echo 0;} else {echo $stockRetire; } ?>pcs</td>
																	</tr>
																	<tr>
																		<td>Stock écoulé (tout inclu)</td>
																		<td><?php echo $stockEcoule; ?>pcs</td>
																	</tr>
																	<tr>
																		<td><strong>Stock restant</strong></td>
																		<td><?php echo $stockRestant; ?>pcs</td>
																	</tr>
																</tbody>
															</table>
														</div>	
														
														<div class="col-lg-12">
															<table class='modal_table'>
																<caption>Ventes effectuées</caption>
																<tbody>
																	<tr>
																		<td>Nombre de ventes</td>
																		<td><?php echo $ventesEffectuees; ?></td>
																	</tr>
																	<tr>
																		<td>Nombre de differes</td>
																		<td><?php echo $differes; ?></td>
																	</tr>
																	<tr>
																		<td>Nombre de differes en cours</td>
																		<td><?php echo $differesEnCours ?></td>
																	</tr>
																	<tr>
																		<td>Montant récupéré</td>
																		<td><?php echo $argentRecupere; ?>€</td>
																	</tr>
																	<tr>
																		<td>Montant à récupérer</td>
																		<td><?php echo $argentDu; ?>€</td>
																	</tr>
																	<tr title="Montant récupéré + montant à récupérer">
																		<td><strong>Total des ventes</strong></td>
																		<td><?php echo $argentVentes; ?>€</td>
																	</tr>
																</tbody>
															</table>
														</div>
														
														<div class="col-lg-6">
															<table class='modal_table'>
																<caption>Prévisions</caption>
																<tbody>
																	<tr>
																		<td>Tarif de vente moyen</td>
																		<td><?php if ($tarifVenteMoyen == "?") {echo $tarifVenteMoyen;} else {echo round($tarifVenteMoyen, 2)."€/pcs"; } ?></td>
																	</tr>
																	<tr title="Ce potentiel est calculé en fonction de la quantite d'argent déjà récupéré et du stock de marchandise restant.                  P_net = (argent récupéré / quantité vendue) * stock restant">
																		<td>Potentiel Net</td>
																		<td><?php if($potentielNet == "?") {echo $potentielNet;} else {echo round($potentielNet, 2)."€"; } ?></td>
																	</tr>
																	<tr title="Ce potentiel est calculé en fonction de la somme des ventes (les differes non récupérés sont pris en compte) et du stock de marchandise restant.                P_brut = tarif moyen * stock restant                Cette valeur est celle que vous récupèrerez encore sur ce cycle en continuant à vendre au même rythme et en récupérant tous vos differes.">
																		<td><strong>Potentiel Brut</strong></td>
																		<td><?php if($potentielBrut == "?") {echo $potentielBrut;} else {echo round($potentielBrut, 2)."€"; }?></td>
																	</tr>
																	<tr>
																		<td>Dette à rembourser</td>
																		<td><?php echo $handicapRestant ?>€</td>
																	</tr>
																</tbody>
															</table>
														</div>
														
														<div class="col-lg-6">															
															<table class='modal_table'>
																<caption>Bénéfices</caption>
																<tbody>
																	<tr title="Somme d'argent récupérée par rapport à la somme dépensé pour l'achat du cycle. Le handicap restant est compté dans la somme dépensée. Les differes non remboursés ne sont pas comptabilisés.">
																		<td>Bénéfice net</td>
																		<td><?php echo $valeurAjouteeNette; ?>€</td>
																	</tr>
																	<tr title="Somme d'argent récupérée par rapport à la somme dépensé pour l'achat du cycle. Le handicap restant est compté dans la somme dépensée. Les differes non remboursés sont pas comptabilisés. Une fois que tous les differes seront remboursés, cette valeur sera égale au bénéfice net.">
																		<td>Bénéfice Brut</td>
																		<td><?php echo $valeurAjouteeBrute; ?>€</td>
																	</tr>
																	<tr title="Cette valeur est le bénéfice prévisionnel du cycle. Elle prend en compte les ventes effectuées, les differes à récupérer, et le montant des ventes à venir jusqu'à la fin du cycle en fonction du tarif de vente moyen.             Benéfice potentiel = bénéfice brut + potentiel brut">
																		<td><strong>Bénéfice potentiel</strong></td>
																		<td><?php if ($VAPotentielle == "?") {echo $VAPotentielle;} else {echo round($VAPotentielle, 2)."€"; } ?></td>
																	</tr>
																	<tr title="Cette valeur est la valeur théorique que vous possédez. Elle prend en compte l'argent récupéré sur le cycle (les differes en cours ne sont donc pas comptabilisés) et la dette à rembourser.         Somme en main = argent récupéré - handicap remboursé">
																		<td>Somme en main</td>
																		<td><?php echo $sommeEnMain ?>€</td>
																	</tr>
																</tbody>
															</table>
														</div>
													</div>
												</div>
											</div>
											
											<div class="modal-footer">
								        		<button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
											</div>
										</div>
									</div>
								</div>
								
								
								<!-- Création du modal "Enregistrer une vente" pour chaque media -->
								<div class="modal fade modal_vente" id="modal_cycle_<?php echo $identifiant; ?>_vente" tabindex="-1" role="dialog" aria-labelledby="modal_cycle_vente_label" aria-hidden="true">
									<div class="modal-dialog modal-dialog-centered" role="document">
										<div class="modal-content modal_content">
											<div class="modal-header modal_header">
												<h3 class="modal-title">Enregistrer une vente - Cycle <?php echo $identifiant; ?></h3>
												<button class="close close_button" data-dismiss="modal" aria-label="Close"> <!--Croix pour fermer-->
										        	<span aria-hidden="true">&times;</span>
										        </button>
											</div>
											
											<div class="modal-body">
												<div class="container-fluid">
													<div class="row">
														<form class="col-lg-8 col-lg-offset-2 modal_form" data-identifier="<?php echo $identifiant; ?>">
															<div class="row form-group">
																<label class="col-lg-6 control-label">Date de la vente</label>
																<input class="col-lg-6" type="date" name="dateVente" />
															</div>
															<div class="row form-group">
																<label class="col-lg-6 control-label">Client</label>
																<select class="col-lg-6" name="client">
																	<?php
																		foreach ($clientsTab as $val => $nom) {
																			echo '<option value="'.$val.'">'.$nom.'</option>';
																		}
																	?>
																</select>
															</div>
															<div class="row form-group">
																<label class="col-lg-6 control-label">Quantité vendue</label>
																<input class="col-lg-6" type="text" name="quantiteVendue" data-identifier="<?php echo $identifiant; ?>"/>
															</div>
															<div class="row form-group">
																<label class="col-lg-6 control-label">Tarif</label>
																<input class="col-lg-6" type="text" name="tarif" data-identifier="<?php echo $identifiant; ?>"/>
															</div>
															<div class="row form-group">
																<label class="col-lg-6 control-label">Prix</label>
																<input class="col-lg-6" type="text" name="prix" data-identifier="<?php echo $identifiant; ?>"/>
															</div>
															<div class="row form-group">
																<label class="col-lg-6 control-label">Méthode de payement</label>
																<select name="payement" class="methodePayementVente" data-identifier="<?php echo $identifiant; ?>">
																	<option value="Cash">Cash</option>
																	<option value="Differe">Differe</option>
																	<option value="Accompte">Accompte</option>
																</select>
															</div>
															<div class="champs_optionnel_vente" data-identifier="<?php echo $identifiant; ?>">
																<div class="row form-group">
																	<label class="col-lg-6 control-label">Montant payé</label>
																	<input class="col-lg-6" type="text" name="montantPaye" />
																</div>
															</div>
														</form>
													</div>
												</div>
											</div>
											
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-dismiss="modal">Abandonner</button>
								        		<button type="submit" name="submit" class="btn btn-primary validation_vente"  data-identifier="<?php echo $identifiant; ?>">
								        			Enregistrer la vente</button>
											</div>
										</div>
									</div>
								</div>
								
								
								<!-- Création du modal "Enregistrer un retrait de marchandise" pour chaque media -->
								<div class="modal fade modal_retrait_marchandise" id="modal_cycle_<?php echo $identifiant; ?>_retrait_marchandise" tabindex="-1" role="dialog" 
									aria-labelledby="modal_cycle_retrait_marchandise_label" aria-hidden="true">
									
									<div class="modal-dialog modal-dialog-centered" role="document">
										<div class="modal-content modal_content">
											<div class="modal-header modal_header">
												<h3 class="modal-title">Retrait de marchandise - Cycle <?php echo $identifiant; ?></h3>
												<button class="close close_button" data-dismiss="modal" aria-label="Close"> <!--Croix pour fermer-->
										        	<span aria-hidden="true">&times;</span>
										        </button>
											</div>
											
											<div class="modal-body">
												<div class="container-fluid">
													<div class="row">
														<form class="col-lg-8 col-lg-offset-2 modal_form" data-identifier="<?php echo $identifiant; ?>">
															<div class="row form-group">
																<label class="col-lg-6 control-label">Quantité</label>
																<input class="col-lg-6" type="text" name="montant"/>
															</div>
															<div class="row form-group">
																<label class="col-lg-6 control-label">Date du retrait</label>
																<input class="col-lg-6" type="date" name="dateRetrait" />
															</div>
															<div class="row form-group">
																<label class="col-lg-6 control-label">Motif</label>
																<input class="col-lg-6" type="text" name="motif" />
															</div>
														</form>
													</div>
												</div>
											</div>
											
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-dismiss="modal">Abandonner</button>
								        		<button type="submit" name="submit" class="btn btn-primary validation_retrait_marchandise"  data-identifier="<?php echo $identifiant; ?>">
								        			Enregistrer le retrait de marchandise</button>
											</div>
										</div>
									</div>
								</div>
								
								
								<!-- Création du modal "Rembourser le handicap" pour chaque media -->
								<div class="modal fade modal_remboursement_handicap" id="modal_cycle_<?php echo $identifiant; ?>_remboursement_handicap" tabindex="-1" role="dialog" 
									aria-labelledby="modal_cycle_remboursement_handicap_label" aria-hidden="true">
									
									<div class="modal-dialog modal-dialog-centered" role="document">
										<div class="modal-content modal_content">
											<div class="modal-header modal_header">
												<h3 class="modal-title">Remboursement de handicap - Cycle <?php echo $identifiant; ?></h3>
												<button class="close close_button" data-dismiss="modal" aria-label="Close"> <!--Croix pour fermer-->
										        	<span aria-hidden="true">&times;</span>
										        </button>
											</div>
											
											<div class="modal-body">
												<div class="container-fluid">
													<div class="row">
														<form class="col-lg-8 col-lg-offset-2 modal_form" data-identifier="<?php echo $identifiant; ?>">
															<div class="row form-group">
																<label class="col-lg-6 control-label">Montant remboursé</label>
																<input class="col-lg-6" type="text" name="montant"/>
															</div>
															<div class="row form-group">
																<label class="col-lg-6 control-label">Date du remboursement</label>
																<input class="col-lg-6" type="date" name="dateRemboursement" />
															</div>
														</form>
													</div>
												</div>
											</div>
											
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-dismiss="modal">Abandonner</button>
								        		<button type="submit" name="submit" class="btn btn-primary validation_remboursement_handicap"  data-identifier="<?php echo $identifiant; ?>">
								        			Enregistrer le remboursement</button>
											</div>
										</div>
									</div>
								</div>
								
								
								
								<?php
							}			
						}
							
					?>
					
					<!--Fin zone medias-->
				</div>
			</div>
		</div>
		
		
		
		
		
		<!--ZONES VENTES / CHROMES-->
		<div class="row">
			<div class="col-lg-6">
				<h3>Historique des opérations sur les cycles</h3>
				<?php
					//ZONE opérations (ventes, remboursements, etc)
					if (isset($cyclesCourants)) {
						$IDcyclesTab = array();
						foreach ($cyclesCourants as $cycle) {
							array_push($IDcyclesTab, $cycle ->getIdentifiant());
						}
						query($mysqli, "DROP TABLE IF EXISTS `historique_cycles_courants`");
						query($mysqli, "CREATE TABLE IF NOT EXISTS `historique_cycles_courants` (
										`ID` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
										`cycle` int(11) NOT NULL,
										`date` date NOT NULL,
										`operation` varchar(32) NOT NULL,
										`client` varchar(32) NOT NULL,
										`modif_stock` double NOT NULL, 
										`modif_portefeuille` double NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=latin1;
							  ");
						
						$requeteAjout = "INSERT INTO `historique_cycles_courants` (date, cycle, operation, client, modif_stock, modif_portefeuille) VALUES ";
						
						$cyclesString = implode("', '", $IDcyclesTab);


						$resultVentes = query($mysqli, "SELECT date, cycle, clients.nom client, quantite, tarif, methode, montant_paye
														FROM ventes
														INNER JOIN clients ON ventes.client = clients.ID
														WHERE ventes.compte = '$IDcompte'
														AND clients.compte = '$IDcompte'
														AND cycle IN ('$cyclesString');
										");
						while ($n_uplet = mysqli_fetch_assoc($resultVentes)) {
							$date = $n_uplet['date'];
							$cycle = $n_uplet['cycle'];
							$quantite = $n_uplet['quantite'];
							//$tarif = $n_uplet['tarif']; 
							$client = $n_uplet['client']; 
							$methode = $n_uplet['methode'];
							$montant_paye = $n_uplet['montant_paye']; 
							//$prix = $tarif * $quantite;
							if (!$montant_paye) {$montant_paye = 0;}
							$requeteAjout .= "('$date', '$cycle', 'Vente ($methode)', '$client', '-$quantite', '$montant_paye'),";
						}
						
						$resultRetraitsMarchandise = query($mysqli, "SELECT date, cycle, quantite_retiree, motif
																	FROM retraits_marchandise
																	WHERE compte = '$IDcompte'
																	AND cycle IN ('$cyclesString');
										");
						while ($n_uplet = mysqli_fetch_assoc($resultRetraitsMarchandise)) {
							$date = $n_uplet['date'];
							$cycle = $n_uplet['cycle'];
							$quantite = $n_uplet['quantite_retiree'];
							$motif = $n_uplet['motif']; 
							if ($motif == "") {$motif = "Retrait marchandise"; }
							$requeteAjout .= "('$date', '$cycle', '$motif', '', '-$quantite', ''),";
						}
						
						$resultRemboursements = query($mysqli, "SELECT date, cycle, quantite_remboursee
																FROM remboursements_handicap
																WHERE compte = '$IDcompte'
																AND cycle IN ('$cyclesString');
										");
						while ($n_uplet = mysqli_fetch_assoc($resultRemboursements)) {
							$date = $n_uplet['date'];
							$cycle = $n_uplet['cycle'];
							$quantite = $n_uplet['quantite_remboursee'];
							$requeteAjout .= "('$date', '$cycle', 'Remboursement dette', '', '', '-$quantite'),";
						}
						
						$resultDifferes = query($mysqli, "SELECT date, cycle, clients.nom client, montant_rembourse
																FROM remboursements_differes
																INNER JOIN clients ON remboursements_differes.client = clients.ID
																WHERE remboursements_differes.compte = '$IDcompte'
																AND clients.compte = '$IDcompte'
																AND cycle IN ('$cyclesString');
										");
						while ($n_uplet = mysqli_fetch_assoc($resultDifferes)) {
							$date = $n_uplet['date'];
							$cycle = $n_uplet['cycle'];
							$client = $n_uplet['client'];
							$montant = $n_uplet['montant_rembourse'];
							$requeteAjout .= "('$date', '$cycle', 'Remboursement differe', '$client', '', '$montant'),";
						}


						//Vérification qu'il y ait au moins une valeur dans la requete
						if ($requeteAjout == "INSERT INTO `historique_cycles_courants` (date, cycle, operation, client, modif_stock, modif_portefeuille) VALUES ") {
							echo "<p class='infos'>Aucun historique à afficher.</p>";
						}
						else {
							//Ecrasement dernière virgule
							$requeteAjout[strlen($requeteAjout) - 1] = "; ";
							$ok = query($mysqli, $requeteAjout);
							
							
							
							//Affichage en tableau
							echo "<table class='table table-bordered table-striped my_table'>";
							echo "<thead><tr>";
							echo "<th>Date</th>
								  <th>Cycle</th>
								  <th>Opération</th>
								  <th>Client</th>
								  <th>Stock</th>
								  <th>Portefeuille</th>
								  ";
							echo "</tr></thead>";
							echo "<tbody>";
							
							$result = query($mysqli, "SELECT * FROM historique_cycles_courants ORDER BY date DESC");
							while ($n_uplet = mysqli_fetch_assoc($result)) {
								$date = $n_uplet['date'];
								$cycle = $n_uplet['cycle'];
								$operation = $n_uplet['operation'];
								$client = $n_uplet['client'];
								if ($operation == "") {$operation = "Opération non précisée"; }
								$modifStock = $n_uplet['modif_stock'];
								if ($modifStock == 0) {$modifStock = ""; }
								$modifPortefeuille = $n_uplet['modif_portefeuille'];
								if ($modifPortefeuille == 0 && strpos($operation, "Vente") !== 0) {$modifPortefeuille = ""; }
								echo "<tr>
										<td>$date</td>
										<td>$cycle</td>
										<td>$operation</td>
										<td>$client</td>
										<td>$modifStock</td>
										<td>$modifPortefeuille</td>
									 </tr>";
							}
							echo "</tbody></table>";
						}
					}
					
					else { //Si aucun cycle courant
						echo "<p class='infos'>Aucun cycle actif.</p>";
					}
				?>
			</div>
			<div class="col-lg-6">
				<?php 
					$resultTotal = query($mysqli, "SELECT sum(montant) montant, sum(montant_rembourse) montant_recupere 
													FROM differes
													WHERE compte = '$IDcompte'
													AND montant != montant_rembourse; ");
					$valeurs = mysqli_fetch_assoc($resultTotal);
					$total = $valeurs['montant'] - $valeurs['montant_recupere'];	
				?>
				<h3>Differes en cours (total : <?php if (!isset($total)) {echo 0;} else {echo $total;} ?>€)</h3>
				<?php
					//ZONE differes
					if (isset($cyclesString)) {
						$resultDifferes = query($mysqli, "SELECT differes.primary_key, cycle, date_emprunt, clients.nom client, montant, montant_rembourse 
														FROM differes
														INNER JOIN clients ON differes.client = clients.ID
														WHERE differes.compte = '$IDcompte'
														AND clients.compte = '$IDcompte'
														AND cycle IN ('$cyclesString')
														AND montant != montant_rembourse;
											  ");
						if (mysqli_num_rows($resultDifferes) != 0) {
							//Afichage en tableau
							echo "<table class='table table-bordered table-striped my_table'>";
							echo "<thead><tr>";
							echo "<th>Date d'emprunt</th>
								  <th>Cycle</th>
								  <th>Client</th>
								  <th>Montant dû par le client</th>
								  <th>Remboursement</th>
								  ";
							echo "</tr></thead>";
							echo "<tbody>";
							
							while ($n_uplet = mysqli_fetch_assoc($resultDifferes)) {
								$dateEmprunt = $n_uplet['date_emprunt'];
								$cycle = $n_uplet['cycle'];
								$client = $n_uplet['client'];
								$montant = $n_uplet['montant'];
								$montantRembourse = $n_uplet['montant_rembourse'];
								$montantDu = $montant - $montantRembourse;
								$cycle = $n_uplet['cycle'];
								$id = $n_uplet['primary_key'];
								echo "	<tr>
											<td>$dateEmprunt</td>
											<td>$cycle</td>
											<td>$client</td>
											<td>$montantDu</td>
											<td>
												<form class='remboursement_differe' data-cycle='$cycle' data-identifier='$id'>
													<input type='text' name='valeur' />
												</form>
											</td>
										</tr>
									  ";	
							}
							echo "</tbody></table>";
						}
						else {
							echo "<p class='infos'>Aucun differe en cours.</p>";
						}
					}
					else {
						echo "<p class='infos'>Aucun differe en cours.</p>";
					}
					
						
				?>
			</div>
			
			<div id="resultatAJAX">
				
			</div>
		</div>
	</div>
</main>
