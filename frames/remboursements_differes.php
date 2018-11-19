<link type="text/css" rel="stylesheet" href="style/mon_compte.css">

<main class="page_content col-lg-10">
	<div class="container-fluid">
		<div class="row">
			<div class="container-fluid">
				<div class="mon_compte_container">
					<h1>Historique des remboursements</h1>
					<a class="lien_page_mon_compte" href="index.php?differes">Retour à l'historique des differes...</a>

					<form method="post" action="#" class="col-lg-10 modal_form little_down form_search">
						<div class="row">
							<input class="pull-left col-lg-1" type="checkbox" name="search[]" value="dateRemboursement" 
								<?php if (isset($_POST['search']) && in_array("dateRemboursement", $_POST['search'])) { echo 'checked="checked"'; } ?> />
							<label class="col-lg-2">Perçu entre le</label>
							<input class="col-lg-2" type="date" name="dateRemboursementDebut" <?php if (isset($_POST['dateRemboursementDebut'])) {echo 'value="'.$_POST['dateRemboursementDebut'].'"'; }?> />
							<label class="col-lg-1"> et le </label>
							<input class="col-lg-2" type="date" name="dateRemboursementFin" <?php if (isset($_POST['dateRemboursementFin'])) {echo 'value="'.$_POST['dateRemboursementFin'].'"'; }?> />
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
								<option value="date" <?php if (isset($_POST['classement']) && $_POST['classement'] == "date") {echo 'selected="selected"'; } ?>>Date</option>
								<option value="cycle" <?php if (isset($_POST['classement']) && $_POST['classement'] == "cycle") {echo 'selected="selected"'; }?>>Cycle</option>
								<option value="montant_rembourse" <?php if (isset($_POST['classement']) && $_POST['classement'] == "montant_rembourse") {echo 'selected="selected"'; }?>>Montant</option>
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
						
						$requete = "SELECT date, cycle, clients.nom client, montant_rembourse
						 			FROM remboursements_differes
						 			INNER JOIN clients ON remboursements_differes.client = clients.ID
						 			WHERE remboursements_differes.compte = '$ID'
						 			AND clients.compte = '$ID'";
									
						
						if (isset($_POST['submit'])) {
							if (isset($_POST['search'])) {
								$search = $_POST['search'];
								if (in_array("dateRemboursement", $search)) {
									if (isset($_POST['dateRemboursementDebut']) && isset($_POST['dateRemboursementFin'])) {
										$dateRemboursementDebut = $_POST['dateRemboursementDebut'];
										$dateRemboursementFin = $_POST['dateRemboursementFin'];
										$requete .= " AND date >= '$dateRemboursementDebut' AND date <= '$dateRemboursementFin'";
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
										$requete .= " AND montant_rembourse >= '$montantMin' AND montant_rembourse <= '$montantMax'";
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
							echo "<p class='mon_compte_infos'>Aucun remboursement correspondant aux critères de recherche n'a été enregistré sur ce compte.</p>";
						}
						else {
							//Sinon, affichage d'un tableau récapitulatif
							echo "<table class='table table-bordered table-striped my_table'>";
							echo "<thead><tr>";
							echo "<th>Date du remboursement</th>
								  <th>Cycle</th>
								  <th>Client</th>
								  <th>Montant</th>";
							echo "</tr></thead>";
							
							while ($n_uplet = mysqli_fetch_assoc($result)) {
								$dateRemboursement = $n_uplet['date'];
								$cycle = $n_uplet['cycle'];
								$client = $n_uplet['client'];
								$montant = $n_uplet['montant_rembourse'];
								
								echo "<tr>
										<td>$dateRemboursement</td>
										<td>$cycle</td>
										<td>$client</td>
										<td>$montant</td>
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