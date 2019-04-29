      var numlineas = 0;
      var fs_nf0 = 2;
      var fs_nf0_art = 2;
      var all_impuestos = [];
      var default_impuesto = '';
      var all_series = [];
      var proveedor = false;
      var nueva_compra_url = '';
      var precio_compra = 'coste';
      var fin_busqueda1 = true;
      var fin_busqueda2 = true;
      var siniva = false;
      var irpf = 0;
      var tiene_recargo = false;
      var accion_pagina = 0;



      function usar_proveedor(codproveedor) {
        if (nueva_compra_url !== '') {
          $.getJSON(nueva_compra_url, 'datosproveedor=' + codproveedor, function(json) {
            proveedor = json;
            document.f_buscar_articulos.codproveedor.value = proveedor.codproveedor;
            if (proveedor.regimeniva == 'Exento') {
              irpf = 0;
              for (var j = 0; j < numlineas; j++) {
                if ($("#linea_" + j).length > 0) {
                  $("#iva_" + j).val(0);
                  $("#recargo_" + j).val(0);
                }
              }
            }
            recalcular();
          });
        }
      }

      function abrir_ocr() {

        $(".container").show("slow");

        $(".step1").show("slow");

        $("#contenedorprincipal_container_id").show("slow");
        $("#contenedorprincipal_container_id").css("display", "block");

        $("#contenedorprincipal").show("slow");

        $("#step1").css("display", "block");
        $("#step1").show("slow");

        $("#contenedorprincipal").css("display", "table");

      }

      function handleFileSelect_compra(evt) {

        localStorage.removeItem("imgData");
        localStorage.removeItem("ocr_result");
        localStorage.removeItem("primera_busqueda");

        $("#figure_step2").show();
        $("#panelherramiente").show();
        $("#panelherramiente").css("display", "inline-table");
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

        busqueda_proveedor_finalizada();
        step2imagen_compra();

      }

      function inicializacion_ocr_compras() {



        var spte1_validacion = $("#contenedor_eleccion_sistema").length;

        if (localStorage.getItem("imgData") != null) {
          {
            console.log("IMAGEN CACHE: " + localStorage.getItem("imgData"));



            $(".container").show("slow");

            $(".step1").show("slow");

            $("#contenedorprincipal_container_id").show("slow");

            $("#modal_proveedor").css("display", "block");

            $("#modal_proveedor").show("slow");

            $("#modal_proveedor").css("z-index", "1041");


            $("#panelherramiente").css("display", "inline-block");
            $("#panelherramiente").show();

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



            img.src = localStorage.getItem('imgData');

            var ctx = canvas.getContext('2d');

            //draw picture from video on canvas
            ctx.drawImage(img, 0, 0);

            //modify the picture using glfx.js filters

            step2imagen_compra();

          }

        }
      }

      function step2imagen_compra() {

        //$("#adjust").show();
        //console.log("valor" + valor);

        $("#step1").hide();

        $("#step2").show();

        $(".step2").show();

        $(".panelherramiente").show();

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
            //$('.jcrop-holder img').attr('src', fxCanvas.toDataURL());


          }
        });
        $(document).ready(function() {

          busqueda_ocr_2();
        });




      }


      function busqueda_ocr_2() {

        if (localStorage.getItem("primera_busqueda") == null) {

          $("#adjust").attr('disabled', 'disabled');
          $("#contenedor_progreso").css('display', "block");
          console.log("Procesando. valora_ocr_2.");



          valora_ocr_2();


        }

      }

      function valora_ocr_2() {

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

        runOCR_ocr_2(ctx);


      }

      function runOCR_ocr_2(url) {

        Tesseract.recognize(url).then(function(result) {

          if (result.text != "") {

            console.log("Resultado OCR compras " + result.text);

            if ($("#contenedor_eleccion_sistema").length == 0) {

              dividirCadena_productos(result.text, " ");

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

      function eliminar_cache_ocr_compra() {
        localStorage.removeItem("imgData");
        localStorage.removeItem("ocr_result");
        localStorage.removeItem("primera_busqueda");
        localStorage.removeItem("datosfactura");
        localStorage.removeItem("valortipoPA");

        var spte1_validacion = $("#nuevo_proveedor").length;

        if(spte1_validacion > 0){

          $("#ac_proveedor").val("");

          $("#nuevo_proveedor").val("");


        }

        notificacion("success", "topCenter", "Datos en cache eliminados");

        // inicializacion_ocr();

          //	 location.reload();
        //window.location.href = 'index.php?page=nueva_compra&tipo=factura';

        //location.reload();
      }

      function escapeRegExp(string) {
        return string.replace(/[//g", "''.*+?^${%}(#)|[\\\]]/g, '');
      }


      var valor111 = "A%A%a/%";
      var re = new RegExp(escapeRegExp(valor111), '');
      var sadsas = "";

      // alert(reemplazarTodos(valor111, "", "", true));

      cuenta = 0;
      posicion = valor111.indexOf("%");
      while (posicion != -1) {
        cuenta++;
        posicion = valor111.indexOf("x", posicion + 1);
      }

      //alert("POSICION " + posicion);


      var cadena = "texto enviado";
      for (i = 0; i < valor111.length; i++) {
        if (valor111.charAt(i) == '%') {

          // alert(i + ': ' + valor111.charAt(i));
          sadsas = valor111.replace(valor111.charAt(i), "");

        }

      }

      function reemplazarTodos(texto, reemplazarQue, reemplazarCon, ignorarMayMin) {
        //   var reemplazarQue = reemplazarQue.replace(/[\\^$.|""?*+()[{']/g, ""),
        reemplazarCon = reemplazarQue.replace(/[\\^$.|""?*+()[{']/g, ""),
          modif = "g" + (ignorarMayMin ? "i" : "");
        regex = new RegExp(reemplazarCon, modif);

        return texto.replace(regex, reemplazarCon).toLowerCase().replace(" ", "");
      }

      function dividirCadena_productos(cadenaADividir, separador) {

        console.log("Resultado dividir cadena main");

        var arrayDeCadenas = cadenaADividir.split(separador);
        var arrayDeCadenas_ESPACIO = cadenaADividir.split(" ");
        var arrayDeCadenas_coma = cadenaADividir.split(",");
        var arrayDeCadenas_saltos = cadenaADividir.split("\n");
        var cadenafinal_ocr = arrayDeCadenas.toString().split("\n");
        var cadenbuscar_ocr = cadenafinal_ocr.toString().split(",");


        var datos = [];
        var cuenta = 0;
        var datos_productos_b = [];


        /*console.log('Cadena original : ' + cadenaADividir);
        console.log('La cadena original con espacios es : ' + arrayDeCadenas_ESPACIO);
        console.log('La cadena original con comas es : ' + arrayDeCadenas);

        console.log('El array tiene ' + arrayDeCadenas_ESPACIO.length + " elementos con espacios: ");
        console.log('El array tiene ' + arrayDeCadenas_coma.length + " elementos con comas: ");

        console.log('El array tiene ' + arrayDeCadenas_saltos.length + " elementos con saltos: ");*/


        if (cadenaADividir.length > 0) {

          for (var a = 0; a < cadenbuscar_ocr.length; a++) {


            if (cadenbuscar_ocr[a] != "" && cadenbuscar_ocr[a].length > 3 && cadenbuscar_ocr[a] != "") {



              datos_productos_b[a] = resultado2 = reemplazarTodos(cadenbuscar_ocr[a], "", "", true);
            }

          }
        }

        busqueda_articulos_acr(datos_productos_b);


        console.log("total : " + cuenta);
        //alert("datos="+datos);

        console.log("..");

        console.log(cadenbuscar_ocr.length + " productos a buscar compra " + cadenbuscar_ocr);
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
            timeout: 5000,
            container: '#custom_container'
          }).show()
          return false
        }

        new Noty({
          text: notes[type],
          type: type,
          timeout: 5000,
          layout: layout
        }).show()

      }


      function eliminar_td_ocr(arrayProveedores) {


        var testimonialElements = $(".sugerencia");

        for (var i = 0; i < arrayProveedores.length; i++) {

          $("#lista_productos").parents('tr').eq(i).remove();


        }


      }





      function agrefa_fila_table_ocr_result(tabla) {

        var tbody = $(tabla);
        // var tbody =table; $('#lista_bloqueadas_usuario tbody');
        var fila_contenido = tbody.find('tr').first().html();

        var fila_nueva = $('<tr class="tr_ocr"></tr>');

        fila_nueva.append(fila_contenido);

        tbody.append(fila_nueva);

      }


      function edit_table_ocr_articulos(arrayProveedores, classe) {

        var testimonialElements = $(classe);

        for (var i = 0; i < arrayProveedores.length; i++) {
          testimonialElements.eq(i).val(arrayProveedores[i]);
          var element = testimonialElements.eq(i).text(); //do something with element }
        }
        // eliminar_td_ocr(arrayProveedores);
      }


      function busqueda_articulos_acr(buscardor) {

        $.ajax({
          type: 'POST',
          url: nueva_compra_url,
          data: "busqueda_ocr=" + buscardor,

          success: function(result) {

            console.log(result);

            var content = JSON.parse(result);

            if (typeof content[0].referencia == "undefined") {

               if(content=="BLOQUEO"){
                 notificacion("error", "topCenter", "Esta factura no puede ser procesada.");
                 return;
               }


              datos_final = result.split(",");

              notificacion("warning", "topCenter", "Articulos no encontrado");

              var proveedor_valor = [];
              var result_proveedores = [];

              eliminarFilas_ocr_bloque("2");


              if (datos_final.length > 0) {
                for (var i = 0; i < datos_final.length; i++) {
                  if (normalize(datos_final[i]) != "") {
                    proveedor_valor[i] = normalize(datos_final[i]);
                    console.log("datos: " + normalize(datos_final[i]));
                    agrefa_fila_table_ocr_result('#lista_productos tbody');

                  }

                }

                edit_table_ocr_articulos(proveedor_valor, ".sugerencia");

              } else {

                notificacion("warning", "topCenter", "Sin palabras en sugerencias");

              }



            } else {
              var content = JSON.parse(result);
              console.log(content);

              for (var i = 0; i < content.length; i++) {

                notificacion("success", "topCenter", "Producto " + content[i].referencia + ", encontrado con exito.");
                var descripcion = Base64.encode(content[i].descripcion);
                add_articulo(content[i].referencia, descripcion, content[i].pvp, "0", content[i].codimpuesto);
              }



            }






          }

        });


        localStorage.setItem("primera_busqueda", "primera");


        $("#contenedor_progreso").css('display', "none");
        $("#adjust").attr('disabled', 'enabled');

        recalcular();




      }



      function cerrar_ocr() {

        $("#contenedorprincipal_container_id").hide();
        $("#contenedorprincipal").hide();
        $('.contenedorprincipal_container').css("position:", "absolute");


      }



      function usar_serie() {
        for (var i = 0; i < all_series.length; i++) {
          if (all_series[i].codserie == $("#codserie").val()) {
            siniva = all_series[i].siniva;
            irpf = all_series[i].irpf;

            for (var j = 0; j < numlineas; j++) {
              if ($("#linea_" + j).length > 0 && siniva) {
                $("#iva_" + j).val(0);
                $("#recargo_" + j).val(0);
              }
            }

            break;
          }
        }
      }

      function usar_almacen() {
        document.f_buscar_articulos.codalmacen.value = $("#codalmacen").val();
      }

      function usar_divisa() {
        document.f_buscar_articulos.coddivisa.value = $("#coddivisa").val();
      }

      function recalcular() {
        var subtotales = [];
        var total_irpf = 0;

        for (var i = 0; i < numlineas; i++) {
          if ($("#linea_" + i).length > 0) {
            /// cambiamos coma por punto
            if (input_number == 'text' && $("#cantidad_" + i).val().search(",") >= 0) {
              $("#cantidad_" + i).val($("#cantidad_" + i).val().replace(",", "."));
            }
            if ($("#pvp_" + i).val().search(",") >= 0) {
              $("#pvp_" + i).val($("#pvp_" + i).val().replace(",", "."));
            }
            if ($("#dto_" + i).val().search(",") >= 0) {
              $("#dto_" + i).val($("#dto_" + i).val().replace(",", "."));
            }
            if ($("#iva_" + i).val().search(",") >= 0) {
              $("#iva_" + i).val($("#iva_" + i).val().replace(",", "."));
            }
            if ($("#irpf_" + i).val().search(",") >= 0) {
              $("#irpf_" + i).val($("#irpf_" + i).val().replace(",", "."));
            }
            if ($("#recargo_" + i).val().search(",") >= 0) {
              $("#recargo_" + i).val($("#recargo_" + i).val().replace(",", "."));
            }

            var l_uds = parseFloat($("#cantidad_" + i).val());
            var l_pvp = parseFloat($("#pvp_" + i).val());
            var l_dto = parseFloat($("#dto_" + i).val());
            var l_neto = l_uds * l_pvp * (100 - l_dto) / 100;
            var l_iva = parseFloat($("#iva_" + i).val());
            var l_irpf = parseFloat($("#irpf_" + i).val());
            var l_recargo = parseFloat($("#recargo_" + i).val());

            $("#neto_" + i).val(l_neto);
            if (numlineas == 1) {
              $("#total_" + i).val(fs_round(l_neto, fs_nf0) + fs_round(l_neto * (l_iva - l_irpf + l_recargo) / 100, fs_nf0));
            } else {
              $("#total_" + i).val(number_format(l_neto + (l_neto * (l_iva - l_irpf + l_recargo) / 100), fs_nf0, '.', ''));
            }

            /// adaptamos el alto del textarea al texto
            var txt = $("textarea[name='desc_" + i + "']").val();
            txt = txt.split(/\r*\n/);
            if (txt.length > 1) {
              $("textarea[name='desc_" + i + "']").prop('rows', txt.length);
            }

            /// calculamos los subtotales
            var l_codimpuesto = Math.round(l_iva * 100);
            if (subtotales[l_codimpuesto] === undefined) {
              subtotales[l_codimpuesto] = {
                neto: 0,
                iva: 0,
                recargo: 0,
              };
            }

            subtotales[l_codimpuesto].neto += l_neto;
            subtotales[l_codimpuesto].iva += l_neto * l_iva / 100;
            subtotales[l_codimpuesto].recargo += l_neto * l_recargo / 100;
            total_irpf += l_neto * l_irpf / 100;
          }
        }

        /// redondeamos los subtotales
        var neto = 0;
        var total_iva = 0;
        var total_recargo = 0;
        total_irpf = fs_round(total_irpf, fs_nf0);
        subtotales.forEach(function(elem) {
          neto += fs_round(elem.neto, fs_nf0);
          total_iva += fs_round(elem.iva, fs_nf0);
          total_recargo += fs_round(elem.recargo, fs_nf0);
        });

        var total = fs_round(neto + total_iva - total_irpf + total_recargo, fs_nf0);

        $("#aneto").html(number_format(neto, fs_nf0, '.', ''));
        $("#aiva").html(number_format(total_iva, fs_nf0, '.', ''));
        $("#are").html(number_format(total_recargo, fs_nf0, '.', ''));
        $("#airpf").html(number_format(total_irpf, fs_nf0, '.', ''));
        $("#atotal").val(total);

        /*       console.log("---------");
               console.log("Neto: " + neto);
               console.log("IVA: " + total_iva);
               console.log("RE: " + total_recargo);
               console.log("IRPF: " + total_irpf);
               console.log("Total: " + (neto + total_iva - total_irpf + total_recargo));*/

        if (total_recargo == 0 && !tiene_recargo) {
          $(".recargo").hide();
          disable_inputs('recargo', true);
        } else {
          $(".recargo").show();
          disable_inputs('recargo', false);
        }

        if (total_irpf == 0 && irpf == 0) {
          $(".irpf").hide();
          disable_inputs('irpf', true);
        } else {
          $(".irpf").show();
          disable_inputs('irpf', false);
        }
      }

      function disable_inputs(name, value) {
        for (var i = 0; i < numlineas; i++) {
          if ($("#linea_" + i).length > 0) {
            $("#" + name + "_" + i).prop('disabled', value);
          }
        }
      }

      function ajustar_neto(i) {
        var l_uds = 0;
        var l_pvp = 0;
        var l_dto = 0;
        var l_neto = 0;

        if ($("#linea_" + i).length > 0) {
          /// cambiamos coma por punto
          if ($("#neto_" + i).val().search(",") >= 0) {
            $("#neto_" + i).val($("#neto_" + i).val().replace(",", "."));
          }

          l_uds = parseFloat($("#cantidad_" + i).val());
          l_pvp = parseFloat($("#pvp_" + i).val());
          l_dto = parseFloat($("#dto_" + i).val());
          l_neto = parseFloat($("#neto_" + i).val());
          if (isNaN(l_neto)) {
            l_neto = 0;
          } else if (l_neto < 0) {
            l_neto = Math.abs(l_neto);
          }

          if (l_neto <= l_pvp * l_uds) {
            l_dto = 100 - 100 * l_neto / (l_pvp * l_uds);
            if (isNaN(l_dto)) {
              l_dto = 0;
            }

            l_dto = fs_round(l_dto, 2);
          } else {
            l_dto = 0;
            l_pvp = 100 * l_neto / (l_uds * (100 - l_dto));
            if (isNaN(l_pvp)) {
              l_pvp = 0;
            }

            l_pvp = fs_round(l_pvp, 4);
          }

          $("#pvp_" + i).val(l_pvp);
          $("#dto_" + i).val(l_dto);
        }

        recalcular();
      }

      function ajustar_total(i) {
        var l_uds = 0;
        var l_pvp = 0;
        var l_dto = 0;
        var l_iva = 0;
        var l_irpf = 0;
        var l_recargo = 0;
        var l_neto = 0;
        var l_total = 0;

        if ($("#linea_" + i).length > 0) {
          /// cambiamos coma por punto
          if ($("#total_" + i).val().search(",") >= 0) {
            $("#total_" + i).val($("#total_" + i).val().replace(",", "."));
          }

          l_uds = parseFloat($("#cantidad_" + i).val());
          l_pvp = parseFloat($("#pvp_" + i).val());
          l_dto = parseFloat($("#dto_" + i).val());
          l_iva = parseFloat($("#iva_" + i).val());
          l_recargo = parseFloat($("#recargo_" + i).val());
          l_irpf = parseFloat($("#irpf_" + i).val());

          l_total = parseFloat($("#total_" + i).val());
          if (isNaN(l_total)) {
            l_total = 0;
          } else if (l_total < 0) {
            l_total = Math.abs(l_total);
          }

          if (l_total <= l_pvp * l_uds + (l_pvp * l_uds * (l_iva - l_irpf + l_recargo) / 100)) {
            l_neto = 100 * l_total / (100 + l_iva - l_irpf + l_recargo);
            l_dto = 100 - 100 * l_neto / (l_pvp * l_uds);
            if (isNaN(l_dto)) {
              l_dto = 0;
            }

            l_dto = fs_round(l_dto, 2);
          } else {
            l_dto = 0;
            l_neto = 100 * l_total / (100 + l_iva - l_irpf + l_recargo);
            l_pvp = fs_round(l_neto / l_uds, 4);
          }

          $("#pvp_" + i).val(l_pvp);
          $("#dto_" + i).val(l_dto);
        }

        recalcular();
      }

      function ajustar_iva(num) {
        if ($("#linea_" + num).length > 0) {
          if (proveedor.regimeniva == 'Exento') {
            $("#iva_" + num).val(0);
            $("#recargo_" + num).val(0);

            bootbox.alert({
              message: 'El proveedor tiene regimen de IVA: ' + proveedor.regimeniva,
              title: "<b>Atención</b>"
            });
          } else if (siniva && $("#iva_" + num).val() != 0) {
            $("#iva_" + num).val(0);
            $("#recargo_" + num).val(0);

            bootbox.alert({
              message: 'La serie selecciona es sin IVA.',
              title: "<b>Atención</b>"
            });
          } else if (tiene_recargo) {
            for (var i = 0; i < all_impuestos.length; i++) {
              if ($("#iva_" + num).val() == all_impuestos[i].iva) {
                $("#recargo_" + num).val(all_impuestos[i].recargo);
              }
            }
          }
        }

        recalcular();
      }

      function aux_all_impuestos(num, codimpuesto) {
        var iva = 0;
        var recargo = 0;
        if (proveedor.regimeniva != 'Exento' && !siniva) {
          for (var i = 0; i < all_impuestos.length; i++) {
            if (all_impuestos[i].codimpuesto == codimpuesto || codimpuesto == '') {
              iva = all_impuestos[i].iva;
              if (tiene_recargo) {
                recargo = all_impuestos[i].recargo;
              }
              break;
            }
          }
        }

        var html = "<td><select id=\"iva_" + num + "\" class=\"form-control\" name=\"iva_" + num + "\" onchange=\"ajustar_iva('" + num + "')\">";
        for (var i = 0; i < all_impuestos.length; i++) {
          if (iva == all_impuestos[i].iva) {
            html += "<option value=\"" + all_impuestos[i].iva + "\" selected=\"\">" + all_impuestos[i].descripcion + "</option>";
          } else {
            html += "<option value=\"" + all_impuestos[i].iva + "\">" + all_impuestos[i].descripcion + "</option>";
          }
        }
        html += "</select></td>";

        html += "<td class=\"recargo\"><input type=\"text\" class=\"form-control text-right\" id=\"recargo_" + num + "\" name=\"recargo_" + num +
          "\" value=\"" + recargo + "\" onclick=\"this.select()\" onkeyup=\"recalcular()\" autocomplete=\"off\"/></td>";

        html += "<td class=\"irpf\"><input type=\"text\" class=\"form-control text-right\" id=\"irpf_" + num + "\" name=\"irpf_" + num +
          "\" value=\"" + irpf + "\" onclick=\"this.select()\" onkeyup=\"recalcular()\" autocomplete=\"off\"/></td>";

        return html;
      }



      function add_articulo(ref, desc, pvp, dto, codimpuesto, cantidad, codcombinacion) {
        if (typeof cantidad == 'undefined') {
          cantidad = 1;
        }

        if (typeof codcombinacion == 'undefined') {
          codcombinacion = '';
        }

        desc = Base64.decode(desc);
        $("#lineas_doc").append("<tr id=\"linea_" + numlineas + "\">\n\
            <td><input type=\"hidden\" name=\"idlinea_" + numlineas + "\" value=\"-1\"/>\n\
               <input type=\"hidden\" name=\"referencia_" + numlineas + "\" value=\"" + ref + "\"/>\n\
               <input type=\"hidden\" name=\"codcombinacion_" + numlineas + "\" value=\"" + codcombinacion + "\"/>\n\
               <div class=\"form-control\"><small><a target=\"_blank\" href=\"index.php?page=ventas_articulo&ref=" + ref + "\">" + ref + "</a></small></div></td>\n\
            <td><textarea class=\"form-control\" id=\"desc_" + numlineas + "\" name=\"desc_" + numlineas + "\" rows=\"1\">" + desc + "</textarea></td>\n\
            <td><input type=\"" + input_number + "\" step=\"any\" id=\"cantidad_" + numlineas + "\" class=\"form-control text-right\" name=\"cantidad_" + numlineas +
          "\" value=\"" + cantidad + "\" onchange=\"recalcular()\" onkeyup=\"recalcular()\" autocomplete=\"off\" value=\"1\"/></td>\n\
            <td><button class=\"btn btn-sm btn-danger\" type=\"button\" onclick=\"$('#linea_" + numlineas + "').remove();recalcular();\">\n\
               <span class=\"glyphicon glyphicon-trash\"></span></button></td>\n\
            <td><input type=\"text\" class=\"form-control text-right\" id=\"pvp_" + numlineas + "\" name=\"pvp_" + numlineas + "\" value=\"" + pvp +
          "\" onkeyup=\"recalcular()\" onclick=\"this.select()\" autocomplete=\"off\"/></td>\n\
            <td><input type=\"text\" id=\"dto_" + numlineas + "\" name=\"dto_" + numlineas + "\" value=\"" + dto +
          "\" class=\"form-control text-right\" onkeyup=\"recalcular()\" onchange=\"recalcular()\" onclick=\"this.select()\" autocomplete=\"off\"/></td>\n\
            <td><input type=\"text\" class=\"form-control text-right\" id=\"neto_" + numlineas + "\" name=\"neto_" + numlineas +
          "\" onchange=\"ajustar_neto(" + numlineas + ")\" onclick=\"this.select()\" autocomplete=\"off\"/></td>\n\
            " + aux_all_impuestos(numlineas, codimpuesto) + "\n\
            <td class=\"warning\" title=\"Cálculo aproximado del total de la linea\">\n\
               <input type=\"text\" class=\"form-control text-right\" id=\"total_" + numlineas + "\" name=\"total_" + numlineas +
          "\" onchange=\"ajustar_total(" + numlineas + ")\" onclick=\"this.select()\" autocomplete=\"off\"/></td></tr>");
        numlineas += 1;
        $("#numlineas").val(numlineas);
        recalcular();

        $("#modal_articulos").modal('hide');

        $("#desc_" + (numlineas - 1)).select();
        return false;
      }

      function add_articulo_atributos(ref, desc, pvp, dto, codimpuesto) {
        if (nueva_compra_url !== '') {
          $.ajax({
            type: 'POST',
            url: nueva_compra_url,
            dataType: 'html',
            data: "referencia4combi=" + ref + "&desc=" + desc + "&pvp=" + pvp + "&dto=" + dto + "&codimpuesto=" + codimpuesto,
            success: function(datos) {
              $("#nav_articulos").hide();
              $("#search_results").html(datos);
            },
            error: function() {
              bootbox.alert({
                message: 'Se ha producido un error al obtener los atributos.',
                title: "<b>Atención</b>"
              });
            }
          });
        }
      }

      function add_linea_libre() {
        $("#lineas_doc").append("<tr id=\"linea_" + numlineas + "\">\n\
            <td><input type=\"hidden\" name=\"idlinea_" + numlineas + "\" value=\"-1\"/>\n\
               <input type=\"hidden\" name=\"referencia_" + numlineas + "\"/>\n\
               <input type=\"hidden\" name=\"codcombinacion_" + numlineas + "\"/>\n\
               <div class=\"form-control\"></div></td>\n\
            <td><textarea class=\"form-control\" id=\"desc_" + numlineas + "\" name=\"desc_" + numlineas + "\" rows=\"1\"></textarea></td>\n\
            <td><input type=\"" + input_number + "\" step=\"any\" id=\"cantidad_" + numlineas + "\" class=\"form-control text-right\" name=\"cantidad_" + numlineas +
          "\" onchange=\"recalcular()\" onkeyup=\"recalcular()\" autocomplete=\"off\" value=\"1\"/></td>\n\
            <td><button class=\"btn btn-sm btn-danger\" type=\"button\" onclick=\"$('#linea_" + numlineas + "').remove();recalcular();\">\n\
               <span class=\"glyphicon glyphicon-trash\"></span></button></td>\n\
            <td><input type=\"text\" class=\"form-control text-right\" id=\"pvp_" + numlineas + "\" name=\"pvp_" + numlineas + "\" value=\"0\"\n\
                onkeyup=\"recalcular()\" onclick=\"this.select()\" autocomplete=\"off\"/></td>\n\
            <td><input type=\"text\" id=\"dto_" + numlineas + "\" name=\"dto_" + numlineas + "\" value=\"0\" class=\"form-control text-right\"\n\
                onkeyup=\"recalcular()\" onclick=\"this.select()\" autocomplete=\"off\"/></td>\n\
            <td><input type=\"text\" class=\"form-control text-right\" id=\"neto_" + numlineas + "\" name=\"neto_" + numlineas +
          "\" onchange=\"ajustar_neto(" + numlineas + ")\" onclick=\"this.select()\" autocomplete=\"off\"/></td>\n\
            " + aux_all_impuestos(numlineas, default_impuesto) + "\n\
            <td class=\"warning\" title=\"Cálculo aproximado del total de la linea\">\n\
               <input type=\"text\" class=\"form-control text-right\" id=\"total_" + numlineas + "\" name=\"total_" + numlineas +
          "\" onchange=\"ajustar_total(" + numlineas + ")\" onclick=\"this.select()\" autocomplete=\"off\"/></td></tr>");
        numlineas += 1;
        $("#numlineas").val(numlineas);

        recalcular();

        $("#desc_" + (numlineas - 1)).select();
        return false;
      }


      function get_precios(ref) {
        if (nueva_compra_url !== '') {
          $.ajax({
            type: 'POST',
            url: nueva_compra_url,
            dataType: 'html',
            data: "referencia4precios=" + ref + "&codproveedor=" + proveedor.codproveedor,
            success: function(datos) {
              $("#nav_articulos").hide();
              $("#search_results").html(datos);
            },
            error: function() {
              bootbox.alert({
                message: 'Se ha producido un error al obtener los precios.',
                title: "<b>Atención</b>"
              });
            }
          });
        }
      }

      function new_articulo() {
        if (nueva_compra_url !== '') {
          $.ajax({
            type: 'POST',
            url: nueva_compra_url + '&new_articulo=TRUE',
            dataType: 'json',
            data: $("form[name=f_nuevo_articulo]").serialize(),
            success: function(datos) {
              if (typeof datos[0] == 'undefined') {
                bootbox.alert({
                  message: 'Se ha producido un error al crear el artículo.',
                  title: "<b>Atención</b>"
                });
              } else {
                document.f_buscar_articulos.query.value = document.f_nuevo_articulo.referencia.value;

                $("#nav_articulos li").each(function() {
                  $(this).removeClass("active");
                });
                $("#li_mis_articulos").addClass('active');
                $("#search_results").show();
                $("#nuevo_articulo").hide();

                if (precio_compra == 'coste') {
                  add_articulo(datos[0].referencia, Base64.encode(datos[0].descripcion), datos[0].coste, 0, datos[0].codimpuesto);
                } else {
                  add_articulo(datos[0].referencia, Base64.encode(datos[0].descripcion), datos[0].pvp, 0, datos[0].codimpuesto);
                }
              }
            },
            error: function() {
              bootbox.alert({
                message: 'Se ha producido un error al crear el artículo.',
                title: "<b>Atención</b>"
              });
            }
          });
        }
      }

      function buscar_articulos() {
        document.f_nuevo_articulo.referencia.value = document.f_buscar_articulos.query.value;
        document.f_nuevo_articulo.refproveedor.value = document.f_buscar_articulos.query.value;

        if (document.f_buscar_articulos.query.value == '') {
          $("#nav_articulos").hide();
          $("#search_results").html('');
          $("#nuevo_articulo").hide();

          fin_busqueda1 = true;
          fin_busqueda2 = true;
        } else {
          $("#nav_articulos").show();

          if (nueva_compra_url !== '') {
            fin_busqueda1 = false;
            $.getJSON(nueva_compra_url, $("form[name=f_buscar_articulos]").serialize(), function(json) {
              var items = [];
              var insertar = false;
              $.each(json, function(key, val) {
                var stock = val.stockalm;

                if (val.nostock) {
                  stock = '-';
                } else if (val.stockalm != val.stockfis) {
                  stock += ' <span title="stock general">(' + val.stockfis + ')</span>';
                }

                var descripcion = Base64.encode(val.descripcion);
                var descripcion_visible = val.descripcion;
                if (val.codfamilia) {
                  descripcion_visible += ' <span class="label label-default" title="Familia: ' + val.codfamilia + '">' +
                    val.codfamilia + '</span>';
                }
                if (val.codfabricante) {
                  descripcion_visible += ' <span class="label label-default" title="Fabricante: ' + val.codfabricante + '">' +
                    val.codfabricante + '</span>';
                }
                if (val.trazabilidad) {
                  descripcion_visible += ' &nbsp; <i class="fa fa-code-fork" aria-hidden="true" title="Trazabilidad activada"></i>';
                }

                var precio = val.coste;
                if (precio_compra == 'pvp') {
                  precio = val.pvp;
                }

                var tr_aux = '<tr>';
                if (val.bloqueado) {
                  tr_aux = "<tr class=\"danger\">";
                } else if (val.stockfis < val.stockmin) {
                  tr_aux = "<tr class=\"warning\">";
                } else if (val.stockfis > val.stockmax) {
                  tr_aux = "<tr class=\"success\">";
                }

                if (val.secompra) {
                  var funcion = "add_articulo('" + val.referencia + "','" + descripcion + "','" + precio + "','" +
                    val.dtopor + "','" + val.codimpuesto + "','" + val.cantidad + "')";
                  var funcion1 = "add_articulo('" + val.referencia + "','" + descripcion + "','" + val.coste + "','" +
                    val.dtopor + "','" + val.codimpuesto + "','" + val.cantidad + "')";
                  var funcion2 = "add_articulo('" + val.referencia + "','" + descripcion + "','" + val.pvp + "','" +
                    val.dtopor + "','" + val.codimpuesto + "','" + val.cantidad + "')";

                  if (val.tipo) {
                    funcion = "add_articulo_" + val.tipo + "('" + val.referencia + "','" + descripcion + "','" +
                      precio + "','" + val.dtopor + "','" + val.codimpuesto + "','" + val.cantidad + "')";
                    funcion1 = "add_articulo_" + val.tipo + "('" + val.referencia + "','" + descripcion + "','" +
                      val.coste + "','" + val.dtopor + "','" + val.codimpuesto + "','" + val.cantidad + "')";
                    funcion2 = "add_articulo_" + val.tipo + "('" + val.referencia + "','" + descripcion + "','" +
                      val.pvp + "','" + val.dtopor + "','" + val.codimpuesto + "','" + val.cantidad + "')";
                  }

                  items.push(tr_aux + "<td><a href=\"#\" onclick=\"get_precios('" + val.referencia + "')\" title=\"más detalles\">\n\
                           <span class=\"glyphicon glyphicon-eye-open\"></span></a>\n\
                           &nbsp; <a href=\"#\" onclick=\"return " + funcion + "\">" + val.referencia + '</a> ' + descripcion_visible + "</td>\n\
                           <td class=\"text-right\"><a href=\"#\" onclick=\"return " + funcion1 + "\">" + show_precio(val.coste, val.coddivisa) + "</a></td>\n\
                           <td class=\"text-right\"><a href=\"#\" onclick=\"return " + funcion2 + "\" title=\"actualizado el " +
                    val.factualizado + "\">" + show_precio(val.pvp, val.coddivisa) + "</a></td>\n\
                           <td class=\"text-right\">" + stock + "</td></tr>");
                }

                if (val.query == document.f_buscar_articulos.query.value) {
                  insertar = true;
                  fin_busqueda1 = true;
                }


              });

              if (items.length == 0 && !fin_busqueda1) {
                items.push("<tr><td colspan=\"4\" class=\"warning\">Sin resultados. Usa la pestaña\n\
                                    <b>Nuevo</b> para crear uno.</td></tr>");
                document.f_nuevo_articulo.referencia.value = document.f_buscar_articulos.query.value;
                insertar = true;
              }

              if (insertar) {
                $("#search_results").html("<div class=\"table-responsive\"><table class=\"table table-hover\"><thead><tr>\n\
                        <th class=\"text-left\">Referencia + descripción</th>\n\
                        <th class=\"text-right\" width=\"80\">Compra</th>\n\
                        <th class=\"text-right\" width=\"80\">Venta</th>\n\
                        <th class=\"text-right\" width=\"80\">Stock</th>\n\
                        </tr></thead>" + items.join('') + "</table></div>\n\
                        <p class='help-block'>&nbsp; <span class='glyphicon glyphicon-info-sign'></span>\n\
                        Los precios de compra son los de este proveedor, o bien el precio de coste del artículo.\n\
                        Puedes modificar esta configuración desde Admin &gt; Almacenes.</p>");
              }
            });
          }
        }
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



      $(document).ready(function() {
        /**
         * Renombramos el id "lineas_albaran" a "lineas_doc", para asegurar que no deja de funcionar
         * hasta que todos los plugins gratuitos y de pago hayan aplicado el cambio.
         */
        $("#lineas_albaran").attr('id', 'lineas_doc');

        $("#i_new_line").click(function() {
          $("#i_new_line").val("");
          $("#nav_articulos li").each(function() {
            $(this).removeClass("active");
          });
          $("#li_mis_articulos").addClass('active');
          $("#search_results").show();
          $("#nuevo_articulo").hide();
          $("#modal_articulos").modal('show');
          document.f_buscar_articulos.query.select();
        });

        $("#i_new_line").keyup(function() {
          document.f_buscar_articulos.query.value = $("#i_new_line").val();
          $("#i_new_line").val('');
          $("#nav_articulos li").each(function() {
            $(this).removeClass("active");
          });
          $("#li_mis_articulos").addClass('active');
          $("#search_results").show();
          $("#nuevo_articulo").hide();
          $("#modal_articulos").modal('show');
          document.f_buscar_articulos.query.select();
          buscar_articulos();
        });

        $("#f_buscar_articulos").keyup(function() {
          buscar_articulos();
        });

        $("#f_buscar_articulos").submit(function(event) {
          event.preventDefault();
          buscar_articulos();
        });

        $("#b_mis_articulos").click(function(event) {
          event.preventDefault();
          $("#nav_articulos li").each(function() {
            $(this).removeClass("active");
          });
          $("#li_mis_articulos").addClass('active');
          $("#nuevo_articulo").hide();
          $("#search_results").show();
          document.f_buscar_articulos.query.focus();
        });

        $("#b_nuevo_articulo").click(function(event) {
          event.preventDefault();
          $("#nav_articulos li").each(function() {
            $(this).removeClass("active");
          });
          $("#li_nuevo_articulo").addClass('active');
          $("#search_results").hide();
          $("#nuevo_articulo").show();
          document.f_nuevo_articulo.referencia.select();
        });
      });
