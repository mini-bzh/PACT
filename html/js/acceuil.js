const carousel = document.querySelector(".card");
if(carousel != undefined)
{
    // Arrêter le défilement au survol quand on passe la souris dessus
    carousel.addEventListener("mouseover", () => {
        carousel.style.animationPlayState = "paused"; // l'animation est mit en pause
    });

    // rédemarre le défilement quand la souris n'est plus dessus
    carousel.addEventListener("mouseleave", () => {
        carousel.style.animationPlayState = "running"; // l'animation est redémarrer
    });
}

