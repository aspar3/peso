<?php 
session_start();
$idiomaUrlOut = "";
if ($idiomaTxt != "" && $idiomaTxt != "es") {
	$idiomaUrlOut = "/en";
}

?>
							<nav id="nav">
								<ul>
									<li><a class="icon solid fa-home" href="<?=($idiomaUrlOut!=""?$idiomaUrlOut:"/")?>"><span><?=sprintf(litMenuInicio)?></span></a></li>
									<?php /*
									<li>
										<a href="#" class="icon fa-chart-bar"><span>Dropdown</span></a>
										<ul>
											<li><a href="#">Lorem ipsum dolor</a></li>
											<li><a href="#">Magna phasellus</a></li>
											<li><a href="#">Etiam dolore nisl</a></li>
											<li>
												<a href="#">Phasellus consequat</a>
												<ul>
													<li><a href="#">Magna phasellus</a></li>
													<li><a href="#">Etiam dolore nisl</a></li>
													<li><a href="#">Phasellus consequat</a></li>
												</ul>
											</li>
											<li><a href="#">Veroeros feugiat</a></li>
										</ul>
									</li>
									<li><a class="icon solid fa-cog" href="left-sidebar.html"><span>Left Sidebar</span></a></li>
									<li><a class="icon solid fa-retweet" href="right-sidebar.html"><span>Right Sidebar</span></a></li>
									*/?>
									<?php if (!isset($_SESSION["sesIduser"]) || $_SESSION["sesIduser"]=="" || $_SESSION["sesType"]!=1){?>
										<li><a class="icon solid fa-sign-in-alt" href="/login<?=$idiomaUrlOut?>"><span><?=sprintf(litMenuIniciarSesion)?></span></a></li>
										<li><a class="icon solid fa-user-plus" href="/sign-up<?=$idiomaUrlOut?>"><span><?=sprintf(litMenuRegistrarse)?></span></a></li>
									<?php } else {?>
										<li><a class="icon solid fa-user" href="/my-profile"><span><?=sprintf(litMenuMiPerfil)?></span></a></li>
										<li><a class="icon solid fa-users" href="/mis-grupos"><span><?=sprintf(litMenuMisGrupos)?></span></a></li>
										<li><a class="icon solid fa-balance-scale" href="/mis-pesos"><span><?=sprintf(litMenuMisPesos)?></span></a></li>
										<li><a class="icon solid fa-weight-hanging" href="/nuevo-peso"><span><?=sprintf(litMenuNuevoPeso)?></span></a></li>
										<li><a class="icon solid fa-sign-out-alt" href="/sign-out"><span><?=sprintf(litMenuSalir)?></span></a></li>
									<?php }?>
								</ul>
							</nav>
							