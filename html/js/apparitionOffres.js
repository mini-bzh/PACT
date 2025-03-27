console.log("cc")
let observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if(entry.isIntersecting){
            entry.target.animate([
                {transform: 'scale(0.7)', opacity: 0.2},
                {transform: 'scale(1)', opacity: 1}
            ], {duration: 350});        
        }
    })
}, {threshold: 0})

console.log(document.querySelectorAll(".conteneurOffres .apercuOffre"))
document.querySelectorAll(".conteneurOffres .apercuOffre").forEach(offre => {
    observer.observe(offre)
})
