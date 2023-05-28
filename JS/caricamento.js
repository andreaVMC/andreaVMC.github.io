$(document).ready(function(){
    //nascondi il caricamento
    //funzione sleep
    sleep(4000).then(() => {
        $("#caricamento").fadeOut();
        
    });
});

//funzione sleep
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}