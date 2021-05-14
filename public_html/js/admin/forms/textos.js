LWDKExec(function(){
    FormCreateAction("textos", function(){
        let textos = [];

        $(".summernote").each(function(){
            textos.push($(this).summernote('code'));
        });

        dados = {data: textos};

        $.post("{myurl}", dados, function(success){
            if(success===true){
                successRequest(null, "{nomecampo} atualizado(a)!");
            } else {
                errorRequest(refresh);
            }
        });
    });

    let i = 0;
    for(content of {valuesof}){
        $(".summernote").eq(i).summernote('code', content);
        i++;
    }
});
