LWDKExec(()=>One(".repeater-instance").repeater({
   initEmpty: !1,
   isFirstItemUndeletable: true,
   show: function (e){
       $(this).slideDown();
       $(this).find('.m_selectpicker').each(function(){
           One(this).selectpicker();
       });
       $(this).find('button').removeClass("m--hide");
       LWDKInitFunction.exec();
       if($(this).find('.dropdown.bootstrap-select.m_.form-control > .dropdown.bootstrap-select.m_.form-control').length>0){
           $(this).find('.dropdown.bootstrap-select.m_.form-control').replaceWith($(this).find('.dropdown.bootstrap-select.m_.form-control > .dropdown.bootstrap-select.m_.form-control select').removeClass("_mod")[0].outerHTML);
           $(this).find('.m_selectpicker').each(function(){
               One(this).selectpicker();
           });
       }
   },
   hide: function (e) {
       $(this).slideUp(e);
       LWDKInitFunction.exec();
   }
}));
