LWDKInitFunction.addFN(()=>{
    let close_modal = (function(selector){
        return $(selector).attr("href","javascript:;").each(function(){
            this.click();
        });
    });

    close_modal("a.js-dropdn-close");

    close_modal(".mobilemenu-close");

    $.post("/cli_ajax/", function(cliente){
        if(cliente){
            $(".caixa-cliente-login").hide();
            $(".caixa-cliente-logado").show();

            for( i in cliente ){
                !/@/.test(i) && $("." + i + "-cliente").text(cliente[i]);
            }
        } else {
            $(".caixa-cliente-login").show();
            $(".caixa-cliente-logado").hide();
        }
    });

    $("footer a.custom-color[href*=\"conteudos\"]").each(function(){
        One(this).click(function(){
            setTimeout(()=>$('html, body').animate({scrollTop:0}, 'slow'), 1000);
        });
    });

    var body = $("html, body");
    body.stop().animate({scrollTop:0}, 250, 'swing', function() {
       // alert("Finished animating");
    });

	$(".js-add-wishlist").each(function(){
		One(this, "click__event").click(function(){
			if($(this).hasClass("not-execute")){
				$(this).removeClass("not-execute");
				return true;
			} else {
				$('.prod-' + $(this).data("prodid") + '-fav-btn a.js-add-wishlist').addClass("not-execute").each(function(){
					console.log(this);
					this.click();
				});
				$.post("/fav_ajax/", {addFav: $(this).data("prodid")});
			}
		});
	});

	$(".js-remove-wishlist").each(function(){
		One(this, "click__event").click(function(){
			if($(this).hasClass("not-execute")){
				$(this).removeClass("not-execute");
				return true;
			}
			$('.prod-' + $(this).data("prodid") + '-fav-btn a.js-remove-wishlist').addClass("not-execute").each(function(){
				console.log(this);
				this.click();
			});
			$.post("/fav_ajax/", {remFav: $(this).data("prodid")});
		});
	});
});
