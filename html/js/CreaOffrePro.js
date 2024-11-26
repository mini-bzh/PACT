let prix = document.getElementById("prix-minimal") ;
let num_adr = document.getElementById("num") ;
let code_postal = document.getElementById("codePostal") ;



prix.onkeydown = (event) => {
    if((isNaN(event.key) && event.key !== 'Backspace' )|| event.key ===' ') {
      event.preventDefault();
    }
  };


num_adr.onkeydown = (event) => {
  if((isNaN(event.key) && event.key !== 'Backspace' )|| event.key ===' ') {
      event.preventDefault();
    }
  };


code_postal.onkeydown = (event) => {
  if((isNaN(event.key) && event.key !== 'Backspace' )|| event.key ===' ') {
      event.preventDefault();
    }
  };

