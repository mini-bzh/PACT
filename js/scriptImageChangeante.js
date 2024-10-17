let tabImages = ["carrou_fort1.jpg", "carrou_fort2.jpg", "carrou_fort3.jpg"];

let index = 0;
function changeImage()
{
    index = (index + 1) % tabImages.length;
    document.getElementById("imageChangeante").src = "../images/images_illsutration_tempo/fort_la_latte/" + tabImages[index];
}



setInterval(() => {
    changeImage();
}, 2000);

