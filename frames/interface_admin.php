<script type="application/javascript" src="scripts/gestion_interface_admin.js"></script>
<link type="text/css" rel="stylesheet" href="style/style_interface_admin.css" />


<main class="page_content col-lg-10 show_borders">
	<div id="interface_requetes">
		<label><?php echo "ID du compte : ".$_SESSION['ID']; ?></label>
		<form>
			<label>Entrez ci-dessous une requête SQL à la base de données X </label>
			<br />
			
			<input type="text" name="requete" id="requeteUtilisateur" /> <br />
			<input type="submit" name="submit" value="Envoyer" />
			<input type="button" value="Effacer" id="effacer_requete" />
		</form>
		
		<div id="resultat_requete">
			
		</div>
	</div>
</main>