$(".theme").click(function(){
    if($(this).is(":checked")){
        //light mode
        $(':root').css({
            '--sfondo': '#fff',
            '--text': '#000',
            '--contrasto': '#682bd7',
            '--contrasto-secondario': '#a37cf0',
            '--text-trs': '#000000bb',
        });
    }else{
        //dark mode
        $(':root').css({
            '--sfondo': '#393E46',
            '--text': '#fff',
            '--contrasto': '#682bd7',
            '--contrasto-secondario': '#a37cf0',
            '--text-trs': '#ffffffbb',
        });
    }
});

var smooth = [$('a.smooth'), 100, 750];

smooth[0].click(function() {
   $('html, body').animate({
      scrollTop: $('[id="' + $.attr(this, 'href').substr(1) + '"]').offset().top -smooth[1]
   }, smooth[2]);
   return false;
});