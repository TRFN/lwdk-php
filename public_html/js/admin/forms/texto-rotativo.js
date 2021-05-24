LWDKExec(function(){
    FormCreateAction("texto-rotativo", function(){
        let textos = [];

        $(".m-input").each(function(i){
			let smtctx = Math.sign($(this).closest("[data-repeater-item]").find(".summernote").length),
				smt = $(this).hasClass("summernote"),
				v = (!!smtctx && smt ? $(this).summernote("code"):$(this).val()),
				d = $(this).closest("[data-repeater-item]").find("[data-repeater-delete]");

            if(i > smtctx && smtctx && ((v&&smt&&v=="<p><br></p>") || (v=="" && !smt)) && !/imgpagsfixas/.test(v)){
                d.length && d[0].click();
            } else {
                textos.push(v);
            }
        });

        textos.length>0&&textos[0].length>0&&$("[data-repeater-create]")[0].click();

        dados = {data: textos};

        $.post("{myurl}", dados, function(success){
			typeof success == "string" && (success=(success==="true"));
            if(success===true){
                successRequest(null, "A galeria de videos foi atualizada!");
            } else {
                errorRequest(refresh);
            }
        });
    });

    let i = 0, c = {valuesof}, content;
	c = c.filter(function(word,index){
	    if(word.match(/imgpagsfixas/g)){/*the regex part*/
			$.post("/admin/ajax_oracoes_get_img/", {img: word}, function(data){
				$("#gallery input.m-input").replaceWith(data);
			});
		    return true;
		} else {
		    return true;
		}
	});

    for(content of c){
        i%2&&$("[data-repeater-create]")[0].click();

        $(".m-input").eq(i).val(content);

        i++;
    }
});
