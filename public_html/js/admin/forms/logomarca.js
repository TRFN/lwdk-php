LWDKExec(function(){
    // if(`{categorias}`.length == 0){
    //     return errorRequest(()=>Go("categorias"), "Antes de cadastrar um produto, voc&ecirc; precisa criar uma categoria.");
    // }
    // if(`{subcathtml}`.length == 0){
    //     return errorRequest(()=>Go("sub_categorias"), "Antes de cadastrar um produto, voc&ecirc; precisa criar uma sub-categoria.");
    // }

	One("#img_upload1").addClass("dropzone").dropzone({ // The camelized version of the ID of the form element

        // The configuration we've talked about above
        autoProcessQueue: true,
        uploadMultiple: true,
        parallelUploads: 1,
        maxFiles: 1,
        acceptedFiles: "image/*",

        // The setting up of the dropzone
        init: function() {
            var myDropzone = this;

            setInterval(function(){
            //     $("[data-name=\"imagens\"]").val(JSON.stringify(MapTranslate(MapEl("#gallery1 .img", function(){
            //         return $(this).css("background-image").split('"')[1] + "|" + $(this).parent().find("input:not([type=\"hidden\"])").first().val();
            //     }), ["url","legend"])));
            //
				$(".apagar").each(function(){
					One(this).click(function(){
						confirm("Deseja mesmo remover essa imagem?") && $(this).parent().parent().slideUp('slow', function(){
							$.post(LWDKLocal, {act: "erase", file: (f=$(this).find(".img:first").data("img-url"))});
							// console.log(f);
							$(this).remove();
						})
					});
				});
            //
            //     // One("#gallery1 input", "AutoComplete").change(function(){
            //         map = MapEl("#gallery1 input:not([type=\"hidden\"])", function(){return $(this).val();});
            //         $("#gallery1 input").each(function(){
            //             AutoComplete(this, map);
            //         });
            //     // });
            }, 500);

            myDropzone.on("successmultiple", function(file, response) {
                $.post("{myurl}", {imgs: response}, function(data){
                    $("#gallery1.start").removeClass("start").html("");
                    $("#gallery1").append(data);
                    $("#img_upload1")[0].dropzone.disable();
                });
            });
        },

        complete: function(file){
            this.removeFile(file);
        }
    });

	One("#img_upload2").addClass("dropzone").dropzone({ // The camelized version of the ID of the form element

        // The configuration we've talked about above
        autoProcessQueue: true,
        uploadMultiple: true,
        parallelUploads: 1,
        maxFiles: 1,
        acceptedFiles: "image/*",

        // The setting up of the dropzone
        init: function() {
            var myDropzone = this;

            setInterval(function(){
            //     $("[data-name=\"imagens\"]").val(JSON.stringify(MapTranslate(MapEl("#gallery1 .img", function(){
            //         return $(this).css("background-image").split('"')[1] + "|" + $(this).parent().find("input:not([type=\"hidden\"])").first().val();
            //     }), ["url","legend"])));
            //
                $(".apagar").each(function(){
                    One(this).click(function(){
                        let the = $(this).parent().parent();
                        if(confirm("Deseja mesmo remover esta logo?")){the.slideUp('slow', function(){
                            $("#img_upload2")[0].dropzone.enable();
                            $(this).remove();
                        })}
                    });
                });
            //
            //     // One("#gallery1 input", "AutoComplete").change(function(){
            //         map = MapEl("#gallery1 input:not([type=\"hidden\"])", function(){return $(this).val();});
            //         $("#gallery1 input").each(function(){
            //             AutoComplete(this, map);
            //         });
            //     // });
            }, 500);

            myDropzone.on("successmultiple", function(file, response) {
                $.post("{myurl}", {imgs: response}, function(data){
                    $("#gallery2.start").removeClass("start").html("");
                    $("#gallery2").append(data);
                    $("#img_upload2")[0].dropzone.disable();
                });
            });
        },

        complete: function(file){
            this.removeFile(file);
        }
    });

    const getLogoData = window.getLogoData = ((id) => {
		id--;
        return $("input.img").eq(id).length?$("input.img").eq(id).val():null;
    });

    const setLogoData = window.setLogoData = ((data,id=1) => {
        if(data === null || typeof data !== "string" || typeof data.length !== "number" || data.length === 0)return console.warn("Out: " + String(data));

        $("#img_upload" + String(id))[0].dropzone.disable();

        $("#gallery" + String(id) + ".start").removeClass("start").html("");
        $("#gallery" + String(id)).append(
            `<div class='col-12 text-center'>
                <input type=hidden class=img value='${data}' />
                <div class='col-12 img' style='background-image:url(/${data})'>
                    <br /><br /><br />
                </div>
                <div class='col-12 text-center'>
                    <button  class='apagar m-btn text-center m-btn--pill btn-outline-danger btn'>
                        <i class='la las la-trash'></i> Apagar
                    </button>
                </div>
            </div>`
        );
    });

    One(".submit").click(function(){
        $.post(LWDKLocal, {0:getLogoData(1),1:getLogoData(2)}, function(success){
            return success ? successRequest(refresh):errorRequest(refresh);
        });
    });

    typeof {valuesof} == "object" && typeof {valuesof} !== "null" && typeof {valuesof}[0] == "string" && setLogoData({valuesof}[0] , 1);
    typeof {valuesof} == "object" && typeof {valuesof} !== "null" && typeof {valuesof}[1] == "string" && setLogoData({valuesof}[1] , 2);
});
