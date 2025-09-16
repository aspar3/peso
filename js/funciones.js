function anadirFiltro(valor, nombreFiltroHidden) {
	filtroId = document.getElementById(nombreFiltroHidden);
	filtroId.value += " / " + valor;
	document.formulario.target = "";
	document.formulario.action = "";
	document.formulario.submit();
}

function anadirFiltroId(valor, nombreFiltroHidden, formulario) {
    var fd = new FormData();
	fd.append("accion", "getId");
	fd.append("valor", valor);
    var xhr = new XMLHttpRequest();
	if (nombreFiltroHidden == "filtroClientId") {
    	xhr.open("POST", pathUrlInstall + "/clientes-ajax.php");
	} else if (nombreFiltroHidden == "filtroEmpresaFactId") {
    	xhr.open("POST", pathUrlInstall + "/facturas-ajax.php");
	} else {
		xhr.open("POST", pathUrlInstall + "/proveedores-ajax.php");
	}
    xhr.send(fd);

	xhr.onreadystatechange=function() {
	    if (this.readyState==4 && this.status==200) {
			var json = JSON.parse(xhr.responseText);
			filtroId = document.getElementById(nombreFiltroHidden);
			if (json.idObtenido != "") {
				filtroId.value += " / " + json.idObtenido;
			}
			formulario.target = "";
			formulario.action = "";
			formulario.submit();
	    }
	}
}

function quitarFiltroId(idQuitar, nombreFiltroHidden, formulario) {
	filtroId = document.getElementById(nombreFiltroHidden);
	if (filtroId.value.includes(" / " + idQuitar)) {
		filtroId.value = filtroId.value.replace(" / " + idQuitar, "");
	} else {
		filtroId.value = filtroId.value.replace(idQuitar, "");
	}
	formulario.target = "";
	formulario.action = "";
	formulario.submit();
}

function getClientId(clientAlias) {
	if (clientAlias == "") {
		document.getElementById("clientId").value = "";
	} else {
	    var fd = new FormData();
		fd.append("accion", "getId");
		fd.append("valor", clientAlias);
		var xhr = new XMLHttpRequest();
		xhr.open("POST", pathUrlInstall + "/clientes-ajax.php");
	    xhr.send(fd);
	
		xhr.onreadystatechange=function() {
		    if (this.readyState==4 && this.status==200) {
				var json = JSON.parse(xhr.responseText);
				document.getElementById("clientId").value = json.idObtenido;
		    }
		}
	}
}



function orden(order, asc) {
	formulario.action = "";
	formulario.target = "";
	formulario.order.value = order;
	formulario.asc.value = asc;
	formulario.submit();
}

function ordenFiltro(order, asc) {
	document.formularioFiltro.action = "";
	document.formularioFiltro.target = "";
	document.formularioFiltro.order.value = order;
	document.formularioFiltro.asc.value = asc;
	document.formularioFiltro.submit();
}
			
function ordenFiltro(order, asc) {
	formularioFiltro.action = "";
	formularioFiltro.target = "";
	formularioFiltro.order.value = order;
	formularioFiltro.asc.value = asc;
	formularioFiltro.submit();
}
			
function valorEspToNumber(valor) {
	if (valor == "" || valor == 0 || valor != valor) { // valor != valor es por si viene NaN, asi se averigua
		return 0;
	} 
	return parseFloat(valor.replace(/\./g, "").replace(/,/g, "."));
}

function format(campo, numDecimales, esNumerico) {
	if (campo.value.trim() == "") {
		campo.value = "";
		return;
	}
	
	if (campo.value.trim() == "-1") {
		campo.value.trim() = $("<div>", {html:"&infin;"}).text();
	} else {
		campo.value = formatValor(campo.value.trim(), numDecimales, esNumerico);
	}
}

function roundDec(valorNumerico, numDecimales) {
	if (valorNumerico === 0) {
		return 0;
	}
	if (valorNumerico == "") {
		return;
	}
	if (numDecimales == 0) {
		return Math.round(valorNumerico);
	} else {
		multiplo = Math.pow(10, numDecimales);
		result = Math.round(valorNumerico * multiplo) / multiplo;
		return result;
	}	
}

function formatValor(valor, numDecimales, esNumerico) {
	if (valor === 0) {
		formateado = "0";
		if (numDecimales > 0) {
			formateado += ",";
			for (j=0; j<numDecimales; j++) {
				formateado += "0";
			}
		}
		return formateado;
	} else if (valor == "") {
		return;
	}

	var orig; 
	if (!esNumerico) {
		valor = valor + '';
		orig =  valorEspToNumber(valor);
	} else {
		orig = valor;
	}

//	if (numDecimales == 0) {
//		orig = Math.round(orig);
//	}
	
	orig = roundDec(orig, numDecimales);

	formateado = orig.toLocaleString('de-DE')
	if (numDecimales > 0) {
		coma = formateado.lastIndexOf(",");
		despuesComa = formateado.length - 1;
		decimalesActuales = despuesComa - coma;
		
		if (coma == -1) {
			formateado += ",";
			for (j=0; j<numDecimales; j++) {
				formateado += "0";
			}
		} else if (decimalesActuales > numDecimales) {
			decimales = formateado.substring(coma + 1);
			decimales = Math.round(("0." + decimales) * Math.pow(10, numDecimales), numDecimales);
			formateado = formateado.substring(0, coma + 1) + decimales;
		} else if (decimalesActuales < numDecimales) {
			for (i=decimalesActuales; i<numDecimales ;i++){
				formateado += "0";
			}
		}
	}
	return formateado;
}

