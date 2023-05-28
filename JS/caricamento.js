$(document).ready(function(){
    //nascondi il caricamento
    //funzione sleep
    sleep(2500).then(() => {
        $(".loading").fadeOut();
        document.getElementById("caricamento").style.height = "0%";
    });
});

//funzione sleep
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}