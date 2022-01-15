function getNumberOfOptions(){
    $opts = $('table tr:nth-child(2) td:nth-child(2) select').children();
    return $opts;
}

function calc(){
    var calc = 60 + Math.round(Math.random()*Number(195));
    return calc;
}

function setColors($opts){
    $elements = $('table select').children().sort();
    $opts.each(function(){
        $dataName = $(this).attr("data-name");
        $dataColor = "rgba(" + calc() +"," + calc() +"," + calc() + ")";
        $elements.each(function(){
            if($(this).attr("data-name") == $dataName){
                $(this).css("background-color", $dataColor);
                $(this).attr("data-color", $dataColor);
                if($(this).attr("selected")){
                    $(this).parent().css("background-color", $dataColor);
                }
            }
        });
    });
}

$(function() {
    $opts = getNumberOfOptions();
    setColors($opts);
});

$("select option").on("click keypress", function(){
    $(this).addClass("active");
    $(this).siblings().removeClass("active");
    $val = $(this).attr("data-color");
    $(this).parent().css("background-color", $val);
});