function fechaTxtUsToSpain(txtFecha) {
	var arr1 = txtFecha.split('-');
	return arr1[2] + "-" + arr1[1] + "-" + arr1[0];
}

function validar_clave(contrasena, minimo) {
	if(contrasena.length >= minimo) {
				
		var mayuscula = false;
		var minuscula = false;
		var numero = false;
		var caracter_raro = true;
		
		for(var i = 0;i<contrasena.length;i++) {
			if(contrasena.charCodeAt(i) >= 65 && contrasena.charCodeAt(i) <= 90) {
				mayuscula = true;
			} else if(contrasena.charCodeAt(i) >= 97 && contrasena.charCodeAt(i) <= 122) {
				minuscula = true;
			} else if(contrasena.charCodeAt(i) >= 48 && contrasena.charCodeAt(i) <= 57) {
				numero = true;
			} else {
				caracter_raro = true;
			}
		}
		if(mayuscula == true && minuscula == true && caracter_raro == true && numero == true) {
			return true;
		}
	}
	return false;
}

function progressFunction(evt){  
	var progressBar = document.getElementById("progressBar");  
	var percentageDiv = document.getElementById("percentageCalc");  
	if (evt.lengthComputable) {  
		progressBar.max = evt.total;  
		progressBar.value = evt.loaded;  
		percentageDiv.innerHTML = Math.round(evt.loaded / evt.total * 100) + "%";  
	}  
}

function resetForm(formulario){
	formulario.accion.value="reset";
	formulario.target = "";
	formulario.action = "";
	formulario.submit();
}

function guardar(formulario){
	var hayError = false;
	let labels = Array.from(document.querySelectorAll('label'));
	errorMostrado = document.getElementById("id-error-message");
	if (errorMostrado != null && typeof errorMostrado !== 'undefined') {
		errorMostrado.parentNode.removeChild(errorMostrado);
	}
	for (let label of labels) {
		if (label.classList.contains("oblig")) {
			nombreCampo = label.textContent
			campo = document.getElementById(label.htmlFor);
			switch(campo.type.toLowerCase()) {
				case "text":
			    case "password":
				case "date":
				case "number":
					if (campo.value == "") {
						hayError = true;
						campo.focus();
			      	}
					break;
			    case "textarea":
					if (campo.innerHTML == "") {
						hayError = true;
						campo.focus();
			      	}
					break;
			    case "select-one":
			    case "select-multi":
					if (campo.selectedIndex == -1) {
						hayError = true;
						campo.focus();
					}
					break;
			    default:
					break;
			}
		}
		if (hayError) {
			break;
		}
	}
	if (hayError) {
		campo.classList.add("error");
		campo.insertAdjacentHTML("beforebegin", "<div id='id-error-message' class='error-message'>Campo obligatorio</div>");
	} else {
		formulario.target = "";
		formulario.action = "";
		formulario.accion.value = "save";
		formChanged = false;
		document.getElementById("loading").style.display = "block";
		formulario.submit();
	}
}

function lastDay(y, m){
	return new Date(y, m, 0).getDate();
}

function getToday() {
	let today = new Date();
	return today.toISOString().split('T')[0];
}

function excel(formulario, destino) {
	formulario.action = "" + destino;
	formulario.target = "_blank";
	formulario.submit();
	formulario.action = "";
	formulario.target = "";
}

function checkSNsubmit(formulario, campo, oculto) {
	if (campo.checked) {
		oculto.value = "S";
	} else {
		oculto.value = "N";
	}
	formulario.submit();
}

function cambiarYear(formulario, year) {
	formulario.fechaDesde.value = year + "-01-01";
	formulario.fechaHasta.value = year + "-12-31";
	document.formulario.target = "";
	document.formulario.action = "";
	formulario.submit();
}

function fechaMayorIgualHoy(campo) {
	hoy = new Date();
	hoy.setHours(0,0,0,0);
	varDate = new Date(campo.value)
	if (varDate < hoy) {
		alert('La fecha de cobro debe ser mayor o igual que hoy');
		campo.value = "";
	}
}

function fechaMayorIgualFechaFactura(campo) {
	varDateFactura = new Date(document.getElementById("date").value);
	varDateCobro = new Date(campo.value);
	if (varDateCobro < varDateFactura) {
		alert('Fecha de cobro es anterior a la Fecha factura');
	}
}