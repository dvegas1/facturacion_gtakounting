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
						    window.location.href = 'index.php?page=nueva_compra&tipo=factura';

						    //location.reload();


						  }

						  function edit_table_ocr(arrayProveedores) {


						    var testimonialElements = $(".sugerencia");

						    for (var i = 0; i < arrayProveedores.length; i++) {
						      testimonialElements.eq(i).val(arrayProveedores[i]);
						      var element = testimonialElements.eq(i).text(); //do something with element }
						    }
						    eliminar_td_ocr(arrayProveedores);
						  }


						  function eliminar_td_ocr(arrayProveedores) {


						    var testimonialElements = $(".sugerencia");

						    for (var i = 0; i < arrayProveedores.length; i++) {

						      $("#lista_productos").parents('tr').eq(i).remove();
						      //var element = testimonialElements.eq(i).text(); //do something with element }

						    }


						    //	$('#lista_productos').on('click', '.button_eliminar_producto', function(){
						    //		$(#lista_productos).parents('tr').eq(0).remove();



						  }



						  function agrefa_fila_table_ocr() {

						    var tbody = $('#lista_productos tbody');
						    var fila_contenido = tbody.find('tr').first().html();

						    var fila_nueva = $('<tr class="tr_ocr"></tr>');

						    fila_nueva.append(fila_contenido);
						    tbody.append(fila_nueva);

						  }

						  function wordCount() {
						    textoArea = document.getElementById("area").value;
						    numeroCaracteres = textoArea.length;
						    inicioBlanco = /^ /
						    finBlanco = / $/
						    variosBlancos = /[ ]+/g
						    textoArea = textoArea.replace(inicioBlanco, "");
						    textoArea = textoArea.replace(finBlanco, "");
						    textoArea = textoArea.replace(variosBlancos, " ");
						    textoAreaDividido = textoArea.split(" ");
						    numeroPalabras = textoAreaDividido.length;
						    tC = (numeroCaracteres == 1) ? " carácter" : " caracteres";
						    tP = (numeroPalabras == 1) ? " palabra" : " palabras";
						    alert(numeroCaracteres + tC + "\n" + numeroPalabras + tP);
						  }

							function eliminarFilas_ocr_bloque(tipo) {

								switch (tipo) {
							    case 2:
									$('#lista_productos tbody tr').not(':first').remove();
									break;


									}


							}


						  function buscar_proveedor_main(proveedor) {
						    $.ajax({

						      type: 'POST',
						      url: tpv_url,
						      data: 'buscar_proveedor_ocr=' + proveedor,
						      success: function(datos) {

									console.log("Resultados ajax (buscar_proveedor_main) " + datos);

						        //	 var content = JSON.parse(result);
						        if (datos == "BLOQUE") {
						          notificacion("error", "topCenter", "Esta factura no puede ser procesada.");
						          busqueda_proveedor_finalizada();
						          return;
						        }

						        document.f_nueva_compra.nuevo_proveedor.value = '';
						        document.f_nueva_compra.nuevo_cifnif.value = '';



						        if (typeof datos[0].nombre != "undefined") {

						          console.log("Proveedor encontrado " + datos[0].nombre);

						          localStorage.setItem("ocr_result", datos[0].nombre);

						          $("#contenedorprincipal").css("z-index", "1040");
						          $("#adjust").attr('disabled', 'enabled');
						          $("#contenedorprincipal").css('display', "none");
						          $("#modal_proveedor").css('display', "block");
						          $("#modal_proveedor").show("slow");

						          $("#ac_proveedor").focus();
						          $("#ac_proveedor").val(datos[0].nombre);
						          notificacion("success", "topCenter", " Proveedor encontrado con exito. ");

						        } else {

											eliminarFilas_ocr_bloque(2);


						          datos_final = datos.split(",");

						          busqueda_proveedor_finalizada();



						          notificacion("warning", "topCenter", "Proveedor no encontrado");

						          var proveedor_valor = [];
						          var result_proveedores = [];

						          for (var i = 0; i < datos_final.length; i++) {
						            if (normalize(datos_final[i]) != "") {
						              proveedor_valor[i] = normalize(datos_final[i]);
						              console.log("dato[ " + normalize(datos_final[i]) + " ]");
						              agrefa_fila_table_ocr();

						            }

						          }
						          edit_table_ocr(proveedor_valor);



						        }


						      }






						    });

						  }

						  var normalize = (function() {
						    var from = "ÃÀÁÄÂÈÉËÊÌÍÏÎÒÓÖÔÙÚÜÛãàáäâèéëêìíïîòóöôùúüûÑñÇç'/?",
						      to = "AAAAAEEEEIIIIOOOOUUUUaaaaaeeeeiiiioooouuuunncc",
						      mapping = {};

						    for (var i = 0, j = from.length; i < j; i++)
						      mapping[from.charAt(i)] = to.charAt(i);

						    return function(str) {
						      var ret = [];
						      for (var i = 0, j = str.length; i < j; i++) {
						        var c = str.charAt(i);
						        if (mapping.hasOwnProperty(str.charAt(i)))
						          ret.push(mapping[c]);
						        else
						          ret.push(c);
						      }
						      return getCleanedString(ret.join(''));
						    }

						  })();

							function getCleanedString(cadena){
   // Definimos los caracteres que queremos eliminar
   var specialChars = "!@#$^&%*()+=-[]\/{}|:<>?,.";

   // Los eliminamos todos
   for (var i = 0; i < specialChars.length; i++) {
       cadena= cadena.replace(new RegExp("\\" + specialChars[i], 'gi'), '');
   }

   // Lo queremos devolver limpio en minusculas
   cadena = cadena.toLowerCase();

   // Quitamos espacios y los sustituimos por _ porque nos gusta mas asi
   cadena = cadena.replace(/ /g,"_");

   // Quitamos acentos y "ñ". Fijate en que va sin comillas el primer parametro
   cadena = cadena.replace(/á/gi,"a");
   cadena = cadena.replace(/é/gi,"e");
   cadena = cadena.replace(/í/gi,"i");
   cadena = cadena.replace(/ó/gi,"o");
   cadena = cadena.replace(/ú/gi,"u");
   cadena = cadena.replace(/ñ/gi,"n");
	 cadena = cadena.replace(/['"]+/g, '');
	 cadena = cadena.replace(/['“]+/g, '');
	 cadena = cadena.replace(/[',]+/g, '');
	 cadena = cadena.replace(/['”]+/g, '');
	 cadena = cadena.replace(/['‘]+/g, '');
	 cadena = cadena.replace(/['']+/g, '');
	 cadena = cadena.replace(/['»]+/g, '');
	 cadena = cadena.replace(/['(]+/g, '');
	 cadena = cadena.replace(/[')]+/g, '');
	 cadena = cadena.replace(/['-]+/g, '');
	 cadena = cadena.replace(/[' ']+/g, '');
	 cadena = cadena.replace(/['_]+/g, '');
	 cadena = cadena.replace(/['€]+/g, '');
	 cadena = cadena.replace(/\//g, '');
	 cadena = cadena.replace(/['—]+/g, '');
	 cadena = cadena.replace(/[//g", "''.*+?^${%}(#)|[\\\]]/g, '');
	 cadena = cadena.replace("\\", '');
	 cadena = cadena.replace(",", '');
	 var regex = /\\/g;
   cadena = cadena.replace(regex, "\\\\");
	 cadena = cadena.replace(/\/$/, "");
	 cadena.trim().replace(/&nbsp;/g, '').replace(/<[^\/>][^>]*><\/[^>]+>/g, "");

   return replaceAllBackSlash(cadena);
}

function replaceAllBackSlash(targetStr){
    var index=targetStr.indexOf("\\");
    while(index >= 0){
        targetStr=targetStr.replace("\\","");
        index=targetStr.indexOf("\\");
    }
    return targetStr;
}


						  function dividirCadena_proveedor(cadenaADividir, separador) {

						    console.log("Resultado dividir cadena main");

						    var datos_proveedor_b = [];
						    var arrayDeCadenas = cadenaADividir.split(separador);
						    var arrayDeCadenas_ESPACIO = cadenaADividir.split(" ");
						    var arrayDeCadenas_coma = cadenaADividir.split(",");
						    var arrayDeCadenas_saltos = cadenaADividir.split("\n");
						    var cadenafinal_ocr = arrayDeCadenas.toString().split("\n");
						    var cadenbuscar_ocr = cadenafinal_ocr.toString().split(",");

						    var datos = [];

						    if (cadenaADividir.length > 0) {

						      for (var a = 0; a < cadenbuscar_ocr.length; a++) {

						        if (cadenbuscar_ocr[a] != "" && cadenbuscar_ocr[a].length > 3 && cadenbuscar_ocr[a] != " ") {

						          datos_proveedor_b[a] = normalize(cadenbuscar_ocr[a]);
						        }
						      }

						      buscar_proveedor_main(datos_proveedor_b);

						      console.log(datos_proveedor_b.length + ", Datos a buscar. " + datos_proveedor_b);
						    }
						  }


						  function notificacion(type, layout, texto) {

						    var notes = []
						    notes[type] = texto
						    /*notes['error'] = 'Change a few things up and try submitting again.'
						    notes['success'] = 'Proveedor encontrado con exito.'
						    notes['information'] = 'This alert needs your attention, but it\'s not super important.'
						    notes['warning'] = '<strong>Warning!</strong> <br /> Best check yo self, you\'re not looking too good.'*/



						    //	var layout = "topCenter"
						    //	var type = "success"

						    layout = layout
						    type = type

						    //	var types = ['alert', 'error', 'success', 'information', 'warning']
						    /*	if (type === 'random')
						    		type = types[Math.floor(Math.random() * types.length)]*/

						    if (layout === 'inline') {
						      new Noty({
						        text: notes[type],
						        type: type,
						        timeout: 3000,
						        container: '#custom_container'
						      }).show()
						      return false
						    }

						    new Noty({
						      text: notes[type],
						      type: type,
						      timeout: 3000,
						      layout: layout
						    }).show()

						  }


						  $('#config').click(function() {

						    $("#config_ocr").css("display", "block");

						  });

						  $('#close_b_proveedor').click(function() {

						    $('#modal_proveedor').hide();


						  });

						  $('#btn_sistema_prov').click(function() {

						    $('#modal_proveedor').show();

						  });







						  $('#btnabrircamara').click(function() {


						    step1();
						    changeStep(1);
						  });





						  $('#video').click(function() {
						    step2();
						    changeStep(2);
						  });


						  function carga_ocr_modal() {


						    $("#contenedorprincipal").show("slow");
						    $('html, body').animate({
						      scrollTop: $("#contenedorprincipal").offset().top
						    }, 1000);

						  }

						  /* boton cargar */



						  $('#cargar').click(function() {



						    inicializacion_ocr();



						  });



						  $('#cargar_1').click(function() {



						    ab_ocr();



						  });


						  $('.closebtn').click(function() {

						    // $(".container").css("display", "block");

						    $("#contenedorprincipal").hide("slow");
						    $("#contenedorprincipal").css("display", "none");
						    $(".step1").hide("slow");


						  });

						  var reader;
						  var progress = document.querySelector('.percent');

						  function abortRead() {
						    reader.abort();
						  }

						  function errorHandler(evt) {
						    switch (evt.target.error.code) {
						      case evt.target.error.NOT_FOUND_ERR:
						        alert('Archivo no encontrado!');
						        break;
						      case evt.target.error.NOT_READABLE_ERR:
						        alert('El archivo no es legible');
						        break;
						      case evt.target.error.ABORT_ERR:
						        break; // noop
						      default:
						        alert('Se produjo un error al leer este archivo.');
						    };
						  }

						  function updateProgress(evt) {
						    // evt is an ProgressEvent.
						    if (evt.lengthComputable) {
						      var percentLoaded = Math.round((evt.loaded / evt.total) * 100);
						      // Increase the progress bar length.
						      if (percentLoaded < 100) {
						        progress.style.width = percentLoaded + '%';
						        progress.textContent = percentLoaded + '%';
						      }
						    }
						  }



						  var videoimagen = document.querySelector('video');

						  var video = document.querySelector('video');

						  var pictureWidth = 640;
						  var pictureHeight = 480;

						  var fxCanvas = null;
						  var texture = null;


						  'use strict';

						  var videoSelect = document.querySelector('select#videoSource');

						  navigator.mediaDevices.enumerateDevices()
						    .then(gotDevices).then(getStream).catch(handleError);

						  //audioSelect.onchange = getStream;
						  videoSelect.onchange = getStream;

						  function gotDevices(deviceInfos) {
						    for (var i = 0; i !== deviceInfos.length; ++i) {
						      var deviceInfo = deviceInfos[i];
						      var option = document.createElement('option');
						      option.value = deviceInfo.deviceId;

						      if (deviceInfo.kind === 'videoinput') {
						        option.text = deviceInfo.label || 'camera ' +
						          (videoSelect.length + 1);
						        videoSelect.appendChild(option);
						      } else {
						        console.log('Found one other kind of source/device: ', deviceInfo);
						      }
						    }
						  }

						  function getStream() {
						    if (window.stream) {
						      window.stream.getTracks().forEach(function(track) {
						        track.stop();
						      });
						    }

						    var constraints = {

						      video: {
						        deviceId: {
						          exact: videoSelect.value
						        }
						      }
						    };

						    navigator.mediaDevices.getUserMedia(constraints).
						    then(gotStream).catch(handleError);
						  }

						  function gotStream(stream) {
						    window.stream = stream; // make stream available to console
						    video.srcObject = stream;
						  }

						  function handleError(error) {
						    console.log('Error: ', error);
						  }


						  function checkRequirements() {
						    var deferred = new $.Deferred();

						    //Check if getUserMedia is available
						    if (!Modernizr.getusermedia) {
						      deferred.reject('Su navegador no es compatible con getUserMedia (de acuerdo con Modernizr).');
						    }

						    //Check if WebGL is available
						    if (Modernizr.webgl) {
						      try {
						        //setup glfx.js
						        fxCanvas = fx.canvas();
						      } catch (e) {
						        deferred.reject('Lo sentimos, no se pudo inicializar glfx.js. ¿Problemas con WebGL?');
						      }
						    } else {
						      deferred.reject('Su navegador no es compatible con WebGL (de acuerdo con Modernizr).');
						    }

						    deferred.resolve();

						    return deferred.promise();
						  }

						  /*  function searchForRearCamera() {
								var deferred = new $.Deferred();

								//MediaStreamTrack.getSources seams to be supported only by Chrome
								if (MediaStreamTrack && MediaStreamTrack.getSources) {
									MediaStreamTrack.getSources(function (sources) {
										var rearCameraIds = sources.filter(function (source) {
											return (source.kind === 'video' && source.facing === 'environment');
										}).map(function (source) {
											return source.id;
										});

										if (rearCameraIds.length) {
											deferred.resolve(rearCameraIds[0]);
										} else {
											deferred.resolve(null);
										}
									});
								} else {
									deferred.resolve(null);
								}
								console.log("deferred.promise: " + deferred.promise());
								return deferred.promise();
							}
						*/



						  function setupVideo(rearCameraId) {

						    if (Modernizr.adownload) {
						      console.log("SUPPORT");
						    } else {
						      console.log("NO SUPPORT");
						    }


						    var deferred = new $.Deferred();
						    var getUserMedia = Modernizr.prefixed('getUserMedia', navigator);
						    var videoSettings = {
						      video: {
						        optional: [{
						            width: {
						              min: pictureWidth
						            }
						          },
						          {
						            height: {
						              min: pictureHeight
						            }
						          }
						        ]
						      }
						    };

						    //if rear camera is available - use it
						    if (rearCameraId) {
						      videoSettings.video.optional.push({
						        sourceId: rearCameraId
						      });
						    }

						    getUserMedia(videoSettings, function(stream) {
						      //Setup the video stream
						      video.src = window.URL.createObjectURL(stream);

						      window.stream = stream;

						      video.addEventListener("loadedmetadata", function(e) {
						        //get video width and height as it might be different than we requested
						        pictureWidth = this.videoWidth;
						        pictureHeight = this.videoHeight;

						        if (!pictureWidth && !pictureHeight) {
						          //firefox fails to deliver info about video size on time (issue #926753), we have to wait
						          var waitingForSize = setInterval(function() {
						            if (video.videoWidth && video.videoHeight) {
						              pictureWidth = video.videoWidth;
						              pictureHeight = video.videoHeight;

						              clearInterval(waitingForSize);
						              deferred.resolve();
						            }
						          }, 100);
						        } else {
						          deferred.resolve();
						        }
						      }, false);
						    }, function() {
						      deferred.reject('No hay acceso a su cámara, ¿lo ha negado?');
						    });

						    return deferred.promise();
						  }


						  function step1() {
						    checkRequirements()
						      .then(setupVideo)
						      .done(function() {
						        //Enable the 'take picture' button
						        $('#takePicture').removeAttr('disabled');
						        //Hide the 'enable the camera' info
						        $('#step1 figure').removeClass('not-ready');

						      })
						      .fail(function(error) {
						        showError(error);
						      });
						  }

						  function drawImage(imageObj) {
						    var canvas = document.getElementById('myCanvas');
						    var context = canvas.getContext('2d');
						    var x = 69;
						    var y = 50;

						    context.drawImage(imageObj, x, y);

						    var imageData = context.getImageData(x, y, imageObj.width, imageObj.height);
						    var data = imageData.data;

						    for (var i = 0; i < data.length; i += 4) {
						      var brightness = 0.34 * data[i] + 0.5 * data[i + 1] + 0.16 * data[i + 2];
						      // red
						      data[i] = brightness;
						      // green
						      data[i + 1] = brightness;
						      // blue
						      data[i + 2] = brightness;
						    }

						    // overwrite original image
						    context.putImageData(imageData, x, y);
						  }


						  function step2() {


						    //$("#step2").show();

						    //	$("#step1").hide();
						    $("#step3").hide();


						    var canvas = document.querySelector('#step2 canvas');
						    var img = document.querySelector('#step2 img');



						    //setup canvas
						    canvas.width = pictureWidth;
						    canvas.height = pictureHeight;

						    var ctx = canvas.getContext('2d');

						    //draw picture from video on canvas
						    ctx.drawImage(video, 0, 0);

						    //modify the picture using glfx.js filters
						    texture = fxCanvas.texture(canvas);
						    fxCanvas.draw(texture)
						      .hueSaturation(0, 0) //grayscale
						      .unsharpMask(0, 0)
						      .brightnessContrast(10.0, 0.0)
						      .update();




						    window.texture = texture;
						    window.fxCanvas = fxCanvas;

						    $(img)
						      //setup the crop utility
						      .one('load', function() {
						        if (!$(img).data().Jcrop) {
						          $(img).Jcrop({
						            onSelect: function() {
						              //Enable the 'done' button
						              $('#adjust').removeAttr('disabled');
						              console.log("boton habilitado");


						            }
						          });
						        } else {
						          //update crop tool (it creates copies of <img> that we have to update manually)
						          // actualizar herramienta de recorte (crea copias de <img> que tenemos que actualizar manualmente)
						          //   $('.jcrop-holder img').attr('src', fxCanvas.toDataURL());

						        }
						      })
						      // muestra el resultado de glfx.js
						      .attr('src', fxCanvas.toDataURL());

						  }

						  function step3() {



						    var canvas = document.querySelector('#step3 canvas');
						    var step2Image = document.querySelector('#step2 img');
						    var cropData = $(step2Image).data().Jcrop.tellSelect();

						    var scale = step2Image.width / $(step2Image).width();

						    //draw cropped image on the canvas
						    canvas.width = cropData.w * scale;
						    canvas.height = cropData.h * scale;

						    var ctx = canvas.getContext('2d');
						    ctx.drawImage(
						      step2Image,
						      cropData.x * scale,
						      cropData.y * scale,
						      cropData.w * scale,
						      cropData.h * scale,
						      0,
						      0,
						      cropData.w * scale,
						      cropData.h * scale);

						    recognizeFile(ctx);
						    //use ocrad.js to extract text from the canvas
						    var resultText = OCRAD(ctx);
						    resultText = resultText.trim();

						    //show the result
						    $('blockquote p').html('&bdquo;' + resultText + '&ldquo;');
						    $('blockquote footer').text('(' + resultText.length + ' characters)')

						    $("#step1").hide();
						    $("#step2").hide();

						    $("#step3").show();

						  }






						  /*********************************
						   * UI Stuff
						   *********************************/

						  //start step1 immediately
						  step1();
						  $('.help').popover();

						  function changeStep(step) {
						    if (step === 1) {
						      video.play();
						    } else {
						      video.pause();
						    }

						    $('.step' + step);
						    $('.nav li.active').removeClass('active');
						    $('.nav li:eq(' + (step - 1) + ')').removeClass('disabled').addClass('active');
						  }

						  function showError(text) {
						    $('.alert').show().find('span').text(text);
						  }

						  //handle brightness/contrast change
						  $('#brightness, #contrast').on('change', function() {
						    var brightness = $('#brightness').val() / 100;
						    var contrast = $('#contrast').val() / 100;
						    var img = document.querySelector('#step2 img');

						    fxCanvas.draw(texture)
						      .hueSaturation(-1, -1)
						      .unsharpMask(20, 2)
						      .brightnessContrast(brightness, contrast)
						      .update();

						    img.src = fxCanvas.toDataURL();





						    // actualizar la herramienta de recorte (crea copias de <img> que tenemos que actualizar manualmente)

						    //  $('.jcrop-holder img').attr('src', fxCanvas.toDataURL());





						  });



						  $('#video').click(function() {
						    step2();
						    changeStep(2);
						  });





						  $('#close_modal_seleccion').click(function() {



						    $(".modal_proveedor_seleccion").css("display", "none");
						  });



						  $('#btn_modal_clasic').click(function() {


						    if ($(".modal_proveedor_seleccion").length > 0) {

						      $(".modal_proveedor_seleccion").css("display", "block");
						    }



						  });




						  function cargar_ocr() {


						    $("#contenedorprincipal_container").css("display", "table");
						    //   $('#modal_proveedor').show();

						    $('.contenedorprincipal_container').css("position:", "absolute");
						    //  $(".modal_proveedor_seleccion").hide();



						  }

						  function cerrar_ocr() {

						    $("#contenedorprincipal_container_id").hide();
						    $("#contenedorprincipal").hide();
						    $('.contenedorprincipal_container').css("position:", "absolute");

						    $("#ac_proveedor").focus();


						  }




						  function open_modal_ocr() {

						    $("#contenedorprincipal_container_id").show();
						    $("#contenedorprincipal").show();
						    $("#modal_proveedor").show();
						    $('.contenedorprincipal_container').css("position:", "absolute");
						    //					procesar_operacion_2();



						  }



						  /* TESERA */

						  function progressUpdate(packet) {

						    var log = document.getElementById('log');

						    if (log.firstChild && log.firstChild.status === packet.status) {
						      if ('progress' in packet) {
						        var progress = log.firstChild.querySelector('progress')
						        progress.value = packet.progress
						      }
						    } else {
						      var line = document.createElement('div');
						      line.status = packet.status;
						      var status = document.createElement('div')
						      status.className = 'status progress-bar'
						      status.appendChild(document.createTextNode(packet.status))

						      // Texto antes del progress  line.appendChild(status)

						      if ('progress' in packet) {

						        // var progress = document.getElementsByClassName('progress.progress-bar');
						        var progress = document.createElement('progress progress-bar')


						        progress.value = packet.progress

						        progress.max = 1
						        line.appendChild(progress)
						      }


						      if (packet.status == 'done') {
						        var pre = document.createElement('pre')
						        pre.appendChild(document.createTextNode(packet.data.text))
						        line.innerHTML = ''
						        line.appendChild(pre)

						        console.log(packet.data.text);

						      }

						      log.insertBefore(line, log.firstChild);


						    }


						  }

						  var result = "";

						  function recognizeFile(file) {

						    var job1 = Tesseract.recognize(file);

						    job1.progress(message => console.log(message));


						  }


						  $('#go-back').click(function() {
						    step2();
						    changeStep(2);
						  });

						  $('#start-over').click(function() {
						    step1();
						    changeStep(1);
						  });

						  $('.nav').on('click', 'a', function() {
						    if (!$(this).parent().is('.disabled')) {
						      var step = $(this).data('step');
						      changeStep(step);
						    }

						    return false;
						  });


						})();
