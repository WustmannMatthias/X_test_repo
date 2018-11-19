<aside class="page_content col-lg-2 my_aside">
	<ul class="list-group">
		<a class="list-group-item my_list_group_item <?php if(isset($_GET['accueil']) || $_SERVER['QUERY_STRING'] == "") echo "my_active"; ?>" href="index.php?accueil">
			Accueil
			<span class="glyphicon glyphicon-chevron-right pull-right"></span>
		</a>
		
		<a class="list-group-item my_list_group_item <?php if(isset($_GET['mon_compte'])) echo "my_active"; ?>" href="index.php?mon_compte">
			Mon Compte
			<span class="glyphicon glyphicon-chevron-right pull-right"></span>
		</a>
		
		<a class="list-group-item my_list_group_item <?php if(isset($_GET['cycles'])) echo "my_active"; ?>" href="index.php?cycles">
			Cycles
			<span class="glyphicon glyphicon-chevron-right pull-right"></span>
		</a>
		
		<a class="list-group-item my_list_group_item <?php if(isset($_GET['ventes'])) echo "my_active"; ?>" href="index.php?ventes">
			Ventes
			<span class="glyphicon glyphicon-chevron-right pull-right"></span>
		</a>
		
		<a class="list-group-item my_list_group_item <?php if(isset($_GET['differes'])) echo "my_active"; ?>" href="index.php?differes">
			Différés
			<span class="glyphicon glyphicon-chevron-right pull-right"></span>
		</a>
		
		<a class="list-group-item my_list_group_item <?php if(isset($_GET['clients'])) echo "my_active"; ?>" href="index.php?clients">
			Clients
			<span class="glyphicon glyphicon-chevron-right pull-right"></span>
		</a>
		
		<a class="list-group-item my_list_group_item <?php if(isset($_GET['statistiques'])) echo "my_active"; ?>" href="index.php?statistiques">
			Statistiques
			<span class="glyphicon glyphicon-chevron-right pull-right"></span>
		</a>
		
		<a class="list-group-item my_list_group_item <?php if(isset($_GET['interface_admin'])) echo "my_active"; ?>" href="index.php?interface_admin">
			Interface admin
			<span class="glyphicon glyphicon-chevron-right pull-right"></span>
		</a>
		
		<a class="list-group-item my_list_group_item" href="programmes/deconnexion.php?deconnexion">
			Déconnexion...
		</a>
	</ul>
	
</aside>