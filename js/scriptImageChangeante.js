let tabImages = ["carrou_fort1.jpg", "carrou_fort2.jpg", "carrou_fort3.jpg"];

let index = 0;
function changeImage()
{
    let image = document.getElementById("imageChangeante");

    image.style.opacity = 0;

    setTimeout(() => {
        index = (index + 1) % tabImages.length;

        image.src = "../images/images_illsutration_tempo/fort_la_latte/" + tabImages[index];

        image.style.opacity = 1;

    }, 300);

}

setInterval(() => {
    changeImage();
}, 2000);