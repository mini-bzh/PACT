//alert(categorie_offre);

if (categorie_offre == 'visite') {
    document.getElementById('champsVisite').style.display = 'block';
} else if (categorie_offre == 'restauration') {
    document.getElementById('champsRestauration').style.display = 'block';
} else if (categorie_offre ==  'parcattraction') {
    document.getElementById('champsPA').style.display = 'block';
} else if (categorie_offre == 'spectacle') {
    document.getElementById('champsSpectacle').style.display = 'block';
} else if (categorie_offre == 'activite') {
    document.getElementById('champsActivite').style.display = 'block';
}