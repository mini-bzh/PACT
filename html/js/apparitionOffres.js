console.log("cc")
const observer = new IntersectionObserver((entries) => {

    entries.forEach(entry => {
        if(entry.isIntersecting){
            console.log("dedans")
            entry.target.animate([
                {transform: 'scale(0.7)', opacity: 0.2},
                {transform: 'scale(1)', opacity: 1}
            ], {duration: 350});        
        }
    })
}, {threshold: 0, root: document.querySelector(".conteneuroffres")})

console.log(document.querySelectorAll(".conteneurOffres .apercuOffre"))
document.querySelectorAll(".conteneurOffres .apercuOffre").forEach(offre => {
    console.log(offre)
    observer.observe(offre)
})
