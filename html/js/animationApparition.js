let observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if(entry.isIntersecting){
            observer.unobserve(entry.target)

            let animation = entry.target.animate([
                {transform: 'scale(0.7)', opacity: 0.2},
                {transform: 'scale(1)', opacity: 1}
            ], {duration: 350});
            
            
            
            animation.onfinish = () =>{
                let exitObserver = new IntersectionObserver((exitEntries, exitObserver) => {
                    exitEntries.forEach(exitEntry => {
                        if (!exitEntry.isIntersecting) { 
                            observer.observe(exitEntry.target); // Réactive l'observation
                            exitObserver.unobserve(exitEntry.target); // Arrête l'observation de sortie
                        }
                    });
                });
                exitObserver.observe(entry.target); // Surveille quand l'élément quitte la vue
            }
        }
    })
}, {threshold: 0})

switch (document.title) {
    case "Gestion des offres":
        console.log("cc")
        document.querySelectorAll("#conteneurOffre .offre").forEach(elem => {
            observer.observe(elem)
        })
        break;

    case "Avis":
        document.querySelectorAll(".conteneurAvis .avis").forEach(elem => {
            observer.observe(elem)
        })
        break;

    case "Détail de l'offre":
        document.querySelectorAll(".conteneurAvis .avis").forEach(elem => {
            observer.observe(elem)
        })
        break;

    case "Accueil":
        document.querySelectorAll(".conteneurOffres .apercuOffre").forEach(elem => {
            observer.observe(elem)
        })
        break;

    case "Rechercher":
        document.querySelectorAll(".conteneurOffres .apercuOffre").forEach(elem => {
            observer.observe(elem)
        })
        break;

    default:
        break;
}

