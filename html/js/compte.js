

let btnDetailPrix = document.getElementsByClassName("btnDetailPrix");
let detailPrixDeplie = document.getElementsByClassName("detailPrixDeplie");

btnDetailPrix.addEventListener('click', () => {
    detailPrixDeplie.classList.toggle('displayNone');
});