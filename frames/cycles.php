<link type="text/css" rel="stylesheet" href="style/mon_compte.css" />

<main class="page_content col-lg-10">
	<div class="container-fluid">
		<!--Zone formulaire-->
		<div class="row">
			<div class="container-fluid">
				<div class="mon_compte_container">
					<h1>Historique des cycles</h1>
				</div>
			</div>
		</div>
		
		
		<!--Tableau résultat-->
		<div class="row">
			<div class="container-fluid">
				<div class="mon_compte_container little_down">
					<?php
						$IDcomtpe = $_SESSION['ID'];
						
						$resultCycles = query($mysqli, "SELECT * FROM cycles WHERE compte = '$IDcomtpe' AND actif = 'false'; ");
						
						//Si aucun cycle passé
						if (mysqli_num_rows($resultCycles) == 0) {
							echo "<p class='infos'>Aucun cycle à afficher dans l'historique.</p>";
						}
						//Sinon, affichage d'un tableau récapitulatif
						else {
							//Sinon, affichage d'un tableau récapitulatif
							echo "<table class='table table-bordered table-striped my_table'>";
							echo "<thead><tr>";
							echo "<th>Cycle</th>
								  <th>Produit</th>
								  <th>Date d'achat</th>
								  <th>Stock acheté</th>
								  <th>Tarif d'achat</th>
								  <th>Handicap à l'achat</th>
								  ";
							echo "</tr></thead>";
							echo "<tbody>";
							
							while ($n_uplet = mysqli_fetch_assoc($resultCycles)) {
								$primaryKey 	= $n_uplet['primary_key'];
								$ID 			= $n_uplet['ID'];
								$dateAchat 		= $n_uplet['date_achat'];
								$quantiteAchat 	= $n_uplet['quantite_achat'];
								$tarif 			= $n_uplet['tarif'];
								$handicap 		= $n_uplet['handicap'];
								$commentaire 	= $n_uplet['commentaire'];
								
								echo "<tr>
										<td style='text-align: center;'><a href='index.php?cycle&primary_key=$primaryKey'>$ID</a></td>
										<td>$commentaire</td>
										<td>$dateAchat</td>
										<td>$quantiteAchat</td>
										<td>$tarif</td>
										<td>$handicap</td>
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
