<link type="text/css" rel="stylesheet" href="style/mon_compte.css" />

<main class="page_content col-lg-10">
	<div class="container-fluid">
		<div class="row">
			<div class="container-fluid">
				<div class="mon_compte_container">
					<h1>Historique des ventes</h1>
					
					<form method="post" action="#" class="col-lg-10 modal_form little_down form_search">
						<div class="row">
							<input class="pull-left col-lg-1" type="checkbox" name="search[]" value="date" 
								<?php if (isset($_POST['search']) && in_array("date", $_POST['search'])) { echo 'checked="checked"'; } ?> />
							<label class="col-lg-2">Depuis le &nbsp;&nbsp;</label>
							<input class="col-lg-2" type="date" name="dateDebut" <?php if (isset($_POST['dateDebut'])) {echo 'value="'.$_POST['dateDebut'].'"'; }?> />
							<label class="col-lg-1"> jusqu'au </label>
							<input class="col-lg-2" type="date" name="dateFin" <?php if (isset($_POST['dateFin'])) {echo 'value="'.$_POST['dateFin'].'"'; }?> />
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
							<input class="pull-left col-lg-1" type="checkbox" name="search[]" value="quantite" 
								<?php if (isset($_POST['search']) && in_array("quantite", $_POST['search'])) { echo 'checked="checked"'; } ?> />
							<label class="col-lg-2">Quantité entre &nbsp;&nbsp;</label>
							<input class="col-lg-2" type="number" name="quantiteMin" <?php if (isset($_POST['quantiteMin'])) {echo 'value="'.$_POST['quantiteMin'].'"'; }?> />
							<label class="col-lg-1">et </label>
							<input class="col-lg-2" type="number" name="quantiteMax" <?php if (isset($_POST['quantiteMax'])) {echo 'value="'.$_POST['quantiteMax'].'"'; }?> />
						</div>
						<div class="row">
							<input class="pull-left col-lg-1" type="checkbox" name="search[]" value="tarif" 
								<?php if (isset($_POST['search']) && in_array("tarif", $_POST['search'])) { echo 'checked="checked"'; } ?> />
							<label class="col-lg-2">Tarif entre &nbsp;&nbsp;</label>
							<input class="col-lg-2" type="text" name="tarifMin" <?php if (isset($_POST['tarifMin'])) {echo 'value="'.$_POST['tarifMin'].'"'; }?> />
							<label class="col-lg-1">et </label>
							<input class="col-lg-2" type="text" name="tarifMax" <?php if (isset($_POST['tarifMax'])) {echo 'value="'.$_POST['tarifMax'].'"'; }?> />
						</div>
						<div class="row">
							<input class="pull-left col-lg-1" type="checkbox" name="search[]" value="prix" 
								<?php if (isset($_POST['search']) && in_array("prix", $_POST['search'])) { echo 'checked="checked"'; } ?> />
							<label class="col-lg-2">Prix entre &nbsp;&nbsp;</label>
							<input class="col-lg-2" type="number" name="prixMin" <?php if (isset($_POST['prixMin'])) {echo 'value="'.$_POST['prixMin'].'"'; }?> />
							<label class="col-lg-1">et </label>
							<input class="col-lg-2" type="number" name="prixMax" <?php if (isset($_POST['prixMax'])) {echo 'value="'.$_POST['prixMax'].'"'; }?> />
						</div>
						<div class="row">
							<input class="pull-left col-lg-1" type="checkbox" name="search[]" value="methodePayement" 
								<?php if (isset($_POST['search']) && in_array("methodePayement", $_POST['search'])) { echo 'checked="checked"'; } ?> />
							<label class="col-lg-2">Règlement &nbsp;&nbsp;</label>
							<select class="col-lg-2" name="methodePayement">
								<option value="Cash" <?php if (isset($_POST['methodePayement']) && $_POST['methodePayement'] == "Cash") {echo 'selected="selected"'; }?> >
									Cash</option>
								<option value="Differe" <?php if (isset($_POST['methodePayement']) && $_POST['methodePayement'] == "Differe") {echo 'selected="selected"'; }?> >
									Differe</option>
								<option value="Accompte" <?php if (isset($_POST['methodePayement']) && $_POST['methodePayement'] == "Accompte") {echo 'selected="selected"'; }?> >
									Accompte</option>
							</select>
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
								<option value="date" <?php if (isset($_POST['classement']) && $_POST['classement'] == "date") {echo 'selected="selected"'; }?>>Date</option>
								<option value="cycle" <?php if (isset($_POST['classement']) && $_POST['classement'] == "cycle") {echo 'selected="selected"'; }?>>Cycle</option>
								<option value="quantite" <?php if (isset($_POST['classement']) && $_POST['classement'] == "quantite") {echo 'selected="selected"'; }?>>Quantite</option>
								<option value="tarif" <?php if (isset($_POST['classement']) && $_POST['classement'] == "tarif") {echo 'selected="selected"'; }?>>Tarif</option>
								<option value="prix" <?php if (isset($_POST['classement']) && $_POST['classement'] == "prix") {echo 'selected="selected"'; }?>>Prix</option>
								<option value="reglement" <?php if (isset($_POST['classement']) && $_POST['classement'] == "reglement") {echo 'selected="selected"'; }?>>Règlement</option>
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
						
						$requete = "SELECT date, cycle, clients.nom client, quantite, tarif, (quantite * tarif) prix, methode reglement
						 			FROM ventes
						 			INNER JOIN clients ON ventes.client = clients.ID
						 			WHERE ventes.compte = '$ID'
						 			AND clients.compte = '$ID'";
						
						if (isset($_POST['submit'])) {
							if (isset($_POST['search'])) {
								$search = $_POST['search'];
								if (in_array("date", $search)) {
									if (isset($_POST['dateDebut']) && isset($_POST['dateFin'])) {
										$dateDebut = $_POST['dateDebut'];
										$dateFin = $_POST['dateFin'];
										$requete .= " AND date >= '$dateDebut' AND date <= '$dateFin'";
									}
								}
								if (in_array("cycle", $search)) {
									if (isset($_POST['cycleDebut']) && isset($_POST['cycleFin'])) {
										$cycleDebut = $_POST['cycleDebut'];
										$cycleFin = $_POST['cycleFin'];
										$requete .= " AND cycle >= '$cycleDebut' AND cycle <= '$cycleFin'";
									}
								}
								if (in_array("quantite", $search)) {
									if (isset($_POST['quantiteMin']) && isset($_POST['quantiteMax'])) {
										$quantiteMin = $_POST['quantiteMin'];
										$quantiteMax = $_POST['quantiteMax'];
										$requete .= " AND quantite >= '$quantiteMin' AND quantite <= '$quantiteMax'";
									}
								}
								if (in_array("tarif", $search)) {
									if (isset($_POST['tarifMin']) && isset($_POST['tarifMax'])) {
										$tarifMin = $_POST['tarifMin'];
										$tarifMax = $_POST['tarifMax'];
										$requete .= " AND tarif >= '$tarifMin' AND tarif <= '$tarifMax'";
									}
								}
								if (in_array("prix", $search)) {
									if (isset($_POST['prixMin']) && isset($_POST['prixMax'])) {
										$prixMin = $_POST['prixMin'];
										$prixMax = $_POST['prixMax'];
										$requete .= " AND (quantite * tarif) >= '$prixMin' AND (quantite * tarif) <= '$prixMax'";
									}
								}
								if (in_array("methodePayement", $search)) {
									if (isset($_POST['methodePayement'])) {
										$methodePayement = $_POST['methodePayement'];
										$requete .= " AND methode = '$methodePayement'";
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
							echo "<p class='mon_compte_infos'>Aucune vente correspondant aux critères de recherche n'a été effectuée avec ce compte.</p>";
						}
						else {
							//Sinon, affichage d'un tableau récapitulatif
							echo "<table class='table table-bordered table-striped my_table'>";
							echo "<thead><tr>";
							echo "<th>Date</th>
								  <th>Cycle</th>
								  <th>Client</th>
								  <th>Quantite</th>
								  <th>Tarif</th>
								  <th>Prix</th>
								  <th>Règlement</th>
								  ";
							echo "</tr></thead>";
							echo "<tbody>";
							
							while ($n_uplet = mysqli_fetch_assoc($result)) {
								$date = $n_uplet['date'];
								$cycle = $n_uplet['cycle'];
								$client = $n_uplet['client'];
								$quantite = $n_uplet['quantite'];
								$tarif = $n_uplet['tarif'];
								$prix = $n_uplet['prix'];
								$reglement = $n_uplet['reglement'];
								
								echo "<tr>
										<td>$date</td>
										<td>$cycle</td>
										<td>$client</td>
										<td>$quantite</td>
										<td>$tarif</td>
										<td>$prix</td>
										<td>$reglement</td>
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