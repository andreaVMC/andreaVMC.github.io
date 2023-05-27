$(document).ready(function(){
    //nascondi il caricamento
    $("#caricamento").hide();
});

//funzione sleep
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}