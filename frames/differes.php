<link type="text/css" rel="stylesheet" href="style/mon_compte.css">

<main class="page_content col-lg-10">
	<div class="container-fluid">
		<div class="row">
			<div class="container-fluid">
				<div class="mon_compte_container">
					<h1>Historique des differes</h1>
					<a class="lien_page_mon_compte" href="index.php?remboursements_differes">Consulter l'historique des remboursements...</a>

					<form method="post" action="#" class="col-lg-10 modal_form little_down form_search">
						<div class="row">
							<input class="pull-left col-lg-1" type="checkbox" name="search[]" value="dateEmprunt" 
								<?php if (isset($_POST['search']) && in_array("dateEmprunt", $_POST['search'])) { echo 'checked="checked"'; } ?> />
							<label class="col-lg-2">Emprunté entre le</label>
							<input class="col-lg-2" type="date" name="dateEmpruntDebut" <?php if (isset($_POST['dateEmpruntDebut'])) {echo 'value="'.$_POST['dateEmpruntDebut'].'"'; }?> />
							<label class="col-lg-1"> et le </label>
							<input class="col-lg-2" type="date" name="dateEmpruntFin" <?php if (isset($_POST['dateEmpruntFin'])) {echo 'value="'.$_POST['dateEmpruntFin'].'"'; }?> />
						</div>
						<div class="row">
							<input class="pull-left col-lg-1" type="checkbox" name="search[]" value="cycle" 
								<?php if (isset($_POST['search']) && in_array("cycle", $_POST['search'])) { echo 'checked="checked"'; } ?> />
							<label class="col-lg-2">Entre cycle  &nbsp;&nbsp;</label>
							<input class="col-lg-2" type="number" name="cycleDebut" <?php if (isset($_POST['cycleDebut'])) {echo 'value="'.$_POST['cycleDebut'].'"'; }?> />
							<label class="col-lg-1">et </label>
							<input class="col-lg-2" type="number" name="cycleFin" <?php if (isset($_POST['cycleFin'])) {echo 'value="'.$_POST['cycleFin'].'"'; }?> />
						</div>
						<div class="row">
							<input class="pull-left col-lg-1" type="checkbox" name="search[]" value="montant" 
								<?php if (isset($_POST['search']) && in_array("montant", $_POST['search'])) { echo 'checked="checked"'; } ?> />
							<label class="col-lg-2">Montant entre &nbsp;&nbsp;</label>
							<input class="col-lg-2" type="number" name="montantMin" <?php if (isset($_POST['montantMin'])) {echo 'value="'.$_POST['montantMin'].'"'; }?> />
							<label class="col-lg-1">et </label>
							<input class="col-lg-2" type="number" name="montantMax" <?php if (isset($_POST['montantMax'])) {echo 'value="'.$_POST['montantMax'].'"'; }?> />
						</div>
						<div class="row">
							<input class="pull-left col-lg-1" type="checkbox" name="search[]" value="rembourse" 
								<?php if (isset($_POST['search']) && in_array("rembourse", $_POST['search'])) { echo 'checked="checked"'; } ?> />
							<label class="col-lg-2">Remboursé &nbsp;&nbsp;</label>
							<select class="col-lg-2" name="rembourseSelect">
								<option value="1">Oui</option>
								<option value="0">Non</option>
							</select>
						</div>
						<div class="row">
							<input class="pull-left col-lg-1" type="checkbox" name="search[]" value="dateFinalisation" 
								<?php if (isset($_POST['search']) && in_array("dateFinalisation", $_POST['search'])) { echo 'checked="checked"'; } ?> />
							<label class="col-lg-2">Finalisé entre le</label>
							<input class="col-lg-2" type="date" name="dateFinalisationDebut" <?php if (isset($_POST['dateFinalisationDebut'])) {echo 'value="'.$_POST['dateFinalisationDebut'].'"'; }?> />
							<label class="col-lg-1"> et le </label>
							<input class="col-lg-2" type="date" name="dateFinalisationFin" <?php if (isset($_POST['dateFinalisationFin'])) {echo 'value="'.$_POST['dateFinalisationFin'].'"'; }?> />
						</div>
						<div class="row">
							<input class="pull-left col-lg-1" type="checkbox" name="search[]" value="client" 
								<?php if (isset($_POST['search']) && in_array("client", $_POST['search'])) { echo 'checked="checked"'; } ?> />
							<label class="col-lg-2">Client &nbsp;&nbsp;</label>
							<select class="col-lg-2" name="client">
								<?php
									$clientsTab = getClientsTab();
									foreach ($clientsTab as $val => $nom) {
										$option = "<option value='$val' ";
										if (isset($_POST['client']) && $_POST['client'] == $val) {$option .='selected="selected"'; }
										$option .= ">$nom</option>";
										echo $option;
									}
								?>
							</select>
						</div>
						<div class="row">
							<input class="pull-left col-lg-1" type="checkbox" name="search[]" value="classement" 
								<?php if (isset($_POST['search']) && in_array("classement", $_POST['search'])) { echo 'checked="checked"'; } ?> />
							<label class="col-lg-2">Classement par</label>
							<select class="col-lg-2" name="classement">
								<option value="date_emprunt" <?php if (isset($_POST['classement']) && $_POST['classement'] == "date_emprunt") {echo 'selected="selected"'; }?>>Date d'emprunt</option>
								<option value="cycle" <?php if (isset($_POST['classement']) && $_POST['classement'] == "cycle") {echo 'selected="selected"'; }?>>Cycle</option>
								<option value="montant" <?php if (isset($_POST['classement']) && $_POST['classement'] == "montant") {echo 'selected="selected"'; }?>>Montant</option>
								<option value="rembourse" <?php if (isset($_POST['classement']) && $_POST['classement'] == "rembourse") {echo 'selected="selected"'; }?>>Remboursé</option>
								<option value="date_finalisation" <?php if (isset($_POST['classement']) && $_POST['classement'] == "date_finalisation") {echo 'selected="selected"'; }?>>Date de finalisation</option>
							</select>
							<label class="col-lg-1">Ordre</label>
							<select class="col-lg-2" name="ordre">
								<option value="ASC" <?php if (isset($_POST['ordre']) && $_POST['ordre'] == "ASC") {echo 'selected="selected"'; }?>>Ascendant</option>
								<option value="DESC" <?php if (isset($_POST['ordre']) && $_POST['ordre'] == "DESC") {echo 'selected="selected"'; }?>>Descendant</option>
							</select>
						</div>
						<div class="row">
							<input class="col-lg-6 col-lg-offset-1" type="submit" name="submit" value="Lancer la recherche" />
						</div>
					</form>
					
					
				</div>
			</div>
		</div>
		
		
		<div class="row">
			<div class="container-fluid">
				<div class="mon_compte_container little_down">
					<?php
						$ID = $_SESSION['ID'];
						
						$requete = "SELECT date_emprunt, cycle, clients.nom client, montant, montant_rembourse, (montant = montant_rembourse) rembourse, date_finalisation
						 			FROM differes
						 			INNER JOIN clients ON differes.client = clients.ID
						 			WHERE differes.compte = '$ID'
						 			AND clients.compte = '$ID'";
									
						
						if (isset($_POST['submit'])) {
							if (isset($_POST['search'])) {
								$search = $_POST['search'];
								if (in_array("dateEmprunt", $search)) {
									if (isset($_POST['dateEmpruntDebut']) && isset($_POST['dateEmpruntFin'])) {
										$dateEmpruntDebut = $_POST['dateEmpruntDebut'];
										$dateEmpruntFin = $_POST['dateEmpruntFin'];
										$requete .= " AND date_emprunt >= '$dateEmpruntDebut' AND date_emprunt <= '$dateEmpruntFin'";
									}
								}
								if (in_array("cycle", $search)) {
									if (isset($_POST['cycleDebut']) && isset($_POST['cycleFin'])) {
										$cycleDebut = $_POST['cycleDebut'];
										$cycleFin = $_POST['cycleFin'];
										$requete .= " AND cycle >= '$cycleDebut' AND cycle <= '$cycleFin'";
									}
								}
								if (in_array("montant", $search)) {
									if (isset($_POST['montantMin']) && isset($_POST['montantMax'])) {
										$montantMin = $_POST['montantMin'];
										$montantMax = $_POST['montantMax'];
										$requete .= " AND montant >= '$montantMin' AND montant <= '$montantMax'";
									}
								}
								if (in_array("rembourse", $search)) {
									if (isset($_POST['rembourseSelect'])) {
										$rembourse = $_POST['rembourseSelect'];
										if ($rembourse == 1) {
											$requete .= " AND montant = montant_rembourse";
										} else {
											$requete .= " AND montant != montant_rembourse";
										}
									}
								}
								if (in_array("dateFinalisation", $search)) {
									if (isset($_POST['dateFinalisationDebut']) && isset($_POST['dateFinalisationFin'])) {
										$dateFinalisationDebut = $_POST['dateFinalisationDebut'];
										$dateFinalisationFin = $_POST['dateFinalisationFin'];
										$requete .= " AND date_finalisation >= '$dateFinalisationDebut' AND date_finalisation <= '$dateFinalisationFin'";
									}
								}
								if (in_array("client", $search)) {
									if (isset($_POST['client'])) {
										$client = $_POST['client'];
										$requete .= " AND clients.ID = '$client'";
									}
								}
								if (in_array("classement", $search)) {
									if (isset($_POST['classement']) && isset($_POST['ordre'])) {
										$classement = $_POST['classement'];
										$ordre = $_POST['ordre'];
										$requete .= " ORDER BY $classement $ordre; ";
									}
								}
								
							}
						}
						
						$requete.= "; ";
						
						$result = query($mysqli, $requete);
						//Cas ou l'historique est vide
						if (mysqli_num_rows($result) == 0) {
							echo "<p class='mon_compte_infos'>Aucun differe correspondant aux critères de recherche n'a été enregistré sur ce compte.</p>";
						}
						else {
							//Sinon, affichage d'un tableau récapitulatif
							echo "<table class='table table-bordered table-striped my_table'>";
							echo "<thead><tr>";
							echo "<th>Date d'emprunt</th>
								  <th>Cycle</th>
								  <th>Client</th>
								  <th>Montant</th>
								  <th>Montant remboursé</th>
								  <th>Remboursé</th>
								  <th>Date de finalisation</th>
								  ";
							echo "</tr></thead>";
							
							while ($n_uplet = mysqli_fetch_assoc($result)) {
								$dateEmprunt = $n_uplet['date_emprunt'];
								$cycle = $n_uplet['cycle'];
								$client = $n_uplet['client'];
								$montant = $n_uplet['montant'];
								$montantRembourse = $n_uplet['montant_rembourse'];
								$rembourse = $n_uplet['rembourse'];
								$dateFinalisation = $n_uplet['date_finalisation'];
								if ($rembourse == 1) {
									$rembourse = "Oui";
								}
								else {
									$rembourse = "Non";
								}
								
								echo "<tr>
										<td>$dateEmprunt</td>
										<td>$cycle</td>
										<td>$client</td>
										<td>$montant</td>
										<td>$montantRembourse</td>
										<td>$rembourse</td>
										<td>$dateFinalisation</td>
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