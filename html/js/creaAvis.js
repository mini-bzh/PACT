let menuDeroulant = document.getElementById("menuContexte");
let btnDeplierMenu = document.querySelectorAll("#menuContexte .conteneurSVGtexte")[0];
let inputContexte = document.getElementById("inputContexte");
let optionsContexte = document.querySelectorAll("#conteneurOptionsContexte p");
let titreSelect = document.querySelectorAll("#menuContexte .conteneurSVGtexte p")[0];

btnDeplierMenu.addEventListener("click", toggleMenuContexte);

console.log(titreSelect);

function toggleMenuContexte()
{
    menuDeroulant.classList.toggle("deplie");
}

optionsContexte.forEach(option => {
    option.addEventListener("click", optionSelectionnee);
});

function optionSelectionnee()
{
    console.log(event.target.textContent);
    titreSelect.textContent = event.target.textContent;

    toggleMenuContexte();
}