						(function() {

						  var result_prueba = "";
						  var resultado_Ocr = "";
						  var dataImage = "";
						  var result_ocr = "";
						  var valor_encontrado_ocr = "";
						  var arrayProveedores = [];
						  var accion_pagina = 1;

						  window.onload = function() {
						    $(".loader").fadeOut("slow");
						  };

						  var validar_file_act = $("#files").length;
						  if (validar_file_act > 0) {
						    document.getElementById('files').addEventListener('change', handleFileSelect, false);
						  }


						  $("#figure_step2").hide();


						  function productos() {
						    var tbody = $('#lista_productos tbody');
						    var fila_contenido = tbody.find('tr').first().html();
						    //Agregar fila nueva.
						    /*$('#lista_productos .button_agregar_producto').click(function() {
						   var fila_nueva = $('<tr></tr>');
						   fila_nueva.append(fila_contenido);
						   tbody.append(fila_nueva);
						 });*/

						    //Eliminar fila.
						    $('#lista_productos').on('click', '.button_eliminar_producto', function() {
						      $(this).parents('tr').eq(0).remove();
						    });
						  }




						  function inicializacion() {



						    var spte1_validacion = $("#contenedor_eleccion_sistema").length;

						    $("#contenedorprincipal").show();


						    $(".step1").hide();

						    $("#step1").hide();

						    $("#step2").show();

						    $("#step3").hide();

						    $("#adjust").hide();

						    var scale = 'scale(1)';
						    document.body.style.webkitTransform = scale; // Chrome, Opera, Safari
						    document.body.style.msTransform = scale; // IE 9
						    document.body.style.transform = scale; // General



						  }

						  inicializacion();

						  $("#adjust").attr('disabled', 'disabled');


						  var canvas = "";
						  var valor = 0;
						  var tpv_url = '';

						  function ab_ocr() {
						    console.log("IMAGEN CACHE: " + localStorage.getItem("imgData"));




						    $(".container").show("slow");

						    $(".step1").show("slow");

						    $("#contenedorprincipal_container_id").show("slow");

						    $("#modal_proveedor").css("display", "block");

						    $("#modal_proveedor").show("slow");

						    $("#modal_proveedor").css("z-index", "1041");

						    $("#contenedor_botones").hide("slow");
						    $("#contenedor_botones").css("display", "none");

						    $("#contenedorprincipal").show("slow");
						    $("#contenedorprincipal").css("display", "table");

						    $("#seleccion").css("display", "none");
						    $("#seleccion").hide();

						    $("#step1").css("display", "none");
						    $("#step1").hide();

						    $('#step2 img').show("slow");
						    $('#step2 img').css("display", "block");

						    $(".jumbotron figure").css("max-width", "640px");
						    $(".jumbotron figure").css("top", "50%");
						    $(".jumbotron figure").css("left", "50%");
						    $(".jumbotron figure").css("display", "inline-table");

						    var canvas = document.querySelector('#step2 canvas');
						    var img = document.querySelector('#step2 img');

						    dataImage = localStorage.getItem('imgData');

						    img.src = dataImage;



						    var ctx = canvas.getContext('2d');

						    //draw picture from video on canvas
						    ctx.drawImage(img, 0, 0);

						    //modify the picture using glfx.js filters


						    temp_step2imagen();
						  }

						  function abrir_ocr_main() {

						    $("#enviar_").show();

						    $(".container").show("slow");

						    $(".step1").show("slow");

						    $("#contenedorprincipal_container_id").show("slow");

						    $("#contenedorprincipal_container_id").css("display", "block");

						    $("#contenedorprincipal").show("slow");

						    $("#step1").css("display", "block");

						    $("#step1").show("slow");


						    $("#contenedorprincipal").css("display", "table");

						  }

						  $('#close_modal_ocr').click(function() {

						    $("#contenedorprincipal_container_id").hide();
						    $("#contenedorprincipal").hide();
						    $("#modal_proveedor").hide();
						    $('.contenedorprincipal_container').css("position:", "absolute");



						  });


						  function inicializacion_ocr() {

						    var spte1_validacion = $("#contenedor_eleccion_sistema").length;
						    if (spte1_validacion > 0) {
						      $("#evaluar").val("0");
						      localStorage.setItem("valortipoPA", 0);
						    } else {
						      $("#evaluar").val("1");
						      localStorage.setItem("valortipoPA", 1);
						    }

						    if ((localStorage.getItem("ocr_result")) == null && (localStorage.getItem('imgData') == null)) {


						      abrir_ocr_main();


						    } else {


						      document.getElementById("titulo_ocr_tablas").innerHTML = "Diccionarios de productos."

						      if ((localStorage.getItem("ocr_result")) != null && (localStorage.getItem('imgData') != null)) {

						        console.log("PROVEEDOR SELECCIONADO " + localStorage.getItem("ocr_result"));

						        $("#ac_proveedor").focus();

						        notificacion("warning", "topCenter", "PROVEEDOR SELECCIONADO " + localStorage.getItem("ocr_result"));


						        $("#modal_proveedor").css("display", "block");
						        $("#modal_proveedor").css("z-index:", "1042");

						        $("#ac_proveedor").val(localStorage.getItem("ocr_result"));

						        console.log(localStorage.getItem("ocr_result"));

						        $("#modal_proveedor").focus();


						      } else {

						        busqueda_proveedor_finalizada();

						        console.log("IMAGEN CACHE: " + localStorage.getItem("imgData"));

						        $(".container").show("slow");

						        $(".step1").show("slow");

						        $("#contenedorprincipal_container_id").show("slow");

						        $("#modal_proveedor").css("display", "block");

						        $("#modal_proveedor").show("slow");

						        $("#modal_proveedor").css("z-index", "1041");

						        $("#contenedor_botones").hide("slow");
						        $("#contenedor_botones").css("display", "none");

						        $("#contenedorprincipal").show("slow");
						        $("#contenedorprincipal").css("display", "table");

						        $("#seleccion").css("display", "none");
						        $("#seleccion").hide();

						        $("#step1").css("display", "none");
						        $("#step1").hide();

						        $('#step2 img').show("slow");
						        $('#step2 img').css("display", "block");

						        $(".jumbotron figure").css("max-width", "640px");
						        $(".jumbotron figure").css("top", "50%");
						        $(".jumbotron figure").css("left", "50%");
						        $(".jumbotron figure").css("display", "inline-table");

						        var canvas = document.querySelector('#step2 canvas');
						        var img = document.querySelector('#step2 img');

						        dataImage = localStorage.getItem('imgData');

						        img.src = dataImage;

						        var ctx = canvas.getContext('2d');

						        //draw picture from video on canvas
						        ctx.drawImage(img, 0, 0);

						        //modify the picture using glfx.js filters


						        step2imagen();


						      }

						    }



						    //			canvas.src=dataImage;
						  }




						  function handleFileSelect(evt) {


						    //				localStorage.removeItem("imgData");
						    //				localStorage.removeItem("ocr_result");
						    //				localStorage.removeItem("primera_busqueda");

						    var spte1_validacion = $("#contenedor_eleccion_sistema").length;

						    if (spte1_validacion == 0) {
						      eliminar_cache_ocr_main();
						    }
						    $("#figure_step2").show();
						    $("#seleccion").css("display", "none");
						    $("#videoSource").css("display", "none");
						    $("#videoSource").hide();



						    if (valor == 0) {
						      step1();

						      var files = evt.target.files; // FileList object

						      // Loop through the FileList and render image files as thumbnails.
						      for (var i = 0, f; f = files[i]; i++) {

						        // Only process image files.
						        if (!f.type.match('image.*')) {
						          continue;
						        }

						        var reader = new FileReader();

						        // Closure to capture the file information.
						        reader.onload = (function(theFile) {
						          return function(e) {
						            // Render thumbnail.

						            var canvas = document.querySelector('#step2 canvas');
						            var img = document.querySelector('#step2 img');
						            img.src = e.target.result;

						            localStorage.setItem("imgData", e.target.result);

						          };
						        })(f);

						        // Read in the image file as a data URL.
						        reader.readAsDataURL(f);
						      }
						    }

						    $(".jumbotron figure").css("max-width", "640px");
						    $(".jumbotron figure").css("top", "50%");
						    $(".jumbotron figure").css("left", "50%");
						    $(".jumbotron figure").css("display", "inline-table");





						    step2imagen();
						  }




						  function temp_step2imagen() {


						    $("#adjust").show();
						    console.log("valor" + valor);

						    $("#step1").hide();

						    $("#step2").show();

						    $(".step2").show();


						    $("#step3").hide();



						    var canvas = document.querySelector('#step2 canvas');
						    var img = document.querySelector('#step2 img');
						    var jcrobholder = document.querySelector('.jcrop-holder');

						    var ctx = canvas.getContext('2d');

						    //draw picture from video on canvas
						    ctx.drawImage(img, 0, 0);

						    //modify the picture using glfx.js filters



						    $(img).one('load', function() {
						      if (!$(img).data().Jcrop) {
						        $(img).Jcrop({
						          bgOpacity: 0.4,
						          bgColor: '#FFF',
						          multi: true,
						          multiMax: 5,
						          setSelect: [0, 0, 6000, 6000],
						          allowSelect: false,
						          onSelect: function() {
						            //Enable the 'done' button
						            $('#adjust').removeAttr('disabled');


						          }
						        });

						      } else {
						        //update crop tool (it creates copies of <img> that we have to update manually)
						        // actualizar herramienta de recorte (crea copias de <img> que tenemos que actualizar manualmente)
						        $('.jcrop-holder img').attr('src', fxCanvas.toDataURL());


						      }
						    });


						  }




						  /******************************* STEP2IMAGEN **************************************/


						  function step2imagen() {


						    $("#adjust").show();
						    console.log("valor" + valor);

						    $("#step1").hide();

						    $("#step2").show();

						    $(".step2").show();


						    $("#step3").hide();



						    var canvas = document.querySelector('#step2 canvas');
						    var img = document.querySelector('#step2 img');
						    var jcrobholder = document.querySelector('.jcrop-holder');

						    var ctx = canvas.getContext('2d');

						    //draw picture from video on canvas
						    ctx.drawImage(img, 0, 0);

						    //modify the picture using glfx.js filters



						    $(img).one('load', function() {
						      if (!$(img).data().Jcrop) {
						        $(img).Jcrop({
						          bgOpacity: 0.4,
						          bgColor: '#FFF',
						          multi: true,
						          multiMax: 5,
						          setSelect: [0, 0, 6000, 6000],
						          allowSelect: false,
						          onSelect: function() {
						            //Enable the 'done' button
						            $('#adjust').removeAttr('disabled');


						          }
						        });

						      } else {
						        //update crop tool (it creates copies of <img> that we have to update manually)
						        // actualizar herramienta de recorte (crea copias de <img> que tenemos que actualizar manualmente)
						        $('.jcrop-holder img').attr('src', fxCanvas.toDataURL());


						      }
						    });

						    if (document.readyState === 'complete') {
						      setTimeout(function() {
						        $(document).ready(function() {
						          busqueda_ocr_main();
						        })
						      }, 1000); // 3000ms = 3s

						    }
						  }

						  function valora() {

						    var canvas = document.querySelector('#step3 canvas');
						    var step2Image = document.querySelector('#step2 img');
						    //var cropData = $(step2Image).data().Jcrop.tellSelect();
						    var cropData = $(step2Image).data().Jcrop.tellSelect();


						    var scale = step2Image.width / $(step2Image).width();

						    //draw cropped image on the canvas
						    canvas.width = cropData.w * scale;
						    canvas.height = cropData.h * scale;

						    var ctx = canvas.getContext('2d');
						    ctx.drawImage(step2Image,
						      cropData.x * scale,
						      cropData.y * scale,
						      cropData.w * scale,
						      cropData.h * scale,
						      0,
						      0,
						      cropData.w * scale,
						      cropData.h * scale);

						    //   recognizeFile(ctx);
						    console.log("Texto [ " + ctx + " ] enviado a runOCR");

						    runOCR(ctx);


						  }

						  function busqueda_ocr_main() {

						    $("#adjust").attr('disabled', 'disabled');
						    $("#contenedor_progreso").css('display', "block");
						    console.log("Procesando. valora main.");
						    valora();

						  }

						  function busqueda_proveedor_finalizada() {

						    $("#contenedor_progreso").css('display', "none");
						    console.log("Fin.");
						    $("#panelherramiente").css("display", "inline-block");

						  }

						  $('#adjust').click(function() {


						    $(document).ready(function() {
						      busqueda_ocr_main();
						    });



						  });




						  function runOCR(url) {

						    Tesseract.recognize(url).then(function(result) {

						      if (result.text != "" && result.text != " " && result.text != ",") {
						        resultado_Ocr = result.text;

						        console.log("TOTAL PALABRAS ANALIZAR EN runOCR Main_fs " + result.text.length);


						        if ($("#f_new_albaran:visible").length > 0) {

						          if (localStorage.getItem("imgData") != null) {
						            if ($("#f_new_albaran").length > 0) {

						              dividirCadena_productos(result.text, " ");

						            }
						          }


						        } else {

						          localStorage.setItem("datosfactura", result.text);

						          dividirCadena_proveedor(result.text, " ");



						        }

						      } else {
						        notificacion("warning", "topCenter", "Sin resultado, Vuelve a intentarlo.");
						      }


						    }).progress(function(result) {
						      document.getElementById("ocr_status").innerText = result["status"] + " (" +
						        (result["progress"] * 100) + " %) ";

						      $(".progress-bar").css("width", (result["progress"] * 100) + "%");

						      document.getElementById("ocr_status_progress").innerText = result["progress"] * 100 + " % ";


						    });

						  }









						  $('#step1_herramienta').click(function() {
						    eliminar_cache_ocr_main();

						  });

						  $('#btn_eliminar_imagen_ocr').click(function() {
						    eliminar_cache_ocr_main();

						  });

						  function Validar_cache() {

						    if (localStorage.getItem("imgData") != null && localStorage.getItem("ocr_result") != null && localStorage.getItem("imgData") != null) {

						      return false;

						    } else {

						      return true;

						    }

						  }



						  function eliminar_cache_ocr_main() {

						    localStorage.removeItem("imgData");
						    localStorage.removeItem("ocr_result");
						    localStorage.removeItem("primera_busqueda");
						    localStorage.removeItem("datosfactura");
						    localStorage.removeItem("valortipoPA");

						    var spte1_validacion = $("#nuevo_proveedor").length;

						    if (spte1_validacion > 0) {

						      $("#ac_proveedor").val("");

						      $("#nuevo_proveedor").val("");


						    }

						    notificacion("success", "topCenter", "Datos en cache eliminados");

						    // inicializacion_ocr();

						    //	 location.reload();
						    window.location.href =Z(k,3,c[a+32>>2]|0,o+8|0,d)|0);do{}while(QZ(k,2,c[a+32>>2]|0,o+8|0,d)|0);m=NZ(k,0)|0;Q2(a,k,h);R2(h,k);if((j|0)!=0&f?S2(a,m,o+8|0,h,k,j)|0:0)m=2;else{if(c[84287]|0){c[a+80>>2]=0;c[a+76>>2]=0}T2(a,b,k);PZ(k,l);O2(a,k);do{}while(QZ(k,3,c[a+32>>2]|0,o+8|0,d)|0);do{}while(QZ(k,2,c[a+32>>2]|0,o+8|0,d)|0);do{}while(QZ(k,4,c[a+32>>2]|0,o+8|0,d)|0);if(!(c[84287]|0))m=0;else{c[a+84>>2]=0;m=0}}i=o;return m|0}function G2(a,b,f){a=a|0;b=b|0;f=f|0;var g=0,h=0,j=0,k=0,l=0,m=0,n=0;j=i;i=i+176|0;c[j+8>>2]=b;c[j+12>>2]=f;jN(352312,j+8|0);DT(j+64|0,a);h3(j+64|0,b,f,1);while(1){h=i3(j+64|0)|0;if(!h)break;a=e[h+12>>1]|e[h+14>>1]<<16;c[j>>2]=e[h+8>>1]|e[h+10>>1]<<16;c[j+4>>2]=a;if(!(px(j,+(b|0),+(f|0))|0))continue;if(c[h+4>>2]|0){g=5;break}}if((g|0)==5){a3(h);a=c[h+92>>2]|0;if(a)a3(a);a=c[h+100>>2]|0;if(a)a3(a);a=c[h+104>>2]|0;if(a)a3(a);a=c[h+96>>2]|0;if(a)a3(a);Jaa(h,j+48|0);n=c[j+56>>2]|0;m=c[j+60>>2]|0;l=c[j+52>>2]|0;k=d[h+112>>0]|0;a=d[h+113>>0]|0;b=d[h+108>>0]|0;f=d[h+110>>0]|0;g=d[h+111>>0]|0;h=d[h+109>>0]|0;c[j+8>>2]=c[j+48>>2];c[j+12>>2]=n;c[j+16>>2]=m;c[j+20>>2]=l;c[j+24>>2]=k;c[j+28>>2]=a;c[j+32>>2]=b;c[j+36>>2]=f;c[j+40>>2]=g;c[j+44>>2]=h;jN(337464,j+8|0)}GT(j+148|0);i=j;return}function H2(a,d,f,g,h,j,k){a=a|0;d=d|0;f=f|0;g=g|0;h=h|0;j=j|0;k=k|0;var l=0,m=0,n=0,o=0,p=0,q=0,r=0;r=i;i=i+144|0;c[r+128>>2]=0;c[r+132>>2]=0;c[r+136>>2]=0;c[r+140>>2]=0;p=((b[j+4>>1]|0)+(b[j>>1]|0)|0)/2|0;l=((b[j+6>>1]|0)+(b[j+2>>1]|0)|0)/2|0;DT(r+24|0,a);h3(r+24|0,p,l,2);l=0;a:while(1){do{p=i3(r+24|0)|0;if(!p){a=1;break a}}while((p|0)==(d|0));m=e[p+8>>1]|e[p+10>>1]<<16;a=e[p+12>>1]|e[p+14>>1]<<16;c[r>>2]=m;c[r+4>>2]=a;a=UGa(m|0,a|0,48)|0;b:do if(!(j3(j,r,f,g,h,r+16|0,r+12|0)|0)){n=c[r+16>>2]|0;o=c[r+12>>2]|0;if(!((n|0)>-1|(n|0)>(o|0))){l=b[j+6>>1]|0;m=r+128+(((a&65535)<<16>>16>l<<16>>16?3:1)<<2)|0;a=c[m>>2]|0;if((a|0)!=0?(o|0)>=(HA(b[j+2>>1]|0,l,b[a+10>>1]|0,b[a+14>>1]|0)|0):0)break;c[m>>2]=p;break}if(!((o|0)>-1|(o|0)>(n|0))){l=b[j>>1]|0;m=r+128+(((m&65535)<<16>>16>l<<16>>16?2:0)<<2)|0;a=c[m>>2]|0;if((a|0)!=0?(n|0)>=(lv(l,b[j+4>>1]|0,b[a+8>>1]|0,b[a+12>>1]|0)|0):0)break;c[m>>2]=p}}else{bja(j,r);FL(k,38,p);if(f){jN(337712,r+8|0);jv(r);a=0}else a=0;while(1){m=r+128+(a<<2)|0;if((l|0)!=0?(o=l+8|0,p=e[o+4>>1]|e[o+6>>1]<<16,c[r>>2]=e[o>>1]|e[o+2>>1]<<16,c[r+4>>2]=p,j3(j,r,f,g,h,r+16|0,r+12|0)|0):0){bja(j,r);FL(k,38,l);if(f){jN(337712,r+8|0);jv(r)}c[m>>2]=0;a=0}else{a=a+1|0;if((a|0)>=4)break b}l=c[r+128+(a<<2)>>2]|0}}while(0);l=c[r+128>>2]|0;if(!l){l=0;continue}if(!((c[r+136>>2]|0)==0|(c[r+140>>2]|0)==0|(c[r+132>>2]|0)==0)){a=1;break}}while(1){if(l){l=l+8|0;if(f){jN(337720,r+8|0);jv(l)}if(DD(j,l)|0){q=29;break}}if((a|0)>=4)break;l=c[r+128+(a<<2)>>2]|0;a=a+1|0}if((q|0)==29?(BL(k),f):0)jN(337752,r+8|0);GT(r+108|0);i=r;return}function I2(b,d){b=b|0;d=d|0;var e=0,f=0.0,h=0,j=0,k=0;k=i;i=i+16|0;e=kl(d+8|0)|0;f=+g[d+80>>2]*3.0;do if(+(e<<16>>16)>f?(h=ll(d+8|0)|0,+(h<<16>>16)>f):0){if((e<<16>>16|0)>(h<<16>>16<<2|0)){c[d+104>>2]=0;a[d+111>>0]=0;c[d+96>>2]=0;a[d+109>>0]=0;break}if((h<<16>>16|0)>(e<<16>>16<<2|0)){c[d+92>>2]=0;a[d+108>>0]=0;c[d+100>>2]=0;a[d+110>>0]=0}else j=7}else j=7;while(0);do if((j|0)==7){e=c[b+4>>2]|0;Kaa(d,k+12|0,k+8|0,k+4|0,k);j=c[k+8>>2]|0;if((!((j|0)<((e|0)/4|0|0)?(j+((e|0)/2|0)|0)<(c[k+4>>2]|0):0)?(a[d+114>>0]|0)==0:0)?(a[d+115>>0]|0)==0:0){j=c[k>>2]|0;if(!((j|0)<((e|0)/4|0|0)?(j+((e|0)/2|0)|0)<(c[k+12>>2]|0):0))break;c[d+92>>2]=0;a[d+108>>0]=0;c[d+100>>2]=0;a[d+110>>0]=0;break}c[d+104>>2]=0;a[d+111>>0]=0;c[d+96>>2]=0;a[d+109>>0]=0}while(0);i=k;return}function J2(d,e){d=d|0;e=e|0;var f=0,g=0,h=0,j=0,k=0,l=0,m=0,n=0,o=0;n=i;i=i+64|0;if(!(Oaa(e)|0)){m=xT(2,b[e+8>>1]|0,b[e+10>>1]|0)|0;if(m){k=c[e+40>>2]|0;c[n>>2]=c[e+44>>2];c[n+4>>2]=k;jN(337968,n);jv(e+8|0)}c[n+24>>2]=0;k3(e,n+24|0);f=c[e+92>>2]|0;if(f)k3(f,n+24|0);f=c[e+96>>2]|0;if(f)k3(f,n+24|0);f=c[e+100>>2]|0;if(f)k3(f,n+24|0);f=c[e+104>>2]|0;if(f)k3(f,n+24|0);if((CL(n+24|0)|0)>=4?(fA(n+28|0,n+24|0),gA(n+28|0),(hA(n+28|0)|0)<<24>>24==0):0){g=0;f=0;do{k=iA(n+28|0)|0;Kaa(k,n+56|0,n+60|0,n+20|0,n+16|0);if(m){h=c[n+60>>2]|0;j=c[n+20>>2]|0;o=c[n+16>>2]|0;c[n>>2]=c[n+56>>2];c[n+4>>2]=h;c[n+8>>2]=j;c[n+12>>2]=o;jN(339256,n)}else{h=c[n+60>>2]|0;j=c[n+20>>2]|0}do if(((h|0)>=(j|0)?(a[k+114>>0]|0)==0:0)?(a[k+115>>0]|0)==0:0)if((c[n+16>>2]|0)<(c[n+56>>2]|0)){f=f+1|0;if(!m)break;jN(339304,n);l=27;break}else{if(!m)break;jN(339320,n);l=27;break}else l=20;while(0);if((l|0)==20){l=0;g=g+1|0;if(m){jN(339288,n);l=27}}if((l|0)==27){l=0;jv(k+8|0)}DL(n+28|0)|0}while((hA(n+28|0)|0)<<24>>24==0)}else{g=0;f=0}if(m){fd[c[(c[d>>2]|0)+8>>2]&15](d,(b[e+8>>1]|0)+1|0,(b[e+10>>1]|0)+1|0);c[n>>2]=g;c[n+4>>2]=f;jN(338024,n)}do if(c[n+24>>2]|0){a[e+113>>0]=1;a[e+112>>0]=1;if((g|0)>(f<<1|0)){a[e+113>>0]=0;break}if((f|0)>(g<<1|0))a[e+112>>0]=0}else{a[e+113>>0]=0;a[e+112>>0]=0}while(0);BL(n+24|0)}i=n;return}function K2(d,e,f,h){d=d|0;e=e|0;f=f|0;h=h|0;var j=0,k=0,l=0,m=0,n=0;n=i;i=i+32|0;if(!(a[h+113>>0]|0))if(f)m=4;else m=18;else if((a[h+112>>0]|0)!=0|f)m=4;else m=18;if((m|0)==4){c[n+28>>2]=0;k3(h,n+28|0);fA(n,n+28|0);gA(n);if(!((hA(n)|0)<<24>>24)){j=0;f=0;do{l=iA(n)|0;k=(a[l+113>>0]|0)==0&1;if(!(a[l+112>>0]|0))f=(k^1)+f|0;else j=k+j|0;DL(n)|0}while((hA(n)|0)<<24>>24==0)}else{j=0;f=0}k=b[h+8>>1]|0;l=b[h+10>>1]|0;if(xT(2,k,l)|0){fd[c[(c[d>>2]|0)+8>>2]&15](d,k+1|0,l+1|0);c[n>>2]=j;c[n+4>>2]=f;jN(338064,n)}if((j|0)>(f|0)?!(c3(+g[d+56>>2],e)|0):0){a[h+113>>0]=0;a[h+112>>0]=1}else m=14;if(((m|0)==14?(f|0)>(j|0):0)?!(d3(+g[d+56>>2],e)|0):0){a[h+112>>0]=0;a[h+113>>0]=1}BL(n+28|0)}else if((m|0)==18?(j=b[h+8>>1]|0,k=b[h+10>>1]|0,xT(2,j,k)|0):0){fd[c[(c[d>>2]|0)+8>>2]&15](d,j+1|0,k+1|0);jN(338088,n)}i=n;return}function L2(d,f,j,l){d=d|0;f=f|0;j=j|0;l=l|0;var m=0,n=0.0,o=0,p=0,q=0,r=0,s=0,t=0,u=0,v=0.0,w=0,x=0,y=0,z=0,A=0,B=0,C=0,D=0,E=0,F=0,G=0,H=0,I=0,J=0;J=i;i=i+160|0;G=e[l+8>>1]|e[l+10>>1]<<16;I=e[l+12>>1]|e[l+14>>1]<<16;c[J+16>>2]=G;c[J+20>>2]=I;A=UGa(G|0,I|0,16)|0;B=UGa(G|0,I|0,48)|0;C=xT(2,(G&65535)<<16>>16,(A&65535)<<16>>16)|0;if(C){c[J+24>>2]=f;jN(337784,J+24|0);jv(J+16|0)}D=((I&65535)<<16>>16)-((G&65535)<<16>>16)|0;E=((B&65535)<<16>>16)-((A&65535)<<16>>16)|0;F=(D|0)>=(E|0)?D:E;H=((D|0)<=(E|0)?D:E)<<1;y=(f&-3|0)==0?E:D;z=j?1:(y|0)/3|0;y=j?1:(y|0)/2|0;j=~~(+R(+(+(ba(D,E)|0)))*2.5);x=c[d+4>>2]|0;j=(x|0)>(j|0)?x:j;c[J+8>>2]=G;c[J+12>>2]=I;switch(f|0){case 2:{b[J+12>>1]=I+j;m=8;break}case 1:{b[J+10>>1]=A-j;m=8;break}case 3:{b[J+14>>1]=B+j;m=8;break}case 0:{b[J+8>>1]=G-j;m=8;break}case 4:{j=0;break}default:m=8}if((m|0)==8){DT(J+56|0,d);WV(J+56|0,J+8|0);v=0.0;w=0;x=0;j=0;a:while(1){u=XV(J+56|0)|0;if(!u)break;m=e[u+8>>1]|e[u+10>>1]<<16;o=e[u+12>>1]|e[u+14>>1]<<16;c[J>>2]=m;c[J+4>>2]=o;p=UGa(m|0,o|0,48)|0;q=UGa(m|0,o|0,16)|0;if((u|0)==(l|0)){s=j;t=x;u=w;n=v;j=s;x=t;w=u;v=n;continue}if(((((o&65535)<<16>>16)+((m&65535)<<16>>16)|0)/2|0|0)<(b[l+48>>1]|0)){s=j;t=x;u=w;n=v;j=s;x=t;w=u;v=n;continue}if(((((o&65535)<<16>>16)+((m&65535)<<16>>16)|0)/2|0|0)>(b[l+50>>1]|0)){s=j;t=x;u=w;n=v;j=s;x=t;w=u;v=n;continue}if(C){jN(337808,J+24|0);jv(J)}s=kl(J)|0;t=ll(J)|0;if((((s<<16>>16|0)<=(t<<16>>16|0)?s<<16>>16:t<<16>>16)|0)>(H|0))j=((((s<<16>>16|0)>=(t<<16>>16|0)?s<<16>>16:t<<16>>16)|0)<((F|0)/4|0|0)&1)+j|0;do if(M3((s<<16>>16|0)>=(t<<16>>16|0)?s<<16>>16:t<<16>>16,F)|0){if(!((f&-3|0)==0?L3(t<<16>>16,E)|0:0)){if((f&-3|0)!=1)break;if(!(L3(s<<16>>16,D)|0))break}if(!C){t=x;u=w;n=v;x=t;w=u;v=n;continue a}jN(337824,J+24|0);t=x;u=w;n=v;x=t;w=u;v=n;continue a}while(0);do if(!(f&-3)){d=((p&65535)<<16>>16>(B&65535)<<16>>16?(B&65535)<<16>>16:(p&65535)<<16>>16)-((q&65535)<<16>>16<(A&65535)<<16>>16?(A&65535)<<16>>16:(q&65535)<<16>>16)|0;if((d|0)==(t<<16>>16|0))q=s<<16>>16>t<<16>>16?s<<16>>16:t<<16>>16;else q=d;m=(f|0)==0?((G&65535)<<16>>16)-((m&65535)<<16>>16)|0:((o&65535)<<16>>16)-((I&65535)<<16>>16)|0;if((m|0)>=1){p=m-(s<<16>>16)|0;r=d;m=q;break}if(!C){t=x;u=w;n=v;x=t;w=u;v=n;continue a}jN(337840,J+24|0);t=x;u=w;n=v;x=t;w=u;v=n;continue a}else{d=((o&65535)<<16>>16>(I&65535)<<16>>16?(I&65535)<<16>>16:(o&65535)<<16>>16)-((m&65535)<<16>>16<(G&65535)<<16>>16?(G&65535)<<16>>16:(m&65535)<<16>>16)|0;if((d|0)==(s<<16>>16|0))o=t<<16>>16>s<<16>>16?t<<16>>16:s<<16>>16;else o=d;if((f|0)==1)m=((A&65535)<<16>>16)-((q&65535)<<16>>16)|0;else m=((p&65535)<<16>>16)-((B&65535)<<16>>16)|0;if((m|0)>=1){p=m-(t<<16>>16)|0;r=d;m=o;break}if(!C){t=x;u=w;n=v;x=t;w=u;v=n;continue a}jN(337840,J+24|0);t=x;u=w;n=v;x=t;w=u;v=n;continue a}while(0);if((r|0)<(0-p|0)){if(!C){t=x;u=w;n=v;x=t;w=u;v=n;continue}jN(337856,J+24|0);t=x;u=w;n=v;x=t;w=u;v=n;continue}if((m|0)<(z|0)){if(!C){t=x;u=w;n=v;x=t;w=u;v=n;continue}jN(337880,J+24|0);t=x;u=w;n=v;x=t;w=u;v=n;continue}if(L3(E,t<<16>>16)|0)m=L3(D,s<<16>>16)|0;else m=0;if((r|0)<(y|0)|m)d=0;else d=Qaa(+g[l+72>>2],+g[l+76>>2],+g[l+80>>2],u,.125,1.5)|0;m=(p|0)<1?1:p;n=+(r|0)*(d?2.0:1.0)/+(m|0);if(C){h[k>>3]=n;c[J+24>>2]=c[k>>2];c[J+28>>2]=c[k+4>>2];h[k>>3]=v;c[J+32>>2]=c[k>>2];c[J+36>>2]=c[k+4>>2];c[J+40>>2]=d&1;c[J+44>>2]=r;c[J+48>>2]=m;jN(337904,J+24|0)}t=n>v;v=t?n:v;w=t?d:w;x=t?u:x}c[l+(f<<2)+92>>2]=x;a[l+f+108>>0]=w&1;GT(J+140|0)}i=J;return j|0}function M2(d,e){d=d|0;e=e|0;var f=0,h=0,j=0,k=0,l=0;l=i;i=i+112|0;k=+g[d+56>>2]==0.0?5:4;DT(l,d);C0(l);while(1){h=D0(l)|0;if(!h)break;if(c[h+84>>2]|0)continue;if(!(a[h+113>>0]|0))continue;if(a[h+112>>0]|0)continue;f=l3(h,3)|0;if(!f)continue;j=WEa(192)|0;b[l+104>>1]=0;b[l+106>>1]=1;XX(j,6,l+104|0);ZX(j,h);do{ZX(j,f);f=l3(f,3)|0}while((f|0)!=0);f=l3(h,1)|0;if(f)do{ZX(j,f);f=l3(f,1)|0}while((f|0)!=0);U2(d,k,j,e)}GT(l+84|0);i=l;return}function N2(d,e){d=d|0;e=e|0;var f=0,h=0,j=0,k=0,l=0;l=i;i=i+112|0;k=+g[d+56>>2]==0.0?4:5;DT(l,d);C0(l);while(1){h=D0(l)|0;if(!h)break;if(c[h+84>>2]|0)continue;if(!(a[h+112>>0]|0))continue;if(a[h+113>>0]|0)continue;f=m3(h,2)|0;if(!f)continue;j=WEa(192)|0;b[l+104>>1]=0;b[l+106>>1]=1;XX(j,7,l+104|0);ZX(j,h);do{ZX(j,f);f=m3(f,2)|0}while((f|0)!=0);f=m3(h,0)|0;if(f)do{ZX(j,f);f=l3(f,0)|0}while((f|0)!=0);U2(d,k,j,e)}GT(l+84|0);i=l;return}function O2(b,d){b=b|0;d=d|0;var e=0,f=0;f=WEa(16)|0;c[f>>2]=339056;c[f+4>>2]=b;a[f+8>>0]=49;a[f+9>>0]=0;a[f+10>>0]=0;a[f+11>>0]=0;a[f+12>>0]=0;a[f+13>>0]=0;a[f+14>>0]=0;a[f+15>>0]=0;e=WEa(16)|0;c[e>>2]=338864;c[e+4>>2]=b;a[e+8>>0]=50;a[e+9>>0]=0;a[e+10>>0]=0;a[e+11>>0]=0;a[e+12>>0]=0;a[e+13>>0]=0;a[e+14>>0]=0;a[e+15>>0]=0;JZ(d,f,e);return}function P2(a,b){a=a|0;b=b|0;var d=0;d=i;i=i+32|0;pk(d,a+20|0);rk(d);if(!(sk(d)|0))do{a=tk(d)|0;if(!(c[a+84>>2]|0))bY(a,b);OL(d)|0}while(!(sk(d)|0));i=d;return}function Q2(d,e,f){d=d|0;e=e|0;f=f|0;var g=0,h=0,j=0,k=0;k=i;i=i+128|0;CV(k+96|0,c[d+4>>2]|0,d+20|0,d+24|0);FV(k+96|0,f+12|0);FV(k+96|0,f+4|0);pk(k+64|0,f+12|0);rk(k+64|0);j=0;while(1){if(sk(k+64|0)|0)break;g=tk(k+64|0)|0;if((c[g+84>>2]|0)==0?!(sZ(g)|0):0)g=((V2(d,k+96|0,g)|0)&1)+j|0;else g=j;OL(k+64|0)|0;j=g}pk(k+36|0,f+4|0);rk(k+36|0);g=0;while(1){if(sk(k+36|0)|0)break;f=tk(k+36|0)|0;do if(!(sZ(f)|0)){h=c[f+84>>2]|0;if(!h){if(V2(d,k+96|0,f)|0){b3(d,f);qv(k+64|0,pv(k+36|0)|0);g=g+1|0;break}}else if((a[h+97>>0]|0)==0?(CL(h+76|0)|0)<3:0){fA(k+8|0,h+76|0);gA(k+8|0);while(1){if((hA(k+8|0)|0)<<24>>24)break;if(!(V2(d,k+96|0,iA(k+8|0)|0)|0))break;DL(k+8|0)|0}if(!((hA(k+8|0)|0)<<24>>24))break;while(1){if((aB(k+8|0)|0)<<24>>24)break;f=ZA(k+8|0)|0;c[f+84>>2]=0;DL(k+8|0)|0;b3(d,f);g=g+1|0}qv(k+64|0,pv(k+36|0)|0);oA(e,h);cY(h);YEa(h);break}if(xT(2,b[f+8>>1]|0,b[f+10>>1]|0)|0){jN(338112,k);jv(f+8|0)}}else qv(k+64|0,pv(k+36|0)|0);while(0);OL(k+36|0)|0}if(c[84287]|0){c[k>>2]=j;c[k+4>>2]=g;jN(338160,k)}DV(k+96|0);i=k;return}function R2(b,d){b=b|0;d=d|0;var e=0,f=0;f=i;i=i+32|0;pk(f,b+12|0);rk(f);if(!(sk(f)|0))do{e=tk(f)|0;b=c[e+68>>2]|0;if(b){b=c[b+84>>2]|0;if((((b|0)!=0?(a[b+97>>0]|0)==0:0)?(c[e+84>>2]|0)==0:0)?sZ(e)|0:0){oA(d,b);ZX(b,e);c[e+40>>2]=c[b+48>>2];c[e+44>>2]=c[b+52>>2];c[e+84>>2]=b;wA(d,b)}c[e+68>>2]=0}OL(f)|0}while(!(sk(f)|0));i=f;return}function S2(b,d,f,g,h,j){b=b|0;d=d|0;f=f|0;g=g|0;h=h|0;j=j|0;var k=0,l=0;l=i;i=i+176|0;c[l+168>>2]=0;k=NZ(h,l+168|0)|0;pk(l+140|0,j);j=c[l+168>>2]|0;do if(j){if(+(k|0)>((d|0)==0?4.0:+(d|0)*4.0)?+(k|0)>+(Jv(f)|0)*.001953125:0){b_(h);pk(l+112|0,g+12|0);bA(l+8|0,c[l+168>>2]|0);rk(l+112|0);while(1){if(sk(l+112|0)|0)break;j=tk(l+112|0)|0;c[j+92>>2]=0;c[j+96>>2]=0;c[j+100>>2]=0;c[j+104>>2]=0;c[j+108>>2]=0;if((sZ(j)|0?(c[j+84>>2]|0)==0:0)?(d=e[j+12>>1]|e[j+14>>1]<<16,c[l>>2]=e[j+8>>1]|e[j+10>>1]<<16,c[l+4>>2]=d,d=c[b+4>>2]|0,VV(l,d,d),EX(l+8|0,l),(FX(l+8|0)|0)!=0):0){a[j+116>>0]=1;SX(j);Ck(l+140|0,pv(l+112|0)|0)}OL(l+112|0)|0}$Z(c[l+168>>2]|0);j=c[l+168>>2]|0;if(j)Qc[c[(c[j>>2]|0)+4>>2]&511](j);jA(l+92|0);j=1;break}$Z(j);j=c[l+168>>2]|0;if(j){Qc[c[(c[j>>2]|0)+4>>2]&511](j);j=0}else j=0}else j=0;while(0);i=l;return j|0}function T2(a,b,d){a=a|0;b=b|0;d=d|0;var e=0,f=0,g=0,h=0,j=0,k=0,l=0;l=i;i=i+144|0;DT(l+32|0,a);c[l+28>>2]=0;fA(l,l+28|0);C0(l+32|0);h=1;j=-1;e=-1;while(1){k=D0(l+32|0)|0;if(!k)break;f=c[l+68>>2]|0;g=c[l+72>>2]|0;if((f|0)==(j|0)&(g|0)==(e|0))f=j;else{Y2(a,b,h,d,l+28|0);fA(l,l+28|0);h=1;e=g}if(c[k+84>>2]|0){h=0;j=f;continue}JC(l,k);h=(c[k+44>>2]|0)==1&h;j=f}Y2(a,b,h,d,l+28|0);BL(l+28|0);GT(l+116|0);i=l;return}function U2(a,b,d,f){a=a|0;b=b|0;d=d|0;f=f|0;var h=0;$X(d);h=e[d+16>>1]|e[d+18>>1]<<16;h=xT(2,h<<16>>16,h>>16)|0;h=N7(c[a+36>>2]|0,d,c[a+40>>2]|0,h)|0;if((h|0)>0){if(c3(+g[a+56>>2],b)|0){h=(CL(d+76|0)|0)==1;h=h?0:-2}}else if((h|0)<0){if(d3(+g[a+56>>2],b)|0){h=(CL(d+76|0)|0)==1;h=h?0:2}}else h=0;RY(d,h);aY(d);wA(f,d);return}function V2(d,f,g){d=d|0;f=f|0;g=g|0;var h=0,j=0,k=0,l=0,m=0,n=0,o=0,p=0,q=0,r=0,s=0,t=0,u=0,v=0,w=0,x=0,y=0,z=0,A=0,B=0,C=0,D=0,E=0.0;D=i;i=i+176|0;B=(c[g+40>>2]|0)+-1|0;if(B>>>0<6?(47>>>(B&63)&1)!=0:0){i=D;return 0}v=e[g+8>>1]|e[g+10>>1]<<16;q=e[g+12>>1]|e[g+14>>1]<<16;c[D+32>>2]=v;c[D+36>>2]=q;z=xT(2,v<<16>>16,v>>16)|0;r=UGa(v|0,q|0,16)|0;s=UGa(v|0,q|0,48)|0;if(z){jN(338200,D);jv(D+32|0)}t=_Ga(q|0,0,16)|0;lV(d,((t>>16)+(v<<16>>16)|0)/2|0,((q>>16)+(v>>16)|0)/2|0,D+56|0,D+60|0);t=ll(D+32|0)|0;b[D+48>>1]=32767;b[D+50>>1]=32767;b[D+52>>1]=-32767;b[D+54>>1]=-32767;c[D+40>>2]=v;c[D+44>>2]=q;E=+(c[d+4>>2]|0);w=ok(E*7.0)|0;VV(D+40|0,w,ok(E*1.75)|0);DT(D+64|0,d);a[D+108>>0]=1;WV(D+64|0,D+40|0);w=-32767;x=32767;A=0;y=0;B=0;h=0;while(1){u=XV(D+64|0)|0;if(!u)break;k=c[u+40>>2]|0;if((k+-1|0)>>>0<2){l=h;m=B;n=y;o=A;p=x;u=w;h=l;B=m;y=n;A=o;x=p;w=u;continue}if((k+-3|0)>>>0<2|(u|0)==(g|0)){l=h;m=B;n=y;o=A;p=x;u=w;h=l;B=m;y=n;A=o;x=p;w=u;continue}o=c[u+84>>2]|0;if((o|0)==(c[g+84>>2]|0)){l=h;m=B;n=y;o=A;p=x;u=w;h=l;B=m;y=n;A=o;x=p;w=u;continue}n=e[u+8>>1]|e[u+10>>1]<<16;m=e[u+12>>1]|e[u+14>>1]<<16;c[D+16>>2]=n;c[D+20>>2]=m;l=UGa(n|0,m|0,16)|0;p=UGa(n|0,m|0,48)|0;if(((o|0)!=0?(c[o+48>>2]&-5|0)!=2:0)?((c[u+44>>2]|0)+-3|0)>>>0<2:0){if(((ll(D+16|0)|0)<<16>>16|0)<(~~(+(t<<16>>16)*1.0625)|0)){if(!z){l=h;m=B;n=y;o=A;p=x;u=w;h=l;B=m;y=n;A=o;x=p;w=u;continue}jN(338272,D);jv(D+16|0);l=h;m=B;n=y;o=A;p=x;u=w;h=l;B=m;y=n;A=o;x=p;w=u;continue}o=(((v&65535)<<16>>16<(n&65535)<<16>>16?n&65535:v&65535)<<16>>16)-(((q&65535)<<16>>16>(m&65535)<<16>>16?m&65535:q&65535)<<16>>16)|0;k=UGa(v|0,q|0,48)|0;n=H7(c[d+36>>2]|0,D+32|0,D+16|0,1,c[d+40>>2]|0,z)|0;if(z){c[D>>2]=o;c[D+4>>2]=(((v>>>16&65535)<<16>>16<(l&65535)<<16>>16?l&65535:v>>>16&65535)<<16>>16)-(((k&65535)<<16>>16>(p&65535)<<16>>16?p&65535:k&65535)<<16>>16);c[D+8>>2]=n;jN(338304,D)}k=c[(c[u+84>>2]|0)+32>>2]|0;if(+(n|0)>+(k|0)*1.25){if(!z){l=h;m=B;n=y;o=A;p=x;u=w;h=l;B=m;y=n;A=o;x=p;w=u;continue}c[D>>2]=k;jN(338336,D);jv(u+8|0);l=h;m=B;n=y;o=A;p=x;u=w;h=l;B=m;y=n;A=o;x=p;w=u;continue}if((o|0)<1){if(z){jN(338384,D);jv(D+16|0)}k=(kl(D+32|0)|0)<<16>>16;Raa(D+24|0,u,((v&65535)<<16>>16)-k|0,((q&65535)<<16>>16)+k|0);k=c[D+24>>2]|0;l=c[D+28>>2]|0;c[D+16>>2]=k;c[D+20>>2]=l;m=UGa(k|0,l|0,16)|0;n=UGa(k|0,l|0,48)|0;o=(((r&65535)<<16>>16<(m&65535)<<16>>16?m&65535:r&65535)<<16>>16)-(((s&65535)<<16>>16>(n&65535)<<16>>16?n&65535:s&65535)<<16>>16)|0;if((y|0)==0|(o|0)<(B|0)){c[D+48>>2]=k;c[D+52>>2]=l;if(!z){l=h;p=A;w=n&65535;x=m&65535;y=u;B=o;h=l;A=p;continue}jN(338416,D);jv(D+16|0);l=h;p=A;w=n&65535;x=m&65535;y=u;B=o;h=l;A=p;continue}else{if(!z){l=h;m=B;n=y;o=A;p=x;u=w;h=l;B=m;y=n;A=o;x=p;w=u;continue}jN(338432,D);jv(D+16|0);l=h;m=B;n=y;o=A;p=x;u=w;h=l;B=m;y=n;A=o;x=p;w=u;continue}}if(!(Paa(g,u)|0)){if(!z){l=h;m=B;n=y;o=A;p=x;u=w;h=l;B=m;y=n;A=o;x=p;w=u;continue}jN(338520,D);jv(D+16|0);l=h;m=B;n=y;o=A;p=x;u=w;h=l;B=m;y=n;A=o;x=p;w=u;continue}if((h|0)==0|(n|0)<(A|0)){if(!z){l=B;m=y;o=x;p=w;A=n;h=u;B=l;y=m;x=o;w=p;continue}jN(338464,D);jv(D+16|0);l=B;m=y;o=x;p=w;A=n;h=u;B=l;y=m;x=o;w=p;continue}else{if(!z){l=h;m=B;n=y;o=A;p=x;u=w;h=l;B=m;y=n;A=o;x=p;w=u;continue}jN(338488,D);jv(D+16|0);l=h;m=B;n=y;o=A;p=x;u=w;h=l;B=m;y=n;A=o;x=p;w=u;continue}}if(!z){l=h;m=B;n=y;o=A;p=x;u=w;h=l;B=m;y=n;A=o;x=p;w=u;continue}jN(338240,D);jv(D+16|0);l=h;m=B;n=y;o=A;p=x;u=w;h=l;B=m;y=n;A=o;x=p;w=u}k=(h|0)==0;do if(!y)if(k){j=0;h=0;C=49}else{j=h+8|0;C=45}else{if(!k?(j=h+8|0,!(EA(D+48|0,j)|0)):0){C=45;break}b[g+56>>1]=w;b[g+58>>1]=x;c[g+68>>2]=y;if(z){jN(338552,D);jv(D+32|0);jv(D+48|0);h=1}else h=1}while(0);if((C|0)==45)if(W2(f,D+32|0,j)|0?X2(d,D+32|0,j)|0:0){b[g+56>>1]=b[h+14>>1]|0;b[g+58>>1]=b[h+10>>1]|0;c[g+68>>2]=h;if(z){jN(338584,D);jv(D+32|0);jv(j);h=1}else h=1}else{j=1;C=49}if((C|0)==49)if(z?(jN(338616,D),jv(D+32|0),c[D>>2]=A,c[D+4>>2]=B,jN(338640,D),j):0){h=h+8|0;C=W2(f,D+32|0,h)|0;h=(X2(d,D+32|0,h)|0)&1;c[D>>2]=C&1;c[D+4>>2]=h;jN(338672,D);h=0}else h=0;GT(D+148|0);i=D;return h|0}function W2(a,d,f){a=a|0;d=d|0;f=f|0;var g=0,h=0,j=0,k=0,l=0,m=0,n=0,o=0,p=0;p=i;i=i+112|0;m=ok(+((ll(f)|0)<<16>>16))|0;n=e[f>>1]|e[f+2>>1]<<16;l=e[f+4>>1]|e[f+6>>1]<<16;h=n&65535;j=l&65535;a:while(1){f=b[d>>1]|0;k=b[d+4>>1]|0;k=((f<<16>>16<h<<16>>16?h:f)<<16>>16)-((k<<16>>16>j<<16>>16?j:k)<<16>>16)|0;if((k|0)<=(m|0)){f=1;break}c[p>>2]=n&-65536|h&65535;c[p+4>>2]=l&-65536|j&65535;if(f<<16>>16>j<<16>>16){b[p>>1]=j;b[p+4>>1]=(j&65535)+m}else{b[p+4>>1]=h;b[p>>1]=(h&65535)-m}DT(p+8|0,a);WV(p+8|0,p);do{f=XV(p+8|0)|0;if(!f){o=10;break a}g=b[f+8>>1]|0;f=b[f+12>>1]|0}while((lv(g,f,b[d>>1]|0,b[d+4>>1]|0)|0)>=(k|0));GT(p+92|0);h=g<<16>>16<h<<16>>16?g:h;j=f<<16>>16>j<<16>>16?f:j}if((o|0)==10){GT(p+92|0);f=0}i=p;return f|0}function X2(a,b,d){a=a|0;b=b|0;d=d|0;return g0(b,d,a+44|0,a+52|0,c[a+32>>2]|0)|0}function Y2(a,d,e,f,g){a=a|0;d=d|0;e=e|0;f=f|0;g=g|0;var h=0,j=0;h=i;i=i+48|0;do if(c[g>>2]|0){fA(h,g);if(e){e=ZA(h)|0;g=WEa(192)|0;j=c[e+40>>2]|0;b[h+32>>1]=0;b[h+34>>1]=1;XX(g,j,h+32|0);ZX(g,e);c[g+52>>2]=c[e+44>>2];DL(h)|0;if(!((aB(h)|0)<<24>>24))do{ZX(g,ZA(h)|0);DL(h)|0}while((aB(h)|0)<<24>>24==0);U2(a,d,g,f);break}else{if((aB(h)|0)<<24>>24)break;do{e=ZA(h)|0;j=WEa(192)|0;g=c[e+40>>2]|0;b[h+28>>1]=0;b[h+30>>1]=1;XX(j,g,h+28|0);c[j+52>>2]=c[e+44>>2];ZX(j,e);U2(a,d,j,f);DL(h)|0}while((aB(h)|0)<<24>>24==0)}}while(0);i=h;return}function Z2(a,d,f){a=a|0;d=d|0;f=f|0;if((c[d+48>>2]&-5|0)==2){a=e[f+6>>1]|0;b[f+6>>1]=((kl(f)|0)&65535)+a;a=e[f+2>>1]|0;b[f+2>>1]=a-((kl(f)|0)&65535)}else{a=e[f>>1]|0;b[f>>1]=a-((ll(f)|0)&65535);a=e[f+4>>1]|0;b[f+4>>1]=((ll(f)|0)&65535)+a}return 1}function _2(a,b,d){a=a|0;b=b|0;d=d|0;var e=0,f=0,g=0,h=0,j=0,k=0,l=0;h=i;i=i+16|0;if(!((b|0)!=0&(d|0)!=0)){c[h>>2]=338736;c[h+4>>2]=1901;$L(337e3,338704,2,1702e3,h)}if(!((c[b+76>>2]|0)!=0?(c[d+76>>2]|0)!=0:0)){c[h>>2]=338736;c[h+4>>2]=1902;$L(337e3,338768,2,1702e3,h)}e=c[b+52>>2]|0;if((e|0)==1)if((c[d+52>>2]|0)>2)e=0;else g=10;else if((e|0)>2?(c[d+52>>2]|0)==1:0)e=0;else g=10;do if((g|0)==10){e=c[b+48>>2]|0;if(!((e&-5|0)!=2?(c[d+48>>2]&-5|0)!=2:0))g=12;if((g|0)==12?(k=c[b+40>>2]|0,l=c[d+40>>2]|0,f=c[b+36>>2]|0,j=c[d+36>>2]|0,(((k|0)>(l|0)?l:k)-((f|0)<(j|0)?j:f)|0)<1):0){l=c[b+76>>2]|0;if(!((l|0)!=0?(l|0)==(c[l>>2]|0):0)){f=c[d+76>>2]|0;if(!f){e=0;break}if((f|0)!=(c[f>>2]|0)){e=0;break}}if(!(Kk(b+16|0,d+16|0)|0)){e=0;break}}if(!(!((e|0)==1|(e|0)==7)?(l=c[d+48>>2]|0,!((l|0)==1|(l|0)==7)):0))g=20;if((g|0)==20?(j=c[b+28>>2]|0,g=c[d+28>>2]|0,l=c[b+24>>2]|0,k=c[d+24>>2]|0,(((j|0)>(g|0)?g:j)-((l|0)<(k|0)?k:l)|0)<1):0){l=c[b+76>>2]|0;if(!((l|0)!=0?(l|0)==(c[l>>2]|0):0)){e=c[d+76>>2]|0;if(!e){e=0;break}if((e|0)!=(c[e>>2]|0)){e=0;break}}if((!(Kk(b+16|0,d+16|0)|0)?!(vY(b,d,0)|0):0)?!(vY(d,b,0)|0):0){e=0;break}}if(rY(b,d)|0){if((c[b+52>>2]|0)<2?(c[d+52>>2]|0)<2:0){e=1;break}e=X2(a,b+16|0,d+16|0)|0}else e=0}while(0);i=h;return e|0}function $2(){c[84250]=1701984;c[84252]=389976;c[84254]=390024;c[84256]=390056;c[84258]=390112;c[84260]=390152;c[84262]=390184;c[84264]=390240;c[84266]=390280;c[84268]=390328;c[84270]=390376;c[84272]=390400;c[84274]=390432;c[84276]=390504;c[84278]=390552;c[84280]=390592;c[84282]=390640;rJ(337136,0,337160,337200,0,kM()|0);Wk(337224,0,337240,337280,0,kM()|0);return}function a3(a){a=a|0;var b=0,d=0.0,f=0.0,j=0.0,l=0,m=0,n=0,o=0;b=i;i=i+48|0;n=e[a+8>>1]|e[a+10>>1]<<16;m=e[a+12>>1]|e[a+14>>1]<<16;o=UGa(n|0,m|0,48)|0;l=F;m=_Ga(m|0,0,16)|0;l=_Ga(o|0,l|0,16)|0;j=+g[a+72>>2];f=+g[a+76>>2];o=gla(c[a+4>>2]|0)|0;d=+(o|0)*2.0/+(hla(c[a+4>>2]|0)|0);c[b>>2]=n<<16>>16;c[b+4>>2]=n>>16;c[b+8>>2]=m>>16;c[b+12>>2]=l>>16;h[k>>3]=j;c[b+16>>2]=c[k>>2];c[b+20>>2]=c[k+4>>2];h[k>>3]=f;c[b+24>>2]=c[k>>2];c[b+28>>2]=c[k+4>>2];h[k>>3]=d;c[b+32>>2]=c[k>>2];c[b+36>>2]=c[k+4>>2];jN(339368,b);i=b;return}function b3(a,b){a=a|0;b=b|0;var d=0,f=0,g=0,h=0,j=0,k=0,l=0,m=0;m=i;i=i+48|0;k=e[b+8>>1]|e[b+10>>1]<<16;j=e[b+12>>1]|e[b+14>>1]<<16;l=UGa(k|0,j|0,48)|0;d=F;lV(a,k<<16>>16,k>>16,m+40|0,m+36|0);j=_Ga(j|0,0,16)|0;d=_Ga(l|0,d|0,16)|0;lV(a,j>>16,d>>16,m+32|0,m+28|0);d=c[m+36>>2]|0;j=c[m+28>>2]|0;if((d|0)<=(j|0)){g=c[a+8>>2]|0;l=ba(g,d)|0;h=c[m+32>>2]|0;k=c[m+40>>2]|0;while(1){if((k|0)>(h|0))f=g;else{f=k;while(1){fA(m,(c[a+28>>2]|0)+(f+l<<2)|0);gA(m);if(!((hA(m)|0)<<24>>24))do{if((iA(m)|0)==(b|0))ZA(m)|0;DL(m)|0}while((hA(m)|0)<<24>>24==0);if((f|0)<(h|0))f=f+1|0;else break}f=c[a+8>>2]|0}if((d|0)<(j|0)){g=f;l=f+l|0;d=d+1|0}else break}}i=m;return}function c3(a,b){a=+a;b=b|0;if(a==0.0)b=(b|0)==5;else b=(b|0)!=5&((b|0)!=12&(b|0)>3);return b|0}function d3(a,b){a=+a;b=b|0;if(a==0.0)b=(b|0)!=5&((b|0)!=12&(b|0)>3);else b=(b|0)==5;return b|0}function e3(b,d,e,f,g,h){b=b|0;d=d|0;e=e|0;f=f|0;g=g|0;h=h|0;var j=0,k=0,l=0.0;k=i;i=i+112|0;fA(k+84|0,f);fA(k+56|0,g);fA(k+28|0,h);pk(k,b);rk(k);if(!(sk(k)|0))do{h=tk(k)|0;l=+((ll(h+8|0)|0)<<16>>16);l=l/+((kl(h+8|0)|0)<<16>>16);b=(1.0/l>l?1.0/l:l)<=2.0;g=(a[h+112>>0]|0)==0;if(!(a[h+113>>0]|0))if(!g){c[e>>2]=(c[e>>2]|0)+1;if(b)CT(k+56|0,h)}else j=9;else if(g){c[d>>2]=(c[d>>2]|0)+1;if(b)CT(k+84|0,h)}else j=9;if((j|0)==9?(j=0,b):0)CT(k+28|0,h);OL(k)|0}while(!(sk(k)|0));i=k;return}function f3(a,b,d,e){a=a|0;b=b|0;d=d|0;e=e|0;var f=0;f=c[(c[a>>2]|0)+4>>2]|0;c[a+16>>2]=((e-d<<1)+-1+f|0)/(f|0)|0;c[a+20>>2]=0;JT(a,b,e);return}function g3(b,d){b=b|0;d=d|0;var e=0,f=0;f=i;i=i+16|0;e=d?-1:1;while(1){if(!((hA(b+56|0)|0)<<24>>24)){LT(b);if(!(a[b+44>>0]|0)){e=14;break}MT(f,b+84|0,c[b+48>>2]|0);if(!(c[f>>2]|0)){e=12;break}else continue}d=c[b+20>>2]|0;c[b+20>>2]=d+1;if((d|0)>=(c[b+16>>2]|0)){d=(c[b+36>>2]|0)+e|0;c[b+36>>2]=d;c[b+20>>2]=0;if((d|0)<0){e=6;break}if((d|0)<(c[(c[b>>2]|0)+8>>2]|0))d=0;else{e=6;break}}else d=d+1|0;d=(c[b+8>>2]|0)-d|0;c[b+40>>2]=d;if((d|0)<=-1)continue;if((d|0)>=(c[(c[b>>2]|0)+12>>2]|0))continue;KT(b)}if((e|0)==6){c[b+48>>2]=0;c[b+52>>2]=0;d=0}else if((e|0)==12)if(!(a[b+44>>0]|0))e=14;else{NT(f+8|0,b+84|0,b+48|0);e=14}if((e|0)==14)d=c[b+48>>2]|0;i=f;return d|0}function h3(a,b,d,e){a=a|0;b=b|0;d=d|0;e=e|0;c[a+12>>2]=e;c[a+16>>2]=0;c[a+20>>2]=0;c[a+24>>2]=3;JT(a,b,d);return}function i3(d){d=d|0;var e=0,f=0,g=0,h=0;h=i;i=i+32|0;while(1){if(!((hA(d+56|0)|0)<<24>>24)){LT(d);if(!(a[d+44>>0]|0)){f=16;break}MT(h,d+84|0,c[d+48>>2]|0);if(!(c[h>>2]|0)){f=14;break}else continue}g=(c[d+20>>2]|0)+1|0;c[d+20>>2]=g;f=c[d+16>>2]|0;e=c[d+24>>2]|0;if((g|0)>=(f|0)){c[d+24>>2]=e+1;c[d+20>>2]=0;if((e|0)>2){c[d+16>>2]=f+1;if((f|0)>=(c[d+12>>2]|0)){f=6;break}c[d+24>>2]=0;e=0}else e=e+1|0}kda(h+20|0,e);f=(c[d+16>>2]|0)-(c[d+20>>2]|0)<<16>>16;b[h+20>>1]=ba(f,b[h+20>>1]|0)|0;b[h+22>>1]=ba(b[h+22>>1]|0,f)|0;kda(h+16|0,(c[d+24>>2]|0)+1|0);f=c[d+20>>2]<<16>>16;e=(ba(b[h+16>>1]|0,f)|0)&65535;eB(h+20|0,e,(ba(b[h+18>>1]|0,f)|0)&65535);f=(b[h+20>>1]|0)+(c[d+4>>2]|0)|0;c[d+36>>2]=f;e=(b[h+22>>1]|0)+(c[d+8>>2]|0)|0;c[d+40>>2]=e;if((f|0)<=-1)continue;g=c[d>>2]|0;if(!((e|0)>-1?(f|0)<(c[g+8>>2]|0):0))continue;if((e|0)>=(c[g+12>>2]|0))continue;KT(d)}if((f|0)==6){c[d+48>>2]=0;c[d+52>>2]=0;e=0}else if((f|0)==14)if(!(a[d+44>>0]|0))f=16;else{NT(h+8|0,d+84|0,d+48|0);f=16}if((f|0)==16)e=c[d+48>>2]|0;i=h;return e|0}function j3(a,d,f,g,h,j,k){a=a|0;d=d|0;f=f|0;g=g|0;h=h|0;j=j|0;k=k|0;var l=0.0,m=0.0,n=0,o=0,p=0,q=0,r=0;q=i;i=i+16|0;c[j>>2]=lv(b[a>>1]|0,b[a+4>>1]|0,b[d>>1]|0,b[d+4>>1]|0)|0;c[k>>2]=HA(b[a+2>>1]|0,b[a+6>>1]|0,b[d+2>>1]|0,b[d+6>>1]|0)|0;r=e[d+4>>1]|e[d+6>>1]<<16;c[q>>2]=e[d>>1]|e[d+2>>1]<<16;c[q+4>>2]=r;bja(q,a);if(f){r=c[k>>2]|0;c[q+8>>2]=c[j>>2];c[q+12>>2]=r;jN(339336,q+8|0);jv(q)}if((((c[j>>2]|0)<=(h|0)?(c[k>>2]|0)<=(h|0):0)?(n=kl(q)|0,(n<<16>>16|0)<=(g|0)):0)?(o=ll(q)|0,(o<<16>>16|0)<=(g|0)):0){r=kl(a)|0;l=+(r<<16>>16)/+((ll(a)|0)<<16>>16);if(l<1.0)m=1.0/l;else m=l;if(+(n<<16>>16)/+(o<<16>>16)<1.0)l=1.0/(+(n<<16>>16)/+(o<<16>>16));else l=+(n<<16>>16)/+(o<<16>>16);if(l<=m*1.0625)a=1;else p=12}else p=12;if((p|0)==12)a=0;i=q;return a|0}function k3(a,b){a=a|0;b=b|0;var d=0;t3(a,b);d=c[a+92>>2]|0;if(d)t3(d,b);d=c[a+96>>2]|0;if(d)t3(d,b);d=c[a+100>>2]|0;if(d)t3(d,b);d=c[a+104>>2]|0;if(d)t3(d,b);return}function l3(b,d){b=b|0;d=d|0;var e=0;e=c[b+(d<<2)+92>>2]|0;do if((e|0)!=0?(c[e+84>>2]|0)==0:0){if((a[e+112>>0]|0)!=0?(a[e+113>>0]|0)==0:0){e=0;break}e=(c[e+((d^2)<<2)+92>>2]|0)==(b|0)?e:0}else e=0;while(0);return e|0}function m3(b,d){b=b|0;d=d|0;var e=0;e=c[b+(d<<2)+92>>2]|0;do if((e|0)!=0?(c[e+84>>2]|0)==0:0){if((a[e+113>>0]|0)!=0?(a[e+112>>0]|0)==0:0){e=0;break}e=(c[e+((d^2)<<2)+92>>2]|0)==(b|0)?e:0}else e=0;while(0);return e|0}function n3(a){a=a|0;return}function o3(a){a=a|0;YEa(a);return}function p3(a,b,e){a=a|0;b=b|0;e=e|0;var f=0,g=0;f=d[a+8>>0]|d[a+9>>0]<<8|d[a+10>>0]<<16|d[a+11>>0]<<24;g=d[a+12>>0]|d[a+13>>0]<<8|d[a+14>>0]<<16|d[a+15>>0]<<24;a=(c[a+4>>2]|0)+(g>>1)|0;if(g&1)f=c[(c[a>>2]|0)+f>>2]|0;return Vc[f&63](a,b,e)|0}function q3(a){a=a|0;return}function r3(a){a=a|0;YEa(a);return}function s3(a,b,e){a=a|0;b=b|0;e=e|0;var f=0,g=0;f=d[a+8>>0]|d[a+9>>0]<<8|d[a+10>>0]<<16|d[a+11>>0]<<24;g=d[a+12>>0]|d[a+13>>0]<<8|d[a+14>>0]<<16|d[a+15>>0]<<24;a=(c[a+4>>2]|0)+(g>>1)|0;if(g&1)f=c[(c[a>>2]|0)+f>>2]|0;return Vc[f&63](a,b,e)|0}function t3(a,b){a=a|0;b=b|0;var d=0,e=0;e=0;do{d=c[a+(e<<2)+92>>2]|0;if(d)FL(b,38,d);e=e+1|0}while((e|0)!=