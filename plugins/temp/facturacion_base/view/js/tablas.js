		var array_form_sugerencia = [];
		var array_form_reemplazo = [];
		var array_form_bloqueadas = [];
		var arraypalabras_bloqueadas = [];
		var tr1 = "";
		var tr2 = "";
		var bloquadas = "";
		var estado_bloque_class = "";
		var table_bloque="";
		var sugerencia_agre= $('.bloquadas');
		var lista_sugerencia= $('.bloquadas_usuario');

			function limpiar_tabla(idaccion)	{

				switch(idaccion){

					case 1:
					for (var i = 0; i < $(sugerencia_agre).length +1; i++) {

						if(	$(sugerencia_agre[i]).length < 3){
						$(sugerencia_agre[i]).val("");
						}

						}
					break;

					case 2:

					for (var i = 0; i < $(lista_sugerencia).length +1; i++) {
						if(	$(lista_sugerencia[i]).val().length < 3){
						$(lista_sugerencia[i]).val("");
						}


										}

					break;
				}

			}



		//Eliminar fila.
		$('#lista_bloqueadas').on('click', '#button_eliminar_producto_bloqueada', function() {

		  if (validar_tr_ultima("#lista_bloqueadas tr", "tabla_bloqueo_agrega")) {
		    $(this).parents('tr').eq(0).remove();
		  }

		});

		$('#lista_productos').on('click', '#button_eliminar_producto', function() {

		  if (validar_tr_ultima("#lista_productos tr", "lista_productos")) {
		    $(this).parents('tr').eq(0).remove();
		  }

		});


		$('#lista_bloqueadas_usuario').on('click', '#eliminar_bloqueada_usuario', function() {

		  if (validar_tr_ultima("#lista_bloqueadas_usuario tr", "tabla_sugerencia")) {
		    $(this).parents('tr').eq(0).remove();


		  }
		});




		$('#cargar_lista_palabras_table').on('click', '#eliminar_sugerencia_usuario', function() {

		  if (validar_tr_ultima("#cargar_lista_palabras_table tr", "carga_sugerida")) {
		    $(this).parents('tr').eq(0).remove();
		  }

		});


		function validar_tr_ultima(tabla, campo) {

		  var nFilas = $(tabla).length;

		  if (nFilas > 2) {


		    switch (campo) {
		      case "tabla_sugerencia":

		        return true;
		        break;

		      case "carga_sugerida":

		        if (nFilas < 2) {

		          $("input.palabras_carga").val("");

		        }


		      case "lista_productos":

		        if (nFilas < 2) {







		          return false;
		        } else {


		          return true;
		        }

		        break;

		      case "tabla_bloqueo_agrega":

		        if (nFilas < 2) {

		          $("input.bloquadas").val("");
		          return false;
		        } else {
		          return true;
		        }
		        break;

		      case "carga_bloqueo":
		        $("input.agregadas_bd").val("");
		        return true;

		        break;

		    }


		  } else {

		    return false;
		  }



		}


		$('#lista_productos').on('click', '.button_agregar_producto', function() {

		  var valor = $(this).parents("tr").find('.sugerencia').val();


		  if (valor.length > 0) {
		    $("#contenedorprincipal").css("z-index", "1040");
		    $("#adjust").attr('disabled', 'enabled');
		    $("#contenedorprincipal").css('display', "none");
		    $("#modal_proveedor").css('display', "block");
		    $("#modal_proveedor").show("slow");

		    $("#nuevo_proveedor").focus();
		    $("#nuevo_proveedor").val(valor);
		    notificacion("success", "topCenter", " Click en boton para continuar con el nuevo proveedor. ");
		  } else {
		    notificacion("success", "topCenter", " Sin nombre de proveedor. ");
		  }

		});

		$('#guardar_lista_palabras').click(function() {

		  $(document).ready(function() {
		    Guardar_palabras(2);
		  });
		});

		$('.guardar_palabras_ocr_negativas').click(function() {

		  $(document).ready(function() {
		    Guardar_palabras(1);

		  });
		});





		function guardar_bloqueada(idbloque) {
			array_form_bloqueadas =[];
			bloquadas="";
			table_bloque="";

		  switch (idbloque) {

		    case 1:

		      bloquadas = $('.bloquadas');
			  	table_bloque =  $('.tr_bloque_list');
		      estado_bloque_class = 1;

		      for (var i = 0; i < bloquadas.length; i++) {
		        if ($(bloquadas[i]).val() != "undefined" && $(bloquadas[i]).val() != "" && $(bloquadas[i]).val().length > 2) {
							array_form_bloqueadas[i] = $(bloquadas[i]).val();
		        }
		      }


					if(array_form_bloqueadas.length > 0) {
					Guardar_bloquadas(array_form_bloqueadas, 1);
					proteger_inputs_table();
					}


		      break;
		    case 2:

		      bloquadas = $('.bloquadas_usuario');
					table_bloque = $('.bloque_tr_list');
		      estado_bloque_class = 2;

					for (var i = 0; i < bloquadas.length; i++) {
						if ($(bloquadas[i]).val() != "undefined" && $(bloquadas[i]).val() != "" && $(bloquadas[i]).val().length > 2) {
							array_form_bloqueadas[i] = $(bloquadas[i]).val();
						}
					}

					if(array_form_bloqueadas.length > 0) {
					Guardar_bloquadas(array_form_bloqueadas, 2);
					proteger_inputs_table();
					}



		      break;


		  }
		}



		$('.guardar_palabras_ocr_bloqueadas').click(function() {

		  guardar_bloqueada(1);

		});

		$('#guardar_lista_bloque').click(function() {

		  guardar_bloqueada(2);


		});



		function proteger_inputs_table() {

		  $(".sugerencia").prop('disabled', true);
		  $(".reemplazo").prop('disabled', true);
		  $(".guardar_palabras_ocr_negativas").prop('disabled', true);
		  $(".button_agregar_producto").prop('disabled', true);
		  $(".button_eliminar_producto").prop('disabled', true);
		  $(".button_agregar_palabra").prop('disabled', true);
		  $(".guardar_palabras_ocr_negativas").css("display", "none");



			$(".bloquadas").prop('disabled', true);
			$(".bloquadas_usuario").prop('disabled', true);
			$("#agregar_td_ocr_bloque").prop('disabled', true);
			$("#cargar_lista_bloqueada").prop('disabled', true);
			$("#guardar_lista_bloque").prop('disabled', true);
			$("#eliminar_bloqueada_usuario").prop('disabled', true);
			$("#button_eliminar_producto_bloqueada").css("display", "none");
			$(".guardar_palabras_ocr_bloqueadas").prop('disabled', true);




		}

		function desproteger_inputs_table() {


		  $(".sugerencia").prop('disabled', false);
		  $(".reemplazo").prop('disabled', false);
		  $(".guardar_palabras_ocr_negativas").prop('disabled', false);
		  $(".button_agregar_producto").prop('disabled', false);
		  $(".button_eliminar_producto").prop('disabled', false);
		  $(".button_agregar_palabra").prop('disabled', false);
		  $(".guardar_palabras_ocr_negativas").css("display", "inline-block");


			$(".bloquadas").prop('disabled', false);
			$(".bloquadas_usuario").prop('disabled', false);
			$("#agregar_td_ocr_bloque").prop('disabled', false);
			$("#cargar_lista_bloqueada").prop('disabled', false);
			$("#guardar_lista_bloque").prop('disabled', false);
			$("#eliminar_bloqueada_usuario").prop('disabled', false);
			$("#button_eliminar_producto_bloqueada").css("display", "inline-block");
			$(".guardar_palabras_ocr_bloqueadas").prop('disabled', false);





		    $(tr1).val("");
		    $(tr2).val("");
				limpiar_f();



		}

		function limpiar_f(){
			array_form_bloqueadas =[];
		}





			function limpia_palabras(Array){


				Array.prototype.compacta = function(){
				for(var i = 0; i < this.length; i++){
						if(this[i] === undefined){
								this.splice(i , 1);
							}
						}
					}




				/*if(typeof palabra === 'undefined'){
						return "false";
				}else{

					if (undefined !== palabra && palabra.length) {

						if(palabra !== '' && palabra != "undefined"){

							if(palabra.length > 2){

								return true;

					}

							}
									}


				}*/



			}





		function enviar_datos_proveedor(tipo, id) {

		  var notifi_pro = false;
		  var contador_no_agregadas = 0;

		  var sugerencias = "";
		  var reemplazo = "";
		  var tr_table_sugerencia = "";
			tr1="";
			tr1="";
			array_form_sugerencia=[];
			var nmb_notificacion= [];




		  switch (id) {

		    case 1:
		      sugerencias = $('.sugerencia');
		      reemplazo = $('.reemplazo');
		      tr_table_sugerencia = $('.tr_ocr');
		      tr1 = '.sugerencia';
		      tr2 = '.reemplazo';
		      break;
		    case 2:
		      sugerencias = $('.listas_palabras');
		      reemplazo = $('.listas_palabras_sugerencia');
		      tr_table_sugerencia = $('.tr_ls_suge');

		      tr1 = '.listas_palabras';
		      tr2 = '.listas_palabras_sugerencia';

		      break;
		  }




		  for (var i = 0; i < sugerencias.length; i++) {



				if($(sugerencias[i]).val().length > 2){

					array_form_sugerencia[i] = $(sugerencias[i]).val();

				}

		  }

		  for (var i = 0; i < reemplazo.length; i++) {

				if($(reemplazo[i]).val().length > 2){

		      array_form_reemplazo[i] = $(reemplazo[i]).val();

					}

		  }

			Array.prototype.compacta = function(){
					for(var i = 0; i < this.length; i++){

							if(this[i] === undefined || this[i] === ""){

									this.splice(i , 1);

							}




			}

				}


			array_form_sugerencia.compacta();
			array_form_sugerencia = array_form_sugerencia.filter(Boolean);
			window.console.log(array_form_sugerencia);

			array_form_reemplazo.compacta();
			array_form_reemplazo = array_form_reemplazo.filter(Boolean);
			window.console.log(array_form_reemplazo);


			if (array_form_sugerencia.length > 0 && array_form_reemplazo.length > 0) {

		  for (var i = 0; i < array_form_reemplazo.length; i++) {

					nmb_notificacion[i] = array_form_sugerencia[i];
		      proteger_inputs_table();

		      $.ajax({

		        type: 'POST',
		        url: "index.php?page=compras_facturas",
		        data: 'nombre_sugerencia=' + array_form_sugerencia[i] + '&nombre_sugerido=' + array_form_reemplazo[i] + '&tipo=' + tipo + '&id=' + id,
		        success: function(sugerencia) {


		            var sugerencia_result = JSON.parse(sugerencia);

		            for (var i = 0; i < tr_table_sugerencia.length; i++) {

									var campo_lista = $('.listas_palabras');

									var campo_lista_suge = $('.listas_palabras_sugerencia');


										if(id==2 && campo_lista.length ==1){

											$(campo_lista).val("");
											$(campo_lista_suge).val("");
										}


										if((validar_longitud(1) == 1)){
											$(sugerencias).val("");
		                  $(reemplazo).val("");
										}else{

		                if (validar_longitud(1) > 1) {

		                  $(tr_table_sugerencia[i]).remove();
		                } else {

		                  $(sugerencias).val("");
		                  $(reemplazo).val("");

		                }
									}

									}

											}





		      });


				}



desproteger_inputs_table();

notificacion("success", "topCenter", "Sugerencia agregadas con exitos.");
		}else{

				notificacion("error", "topCenter", "No concuerta la cantidad de palabras a guardar.");

		}

			}



		function validar_longitud(valor) {

		  var campo = "";
		  var contador = 0;

			//campo = $(tr1);
			switch (valor) {

		    case 1:
		      campo = $(tr1);
		      break;
		    case 2:
		      if (estado_bloque_class == 1) {
		       campo = $('.bloquadas');
					}

		     if (estado_bloque_class == 2) {
		        campo = $('.bloquadas_usuario');
		      }
		      break;

					case 3:
						campo = $('.bloquadas_usuario');

						break;
		  }

		  for (var i = 0; i < campo.length; i++) {
		    contador++;
		  }

		  return contador;
		}

		function enviar_datos_productos() {

		  $.ajax({

		    type: 'POST',
		    url: "index.php?page=compras_facturas",
		    data: 'nombre_sugerencia=' + array_form_sugerencia[i] + '&nombre_sugerido=' + array_form_reemplazo[i],
		    success: function(sugerencia) {

		      if (sugerencia != "") {

		        notificacion("success", "topCenter", "Sugerencia " + sugerencia + " agregada con exito. ");

		      }
		    }

		  });
		}

		function edit_table_ocr(array) {

		  var bloqueo_usuarios = $(".bloquadas_usuario");

		  for (var i = 0; i < array.length; i++) {
		    bloqueo_usuarios.eq(i).val(array[i]);
		    var element = bloqueo_usuarios.eq(i).text();
		  }
		  //eliminar_td_ocr(array);
		}

		function edit_table_ocr_sugeridad(array11, array2) {


		  var lista = $(".listas_palabras");
		  var lista_sug = $(".listas_palabras_sugerencia");



		  for (var i = 0; i < array11.length; i++) {
		    lista.eq(i).val(array11[i]);
		    lista_sug.eq(i).val(array2[i]);

		    var element = lista.eq(i).text();
		  }
		  //eliminar_td_ocr(array);
		}



		function cargar_lista(tipo) {

		  if (tipo == 0) {
		    eliminarFilas_ocr_bloque(0);
		  }
		  if (tipo == 1) {
		    eliminarFilas_ocr_bloque(1);
		  }

		  var datos_valor_palabra = [];
		  var datos_valor_sugeridad = [];

		  $.ajax({

		    type: 'POST',
		    url: "index.php?page=nueva_compra",
		    data: 'cargar_palabra=' + tipo,
		    success: function(datos) {

		      console.log("Cargando palabras " + datos);

		      var content = JSON.parse(datos);

		      if (typeof content[0].tipo != "undefined") {

		        for (var i = 0; i < content.length; i++) {

		          if (content[i].palabra != "" && content[i].palabra_sugerida != "") {

		            datos_valor_palabra[i] = content[i].palabra;
		            datos_valor_sugeridad[i] = content[i].palabra_sugerida;

		            console.log("dato[ " + content[i].palabra + " ]" + " " + "dato [ " + content[i].palabra_sugerida + " ]");

		            if (tipo == 0) {

		              var total_carga_table = $('.cargar_lista_palabras_table');

		              agrefa_fila_table_ocr('#cargar_lista_palabras_table tbody', 0);

		            }

		            if (tipo == 1) {

		              agrefa_fila_table_ocr('#lista_bloqueadas_usuario tbody', 1);

		            }

		          }
		        }

		        if (tipo == 0) {
		          edit_table_ocr_sugeridad(datos_valor_palabra, datos_valor_sugeridad);
		        }
		        if (tipo == 1) {

		          edit_table_ocr(datos_valor_palabra);

		        }

		      } else {

		        var datos_valor = [];

		        for (var i = 0; i < content.length; i++) {

		          if (content[i].palabra != "") {
		            datos_valor[i] = content[i].palabra;
		            console.log("dato[ " + content[i].palabra + " ]");


		            if (tipo == "0") {

		              agrefa_fila_table_ocr('#cargar_lista_palabras_table tbody', 0);


		            } else {

		              agrefa_fila_table_ocr('#lista_bloqueadas_usuario tbody', 1);

		            }

		          }
		        }

		        edit_table_ocr(datos_valor);
		      }
		      notificacion("success", "topCenter", " Datos cargado ");




		    }

		  });

		}

		function eliminarFilas_ocr_bloque(tipo) {

			switch (tipo) {
		    case 0:
				$('#cargar_lista_palabras_table tbody tr').not(':first').remove();
				break;

		    case 1:
				$('#lista_bloqueadas_usuario tbody tr').not(':first').remove();
				break;

		    case 2:
				$('#lista_productos tbody tr').not(':first').remove();
				break;

				}


		}

		function agrefa_fila_table_ocr(tabla, tipo) {

		  switch (tipo) {
		    case 0:

		      var tbody = $(tabla);

		      var fila_contenido = tbody.find('tr').first().html();

		      var fila_nueva = $('<tr id="tr_class" class="tr_ls_suge"></tr>');

		      fila_nueva.append(fila_contenido);

		      tbody.append(fila_nueva);

		      break;

		    case 1:

		      var tbody = $(tabla);

		      var fila_contenido = tbody.find('tr').first().html();

		      var fila_nueva = $('<tr id="tr_class" class="tr_ocr_bloque_list bloque_tr_list"></tr>');

		      fila_nueva.append(fila_contenido);

		      tbody.append(fila_nueva);

		      break;

		    case 2:

		      var tbody = $(tabla);

		      var fila_contenido = tbody.find('tr').first().html();

		      var fila_nueva = $('<tr id="tr_class" class="tr_ocr tr_class"></tr>');

		      fila_nueva.append(fila_contenido);

		      tbody.append(fila_nueva);

		      break;

		  }
		}

		function validacion_texto(inputs){

		var input = $('.form-control');
		for (var i = 0; i < input.length; i++) {

		if($(input[i]).val().length < 3) {
		//	$(input[i]).css('border-color', "red");
			$(input[i]).css('background', "rgba(255, 10, 10, 0.06)");
			$(input[i]).css('border-color', "#82272b");


		}else{
			$(input[i]).css('border-color', "d2d6de");
			$(input[i]).css('background', "#fff");

		}


	}

		}


		function Guardar_bloquadas(bloqueadas,idaccion) {

		  $.ajax({

		    type: 'POST',
		    url: "index.php?page=nueva_compra",
		    data: 'palabras_a_bloquar=' + bloqueadas + '&accion=' + idaccion,
		    success: function(datos) {


		      if (datos != "") {
					//	return datos;

						notificacion("alert", "topCenter",datos);



					/*	if(idaccion==1){
							 notificacion("success", "topCenter", "Palabras agregada con exito. ");
						}
						if(idaccion==2){

							 notificacion("success", "topCenter", "Palabras  Modificadas con exito. ");
						}*/



		        //var bloqueo_text = JSON.parse(datos);

		        for (var i = 0; i < table_bloque.length; i++) {

		          if (($(bloqueadas[i]).val() != "") && ($(bloqueadas[i]).val() != "undefined")) {

		            if (validar_longitud(2) > 1) {
									console.log("Total :" + validar_longitud(2) );

		              $(table_bloque[i]).remove();

		            } else {


									if(idaccion==2){

											$(lista_sugerencia[i]).val("");


									}

		              $(bloquadas[i]).val("");
		            }

		          }



		        desproteger_inputs_table();
						limpiar_tabla(idaccion);
		      }



		    }else{
					       notificacion("alert", "topCenter", "Datos no agregados.");
				}

				}

		  });




		} /*fin function */

		function Guardar_palabras(id) {

		  var accion_pagina_e = localStorage.getItem("valortipoPA"); // $("#evaluar").val();

		  console.log("Accion pagina " + accion_pagina_e);

		  if (accion_pagina_e == "") {
		    accion_pagina_e = 3;
		    enviar_datos_proveedor(accion_pagina_e, id);
		  } else {
		    enviar_datos_proveedor(accion_pagina_e, id);

		  }


		}

		$('#cargar_lista_palabras').click(function() {

		  cargar_lista(0);

		});

		$('#cargar_lista_bloqueada').click(function() {

		  cargar_lista(1);

		});







		$('.button_agregar_palabra').click(function() {

		  agregar_tr('#lista_productos tbody');

		});




		$('#agregar_td_ocr_bloque').click(function() {


		  agregar_tr('#lista_bloqueadas tbody');

		});

		function agregar_tr(tabla) {

		  var fila_nueva = "";

		  if (tabla == "#lista_productos tbody") {
		    fila_nueva = $("<tr class='tr_ocr tr_class'</tr>");


		  }
		  if (tabla == "#lista_bloqueadas tbody") {
		    fila_nueva = $("<tr class='tr_bloque_list'</tr>");
		  }

		  var tbody = $(tabla);
		  var fila_contenido = tbody.find('tr').first().html();

		  fila_nueva.append(fila_contenido);

		  tbody.append(fila_nueva);

		}

		function cargar_tabla_bloque(tabla, nombre) {

		  var tbody = $(tabla);
		  var fila_contenido = tbody.find('tr').first().html();
		  var fila_nueva = $("<tr class=tr_ocr></tr>");

		  fila_nueva.append(fila_contenido);

		  tbody.append(fila_nueva);

		  var table = document.getElementById("lista_productos");


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
