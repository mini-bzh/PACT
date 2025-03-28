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

console.log(document.querySelectorAll(".conteneurOffres .apercuOffre"))
document.querySelectorAll(".conteneurOffres .apercuOffre").forEach(offre => {
    observer.observe(offre)
})
