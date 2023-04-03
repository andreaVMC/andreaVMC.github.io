const intro = document.querySelector(".content");
const video = intro.querySelector("video");
const text = intro.querySelector(".titolo-principale");
const text2 = intro.querySelector(".conoscimi1");
//END SECTION
const section = document.querySelector(".archivio");
const end = section.querySelector("h1");

//SCROLLMAGIC
const controller = new ScrollMagic.Controller();

//Scenes
let scene = new ScrollMagic.Scene({
  duration: 24000,
  triggerElement: intro,
  triggerHook: 0
})
  .setPin(intro)
  .addTo(controller);

//Text Animation
    /*let scene2 = new ScrollMagic.Scene({ //titolo grande iniziale
        duration: 3000,
        triggerElement: intro,
        triggerHook: 0
    })
        .setTween(TweenMax.fromTo(text, 3, { opacity: 1 }, { opacity: 0 }))
        .addTo(controller);

    let scene3 = new ScrollMagic.Scene({ //appare prima parte conoscimi
        duration: 3000,
        triggerElement: intro,
        offset: 3000,
        triggerHook: 0
    })
        .setTween(TweenMax.fromTo(text2, 3, { opacity: 0 }, { opacity: 1 },))
        .addTo(controller);

    let scene4 = new ScrollMagic.Scene({ //appare prima parte conoscimi
      duration: 3000,
      triggerElement: intro,
      offset: 6000,
      triggerHook: 0
    })
    .addIndicators()
        .setTween(TweenMax.fromTo(text2, 3, { opacity: 1 }, { opacity: 0 },))
        .addTo(controller);
*/        




//Video Animation
let accelamount = 0.1;
let scrollpos = 0;
let delay = 0;

scene.on("update", e => {
  scrollpos = e.scrollPos / 1000;
});

setInterval(() => {
  delay += (scrollpos - delay) * accelamount;
  console.log(scrollpos, delay);

  video.currentTime = delay;
}, 100);