<html>
	<head>

		<script type="text/javascript" src="{http}js/jquery.js"></script>
		<script type="text/javascript" src="{http}js/jquery.cycle.all.min.js"></script>
		<script type="text/javascript" src="{http}js/index.js"></script>

          [START html_javascript]
          <script type="text/javascript" src="{src}.js"></script>
          [END html_javascript]

          [START html_css]
               <link rel="stylesheet" type="text/css" media="screen" href="{http}css/{href}" />
          [END html_css]
	</head>

	<body>

		<!-- Wrapper -->
			<div id="container" class="no_float">

				<!-- Top -->
					<div id="header">
                    	<img src="{http}images/logo.png" class="logo" />
                         
                         <!-- Header login -->
                              <div id="header_login">
                                   <span>Beheer</span>
                              </div>
                         <!-- Header login -->
                         
                         <div style="width: 100%">&nbsp;</div>
                         
                         <!-- Stappenplan -->
                              <div id="header_steps">
                                   <img src="{http}images/steps.png" alt="Stappenplan" />
                              </div>
                         <!-- Stappenplan -->
                         
                         <div id="header_nav">
                              <ul>
                                   <li><a href="#" title="" class="nav_active">Homepagina</a></li>
                                   <li><a href="#" title="" class="nav_inactive">Orders</a></li>
                                   <li><a href="#" title="" class="nav_inactive">Configuratie</a></li>
                              </ul>
                         </div>
					</div>
				<!-- Top -->


				<!-- Left bar -->
					<div id="left_menu">

                        <div class="menu_top">
                              <span>Orders &amp; klanten</span>
                         </div>
                         
                         <div class="menu_items">
                              <ul>
                                   <li><a href="#" title="">Nieuwe orders <b>(7)</b></a></li>
                                   <li><a href="#" title="">Verzendingen <b>(5)</b></a></li>
                                   <li><a href="#" title="">Klantenbestand</a></li>
                              </ul>
                         </div>

                        <div class="menu_top">
                              <span>Webshop inhoud</span>
                         </div>
                         
                         <div class="menu_items">
                              <ul>
                                   <li><a href="{http}categories/" title="Categorieen">Categorieen</a></li>
                                   <li><a href="{http}products/" title="">Producten</a></li>
                                   <li><a href="#" title="">Vooraadbeheer</a></li>
                                   <li><a href="#" title="">Verzendkosten</a></li>
                              </ul>
                         </div>
                         
                         
                        <div class="menu_top">
                              <span>Webshop configuratie</span>
                         </div>
                         
                         <div class="menu_items">
                              <ul>
                                   <li><a href="#" title="">Vormgeving</a></li>
                                   <li><a href="#" title="">Mijn pakket</a></li>
                                   <li><a href="#" title="">Betaalmogelijkheden</a></li>
                                   <li><a href="#" title="">Gebruikers</a></li>
                              </ul>
                         </div>

					</div>
				<!-- Left bar -->

				<!-- Content -->
					<div id="content">

[START flashMsg]
<div class="flash_{type}">
<p>{msg}</p>
</div>
[END flashMsg]