alert("cc");

let map = new Map();
["L", "Ma", "Me", "J", "V", "S", "D"].forEach(jour => {
    map.set(jour, document.getElementById("btn" + jour));
});

console.log(map);