function cambioEnvioAvisos(idGrupo, avisosMail) {
        const jsonEnviar = {
            idGrupo: idGrupo,
			avisosMail: (avisosMail?"S":"N")
        };
        fetch("/nuevo-grupo-rest.php", {
            method: "PUT",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(jsonEnviar)
        })
        .then(response => response.json())
        .then(result => {
			alert(result.msg);
        })
        .catch(error => {
            alert(result.msg);
        });
}
