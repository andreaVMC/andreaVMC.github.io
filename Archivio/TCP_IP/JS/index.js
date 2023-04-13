$("#mode").click(function(){ 
    if($(this).is(":checked")){
        $(':root').css('--principale', '#393E46');
        $(':root').css('--secondario', '#929AAB');
        $(':root').css('--terziario', '#EEEEEE');
        $(':root').css('--quarto', '#F7F7F7');
        $(':root').css('--box', '#DDD');
        $('.logo').prop("src","IMG/icon.png");
        $('.capsulazione-img').prop("src","IMG/capsulating.png");
    }else{
        $(':root').css('--principale', '#F7F7F7');
        $(':root').css('--secondario', '#929AAB');
        $(':root').css('--terziario', '#EEEEEE');
        $(':root').css('--quarto', '#393E46');
        $(':root').css('--box', '#6a717c');
        $('.logo').prop("src","IMG/icon_light.png");
        $('.capsulazione-img').prop("src","IMG/capsulating-light.png");
    }
});