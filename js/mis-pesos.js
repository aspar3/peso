function tipoDeLista(useTiplis) {
	if (useTiplis == "") {
		document.getElementById("tipoListaIntroducido").style.display = "none";
	} else {
		document.getElementById("loading").style.display = "block";
	    var fd = new FormData();
		fd.append("accion", "cambioTipoLista");
		fd.append("useTiplis", useTiplis);
	    var xhr = new XMLHttpRequest();
	    xhr.open("POST", "/mis-pesos-ajax");
	    xhr.send(fd);
	
		xhr.onreadystatechange=function() {
		    if (this.readyState==4 && this.status==200) {
				document.getElementById("tipoListaIntroducido").style.display = "block";
				document.getElementById("loading").style.display = "none";
		    }
		}
	}
}	

function anadirComida(tcoNombre, tcoIdtco) {
	    var fd = new FormData();
		fd.append("accion", "anadirComida");
		fd.append("tcoIdtco", tcoIdtco);
		fd.append("tcoNombre", tcoNombre);
		document.getElementById("loading").style.display = "block";
	    var xhr = new XMLHttpRequest();
	    xhr.open("POST", "/mis-pesos-ajax");
	    xhr.send(fd);
	
		xhr.onreadystatechange=function() {
		    if (this.readyState==4 && this.status==200) {
				cargarComidas(tcoIdtco, 1);
		    }
		}
}

function borrarComida(tcoIdtco, tcoIdcom) {
	    var fd = new FormData();
		fd.append("accion", "borrarComida");
		fd.append("tcoIdtco", tcoIdtco);
		fd.append("tcoIdcom", tcoIdcom);
		document.getElementById("loading").style.display = "block";
	    var xhr = new XMLHttpRequest();
	    xhr.open("POST", "/mis-pesos-ajax");
	    xhr.send(fd);
	
		xhr.onreadystatechange=function() {
		    if (this.readyState==4 && this.status==200) {
				cargarComidas(tcoIdtco, 0);
		    }
		}
}

function modificarComida(tcoNombre, tcoIdtco, tcoIdcom) {
	    var fd = new FormData();
		fd.append("accion", "modificarComida");
		fd.append("tcoIdtco", tcoIdtco);
		fd.append("tcoIdcom", tcoIdcom);
		fd.append("tcoNombre", tcoNombre);
		document.getElementById("loading").style.display = "block";
	    var xhr = new XMLHttpRequest();
	    xhr.open("POST", "/mis-pesos-ajax");
	    xhr.send(fd);
	
		xhr.onreadystatechange=function() {
		    if (this.readyState==4 && this.status==200) {
				cargarComidas(tcoIdtco, 0);
		    }
		}
}

function subirComida(tcoIdtco, tcoIdcom) {
	    var fd = new FormData();
		fd.append("accion", "subirComida");
		fd.append("tcoIdtco", tcoIdtco);
		fd.append("tcoIdcom", tcoIdcom);
		document.getElementById("loading").style.display = "block";
	    var xhr = new XMLHttpRequest();
	    xhr.open("POST", "/mis-pesos-ajax");
	    xhr.send(fd);
	
		xhr.onreadystatechange=function() {
		    if (this.readyState==4 && this.status==200) {
				cargarComidas(tcoIdtco, 0);
		    }
		}
}

function bajarComida(tcoIdtco, tcoIdcom) {
	    var fd = new FormData();
		fd.append("accion", "bajarComida");
		fd.append("tcoIdtco", tcoIdtco);
		fd.append("tcoIdcom", tcoIdcom);
		document.getElementById("loading").style.display = "block";
	    var xhr = new XMLHttpRequest();
	    xhr.open("POST", "/mis-pesos-ajax");
	    xhr.send(fd);
	
		xhr.onreadystatechange=function() {
		    if (this.readyState==4 && this.status==200) {
				cargarComidas(tcoIdtco, 0);
		    }
		}
}

function cargarComidas(tcoIdtco, focoNuevo) {
	    var fd = new FormData();
		fd.append("accion", "cargarComidas");
		fd.append("tcoIdtco", tcoIdtco);
		document.getElementById("loading").style.display = "block";
	    var xhr = new XMLHttpRequest();
	    xhr.open("POST", "/mis-pesos-ajax");
	    xhr.send(fd);
	
		xhr.onreadystatechange=function() {
		    if (this.readyState==4 && this.status==200) {
				document.getElementById("comidas").innerHTML=this.responseText;
				document.getElementById("loading").style.display = "none";
				if (focoNuevo == 1) {
					document.getElementById("nombreNuevo").focus();
				}
		    }
		}
}