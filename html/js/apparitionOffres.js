console.log("cc")
const observer = new IntersectionObserver((entries) => {
    console.log(entries)

    entries.forEach(entry => {
        if(entry.isIntersecting){
            console.log(entry)
            entry.target.classList.add("appearing")
        }
    })
})

console.log(document.querySelectorAll(".conteneurOffres .apercuOffre"))
document.querySelectorAll(".conteneurOffres .apercuOffre").forEach(offre => {
    console.log(offre)
    observer.observe(offre)
})

console.log("cv")