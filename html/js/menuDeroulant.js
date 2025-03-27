// Sélectionner les éléments
let menu = document.getElementsByClassName('infoId')[0];
let der = document.getElementById("menuDeroule");
let btnDer = document.getElementsByClassName("btnDeplie")[0];
let fleche = document.querySelector(".btnDeplie svg");
let fleche2 = document.getElementsByClassName("fleche2")[0];
let lesParg = document.querySelectorAll("#menuDeroule p, #menuDeroule button");

// Ajouter un événement au clic sur le bouton
if (btnDer) {
    btnDer.addEventListener('click', () => {
        menu.classList.toggle('deroulement');
        der.classList.toggle('displayNone');
        der.classList.toggle('menuDeplie');
        fleche.classList.toggle('rotate180');
        fleche2.classList.toggle('displayNone');

        lesParg.forEach(el => {
            el.classList.toggle('displayNone');
        });
    });
}

window.addEventListener("resize", () => {
    if (window.innerWidth > 428) {
        if (menu.classList.contains("deroulement")) {
            menu.classList.remove('deroulement');
        }
    }
    if (window.innerWidth > 428) {
        if (!der.classList.contains("displayNone")) {
            der.classList.add('displayNone');
        }
    }
    if (window.innerWidth > 428) {
        if (der.classList.contains("menuDeplie")) {
            der.classList.remove('menuDeplie');
        }
    }
    if (window.innerWidth > 428) {
        if (fleche.classList.contains("rotate180")) {
            fleche.classList.remove('rotate180');
        }
    }
    if (window.innerWidth > 428) {
        if (!fleche2.classList.contains("displayNone")) {
            fleche2.classList.add('displayNone');
        }
    }
    if (window.innerWidth > 428) {
        lesParg.forEach(el => {
            if (!el.classList.contains("displayNone")) {
                el.classList.add('displayNone');
            }
        });
    }
});