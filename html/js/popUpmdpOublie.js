let btnOublie = document.getElementById("mdpOublie");
let resetMdp = document.getElementById("resetForm");
let leForm = document.querySelector("#resetForm > form")

function recupMdp() {
    resetMdp.classList.toggle("displayNone");
}

resetMdp.addEventListener("click", recupMdp);
leForm.addEventListener("click", (event) => {
    event.stopPropagation();
});