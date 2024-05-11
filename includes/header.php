<?php 

	session_start();
	require_once("includes/usuarios.php");
	//consultamos la empresa de la sesion

	//verificaremos la existencia de la sesion del usuario
	$usuario = $_SESSION['usuarioPOS'];
	if(empty($usuario)){
		//usuario vacio
		header('location:login.php');
	}else{
		$empresa = datoEmpresaSesion($usuario,"id");
		$empresa = json_decode($empresa);
		$idEmpresaSesion = $empresa->dato;

		$empresa2= datoEmpresaSesion($usuario,"nombre");
		$empresa2 = json_decode($empresa2);
		$nombrEmpresa = $empresa2->dato;

		$empresa3 = datoEmpresaSesion($usuario,"logo");
		$empresa3 = json_decode($empresa3);
		$logoEmp = $empresa3->dato;

		$sucursal = getSucursalUsuario($usuario);
		$sucursal = json_decode($sucursal);
		$nombreSucursal = $sucursal->dato;

		$dataUSer = getDataUser($idEmpresaSesion,$usuario);
		$dataUSer = json_decode($dataUSer);
		$idSucursalN = $dataUSer->sucursalID;

		$tipoRol = verTipoUsuario($usuario);
		$tipoUsuario = json_decode($tipoRol);
		$rolUsuario = "";
		// print_r($tipoUsuario);
		if($tipoUsuario->status == "ok"){
			$rolUsuario = $tipoUsuario->data;
		}else{
			$rolUsuario = "error";
		}

		// $datosUsuario = json_decode(getDataUser($usuario));
		// if($datosUsuario->status == "ok"){
			
		// }else{
		// 	//ERROR EN LA CONSULTA
		// }
	}

	

?>

