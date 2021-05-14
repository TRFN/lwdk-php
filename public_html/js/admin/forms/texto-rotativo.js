LWDKExec(function(){
    FormCreateAction("texto-rotativo", function(){
        let textos = [];

        $("input.m-input").each(function(i){
            if((v=$(this).val()).length==0 && i != 0){
                $(this).closest("[data-repeater-item]").find("[data-repeater-delete]")[0].click();
            } else {
                textos.push(v);
            }
        });

        textos.length>0&&textos[0].length>0&&$("[data-repeater-create]")[0].click();

        dados = {data: textos};

        $.post("{myurl}", dados, function(success){
            if(success===true){
                successRequest(null, "Os textos rotativos do topo foram atualizados!");
            } else {
                errorRequest(refresh);
            }
        });
    });

    let i = 0;
    for(content of {valuesof}){
        $("[data-repeater-create]")[0].click();

        $("#texto-rotativo input.m-input").eq(i).val(content);

        i++;
    }
});
