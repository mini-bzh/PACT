<?php
if(!empty($_POST))
{
  $tab = json_decode($_POST["horaire"]);
  print_r($tab);
}
else
{


  ?>

  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
  </head>
  <body>
    <p>test tooltip</p>
    <form name="form" action="test2.php" method="post" enctype="multipart/form-data">
      <input type="hidden" id="inputH" name="horaire">

      <p id="btn">clique stp</p>
      <button type="submit">submit le form</button>
    </form>
  </body>
  <script>
    let btn = document.getElementById("btn");
    let inputH =document.getElementById("inputH");

    btn.addEventListener("click", ()=>{
      inputH.value =JSON.stringify(["10:10", "12:12", "", ""]);
      console.log(inputH.value);
    })
  </script>
  </html>

  <?php
}

?>