<header class="app-header fixed-top">	   	            
  <div class="app-header-inner">  
	  <div class="container-fluid py-2">
		  <div class="app-header-content"> 
		    <div class="row justify-content-between align-items-center">
			        
          <div class="col-auto">
            <a id="sidepanel-toggler" class="sidepanel-toggler d-inline-block d-xl-none" href="#">
              <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" role="img"><title>Menu</title><path stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2" d="M4 7h22M4 15h22M4 23h22"></path></svg>
            </a>
          </div><!--//col-->

          <div class="search-mobile-trigger d-sm-none col">
            <i class="search-mobile-trigger-icon fa-solid fa-magnifying-glass"></i>
          </div><!--//col-->

          <div class="app-search-box col">
            <form class="app-search-form">   
              <input type="text" placeholder="Buscar pedido" name="search" class="form-control search-input">
              <button type="submit" class="btn search-btn btn-primary" value="Search"><i class="fa-solid fa-magnifying-glass"></i></button> 
            </form>
          </div><!--//app-search-box-->
		            
		      <div class="app-utilities col-auto">
			      <div class="app-utility-item app-notifications-dropdown dropdown">    
				      <a class="dropdown-toggle no-toggle-arrow" id="notifications-dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false" title="Notifications">
					      <!--//Bootstrap Icons: https://icons.getbootstrap.com/ -->
					      <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-bell icon" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                  <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2z"/>
                  <path fill-rule="evenodd" d="M8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5.002 5.002 0 0 1 13 6c0 .88.32 4.2 1.22 6z"/>
                </svg>
					      <span class="icon-badge">1</span>
					    </a><!--//dropdown-toggle-->
					        
					    <div class="dropdown-menu p-0" aria-labelledby="notifications-dropdown-toggle">
					      <div class="dropdown-menu-header p-3">
						      <h5 class="dropdown-menu-title mb-0">Notificaciones</h5>
						    </div><!--//dropdown-menu-title-->

						    <div class="dropdown-menu-content">
							    <div class="item p-3">
							      <div class="row gx-2 justify-content-between align-items-center">
								      <div class="col-auto">
									      <img class="profile-image" src="assets/images/profiles/profileDefault.jpg" alt="">
									    </div><!--//col-->
                      <div class="col">
                        <div class="info"> 
                          <div class="desc">Amy shared a file with you. Lorem ipsum dolor sit amet, consectetur adipiscing elit. </div>
                          <div class="meta"> 2 hrs ago</div>
                        </div>
                      </div><!--//col-->

								    </div><!--//row-->
								      <a class="link-mask" href="notifications.html"></a>
							      </div><!--//item-->

						      </div><!--//dropdown-menu-content-->
						        
                  <div class="dropdown-menu-footer p-2 text-center">
                    <a href="notifications.html">View all</a>
                  </div>			
							  </div><!--//dropdown-menu-->

				      </div><!--//app-utility-item-->
							<?php 
								if($rolUsuario == "Administrador"){
									?>
									<div class="app-utility-item">
										<a href="settings.php" title="Settings">
											<!--//Bootstrap Icons: https://icons.getbootstrap.com/ -->
											<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-gear icon" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
												<path fill-rule="evenodd" d="M8.837 1.626c-.246-.835-1.428-.835-1.674 0l-.094.319A1.873 1.873 0 0 1 4.377 3.06l-.292-.16c-.764-.415-1.6.42-1.184 1.185l.159.292a1.873 1.873 0 0 1-1.115 2.692l-.319.094c-.835.246-.835 1.428 0 1.674l.319.094a1.873 1.873 0 0 1 1.115 2.693l-.16.291c-.415.764.42 1.6 1.185 1.184l.292-.159a1.873 1.873 0 0 1 2.692 1.116l.094.318c.246.835 1.428.835 1.674 0l.094-.319a1.873 1.873 0 0 1 2.693-1.115l.291.16c.764.415 1.6-.42 1.184-1.185l-.159-.291a1.873 1.873 0 0 1 1.116-2.693l.318-.094c.835-.246.835-1.428 0-1.674l-.319-.094a1.873 1.873 0 0 1-1.115-2.692l.16-.292c.415-.764-.42-1.6-1.185-1.184l-.291.159A1.873 1.873 0 0 1 8.93 1.945l-.094-.319zm-2.633-.283c.527-1.79 3.065-1.79 3.592 0l.094.319a.873.873 0 0 0 1.255.52l.292-.16c1.64-.892 3.434.901 2.54 2.541l-.159.292a.873.873 0 0 0 .52 1.255l.319.094c1.79.527 1.79 3.065 0 3.592l-.319.094a.873.873 0 0 0-.52 1.255l.16.292c.893 1.64-.902 3.434-2.541 2.54l-.292-.159a.873.873 0 0 0-1.255.52l-.094.319c-.527 1.79-3.065 1.79-3.592 0l-.094-.319a.873.873 0 0 0-1.255-.52l-.292.16c-1.64.893-3.433-.902-2.54-2.541l.159-.292a.873.873 0 0 0-.52-1.255l-.319-.094c-1.79-.527-1.79-3.065 0-3.592l.319-.094a.873.873 0 0 0 .52-1.255l-.16-.292c-.892-1.64.902-3.433 2.541-2.54l.292.159a.873.873 0 0 0 1.255-.52l.094-.319z"/>
												<path fill-rule="evenodd" d="M8 5.754a2.246 2.246 0 1 0 0 4.492 2.246 2.246 0 0 0 0-4.492zM4.754 8a3.246 3.246 0 1 1 6.492 0 3.246 3.246 0 0 1-6.492 0z"/>
											</svg>
										</a>
									</div><!--//app-utility-item-->
									<?php
								}
								
							?>
              
			            
              <div class="app-utility-item app-user-dropdown dropdown">
                <a class="dropdown-toggle" id="user-dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">
									<img src="<?php echo $logoEmp; ?>" alt="Perfil" class="imagenPerfil">
								</a>
                <ul class="dropdown-menu" aria-labelledby="user-dropdown-toggle">
                  <li><a class="dropdown-item" href="account.php">Mi Cuenta</a></li>
									<?php 
										//verificamos el tipo de usuario debe ser administrador
										//para ver la seccion de configuracion
										if($rolUsuario == "Administrador"){
											echo '<li><a class="dropdown-item" href="settings.php">Configuracion</a></li>';
										}
									?>
                  
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="logOut.php">Cerrar Sesion</a></li>
                </ul>
              </div><!--//app-user-dropdown--> 

		      </div><!--//app-utilities-->

		    </div><!--//row-->
	    </div><!--//app-header-content-->
	  </div><!--//container-fluid-->
  </div><!--//app-header-inner-->


  
        <div id="app-sidepanel" class="app-sidepanel"> 
	        <div id="sidepanel-drop" class="sidepanel-drop"></div>
	        <div class="sidepanel-inner d-flex flex-column">
		        <a href="#" id="sidepanel-close" class="sidepanel-close d-xl-none">&times;</a>
		        <div class="app-branding">
		            <a class="app-logo" href="index.html"><img class="logo-icon me-2" src="assets/images/logo-postRepair.png" alt="logo"><span class="logo-text">PORTAL</span></a>
		        </div><!--//app-branding-->  
		        
			    	<nav id="app-nav-main" class="app-nav app-nav-main flex-grow-1">
				    	<ul class="app-menu list-unstyled accordion" id="menu-accordion">
					    	<li class="nav-item">
					        <!--//Bootstrap Icons: https://icons.getbootstrap.com/ -->
					        <a class="nav-link active" href="index.php">
						        <span class="nav-icon">
						        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-house-door" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
											<path fill-rule="evenodd" d="M7.646 1.146a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 .146.354v7a.5.5 0 0 1-.5.5H9.5a.5.5 0 0 1-.5-.5v-4H7v4a.5.5 0 0 1-.5.5H2a.5.5 0 0 1-.5-.5v-7a.5.5 0 0 1 .146-.354l6-6zM2.5 7.707V14H6v-4a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5v4h3.5V7.707L8 2.207l-5.5 5.5z"/>
											<path fill-rule="evenodd" d="M13 2.5V6l-2-2V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5z"/>
										</svg>
						        </span>
		                <span class="nav-link-text">Inicio</span>
					        </a><!--//nav-link-->
					    	</li><!--//nav-item-->

					    	<li class="nav-item">
					        <!--//Bootstrap Icons: https://icons.getbootstrap.com/ -->
					        <a class="nav-link" href="caja.php">
						        <span class="nav-icon">
											<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-card-list" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
												<path fill-rule="evenodd" d="M14.5 3h-13a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/>
												<path fill-rule="evenodd" d="M5 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 5 8zm0-2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm0 5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5z"/>
												<circle cx="3.5" cy="5.5" r=".5"/>
												<circle cx="3.5" cy="8" r=".5"/>
												<circle cx="3.5" cy="10.5" r=".5"/>
											</svg>
						        </span>
		                <span class="nav-link-text">Caja</span>
					        </a><!--//nav-link-->
					    	</li><!--//nav-item-->


					    	<li class="nav-item">
					        <!--//Bootstrap Icons: https://icons.getbootstrap.com/ -->
					        <a class="nav-link" href="#!">
						        <span class="nav-icon">
											<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-card-list" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
												<path fill-rule="evenodd" d="M14.5 3h-13a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/>
												<path fill-rule="evenodd" d="M5 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 5 8zm0-2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm0 5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5z"/>
												<circle cx="3.5" cy="5.5" r=".5"/>
												<circle cx="3.5" cy="8" r=".5"/>
												<circle cx="3.5" cy="10.5" r=".5"/>
											</svg>
						        </span>
		                <span class="nav-link-text">Trabajos</span>
					        </a><!--//nav-link-->
					    	</li><!--//nav-item-->
								<li class="nav-item">
									<a href="altaTrabajo.php" class="nav-link">
										<span class="nav-icon">
											<svg xmlns="http://www.w3.org/2000/svg" height="16" width="12" viewBox="0 0 384 512">
												<path d="M192 0c-41.8 0-77.4 26.7-90.5 64H64C28.7 64 0 92.7 0 128V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V128c0-35.3-28.7-64-64-64H282.5C269.4 26.7 233.8 0 192 0zm0 64a32 32 0 1 1 0 64 32 32 0 1 1 0-64zM72 272a24 24 0 1 1 48 0 24 24 0 1 1 -48 0zm104-16H304c8.8 0 16 7.2 16 16s-7.2 16-16 16H176c-8.8 0-16-7.2-16-16s7.2-16 16-16zM72 368a24 24 0 1 1 48 0 24 24 0 1 1 -48 0zm88 0c0-8.8 7.2-16 16-16H304c8.8 0 16 7.2 16 16s-7.2 16-16 16H176c-8.8 0-16-7.2-16-16z"/>
											</svg>
										</span>
										<span class="nav-link-text">Nuevo Trabajo</span>
									</a>
								</li>

								<li class="nav-item has-submenu">
									<a href="#!" class="nav-link submenu-toggle" data-bs-toggle="collapse" data-bs-target="#submenu-cajas" aria-expanded="false" aria-controls="submenu-cajas">
										<span class="nav-icon">
											<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cash-coin" viewBox="0 0 16 16">
												<path fill-rule="evenodd" d="M11 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8m5-4a5 5 0 1 1-10 0 5 5 0 0 1 10 0"/>
												<path d="M9.438 11.944c.047.596.518 1.06 1.363 1.116v.44h.375v-.443c.875-.061 1.386-.529 1.386-1.207 0-.618-.39-.936-1.09-1.1l-.296-.07v-1.2c.376.043.614.248.671.532h.658c-.047-.575-.54-1.024-1.329-1.073V8.5h-.375v.45c-.747.073-1.255.522-1.255 1.158 0 .562.378.92 1.007 1.066l.248.061v1.272c-.384-.058-.639-.27-.696-.563h-.668zm1.36-1.354c-.369-.085-.569-.26-.569-.522 0-.294.216-.514.572-.578v1.1zm.432.746c.449.104.655.272.655.569 0 .339-.257.571-.709.614v-1.195z"/>
												<path d="M1 0a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h4.083q.088-.517.258-1H3a2 2 0 0 0-2-2V3a2 2 0 0 0 2-2h10a2 2 0 0 0 2 2v3.528c.38.34.717.728 1 1.154V1a1 1 0 0 0-1-1z"/>
												<path d="M9.998 5.083 10 5a2 2 0 1 0-3.132 1.65 6 6 0 0 1 3.13-1.567"/>
											</svg>
										</span>
										<span class="nav-link-text">Cajas</span>
										<span class="submenu-arrow">
											<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-down" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
												<path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
											</svg>
	                	</span><!--//submenu-arrow-->
									</a>
									<div id="submenu-cajas" class="collapse submenu submenu-cajas" data-bs-parent="#menu-accordion">
										<ul class="submenu-list list-unstyled">
											<li class="submenu-item"><a class="submenu-link" href="cierreCaja.php">Cierre de Caja</a></li>
											<li class="submenu-item"><a class="submenu-link" href="cierreCaja.php">Sal/Ent Efectivo</a></li>
											<li class="submenu-item"><a class="submenu-link" href="reportesCaja.php">Reportes</a></li>
										</ul>
									</div>
								</li>

								<li class="nav-item has-submenu">
									<a href="#!" class="nav-link submenu-toggle" data-bs-toggle="collapse" data-bs-target="#submenu-cliente" aria-expanded="false" aria-controls="submenu-cliente">
										<span class="nav-icon">
										<svg xmlns="http://www.w3.org/2000/svg" height="16" width="20" viewBox="0 0 640 512">
											<path d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192h42.7c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0H21.3C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7h42.7C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3H405.3zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352H378.7C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7H154.7c-14.7 0-26.7-11.9-26.7-26.7z"/>
										</svg>
										</span>
										<span class="nav-link-text">Clientes</span>
										<span class="submenu-arrow">
											<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-down" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
												<path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
											</svg>
	                	</span><!--//submenu-arrow-->
									</a>
									<div id="submenu-cliente" class="collapse submenu submenu-cliente" data-bs-parent="#menu-accordion">
										<ul class="submenu-list list-unstyled">
											<li class="submenu-item"><a class="submenu-link" href="clientes.php">Ver Clientes</a></li>
											<li class="submenu-item"><a class="submenu-link" href="altaCliente.php">Nuevo Cliente</a></li>
										</ul>
									</div>
								</li>

								<li class="nav-item has-submenu">
									<a href="#!" class="nav-link submenu-toggle" data-bs-toggle="collapse" data-bs-target="#submenu-inventario" aria-expanded="false" aria-controls="submenu-cliinventarioente">
										<span class="nav-icon">
										<svg xmlns="http://www.w3.org/2000/svg" height="16" width="18" viewBox="0 0 576 512">
											<path d="M290.8 48.6l78.4 29.7L288 109.5 206.8 78.3l78.4-29.7c1.8-.7 3.8-.7 5.7 0zM136 92.5V204.7c-1.3 .4-2.6 .8-3.9 1.3l-96 36.4C14.4 250.6 0 271.5 0 294.7V413.9c0 22.2 13.1 42.3 33.5 51.3l96 42.2c14.4 6.3 30.7 6.3 45.1 0L288 457.5l113.5 49.9c14.4 6.3 30.7 6.3 45.1 0l96-42.2c20.3-8.9 33.5-29.1 33.5-51.3V294.7c0-23.3-14.4-44.1-36.1-52.4l-96-36.4c-1.3-.5-2.6-.9-3.9-1.3V92.5c0-23.3-14.4-44.1-36.1-52.4l-96-36.4c-12.8-4.8-26.9-4.8-39.7 0l-96 36.4C150.4 48.4 136 69.3 136 92.5zM392 210.6l-82.4 31.2V152.6L392 121v89.6zM154.8 250.9l78.4 29.7L152 311.7 70.8 280.6l78.4-29.7c1.8-.7 3.8-.7 5.7 0zm18.8 204.4V354.8L256 323.2v95.9l-82.4 36.2zM421.2 250.9c1.8-.7 3.8-.7 5.7 0l78.4 29.7L424 311.7l-81.2-31.1 78.4-29.7zM523.2 421.2l-77.6 34.1V354.8L528 323.2v90.7c0 3.2-1.9 6-4.8 7.3z"/>
										</svg>
										</span>
										<span class="nav-link-text">Inventario</span>
										<span class="submenu-arrow">
											<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-down" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
												<path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
											</svg>
	                	</span><!--//submenu-arrow-->
									</a>
									<div id="submenu-inventario" class="collapse submenu submenu-inventario" data-bs-parent="#menu-accordion">
										<ul class="submenu-list list-unstyled">
											<li class="submenu-item"><a class="submenu-link" href="verProductos.php">Ver Productos</a></li>
											<!-- <li class="submenu-item"><a class="submenu-link" href="verArticulo.php">Ver Materiales</a></li> -->
											<li class="submenu-item"><a class="submenu-link" href="altaProducto.php">Registrar Productos</a></li>
											<li class="submenu-item"><a class="submenu-link" href="entradaMercancia.php">Movimientos de Mercancia</a></li>
											<!-- <li class="submenu-item"><a class="submenu-link" href="altaArticulo.php">Registrar Materiales</a></li> -->
											<!-- <li class="submenu-item"><a class="submenu-link" href="altaArticulo.php">Entrada de Materiales</a></li> -->
										</ul>
									</div>
								</li>

								<li class="nav-item has-submenu">
									<a href="#!" class="nav-link submenu-toggle" data-bs-toggle="collapse" data-bs-target="#submenu-servicio" aria-expanded="false" aria-controls="submenu-cliinventarioente">
										<span class="nav-icon">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-usb-symbol" viewBox="0 0 16 16">
											<path d="m7.792.312-1.533 2.3A.25.25 0 0 0 6.467 3H7.5v7.319a2.5 2.5 0 0 0-.515-.298L5.909 9.56A1.5 1.5 0 0 1 5 8.18v-.266a1.5 1.5 0 1 0-1 0v.266a2.5 2.5 0 0 0 1.515 2.298l1.076.461a1.5 1.5 0 0 1 .888 1.129 2.001 2.001 0 1 0 1.021-.006v-.902a1.5 1.5 0 0 1 .756-1.303l1.484-.848A2.5 2.5 0 0 0 11.995 7h.755a.25.25 0 0 0 .25-.25v-2.5a.25.25 0 0 0-.25-.25h-2.5a.25.25 0 0 0-.25.25v2.5c0 .138.112.25.25.25h.741a1.5 1.5 0 0 1-.747 1.142L8.76 8.99a3 3 0 0 0-.26.17V3h1.033a.25.25 0 0 0 .208-.389L8.208.312a.25.25 0 0 0-.416 0"/>
										</svg>
										</span>
										<span class="nav-link-text">Servicios</span>
										<span class="submenu-arrow">
											<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-down" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
												<path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
											</svg>
	                	</span><!--//submenu-arrow-->
									</a>
									<div id="submenu-servicio" class="collapse submenu submenu-servicio" data-bs-parent="#menu-accordion">
										<ul class="submenu-list list-unstyled">
											<li class="submenu-item"><a class="submenu-link" href="verServicios.php">Ver Servicios</a></li>
											<li class="submenu-item"><a class="submenu-link" href="altaServicio.php">Registrar Servicio</a></li>
											<li class="submenu-item"><a class="submenu-link" href="verCatServicio.php">Ver Categorias de Servicio</a></li>
											<li class="submenu-item"><a class="submenu-link" href="altaCatServicio.php">Nueva Categoria de Servicios</a></li>
										</ul>
									</div>
								</li>

								<li class="nav-item has-submenu">
									<a href="#!" class="nav-link submenu-toggle" data-bs-toggle="collapse" data-bs-target="#submenu-sucursales" aria-expanded="false" aria-controls="submenu-cliinventarioente">
										<span class="nav-icon">
											<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-buildings-fill" viewBox="0 0 16 16">
  											<path d="M15 .5a.5.5 0 0 0-.724-.447l-8 4A.5.5 0 0 0 6 4.5v3.14L.342 9.526A.5.5 0 0 0 0 10v5.5a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5V14h1v1.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5zM2 11h1v1H2zm2 0h1v1H4zm-1 2v1H2v-1zm1 0h1v1H4zm9-10v1h-1V3zM8 5h1v1H8zm1 2v1H8V7zM8 9h1v1H8zm2 0h1v1h-1zm-1 2v1H8v-1zm1 0h1v1h-1zm3-2v1h-1V9zm-1 2h1v1h-1zm-2-4h1v1h-1zm3 0v1h-1V7zm-2-2v1h-1V5zm1 0h1v1h-1z"/>
											</svg>
										</span>
										<span class="nav-link-text">Sucursales</span>
										<span class="submenu-arrow">
											<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-down" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
												<path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
											</svg>
	                	</span><!--//submenu-arrow-->
									</a>
									<div id="submenu-sucursales" class="collapse submenu submenu-sucursales" data-bs-parent="#menu-accordion">
										<ul class="submenu-list list-unstyled">
											<li class="submenu-item"><a class="submenu-link" href="verSucursales.php">Ver Sucursales</a></li>
											<li class="submenu-item"><a class="submenu-link" href="altaSucursal.php">Registrar Sucursal</a></li>
										</ul>
									</div>
								</li>

								<li class="nav-item has-submenu">
									<a href="#!" class="nav-link submenu-toggle" data-bs-toggle="collapse" data-bs-target="#submenu-proveedores" aria-expanded="false" aria-controls="submenu-cliinventarioente">
										<span class="nav-icon">
											<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bus-front-fill" viewBox="0 0 16 16">
												<path d="M16 7a1 1 0 0 1-1 1v3.5c0 .818-.393 1.544-1 2v2a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5V14H5v1.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-2a2.5 2.5 0 0 1-1-2V8a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1V2.64C1 1.452 1.845.408 3.064.268A44 44 0 0 1 8 0c2.1 0 3.792.136 4.936.268C14.155.408 15 1.452 15 2.64V4a1 1 0 0 1 1 1zM3.552 3.22A43 43 0 0 1 8 3c1.837 0 3.353.107 4.448.22a.5.5 0 0 0 .104-.994A44 44 0 0 0 8 2c-1.876 0-3.426.109-4.552.226a.5.5 0 1 0 .104.994M8 4c-1.876 0-3.426.109-4.552.226A.5.5 0 0 0 3 4.723v3.554a.5.5 0 0 0 .448.497C4.574 8.891 6.124 9 8 9s3.426-.109 4.552-.226A.5.5 0 0 0 13 8.277V4.723a.5.5 0 0 0-.448-.497A44 44 0 0 0 8 4m-3 7a1 1 0 1 0-2 0 1 1 0 0 0 2 0m8 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0m-7 0a1 1 0 0 0 1 1h2a1 1 0 1 0 0-2H7a1 1 0 0 0-1 1"/>
											</svg>
										</span>
										<span class="nav-link-text">Proveedores</span>
										<span class="submenu-arrow">
											<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-down" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
												<path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
											</svg>
	                	</span><!--//submenu-arrow-->
									</a>
									<div id="submenu-proveedores" class="collapse submenu submenu-proveedores" data-bs-parent="#menu-accordion">
										<ul class="submenu-list list-unstyled">
											<li class="submenu-item"><a class="submenu-link" href="verProveedores.php">Ver Proveedores</a></li>
											<li class="submenu-item"><a class="submenu-link" href="altaProveedor.php">Registrar Proveedores</a></li>
										</ul>
									</div>
								</li>

								<li class="nav-item has-submenu">
									<a href="#!" class="nav-link submenu-toggle" data-bs-toggle="collapse" data-bs-target="#submenu-categorias" aria-expanded="false" aria-controls="submenu-categorias">
										<span class="nav-icon">
											<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-diagram-2-fill" viewBox="0 0 16 16">
												<path fill-rule="evenodd" d="M6 3.5A1.5 1.5 0 0 1 7.5 2h1A1.5 1.5 0 0 1 10 3.5v1A1.5 1.5 0 0 1 8.5 6v1H11a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-1 0V8h-5v.5a.5.5 0 0 1-1 0v-1A.5.5 0 0 1 5 7h2.5V6A1.5 1.5 0 0 1 6 4.5zm-3 8A1.5 1.5 0 0 1 4.5 10h1A1.5 1.5 0 0 1 7 11.5v1A1.5 1.5 0 0 1 5.5 14h-1A1.5 1.5 0 0 1 3 12.5zm6 0a1.5 1.5 0 0 1 1.5-1.5h1a1.5 1.5 0 0 1 1.5 1.5v1a1.5 1.5 0 0 1-1.5 1.5h-1A1.5 1.5 0 0 1 9 12.5z"/>
											</svg>
										</span>
										<span class="nav-link-text">Categorias</span>
										<span class="submenu-arrow">
											<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-down" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
													<path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
												</svg>
	                	</span><!--//submenu-arrow-->
									</a>
									<div id="submenu-categorias" class="collapse submenu submenu-categorias" data-bs-parent="#menu-accordion">
										<ul class="submenu-list list-unstyled">
											<li class="submenu-item"><a class="submenu-link" href="verCategorias.php">Ver Categorias</a></li>
											<li class="submenu-item"><a class="submenu-link" href="altaCategoria.php">Registrar Categorias</a></li>
										</ul>
									</div>
								</li>

								<li class="nav-item has-submenu">
									<a href="#!" class="nav-link submenu-toggle" data-bs-toggle="collapse" data-bs-target="#submenu-usuarios" aria-expanded="false" aria-controls="submenu-cliinventarioente">
										<span class="nav-icon">
											<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill-gear" viewBox="0 0 16 16">
												<path d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-9 8c0 1 1 1 1 1h5.256A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1 1.544-3.393Q8.844 9.002 8 9c-5 0-6 3-6 4m9.886-3.54c.18-.613 1.048-.613 1.229 0l.043.148a.64.64 0 0 0 .921.382l.136-.074c.561-.306 1.175.308.87.869l-.075.136a.64.64 0 0 0 .382.92l.149.045c.612.18.612 1.048 0 1.229l-.15.043a.64.64 0 0 0-.38.921l.074.136c.305.561-.309 1.175-.87.87l-.136-.075a.64.64 0 0 0-.92.382l-.045.149c-.18.612-1.048.612-1.229 0l-.043-.15a.64.64 0 0 0-.921-.38l-.136.074c-.561.305-1.175-.309-.87-.87l.075-.136a.64.64 0 0 0-.382-.92l-.148-.045c-.613-.18-.613-1.048 0-1.229l.148-.043a.64.64 0 0 0 .382-.921l-.074-.136c-.306-.561.308-1.175.869-.87l.136.075a.64.64 0 0 0 .92-.382zM14 12.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0"/>
											</svg>
										</span>
										<span class="nav-link-text">Usuarios</span>
										<span class="submenu-arrow">
											<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-down" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
													<path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
												</svg>
	                	</span><!--//submenu-arrow-->
									</a>
									<div id="submenu-usuarios" class="collapse submenu submenu-usuarios" data-bs-parent="#menu-accordion">
										<ul class="submenu-list list-unstyled">
											<li class="submenu-item"><a class="submenu-link" href="verUsuarioEmpr.php">Ver Usuarios</a></li>
											<li class="submenu-item"><a class="submenu-link" href="altaUsuariosEmpr.php">Registrar Usuarios</a></li>
										</ul>
									</div>
								</li>

					    		    
				    </ul><!--//app-menu-->
			    </nav><!--//app-nav-->

			    <div class="app-sidepanel-footer">
				    <nav class="app-nav app-nav-footer">
					    <ul class="app-menu footer-menu list-unstyled">
						    
						    
						    <li class="nav-item">
						        <!--//Bootstrap Icons: https://icons.getbootstrap.com/ -->
						        <a class="nav-link" href="logOut.php">
							        <span class="nav-icon">
												<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-file-person" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
													<path fill-rule="evenodd" d="M12 1H4a1 1 0 0 0-1 1v10.755S4 11 8 11s5 1.755 5 1.755V2a1 1 0 0 0-1-1zM4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4z"/>
													<path fill-rule="evenodd" d="M8 10a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
												</svg>
							        </span>
			                <span class="nav-link-text">Salir</span>
						        </a><!--//nav-link-->
						    </li><!--//nav-item-->
					    </ul><!--//footer-menu-->
				    </nav>
			    </div><!--//app-sidepanel-footer-->
		       
	        </div><!--//sidepanel-inner-->
	    </div><!--//app-sidepanel-->
    </header><!--//app-header-->