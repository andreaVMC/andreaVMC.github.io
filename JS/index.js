$(".moon").click(function(){
        //light mode
    $(':root').css({
        '--sfondo': '#fff',
        '--text': '#000',
        '--contrasto': '#682bd7',
        '--contrasto-secondario': '#a37cf0',
        '--text-trs': '#000000bb',
    });
    $(".linea_img").attr("src","IMG/time line/line.png");
    $(this).css({
        'margin-bottom':'100%',
    })
    $(".sun").css({
        'margin-bottom':'0%',
    })
        //dark mode
        /*$(':root').css({
            '--sfondo': '#393E46',
            '--text': '#fff',
            '--contrasto': '#682bd7',
            '--contrasto-secondario': '#a37cf0',
            '--text-trs': '#ffffffbb',
        });*/
});

$(".sun").click(function(){
    //dark mode
    $(':root').css({
        '--sfondo': '#393E46',
        '--text': '#fff',
        '--contrasto': '#682bd7',
        '--contrasto-secondario': '#a37cf0',
        '--text-trs': '#ffffffbb',
    });
    $(".linea_img").attr("src","IMG/time line/line-light.png");
    $(this).css({
        'margin-bottom':'100%',
    })
    $(".moon").css({
        'margin-bottom':'0%',
    })
});

$(".indietro").click(function(){
    var deviceWidth = $(window).width();
    var n = $(".time_line").css("margin-left");
    n=converti(n);
    percentuale=getPercentage(n,deviceWidth);
    if(percentuale>=400){
        return;
    }else{
        $(".time_line").css({
            'margin-left':percentuale+100+'%',
        })
    }
});

$(".avanti").click(function(){
    var deviceWidth = $(window).width();
    var n = $(".time_line").css("margin-left");
    n=converti(n);
    percentuale=getPercentage(n,deviceWidth);
    if(percentuale<=(400*-1)){
        return;
    }else{
        $(".time_line").css({
            'margin-left':percentuale-100+'%',
        })
    }
    console.log($(".linea").height()+" "+$(".time_line").width());
});

function converti(stringa){
    var n = stringa.length;
    var n2 = stringa.substring(0,n-2);
    return n2;
}

//make a that get the percetuage of a number lenght with the total lengh
function getPercentage(number,total){
    return (number*100)/total;
}