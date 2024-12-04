<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Formulaire Dynamique avec Deux Checkbox</title>
  <style>
    .hidden {
      display: none;
    }
  </style>
</head>
<body>
  <h1>Formulaire Dynamique</h1>
  <form>
    <label>
      <input type="checkbox" id="showCheckbox">
      Afficher les champs supplémentaires
    </label>
    <br>
    <label>
      <input type="checkbox" id="hideCheckbox">
      Cacher les champs supplémentaires
    </label>

    <!-- Champs supplémentaires -->
    <div id="extraFields" class="hidden">
      <label for="extraInput1">Champ supplémentaire 1 :</label>
      <input type="text" id="extraInput1" name="extraInput1"><br><br>

      <label for="extraInput2">Champ supplémentaire 2 :</label>
      <input type="text" id="extraInput2" name="extraInput2"><br><br>
    </div>
  </form>

  <script>
    // Récupération des éléments
    const showCheckbox = document.getElementById('showCheckbox');
    const hideCheckbox = document.getElementById('hideCheckbox');
    const extraFields = document.getElementById('extraFields');

    // Gestionnaire d'événements pour les deux checkbox
    showCheckbox.addEventListener('change', function() {
      if (this.checked) {
        extraFields.classList.remove('hidden');
        hideCheckbox.checked = false; // Décoche la deuxième checkbox
      }
    });

    hideCheckbox.addEventListener('change', function() {
      if (this.checked) {
        extraFields.classList.add('hidden');
        showCheckbox.checked = false; // Décoche la première checkbox
      }
    });
  </script>
</body>
</html>
