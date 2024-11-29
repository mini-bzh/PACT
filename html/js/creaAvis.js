let menuDeroulant = document.getElementById("menuContexte");
let btnDeplierMenu = document.querySelectorAll("#menuContexte .conteneurSVGtexte")[0];
let inputContexte = document.getElementById("inputContexte");
let optionsContexte = document.querySelectorAll("#conteneurOptionsContexte p");
let titreSelect = document.querySelectorAll("#menuContexte .conteneurSVGtexte p")[0];

btnDeplierMenu.addEventListener("click", toggleMenuContexte);

function toggleMenuContexte()
{
    menuDeroulant.classList.toggle("deplie");
}

optionsContexte.forEach(option => {
    option.addEventListener("click", optionSelectionnee);
});

function optionSelectionnee()
{
    titreSelect.textContent = event.target.textContent;
    inputContexte.value = event.target.textContent;

    toggleMenuContexte();
}