<link type="text/css" rel="stylesheet" href="style/ecran_connexion.css" />

<div class="container-fluid ecran_connexion">
	<div class="row ecran_connexion_top">
		<h1 class='center'>Bienvenue</h1>
	</div>
	
	<hr />
	
	<div class="row">
		<div class="col-lg-4 col-lg-offset-2 ecran_connexion_side border_right">
			
			<form class="form-horizontal col-lg-10 center" id="inscription" method="post" action="programmes/inscription.php">
				<h2 class="center">Créer un compte</h2>
				<div class="row form-group">
					<label class="col-lg-6 control-label">Nom d'utilisateur</label>
					<input class="col-lg-6" type="text" name="login" required="required" />
				</div>
				<div class="row form-group">
					<label class="col-lg-6 control-label">Mot de passe</label>
					<input class="col-lg-6" type="password" name="mdp1" required="required" />
				</div>
				<div class="row form-group">
					<label class="col-lg-6 control-label">Confirmer</label>
					<input class="col-lg-6" type="password" name="mdp2" required="required" />
				</div>
				<div class="row">
					<button class="btn btn-primary" type="submit" name="submit">
						S'inscrire
					</button>
				</div>
				<div class="row">
					<?php
						if (isset($_SESSION['erreurInscription'])) {
							echo "<p class='erreur'>".$_SESSION['erreurInscription']."</p>";
							unset ($_SESSION['erreurInscription']);
						}
						elseif (isset($_SESSION['confirmationInscription'])) {
							echo "<p class='succes'>".$_SESSION['confirmationInscription']."</p>";
							unset ($_SESSION['confirmationInscription']);
						}
							
					?>
				</div>
			</form>
			
		</div>
		
		<div class="col-lg-4 ecran_connexion_side">
			<h2 class="center">Se connecter</h2>
			<form class="form-horizontal col-lg-10 col-lg-offset-1 center" id="connexion" method="post" action="programmes/connexion.php">
				<div class="row form-group">
					<label class="col-lg-6 control-label">Nom d'utilisateur</label>
					<input class="col-lg-6" type="text" name="login" required="required" />
				</div>
				<div class="row form-group">
					<label class="col-lg-6 control-label">Mot de passe</label>
					<input class="col-lg-6" type="password" name="mdp" required="required" />
				</div>
				<div class="row">
					<button class="btn btn-success" type="submit" name="submit" id="button_down">
						Se connecter
					</button>
				</div>
				<div class="row">
					<?php
						if (isset($_SESSION['erreurConnexion'])) {
							echo "<p class='erreur'>".$_SESSION['erreurConnexion']."</p>";
							unset ($_SESSION['erreurConnexion']);
						}
					?>
				</div>
			</form>
		</div>
	</div>
	
	<hr />
	
	<div class="row ecran_connexion_bottom">
		<form class="center" id="supprimer_donnees" method="post" action="programmes/install.php">
			<button class="btn btn-danger" type="submit" name="submit">
				Installer / Effacer toutes les données
			</button>
		</form>
	</div>
</div>









