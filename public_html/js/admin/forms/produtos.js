LWDKExec(function(){
    One("#img_upload").addClass("dropzone").dropzone({ // The camelized version of the ID of the form element

        // The configuration we've talked about above
        autoProcessQueue: true,
        uploadMultiple: true,
        parallelUploads: 25,
        maxFiles: 25,
        acceptedFiles: "image/*",

        // The setting up of the dropzone
        init: function() {
            var myDropzone = this;

            setInterval(function(){
                $("[data-name=\"imagens\"]").val(JSON.stringify(MapEl("#gallery .img", function(){
                    return $(this).data("img-url");
                })));

				$(".apagar").each(function(){
                    One(this).click(function(){
                        confirm("Deseja mesmo remover essa imagem?") && $(this).parent().parent().slideUp('slow', function(){
							$.post(LWDKLocal, {act: "erase", file: (f=$(this).find(".img:first").data("img-url"))});
							// console.log(f);
							$(this).remove();
                        })
                    });
                });
            }, 500);

            myDropzone.on("successmultiple", function(file, response) {
                $.post("{myurl}", {imgs: response}, function(data){
                    $("#gallery.start").removeClass("start").html("");
                    $("#gallery").append(data);
                });
            });
        },

        complete: function(file){
            this.removeFile(file);
        }
    });

    FormCreateAction("dados_produto", function(data){
		data.itens = data.itens.split(",");
		// console.log(data);
		console.log(data.itens);
        let go = function(to){
            to = /(#)/.test(to)?$(to)[0]:$("[data-name=\"" + to + "\"]")[0];

            setTimeout(()=>$(to).focus()[0].click(), 1100);

            $([document.documentElement, document.body]).animate({
                scrollTop: $(to).offset().top - 150
            }, 900);
        };

        for(let n of ["titulo","valor","descricao","itens","ano","combustivel","cambio","cor"]){
            if(data[n].length < 1){
                return errorRequest(()=>go(n), (n.charAt(0).toUpperCase() + n.slice(1)) + " &eacute; obrigat&oacute;rio!");
            }
        }

        if(data.imagens.length < 1){
            return errorRequest(()=>go("#img_upload"), "Carregue ao menos uma imagem para o produto.");
        }

        $.post("{myurl}", {cadprod: data}, function(success){
            if(success===true){
                successRequest(null, "O produto foi {acao} com sucesso!");
            } else {
                // console.log(success);
                errorRequest(refresh);
            }
        });

    });

    let limg={imagens};
	$("[data-name=\"imagens\"]").val(limg);
	$("[data-name=\"itens\"]").data("value",{itens});

    limg.length > 0 && $.post("{myurl}", {imgs: limg}, function(data){
        $("#gallery.start").removeClass("start").html("");
        $("#gallery").append(data);
    });

	setTimeout(()=>{for(n of $("[data-name=\"itens\"]").data("value")){
		$("[data-name=\"itens\"]").tagEditor('addTag', n);
	}}, 1e3);
});
