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