<?php
    class __dinamicJS {
        static function ajaxCore(){
            return 'const animateCSS=(n,e,a="animate__")=>new Promise((t,i)=>{const o=`${a}${e}`,s=document.querySelector(n);s.classList.add(`${a}animated`,o,"animate__faster"),s.addEventListener("animationend",function(n){n.stopPropagation(),s.classList.remove(`${a}animated`,o),t("Animation ended")},{once:!0})});const __LWDKSelectElements = sel => Array.from(document.querySelectorAll(sel));function __LWDKLinks(){var a = __LWDKSelectElements("a[ajax=on]"), i; for( i = 0; i < a.length; i++){a[i].setAttribute("ajax","off");a[i].addEventListener("click",function(evt){evt.stopPropagation(); evt.preventDefault(); __LWDKLoadPage(this.href, __LWDKLinks); return false;})}} function __LWDKLoadPage(page,fn){var ajax={};ajax.x=function(){if("undefined"!=typeof XMLHttpRequest)return new XMLHttpRequest;for(var e,t=["MSXML2.XmlHttp.6.0","MSXML2.XmlHttp.5.0","MSXML2.XmlHttp.4.0","MSXML2.XmlHttp.3.0","MSXML2.XmlHttp.2.0","Microsoft.XmlHttp"],n=0;n<t.length;n++)try{e=new ActiveXObject(t[n]);break}catch(e){}return e},ajax.send=function(e,t,n,o,a){void 0===a&&(a=!0);var r=ajax.x();return(r.open(n,e,a),r.onreadystatechange=function(){4==r.readyState&&t(r.responseText)},"POST"==n&&r.setRequestHeader("Content-type","application/x-www-form-urlencoded"),r.send(o))},ajax.get=function(e,t,n,o){var a=[];for(var r in t)a.push(encodeURIComponent(r)+"="+encodeURIComponent(t[r]));ajax.send(e+(a.length?"?"+a.join("&"):""),n,"GET",null,o)},ajax.post=function(e,t,n,o){var a=[];for(var r in t)a.push(encodeURIComponent(r)+"="+encodeURIComponent(t[r]));return ajax.send(e,n,"POST",a.join("&"),o)||true;};typeof swal != "undefined" && (props=({title: "",width: "72px",html:"<img style=\'width: 100%;\' src=\'/images/loading.gif\' />",showCancelButton: false,showConfirmButton: false, allowOutsideClick: false}),(!(navigator.userAgent.match(/Android/i)||navigator.userAgent.match(/webOS/i)||navigator.userAgent.match(/iPhone/i)||navigator.userAgent.match(/iPad/i)||navigator.userAgent.match(/iPod/i)||navigator.userAgent.match(/BlackBerry/i)||navigator.userAgent.match(/Windows Phone/i))&&(props.showClass = {popup: "animate__animated animate__fadeIn animate__faster"},props.hideClass = {popup: "animate__animated animate__fadeOutDown animate__faster"})),swal.fire(props));return ajax.get(page + "?ajax=1",{},function(data){(e=document.getElementById("page_content")).innerHTML=data;animateCSS("#page_content","fadeIn");LWDKInitFunction.exec();fn();eval(document.querySelector("script[lwdk-addons]").innerText);__LWDKLocal=page;history.pushState("", "", page);setTimeout("swal.close()",600)});}document.addEventListener("DOMContentLoaded",__LWDKLinks,true);window.__LWDKLocal=location.href;setInterval(function(){__LWDKLocal!==location.href&&__LWDKLoadPage(__LWDKLocal=location.href, __LWDKLinks);},600)';
        }

        static function initScripts(){
            return 'LWDKInitFunction = window.LWDKInitFunction = ({
				functions: [],
				addFN: function(toAdd){
					this.functions.push(toAdd);
				},
				exec: function(){
					for(let i in this.functions){
						this.functions[i]();
					}
				}
			});

            document.addEventListener("DOMContentLoaded",()=>LWDKInitFunction.exec(),true);';
        }
    }
