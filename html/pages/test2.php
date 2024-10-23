<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
</head>
<body>
<button id="monBouton">Exécuter PHP</button>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $('#monBouton').on('click', function() {
        const idOffre = <?php echo "4"?>;
        $.ajax({
            url: 'toggleStatutOffre.php', // Le fichier PHP à appeler
            type: 'POST',        // Type de la requête (POST dans ce cas)
            data: idOffre,
            success: function(response) {
                alert(response); // Affiche la réponse de fonction.php
            },
            error: function() {
                alert('Erreur lors de l\'exécution de la fonction PHP');
            }
        });
    });
</script>

</body>
</html>