let conoscimi = document.querySelector(".conoscimi");
conoscimi=conoscimi.querySelector(".frontpage");

//definisco gli elementi
var blackhole=conoscimi.querySelector("#blackhole");
var pianeti=conoscimi.querySelector("#pianeti");
var satelliti=conoscimi.querySelector("#satelliti");
var testo=conoscimi.querySelector("#testo");
var bottone=conoscimi.querySelector("#bottone");

let value;
window.addEventListener('scroll',function(){
    value=window.scrollY;
    blackhole.style.left = (value*0.25)+"px";
    pianeti.style.left = -(value*0.25-200)+"px";
    satelliti.style.left = -(value*0.25-200)+"px";
    testo.style.top = -(value*0.03-35)+"vh";
    bottone.style.top = -(value*0.03-80)+"vh";
});