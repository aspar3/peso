
//Get the modal
var modal = document.getElementById('myModal');
var modalEmail = document.getElementById('myModalEmail');
var modalFactura = document.getElementById('myModalFactura');

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
	
	var id = "";
	if (!(typeof event.target.id === 'undefined')) {
	  id = event.target.id;
	}

	var className = "";
	if (!(typeof event.target.className === 'undefined')) {
	  className = event.target.className;
	}
	
	if (!(typeof id === 'undefined') && (id.includes("myModal") || className.includes("cerrarModal"))) {
        var modals = document.getElementsByClassName("modal");
        var i;
        for (i = 0; i < modals.length; i++) {
          var openModal = modals[i];
            openModal.style.display = "none";
        }
	}
	
	// Close the dropdown menu if the user clicks outside of it
    if (!event.target.matches('.dropbtn') && !className.includes("noCerrar")) {
        var dropdowns = document.getElementsByClassName("dropdown-content");
        var i;
        for (i = 0; i < dropdowns.length; i++) {
          var openDropdown = dropdowns[i];
          if (openDropdown.classList.contains('show')) {
            openDropdown.classList.remove('show');
          }
        }
      }
}

function hideModal() {
	document.getElementById('myModal').style.display = 'none';
}

function hideModalEmail() {
	document.getElementById('myModalEmail').style.display = 'none';
}

function showModalEmail() {
	document.getElementById('myModalEmail').style.display = 'block';
}

function hideModalFactura() {
	document.getElementById('myModalFactura').style.display = 'none';
}

function showModalFactura() {
	document.getElementById('myModalFactura').style.display = 'block';
}