let tabImages = ["carrou_fort1.jpg", "carrou_fort2.jpg", "carrou_fort3.jpg", "carrou_fort4.jpg"];

let index = 0;
function changeImage()
{
    index = (index + 1) % images.length;
    let image = document.getElementById("imageChangeante");
    image.setAttribute("src", "../images/images_illsutration_tempo/fort_la_latte/" + tabImages[index]);
}

setInterval(() => {
    changeImage();
}, 1000);

