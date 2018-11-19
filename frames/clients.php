<?php
	//inclusion des modals
	require "frames/modals.php";
	
	$ID = $_SESSION['ID'];
	
?>

<link type="text/css" rel="stylesheet" href="style/mon_compte.css">

<main class="page_content col-lg-10">
	<div class="container-fluid">
		<div class="row">
			<div class="container-fluid">
				<div class="mon_compte_container">
					<h1>Gestion des clients</h1>
					
					<div class="col-lg-6 little_down">
						<a class="lien_page_mon_compte" href="#" data-toggle="modal" data-target="#modal_ajout_client">
							Enregistrer un nouveau client...
						</a>
						<br />
						<?php
							if (isset($_SESSION['messageConfirmationEnregistrementClient'])) {
								$msg = $_SESSION['messageConfirmationEnregistrementClient'];
								echo "<p class='messageConfirmationEnregistrementClient'>$msg</p>";
								unset($_SESSION['messageConfirmationEnregistrementClient']);
							}
							elseif (isset($_SESSION['messageErreurEnregistrementClient'])) {
								$msg = $_SESSION['messageErreurEnregistrementClient'];
								echo "<br /><p class='messageErreurEnregistrementClient'>$msg</p>";
								unset($_SESSION['messageErreurEnregistrementClient']);
							}
						?>
					</div>
				</div>
			</div>
		</div>
		
		<!--ZONE HISTORIQUE -->
		<div class="row">
			<div class="container-fluid">
				<div class="mon_compte_container">
					<div class="col-lg-6">
						<?php
							$resultClients = query($mysqli, "SELECT nom, date_enregistrement 
															 FROM clients 
															 WHERE compte = '$ID'; ");
							if (mysqli_num_rows($resultClients) != 0) {
								echo "<table class='little_down table table-bordered table-striped my_table'>
										<thead>
											<th>Nom du client</th>
											<th>Date d'enregistrement</th>
										</thead>
										<tbody>";
								while ($n_uplet = mysqli_fetch_assoc($resultClients)) {
									echo "<tr>";
									echo "<td>".$n_uplet['nom']."</td>";
									echo "<td>".$n_uplet['date_enregistrement']."</td>";
									echo "</tr>";
								}
								echo "</tbody></table>";
							}
							else {
								echo "<p class='infos'>Aucun client enregistré sur ce compte.</p>";
							}
							
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>