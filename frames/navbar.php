<?php
	
	/**
	 * Récupération des informations affichées dans la navbar depuis la base de données
	 */
	
	$ID = $_SESSION['ID'];
	$resultat = query($mysqli, "SELECT quantite_possedee, argent_possede, dette
								FROM comptes
								WHERE ID = '$ID'; 
					 ");
	$n_uplet = mysqli_fetch_assoc($resultat);
	$stock = $n_uplet['quantite_possedee'];
	$portefeuille = $n_uplet['argent_possede'];
	$dette = $n_uplet['dette'];
?>

<nav class="col-lg-12 my_navbar">
	<div class='navbar_item'>
		<span class="navbar_label">
			<?php
				echo "<span>".$_SESSION['utilisateur']."</span>";
			?>
		</span>
	</div>
	
	<div class='navbar_item'>
		<span class="navbar_label">
			Dette : <?php echo "<span class='error'>$dette €</span>"; ?>
		</span>
	</div>
	
	<div class='navbar_item'>
		<span class="navbar_label">
			Stock : <?php echo "<span>$stock pcs</span>"; ?>
		</span>
	</div>
	
	<div class='navbar_item'>
		<span class="navbar_label">
			Portefeuille : <?php echo "<span class='success'>$portefeuille €</span>"; ?>
		</span>
	</div>
</nav>






