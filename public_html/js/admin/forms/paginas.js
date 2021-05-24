window.getAllData = function getAllData(){
	function is_cki(e){let name = $(e).data("name"); return typeof window[name] === "object"?[name,window[name].getData()]:-1;}

	return MapKeyAssign(((new Array).concat(MapEl(":not(#app-page-maker) [data-name]", function(){
		return((r=is_cki(this)) === -1)
			? [$(this).data("name"),$(this).data("name") == "ativo" ? $(this).bootstrapSwitch("state"):$(this).val()]
			: [r[0],r[1]];
		}, 0, 0, /string|boolean/))));
};

window.saveData = function saveData(){
	data = getAllData();
    $.post("/admin/ajax_paginas/", data, function(success){
        if(success===true){
            successRequest(null, "A pagina foi {acao} com sucesso!");
        } else {
			errorRequest();
        }
    });
};

LWDKExec(()=>{

});
