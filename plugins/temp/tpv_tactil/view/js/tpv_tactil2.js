			/**
			 * @author Carlos García Gómez      neorazorx@gmail.com
			 * @copyright 2015-2017, Carlos García Gómez. All Rights Reserved.
			 * @copyright 2015-2017, Jorge Casal Lopez. All Rights Reserved.
			 */






			/*$(document).ready(function() {

			 $( ".input-group-btn" ).addClass( "eliminarlinea" );

			});*/

				var  stateCheck = setInterval(() => {
	  if (document.readyState === 'complete') {
	    clearInterval(stateCheck);
			ocultar_guardar_impresion();

	  }
	}, 100);

		//alert(localStorage.getItem("ultima_caja"));






			var  codimpresion =0;
			var  listaimpresion='';
			var  trimpresion='';
			var  linea_eliminar='';
			var  CajaSeleccion ="";
			var  tb_eliminacion=false;
			var  comandaval ="";
			var  _numlineas;
			var contador_uno_imprimir=0;
			var contador_dos_imprimir=0;
			var contador_uno=0;
			var contador_cero =0;
			var contador_dos=0;


			function c(){

			listaimpresion='';
			cantidad_impresion=0;
			listaimpresion_longitud=0;




			if(numlineas > 0) {

			 for (var i = 0; i < numlineas; i++) {


			   listaimpresion += $("#impresion_" + i).val() + ',';
			  // listaimpresion_longitud += $("#impresion_" + i).val();

				}

				listaimpresioncoma=listaimpresion.length -1;

				if(listaimpresioncoma==','){

					listaimpresion=listaimpresion - listaimpresion.length;

				}

				var expresionRegular = ";";
				var total_impresiones = listaimpresion.split(expresionRegular);


				return total_impresiones.length -1;








				}

				}

							/*$("#eliminar_comanda").click(function () {

						});*/


						function disablebt() {

							alert();
						}
						function eliminacio_comanda(comanda){

							$.ajax({

								type: 'GET',
								url: "index.php?page=tpv_tactil",
								data: 'delete_comanda=' + comanda,
									success: function(datos) {


										clean_cache_lineas();
										recalcular();
										notificacion("error", "Center", "  TICKET ELIMINADO CON EXITO.");
										setTimeout("location.href =tpv_url", 1000);





										}

									});



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
									timeout: 4000,
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



				function validacion_comanda(comanda) {

					$.ajax({

						type: 'POST',
						url: "index.php?page=tpv_tactil",
						data: 'tp_eliminar=' + comanda,
						datatype:'JSON',
							success: function(datos) {

						if(datos){
							//	window.location.href="{$fsc->url()}delete_comanda={$fsc->comanda->idtpv_comanda}";
						tb_eliminacion=true;
						comandaval = comanda;
						$('#clave_elimina').autofocus;
						document.getElementById("clave_elimina").autofocus;
						$('#modalclave_eliminar').modal();

						$('#modalclave_eliminar').on('shown.bs.modal', function () {
					  $('#clave_elimina').focus();
						})

						document.getElementById('clave_elimina').click();


						} else {

						tb_eliminacion=false;
						eliminacio_comanda(comanda);

						}

									}

							});



				}


				function enviar_c(datos) {

					console.log("Comanda " + comandaval);
				$.ajax({

								type: 'POST',
								url: "index.php?page=tpv_tactil",
								data: 'tp_seguridad=' + datos + '&accion=' + 3,
								async:false,
								datatype:'JSON',
								success: function(datos) {

									if(datos){

								  	if(tb_eliminacion && datos) {
									  console.log(tb_eliminacion + " " + datos);
										eliminacio_comanda(comandaval);

										} else {


										$("#notificacion").css('display', "none");
										$("#linea_" + linea_eliminar).remove();
										recalcular();
										document.f_tpv.codbar.focus();

										$("#notificacion").css('color', "Green");
										$('#modalclave_eliminar').modal('hide');
										notificacion("error", "topCenter", "ARTICULO ELIMINADO CON EXITO.");
										//setTimeout("location.href =tpv_url", 2000);

												}



								}else{
									$("#notificacion").text("Contraseña Incorrecta.");
									$("#notificacion").css('display', "inline-block");
									$("#notificacion").css('color', "#e45858");
									return;
								}


									}



						});


				}




				function confirmar_elimacion_p(linea){

					linea_eliminar=linea;

				 }

				function impresion_finalizada(){

			  var idtpv_comanda= document.getElementsByName("idtpv_comanda")[0].value;

						 $.ajax({
						type: 'post',
						url: tpv_url,
						dataType: 'html',
						data: 'idtpv_comanda_impresion=' + idtpv_comanda,
						success: function (datos) {






						}
					});





				}


				var beforePrint = function() {



				};
				var afterPrint = function() {



				};

				if (window.matchMedia) {
					var mediaQueryList = window.matchMedia('print');
					mediaQueryList.addListener(function(mql) {
						if (mql.matches) {
							beforePrint();
						} else {


							afterPrint();



						}
					});
				}

				window.onbeforeprint = beforePrint;
				window.onafterprint = afterPrint;




			var defaultimpre=5;
			var numlineas = 1;
			var fs_nf0 = 2;
			var tpv_url = '';
			var siniva = false;
			var irpf = 0;
			var all_impuestos = [];
			var cliente = false;
			var fin_busqueda1 = true;
			var keyboard_id = false;
			var keyboard2_id = 'tpv_efectivo';
			var keyboard_tipo = false;
			var keyboard_num = false;
			var tesoreria = false;

			var lineas_cache = '';
			var usar_cache = true;
			var volver_familias = false;



			function get_articulos(codfamilia)
			{
				/// comprobamos si el navegador soporta localstorage
				if (typeof (Storage) !== "undefined" && usar_cache) {
					/// comprobamos la caché
					var datos = localStorage.getItem("tpv_tactil_fam" + codfamilia);
					if (typeof (datos) == "string") {
						$("#catalogo").html(datos);
						$('#tabs_catalogo a[href="#articulos1"]').tab('show');
					}
				}

				$.ajax({
					type: 'POST',
					url: tpv_url,
					dataType: 'html',
					data: 'codfamilia=' + codfamilia + '&codcliente=' + document.f_tpv.cliente.value,
					success: function (datos) {
						$("#catalogo").html(datos);
						$('#tabs_catalogo a[href="#articulos1"]').tab('show');

						/// comprobamos si el navegador soporta localstorage
						if (typeof (Storage) !== "undefined" && usar_cache) {
							/// nos guardamos para la caché
							localStorage.setItem("tpv_tactil_fam" + codfamilia, datos);
						}
					}
				});

				return false;
			}





			function ocultar_guardar_impresion() {

			limpiar_contadores();

			var elemento = document.getElementById("confirmar_3");
			var elemento_1 = document.getElementById("confirmar_4");

			if(numlineas > 0) {


			for (var i = 0; i < numlineas; i++) {

			valor=parseInt($("#impresion_"+ i).val());

			console.log("elemento " + valor);

			switch (valor) {

			case 0:

			contador_cero++;





			case 1:

			if($("#impresion_"+ i).is(":disabled")){

			contador_uno_imprimir++;
			elemento_1.style.display = 'block';
			}

			if($("#impresion_"+ i).val()=='1' && $("#impresion_"+ i).is(":enabled")){

			contador_uno++;
			elemento.style.display = 'block';

			}

			case 2:




			if($("#impresion_"+ i).val()=='2'){

			contador_dos_imprimir++;

			}





			}

				}

				_numlineas = numlineas -1;

				if(contador_cero ==_numlineas){
				elemento_1.style.display = 'none';
				elemento.style.display = 'none';

			}

				if(contador_uno_imprimir ==_numlineas){
				elemento.style.display = 'none';

			 }

			 if(contador_uno ==_numlineas){
			 elemento_1.style.display = 'none';

			 }

			 if(contador_uno == 0){
			 elemento.style.display = 'none';

			 }






			console.log("--------------------------------------------------------------");
			console.log("Total contador_cero   : "  +  contador_cero);
			console.log("Total contador_uno   : "  +  contador_uno);
			console.log("Total contador_uno_imprimir   : "  +  contador_uno_imprimir);
			console.log("Total contador_dos_imprimir   : "  +  contador_dos_imprimir);
			console.log("--------------------------------------------------------------");





	}


		}

			var Valores_impresion = [];


		function Activar_botones_impre()	{

		 	_numlineas = numlineas -1;

		  //limpiar_contadores();
		  //ocultar_guardar_impresion();

			var elemento = document.getElementById("confirmar_3");
			var elemento_1 = document.getElementById("confirmar_4");
			elemento.style.display = 'none';
			elemento_1.style.display = 'none';

			if(contador_uno > 0){
			elemento.style.display = 'block';
			}

			if(contador_uno_imprimir > 0){
			elemento_1.style.display = 'block';
			}

			if(contador_dos_imprimir ==_numlineas) {

		//	elemento.style.display = 'none';
		 // elemento_1.style.display = 'none';

			}

			if(contador_cero ==_numlineas){
			//elemento_1.style.display = 'none';
			//elemento.style.display = 'none';

		}

			if(contador_uno_imprimir ==_numlineas){
			elemento_1.style.display = 'block';


		}


limpiar_contadores();

}
			function limpiar_contadores(){

			contador_uno_imprimir=0;
			contador_dos_imprimir=0;
			contador_uno=0;
			contador_cero =0;
			contador_dos=0;


					}


			function validarCheck(idcheck){


				if($("#"+idcheck).val() =="0"){
					$("#"+idcheck).val('1');

				} else{

					$("#"+idcheck).val('0');
				}

			ocultar_guardar_impresion();

					}

			function move_row_f(elementoEnviar,idimpresion) {


			if( $("#"+idimpresion).prop('checked') ) {

			 tabla = $('#tpv_albaran');

			 tabla_impre = $('#tpv_albaran1');

			 tr = $('tr:first', tabla);

			 tr.clone().appendTo(tabla_impre).find('td');



			}else{

					let printElement = document.getElementById(elementId);

			 tabla = $('#tpv_albaran');

			 $tabla_impre = $('#tpv_albaran1');

			  tr = $('tr:first',tabla_impre);

			  tr.find('td').remove();

			}



			}

			function move_row(elementoEnviar,idimpresion){

				trimpresion=elementoEnviar;
				var id="";
				let elementoEnviar_A = document.getElementById(elementoEnviar);
				let idimpresion_A = document.getElementById(idimpresion);
				let lineas_tabla_B = document.querySelectorAll(".inputch");


				//alert(elementoEnviar+ "," +  lineas_tabla_B.length);

			 tabla = $('#tpv_albaran');

			 tabla_impre = $('#tpv_albaran1');

			 tr = $('tr:first', tabla);

			 tr.clone().appendTo(tabla_impre).find('td');

					for (let i = 1; i < lineas_tabla_B.length; i++) {


						id = lineas_tabla_B[i].id;


					if(true)

					{
					} else {


					}
							}
									}

			function move_row_restar(elementoEnviar,idimpresion){


				$("#"+elementoEnviar).remove();


					}

			function move_row_agregar(elementoEnviar,idimpresion){


				var tabla1 = $( '#tabledetalle_1' )[0];
				var table = $( '#tabledetalle_impre' )[0];

				var $strNueva_Fila = $(tabla1).find("tbody tr:last").clone();

				$(table).find("tbody tr:last").after($strNueva_Fila);

				 var $tr = $(table).find("tbody tr:last").clone();



			}



				function move_row_1(elementoEnviar,idimpresion){



			var contador_impresion=0;

			 var contenido="";
			let id ="";




				}



			function move_row_1(elementoEnviar,idimpresion){


			var linea=elementoEnviar.length -1;


			var row = $("#"+elementoEnviar).detach();
			$("#tabledetalle_impre").append(row);




				if($("#linea1_"+linea)){

				$("#linea1_"+linea).remove();
				}

			}















			function get_combinaciones(ref)
			{
				$.ajax({
					type: 'POST',
					url: tpv_url,
					dataType: 'html',
					data: 'referencia4combi=' + ref + '&codcliente=' + document.f_tpv.cliente.value,
					success: function (datos) {
						$("#modal_articulos").modal('hide');
						$("#div_combinaciones").html(datos);
						$("#modal_combinaciones").modal('show');
					}
				});

				return false;
			}
			var hiceClick = false;
			var retr =  0000;
			var seg = 3;

			function add_referencia(ref)

			{

				if(!hiceClick)
 {

				$.ajax({
					type: 'POST',
					url: tpv_url,
					dataType: 'html',
					data: 'add_ref=' + ref + '&numlineas=' + numlineas + '&codcliente=' + document.f_tpv.cliente.value + '&defaulimpresion=' +  defaultimpre,
					beforeSend: function() {
							hiceClick=true;
					},
					success: function(datos) {
						setTimeout(function() {
							delaySuccess_ref(datos);
						}, retr);

					}
							});


				return false;
			}

			retr = 1000;
}




			function add_combinacion(ref, desc, pvp, dto, codimpuesto, codigo)
			{



				$.ajax({
					type: 'POST',
					url: tpv_url,
					dataType: 'html',
					data: 'add_ref=' + ref + '&desc=' + desc + '&pvp=' + pvp + '&dto=' + dto + '&codimpuesto=' + codimpuesto
							+ '&numlineas=' + numlineas + '&codcliente=' + document.f_tpv.cliente.value + '&codcombinacion=' + codigo,

				      beforeSend: function() {
				    //    $("#group-panel-ajax").append(res.loader);
				      },
				      success: function(datos) {
				        setTimeout(function() {

				          delaySuccess(datos);
				        }, delay);
				      }
				    });
				    return false;
				  }

					function delaySuccess(data) {

	  //$("#group-panel-ajax").find(res.loader).remove();
	  $('#group-panel-ajax').html($(data).find("#group-panel-ajax"));

		if (datos.indexOf('<!--no_encontrado-->') != -1) {
		alert('¡Artículo no encontrado!');
	} else {
		$('#tabs_tpv a:first').tab('show');

		if (numlineas == 1) {
			$("#tpv_albaran").html(datos);
		} else {
			$("#tpv_albaran").prepend(datos);
		}

		recalcular();
	}

	$("#modal_articulos").modal('hide');
	$("#modal_combinaciones").modal('hide');
	document.f_tpv.codbar.focus();



	return false;




	}


					function delaySuccess_ref(datos) {



						if (datos.indexOf('<!--no_encontrado-->') != -1) {
							bootbox.alert('¡Artículo no encontrado!');

						} else {

							$('#tabs_tpv a:first').tab('show');

							if (numlineas == 1) {

								$("#tpv_albaran").html(datos);

							} else {

			   $("#tpv_albaran").prepend(datos);

							}



							recalcular();
						}

						$("#modal_articulos").modal('hide');

						if(volver_familias) {

							$('#tabs_catalogo a:first').tab('show');
						}

						document.f_tpv.codbar.focus();

						hiceClick=false;
				return false;

						}




	$(function() {
	  var delay = 2000;

	  var res = {
	    loader: $("<div />", {
	      class: "loader"
	    })
	  };

	});



			function recalcular()
			{
				var l_uds = 0;
				var l_pvp = 0;
				var l_iva = 0;
				var l_codimpuesto = null;
				var l_re = 0;
				var neto = 0;
				var total_iva = 0;
				var total_re = 0;
				var total_irpf = 0;
				var total_lineas = 0;
				var total_articulos = 0;

				for (var i = 1; i <= numlineas + 100; i++) {
					if ($("#linea_" + i).length > 0) {
						l_uds = parseFloat($("#cantidad_" + i).val());
						l_pvp = parseFloat($("#pvp_" + i).val());
						l_iva = parseFloat($("#iva_" + i).val());
						l_codimpuesto = parseFloat($("#codimpuesto_" + i).val());
						$("#pvpt_" + i).val(fs_round(l_uds * l_pvp * (100 + l_iva) / 100, fs_nf0));
						neto += l_uds * l_pvp;

						l_re = 0;
						if (cliente.recargo) {
							for (var i = 0; i < all_impuestos.length; i++) {
								if (all_impuestos[i].codimpuesto == l_codimpuesto) {
									l_re = all_impuestos[i].recargo;
									break;
								}
							}
						}

						total_iva += l_uds * l_pvp * l_iva / 100;
						total_re += l_uds * l_pvp * l_re / 100;
						total_irpf += l_uds * l_pvp * irpf / 100;
						total_lineas++;
						total_articulos += l_uds;

						if (i >= numlineas) {
							numlineas = i + 1;
						}
					}
				}

				neto = fs_round(neto, fs_nf0);
				total_iva = fs_round(total_iva, fs_nf0);
				total_re = fs_round(total_re, fs_nf0);
				total_irpf = fs_round(total_irpf, fs_nf0);

				$("#tpv_total").html(show_precio(neto + total_iva + total_re - total_irpf));
				$("#tpv_total2").val(fs_round(neto + total_iva + total_re - total_irpf, fs_nf0));
				$("#total_lineas").html(total_lineas);
				$("#total_articulos").html(total_articulos);

				set_cache_lineas();
			}

			function set_cache_lineas()
			{
				/// comprobamos si el navegador soporta localstorage
				if (typeof (Storage) !== "undefined" && usar_cache) {
					lineas_cache = '';

					for (var i = 1; i <= numlineas + 100; i++) {
						/// solamente nos guardamos la caché si hay alguna linea de venta
						if ($("#linea_" + i).length > 0) {
							lineas_cache = $("#tpv_albaran").html();
							localStorage.setItem("tpv_tactil_lineas", lineas_cache);
							break;
						}
					}
				}
			}

			function clean_cache_lineas()
			{
				/// comprobamos si el navegador soporta localstorage
				if (typeof (Storage) !== "undefined") {
					lineas_cache = '';
					localStorage.removeItem("tpv_tactil_lineas");
				}
			}

			function set_pvpi(num)
			{
				l_pvpi = parseFloat($("#pvpi_" + num).val());
				l_iva = parseFloat($("#iva_" + num).val());

				$("#pvp_" + num).val(l_pvpi * 100 / (100 + l_iva));
				recalcular();
			}

			function set_pvpi_factura(num)
			{
				l_pvpi = parseFloat($("#f_pvpi_" + num).val());
				l_iva = parseFloat($("#f_iva_" + num).val());

				$("#f_pvp_" + num).val(l_pvpi * 100 / (100 + l_iva));
				recalcular_factura();
			}

			function recalcular_factura()
			{
				var l_uds = 0;
				var l_pvp = 0;
				var l_iva = 0;
				var neto = 0;
				var total_iva = 0;

				for (var i = 1; i <= 200; i++) {
					if ($("#f_linea_" + i).length > 0) {
						l_uds = parseFloat($("#f_cantidad_" + i).val());
						l_pvp = parseFloat($("#f_pvp_" + i).val());
						l_iva = parseFloat($("#f_iva_" + i).val());
						$("#f_pvpt_" + i).val(fs_round(l_uds * l_pvp * (100 + l_iva) / 100, fs_nf0));
						neto += l_uds * l_pvp;
						total_iva += l_uds * l_pvp * l_iva / 100;
					}
				}

				neto = fs_round(neto, fs_nf0);
				total_iva = fs_round(total_iva, fs_nf0);
				$("#f_total").val(fs_round(neto + total_iva, fs_nf0));
			}

			function linea_sum_ud(num, value)
			{
				var udl = parseInt($("#cantidad_" + num).val()) + parseInt(value);
				$("#cantidad_" + num).val(udl);
				recalcular();
				document.f_tpv.codbar.focus();
			}

			function send_ticket()
			{
				if (document.f_tpv.codbar.value == '') {
					if (numlineas > 1) {
						save_modal();
					} else {
						bootbox.alert('No has añadido nada para verder.');
					}
				} else {
					$.ajax({
						type: 'POST',
						url: tpv_url,
						dataType: 'html',
						data: 'codbar2=' + document.f_tpv.codbar.value + '&numlineas=' + numlineas,
						success: function (datos) {
							if (datos.indexOf('<!--no_encontrado-->') != -1) {
								bootbox.alert('¡Artículo no encontrado!');
								document.f_tpv.codbar.value = '';
							} else if (datos.indexOf('get_combinaciones(') != -1) {
								eval(datos);
								document.f_tpv.codbar.value = '';
							} else {
								if (numlineas == 1) {
									$("#tpv_albaran").html(datos);
								} else {
									$("#tpv_albaran").prepend(datos);
								}

								document.f_tpv.codbar.value = '';
								recalcular();
							}
						}
					});
				}
			}

			function save_modal()
			{
				$("#modal_guardar").modal('show');
				document.f_tpv.tpv_efectivo.focus();
			}




			function get_comanda()
			{
				var idtpv_comanda= document.getElementsByName("idtpv_comanda")[0].value;


					//alert(idtpv_comanda);
					if (numlineas > 1) {


					$.ajax({
						type: 'POST',
						url: tpv_url,
						async:    false,
						dataType: 'html',

						data: '&idtpv_comanda=' + idtpv_comanda,
						success: function (datos) {
							//alert(idtpv_comanda);
						}
					});

					return false;
				} else {
					bootbox.alert('Sin comanda asignada.');
					document.f_tpv.codbar.focus();
				}


			}


					function impresionRealizada(){


				printDiv1("tabledetalle_impre_1");


				//var checkboxes = document.getElementsByName('dinamico');
				//procesarImpresion();



				 for (var i = 0; i < numlineas; i++) {

					if($("#impresion_" + i).val() =="1"){

						$("#impresion_" + i).val("2");
					//	$("#impresion_" + i).attr('checked',true);




				 }

						}

								}




			function aparcar_ticket1()
			{
				document.getElementById("ac_cliente").focus();

				var idtpv_comanda= document.getElementsByName("idtpv_comanda")[0].value;


					//alert(idtpv_comanda);
					if (numlineas > 1) {


					$.ajax({
						type: 'POST',
						url: tpv_url,
						dataType: 'html',
						data: '&actualizarImpresion=' + idtpv_comanda,
						success: function (datos) {

						}
					});

					return false;
				} else {
					bootbox.alert('Compra sin articulos.');
					document.f_tpv.codbar.focus();
				}


			}



			function prepararTicket()
			{


				if (numlineas > 1) {

					document.f_tpv.aparcar.value = 'TRUE';

					document.f_tpv.numlineas.value = numlineas;




					$.ajax({
						type: 'POST',
						url: tpv_url,
						cache: false,
						dataType: 'html',
						data: $('form[name=f_tpv]').serialize() + '&ids=' +  listaimpresion,
						success: function (datos) {

					console.log("exito");
						}
					});


				} else {
				//	bootbox.alert('la Compra ACTUAL no se guardara.. ! Aun no has vendido nada !.');
														notificacion("success", "topCenter", "COMPRA NO GUARDADA, ! NO POSEE ARTICULOS !.");
					document.f_tpv.codbar.focus();
				}
			}



						function protegerImpresion_1(){

			//	var checkboxes = document.getElementsByName('dinamico');

				contador = 0;
				 for (var i = 0; i < numlineas; i++) {

					if($("#impresion_" + i).val() =="1"){

						//$("#impresion_" + i).attr('checked',true);

						document.getElementById("impresion_" + i).disabled = false;
						//$("#impresion_" + i).disabled('true');


						contador=+1;
					//	$("#diasHabilitados :checkbox").attr('checked',true);



				 }

			}


			//return contador;
			}


					function protegerImpresion(){

			//	var checkboxes = document.getElementsByName('dinamico');

				contador = 0;
				 for (var i = 0; i < numlineas; i++) {

					if($("#impresion_" + i).val() =="0"){

						//$("#impresion_" + i).attr('checked',true);

						document.getElementById("impresion_" + i).disabled = true;
						//$("#impresion_" + i).disabled('true');


						contador=+1;
					//	$("#diasHabilitados :checkbox").attr('checked',true);



				 }

			}


			//return contador;
			}


					function liberar_impresiones(){

			//	var checkboxes = document.getElementsByName('dinamico');

				contador = 0;
				 for (var i = 0; i < numlineas; i++) {

					if($("#impresion_" + i).val() =="0"){

						//$("#impresion_" + i).attr('checked',true);
						$("#impresion_" + i).removeAttr('disabled');
						document.getElementById("impresion_" + i).disabled = false;


						contador=+1;
					//	$("#diasHabilitados :checkbox").attr('checked',true);



				 }

			}


			//return contador;
			}


					function procesarImpresion_1(){

			//	var checkboxes = document.getElementsByName('dinamico');

				contador = 0;
				 for (var i = 0; i < numlineas; i++) {

					if($("#impresion_" + i).val() =="1"){

						//$("#impresion_" + i).attr('checked',true);
						$("#impresion_" + i).removeAttr('disabled');


						contador=+1;
					//	$("#diasHabilitados :checkbox").attr('checked',true);



				 }

			}


			//return contador;
			}


					function procesarImpresion_imprenta(){

			//	var checkboxes = document.getElementsByName('dinamico');



				 for (var i = 0; i < numlineas; i++) {

					if($("#impresion_" + i).val() =="0"){

						//$("#impresion_" + i).attr('checked',true);
						$("#impresion_" + i).removeAttr('disabled');
						$("#impresion_" + i).attr('checked',false);


					}

					if($("#impresion_" + i).val() =="2"){

						$("#impresion_" + i).attr('checked',true);
						$("#impresion_" + i).attr('disabled',false);

					}

					if($("#impresion_" + i).val() =="1"){
						$("#impresion_" + i).attr('checked',true);

						$("#impresion_" + i).attr('disabled',false);

					}

			}


			//return contador;
			}




				function procesarImpresion(){

			//	var checkboxes = document.getElementsByName('dinamico');

				contador = 0;

				 for (var i = 0; i < numlineas; i++) {

					if($("#impresion_" + i).val() =="0"){

						//$("#impresion_" + i).attr('checked',true);
						$("#impresion_" + i).removeAttr('disabled');
						$("#impresion_" + i).attr('checked',false);


					}

					if($("#impresion_" + i).val() =="2"){

						$("#impresion_" + i).attr('checked',true);
						$("#impresion_" + i).attr('disabled',false);


					}

					if($("#impresion_" + i).val() =="1"){
					//	$("#impresion_" + i).val('0');
						$("#impresion_" + i).attr('checked',true);


					}

			}


			//return contador;
			}


		 function printDiv1(elementId) {


				 obtenerfecha();
				//  ocultar();

				let printElement = document.getElementById(elementId);
				let printWindow = window.open('', '_blank');
				printWindow.document.write(document.documentElement.innerHTML);

			var css = printWindow.document.createElement("link");
			css.setAttribute("href", "plugins/tpv_tactil/view/css/impresion.css");
			css.setAttribute("rel", "stylesheet");
			css.setAttribute("type", "text/css");
			css.setAttribute("media", "print");
			printWindow.document.head.appendChild(css);


				setTimeout(() => { // Needed for large documents
				  printWindow.document.body.style.margin = '0 0';
				  printWindow.document.body.style.width = '80mm !important';
				  printWindow.document.title="Impresion Tickets";

						var montos=$("#tpv_total").text();
						$('#valorapagar').text("Total a pagar : " + " " +   "  " + montos + " ");


				printWindow.document.body.innerHTML = printElement.outerHTML;

				printWindow.document.close(); // necessary for IE >= 10
				printWindow.focus(); // necessary for IE >= 10

	 			printWindow.print();

				 printWindow.close();

				var idtpv_comanda= document.getElementsByName("idtpv_comanda")[0].value;

				var idtpv_comanda_pre = parseInt(idtpv_comanda);


				location.href =tpv_url+"&idtpv_comanda="+idtpv_comanda_pre;

				}, 500)






			  }











			function obtenerfecha(){



			var fecha = moment().format('DD/MM/YYYY hh:mm:ss');//Formateamos dicha fecha en el formato tan deseado: 'DD/MM/YYYY hh:mm:ss'


			var fechas=document.getElementById('valuefecha');
			var monto=document.getElementById('tpv_total');


				$('#valuefecha').text("   " + fecha  + " ");



				//$('#valuemesero').text($('#tpv_total').text);


			  }



		function ActualizarImpresionPrevia(){


		}



			function aparcar_ticket_impre_check()
			{

				procesarImpresion();

				document.getElementById("ac_cliente").focus();

				if (numlineas > 1) {

					var idtpv_comanda= document.getElementsByName("idtpv_comanda")[0].value;
					document.f_tpv.aparcar.value = 'TRUE';
					document.f_tpv.tpv_total.disabled = false;
					document.f_tpv.tpv_cambio.disabled = false;
					document.f_tpv.numlineas.value = numlineas;


					$.ajax({
						type: 'POST',
						url: tpv_url,
						cache: false,
						dataType: 'html',

						data: $('form[name=f_tpv]').serialize() + '&ids=' +  listaimpresion,
						success: function (datos) {

									clean_cache_lineas();


						}
					});


					//return false;


				} else {
					//bootbox.alert('la Compra ACTUAL no se guardara.. ! Aun no has vendido nada !.');
				//	document.f_tpv.codbar.focus();
				}




		}


		function aparcar_ticket_impre()
			{

				document.getElementById("ac_cliente").focus();

				if (numlineas > 1) {


					document.f_tpv.aparcar.value = 'TRUE';
					document.f_tpv.tpv_total.disabled = false;
					document.f_tpv.tpv_cambio.disabled = false;
					document.f_tpv.numlineas.value = numlineas;




					$.ajax({
						type: 'POST',
						url: tpv_url,
						cache: false,
						dataType: 'html',

						data: $('form[name=f_tpv]').serialize() + '&ids=' +  listaimpresion,
						success: function (datos) {

									clean_cache_lineas();


				if($("#id_idtpv_comanda_s").length > 0)


				{



				var idtpv_comanda= document.getElementsByName("idtpv_comanda")[0].value;

				var idtpv_comanda_pre = parseInt(idtpv_comanda) + 1;


		//	$fsc->url()}&idtpv_comanda={$value->idtpv_comanda}" />



			location.href =tpv_url+"&idtpv_comanda="+idtpv_comanda_pre;





				}else{

							var newDoc = document.open("text/html", "replace");
							newDoc.write(datos);
							newDoc.close();

							var idtpv_comanda= document.getElementsByName("idtpv_comanda")[0].value;

							var idtpv_comanda_pre = parseInt(idtpv_comanda) + 1;
							location.href =tpv_url+"&idtpv_comanda="+idtpv_comanda_pre;
				}


						}
					});


					return false;


				} else {
				//	bootbox.alert('la Compra ACTUAL no se guardara.. ! Aun no has vendido nada !.');
								notificacion("success", "topCenter", "COMPRA NO GUARDADA, ! NO POSEE ARTICULOS !.");
					document.f_tpv.codbar.focus();
				}




		}










			function aparcar_ticket()
			{
				procesarImpresion();

			//	document.getElementById("ac_cliente").focus();

				if (numlineas > 1) {

					document.f_tpv.aparcar.value = 'TRUE';
					document.f_tpv.tpv_total.disabled = false;
					document.f_tpv.tpv_cambio.disabled = false;
					document.f_tpv.numlineas.value = numlineas;




					$.ajax({
						type: 'POST',
						url: tpv_url,
						cache: false,
						dataType: 'html',
						data: $('form[name=f_tpv]').serialize() + '&ids=' +  listaimpresion,
						success: function (datos) {

							clean_cache_lineas();

							var newDoc = document.open("text/html", "replace");
							newDoc.write(datos);
							newDoc.close();


						}
					});
					$("#ac_cliente").focus();
					$("#ac_cliente").val(CajaSeleccion);



					return false;
				} else {
					//bootbox.alert('la Compra ACTUAL no se guardara.. ! Aun no has vendido nada !.');
							notificacion("success", "topCenter", "COMPRA NO GUARDADA, ! NO POSEE ARTICULOS !.");
					document.f_tpv.codbar.focus();

				}


			}





			function preimprimir_ticket()
			{
				if (numlineas > 1) {
					document.f_tpv.aparcar.value = 'TRUE';
					document.f_tpv.preimprimir.value = 'TRUE';
					document.f_tpv.tpv_total.disabled = false;
					document.f_tpv.tpv_cambio.disabled = false;
					document.f_tpv.numlineas.value = numlineas;

					$.ajax({
						type: 'POST',
						url: tpv_url,
						dataType: 'html',
						data: $('form[name=f_tpv]').serialize(),
						success: function (datos) {
							/// limpiamos la caché
							clean_cache_lineas()

							var newDoc = document.open("text/html", "replace");
							newDoc.write(datos);
							newDoc.close();
						}
					});

					return false;
				} else {
					bootbox.alert('No has vendido nada.');
					document.f_tpv.codbar.focus();
				}
			}

			function guardar_ticket()
			{
				if (numlineas > 1) {
					document.f_tpv.aparcar.value = 'FALSE';
					document.f_tpv.tpv_total.disabled = false;
					document.f_tpv.tpv_cambio.disabled = false;
					document.f_tpv.numlineas.value = numlineas;



					$.ajax({
						type: 'POST',
						url: tpv_url,
						dataType: 'html',
						data: $('form[name=f_tpv]').serialize(),
						success: function (datos) {
							/// limpiamos la caché
							clean_cache_lineas()

							var newDoc = document.open("text/html", "replace");
							newDoc.write(datos);
							newDoc.close();
						}
					});

					return false;
				} else {
					bootbox.alert('No has vendido nada.');
					document.f_tpv.codbar.focus();
				}
			}

			function mostrar_factura(id)
			{
				$.ajax({
					type: 'POST',
					url: tpv_url,
					dataType: 'html',
					data: 'get_factura=' + id,
					success: function (datos) {
						$("#div_modal_factura").html(datos);
						$("#modal_factura").modal('show');
					}
				});

				return false;
			}

			function buscar_articulos()
			{
				if (document.f_buscar_articulos.query.value == '') {
					$("#search_results").html('');
				} else {
					document.f_buscar_articulos.codcliente.value = document.f_tpv.cliente.value;

					fin_busqueda1 = false;
					$.getJSON(tpv_url, $("form[name=f_buscar_articulos]").serialize(), function (json) {
						var items = [];
						var insertar = false;
						$.each(json, function (key, val) {
							var stock = val.stockalm;
							if (val.stockalm != val.stockfis) {
								stock += ' (' + val.stockfis + ')';
							}

							var tr_aux = '<tr>';
							if (val.bloqueado) {
								tr_aux = "<tr class=\"danger\">";
							} else if (val.stockfis < val.stockmin) {
								tr_aux = "<tr class=\"warning\">";
							} else if (val.stockalm > 0) {
								tr_aux = "<tr class=\"success\">";
							}

							if (val.sevende && (val.stockalm > 0 || val.controlstock)) {
								var funcion = "add_referencia('" + val.referencia + "')";

								if (val.tipo == 'atributos') {
									funcion = "get_combinaciones('" + val.referencia + "')";
								}

								items.push(tr_aux + "<td>\n\
							  <a href=\"#\" onclick=\"" + funcion + "\">" + val.referencia + '</a> ' + val.descripcion + "</td>\n\
							  <td class=\"text-right\"><a href=\"#\" onclick=\"" + funcion + "\">" + show_pvp_iva(val.pvp * (100 - val.dtopor) / 100, val.codimpuesto) + "</a></td>\n\
							  <td class=\"text-right\">" + stock + "</td></tr>");
							} else if (val.sevende) {
								items.push(tr_aux + "<td>\n\
							  <a href=\"#\" onclick=\"alert('Sin stock.')\">" + val.referencia + '</a> ' + val.descripcion + "</td>\n\
							  <td class=\"text-right\"><a href=\"#\" onclick=\"alert('Sin stock.')\">" + show_pvp_iva(val.pvp * (100 - val.dtopor) / 100, val.codimpuesto) + "</a></td>\n\
							  <td class=\"text-right\">" + stock + "</td></tr>");
							}

							if (val.query == document.f_buscar_articulos.query.value) {
								insertar = true;
								fin_busqueda1 = true;
							}
						});

						if (items.length == 0 && !fin_busqueda1) {
							items.push("<tr><td colspan=\"4\" class=\"warning\">Sin resultados.</td></tr>");
							insertar = true;
						}

						if (insertar) {
							$("#search_results").html("<div class=\"table-responsive\"><table class=\"table table-hover\"><thead><tr>\n\
						   <th class=\"text-left eliminarlinea\">Referencia + descripción</th><th class=\"text-right\">Precio</th>\n\
						   <th class=\"text-right\">Stock</th></tr></thead>" + items.join('') + "</table></div>");
						}
					});
				}
			}

			function show_pvp_iva(pvp, codimpuesto)
			{
				var iva = 0;
				if (cliente.regimeniva != 'Exento' && !siniva) {
					for (var i = 0; i < all_impuestos.length; i++) {
						if (all_impuestos[i].codimpuesto == codimpuesto) {
							iva = all_impuestos[i].iva;
							break;
						}
					}
				}

				return show_precio(pvp + pvp * iva / 100);
			}

			function get_keyboard(id, tipo, num)
			{
				keyboard_id = id;
				keyboard_tipo = tipo;
				keyboard_num = num;

				$("#modal_keyboard").modal('show');
				$("#i_keyboard").val($("#" + keyboard_id).val());
			}

			function set_keyboard(key)
			{
				if (key == 'back') {
					var str = $("#i_keyboard").val();

					if (str.length > 0) {
						$("#i_keyboard").val(str.substring(0, str.length - 1));
					}
				} else if (key == 'clear') {
					$("#i_keyboard").val('');
				} else if (key == '+/-') {
					$("#i_keyboard").val(0 - parseFloat($("#i_keyboard").val()));
				} else if (key == 'ok') {
					$("#" + keyboard_id).val($("#i_keyboard").val());
					$("#modal_keyboard").modal('hide');

					if (keyboard_tipo == 'pvpi') {
						set_pvpi(keyboard_num);
					}
				} else {
					$("#i_keyboard").val($("#i_keyboard").val() + key);
				}
			}

			function set_keyboard2(key)
			{
				if (key == 'back') {
					var str = $("#" + keyboard2_id).val();

					if (str.length > 0) {
						$("#" + keyboard2_id).val(str.substring(0, str.length - 1));
					}
				} else if (key == 'clear') {
					$("#" + keyboard2_id).val('');
				} else {
					$("#" + keyboard2_id).val($("#" + keyboard2_id).val() + key);
				}

				if (!tesoreria) {
					if (keyboard2_id == 'tpv_efectivo') {
						$("#tpv_tarjeta").val(0);
						$("#tpv_cambio").val(0);
					} else if (keyboard2_id == 'tpv_tarjeta') {
						$("#tpv_efectivo").val(0);
						$("#tpv_cambio").val(0);
					}
				}

				if (keyboard2_id == 'tpv_efectivo') {
					calcular_cambio_efectivo();
				} else if (keyboard2_id == 'tpv_tarjeta') {
					calcular_cambio_tarjeta();
				}
			}

			function calcular_cambio_efectivo()
			{
				var cambio = 0;
				var efectivo = 0;
				if ($("#tpv_efectivo").val() != '') {
					efectivo = parseFloat($("#tpv_efectivo").val());
				}
				var tarjeta = 0;

				if (tesoreria) {
					if ($("#tpv_tarjeta").val() != '') {
						tarjeta = parseFloat($("#tpv_tarjeta").val());
					}

					cambio = efectivo + tarjeta - parseFloat($("#tpv_total2").val());
				} else {
					$("#tpv_tarjeta").val(0);
					cambio = efectivo - parseFloat($("#tpv_total2").val());
				}

				$("#tpv_cambio").val(number_format(parseFloat(cambio), 2, '.', ''));
			}

			function calcular_cambio_tarjeta()
			{
				var cambio = 0;
				var efectivo = 0;
				var tarjeta = 0;
				if ($("#tpv_tarjeta").val() != '') {
					tarjeta = parseFloat($("#tpv_tarjeta").val());
				}

				if (tesoreria) {
					if ($("#tpv_efectivo").val() != '') {
						efectivo = parseFloat($("#tpv_efectivo").val());
					}

					if (efectivo > tarjeta) {
						tarjeta = 0;
						$("#tpv_tarjeta").val(tarjeta);
					}

					if (tarjeta > efectivo + parseFloat($("#tpv_total2").val())) {
						tarjeta = parseFloat($("#tpv_total2").val()) - efectivo;
						$("#tpv_tarjeta").val(tarjeta);
					}

					cambio = efectivo + tarjeta - parseFloat($("#tpv_total2").val());
				} else {
					$("#tpv_efectivo").val(0);
					if (tarjeta > parseFloat($("#tpv_total2").val())) {
						tarjeta = parseFloat($("#tpv_total2").val());
						$("#tpv_tarjeta").val(tarjeta);
					}

					cambio = tarjeta - parseFloat($("#tpv_total2").val());
				}

				$("#tpv_cambio").val(number_format(parseFloat(cambio), 2, '.', ''));
			}

			$(document).ready(function () {
				$("#b_borrar_ticket").click(function () {
					window.location.href = "{$fsc->url()}&delete=" + prompt('Introduce el código del ticket:');
				});
				$("#b_codbar").keypress(function (e) {
					if (e.which == 13) {
						e.preventDefault();
						send_ticket();
					}
				});
				$("#b_buscar_art").click(function () {
					document.f_buscar_articulos.query.value = "";
					$("#search_results").html("");
					$("#modal_articulos").modal('show');
					document.f_buscar_articulos.query.focus();
				});

				$("#f_buscar_articulos").keyup(function () {
					buscar_articulos();
				});
				$("#f_buscar_articulos").submit(function (event) {
					event.preventDefault();
					buscar_articulos();
				});
				$("#tpv_efectivo").keyup(function (e) {
					calcular_cambio_efectivo();
				});



				function validacion(){

					if( $('.micheckbox').prop('checked') ) {
			//	alert('Seleccionado');
			}


				}

				var valorA=false;





				function activarImpre_a_imprimir(){

				liberar_impresiones();
			//	var checkboxes = document.getElementsByName('dinamico');


				 for (var i = 0; i < numlineas; i++) {

					if($("#impresion_" + i).val() =="0"){

						$("#impresion_" + i).attr('checked',false);
						$("#impresion_" + i).attr('disabled',false);

						var elemento = document.getElementById("confirmar_3");

				 }







			}

			}

				function activarImpre(){


				 for (var i = 0; i < numlineas; i++) {

					if($("#impresion_" + i).val() =="1"){

						$("#impresion_" + i).attr('checked',true);


				 }

			}

			}

			$("#clave_elimina").click(function () {

			$("#clave_elimina").on('keyup', function (e) {
		    if (e.keyCode == 13) {

							enviar_c($('#clave_elimina').val());

		    		}
		});

			});



			$("#clickableRow").click(function () {
				if (document.readyState === 'complete') {
			  // The page is fully loaded
			  activarImpre();
				Activar_botones_impre();
			}

			});




		$('#confirmar_4').click(function () {

		impresion_finalizada();

		printDiv1("tabledetalle_impre_1");

		});

			 $('#confirmar_2').click(function () {

			activarImpre_a_imprimir();

			 });





			 $("#confirmar_3").click(function () {

				 procesarImpresion_imprenta();

				 aparcar_ticket_impre();

				});

			$("#b_imprimir_1_1_1").click(function () {

			printDiv1("tabledetalle_impre_1");


			});


				function enviarComanda() {


						 var idtpv_comanda= document.getElementsByName("idtpv_comanda")[0].value;



						 $.ajax({
						type: 'get',
						url: tpv_url,
						dataType: 'html',
						data: '&idtpv_comanda=' + idtpv_comanda,
						success: function (datos) {
						//	alert(datos);


						}
					});



				}

				function actualizarvalueimpre(){



			 $(".chimpresion").each(function (index) {



			   listaCompras += $(".chimpresion").val($(this).val());

				});



				}


				function actualizarImpresion(){

				//	var checkboxes = document.getElementsByClassName("chimpresion").length;




				 var listaCompras = '';
				 var lista = '';


				/*$(".chimpresion").each(function (index) {


				 //     listaCompras += $(this).val();
			  $(this).val($(this).val());

				});*/

					$(".chimpresion").each(function (index) {


				 //     listaCompras += $(this).val();
			  //  listaitem +=  $(this).val()  + ',';
				lista +=  $(this).val()  + ',';


				});

			//	alert(lista);


				/*for (var i = 0; i < numlineas; i++) {
				var selected = [];
			$("input:checkbox[name=impresion_" +i+"]:checked").each(function(){
				selected.push($(this).val("1"));


			});
			}
			*/
			}



				$("#b_buscar_cajas").click(function () {
				$.ajax({
					type: 'POST',
					url: tpv_url,
					dataType: 'html',
					data: 'cajas=',
					success: function (datos) {
						$("#MymodalCajas").modal('show');


					}
				});

			});



			function mostrar(){





					$(".eliminarlinea").show();

				for (var i=0; i<1000; i++) {

				if ( $("#pvpt_" + i).length < 0 ) {

					$("#pvpt_" + i).show();
			}


			}

			}
			function ocultar(){

			//	$(".eliminarlinea").hide();

				for (var i=0; i<1000; i++) {


				//$("#pvpt_" + i).remove();

				if ( $("#pvpt_" + i).length > 0 ) {

					$("#pvpt_" + i).hide();

			}


			}

			}






			  window.name = "ventana_1";


				var fin = 0;



				/* PRINT */



			function imprimirElemento(elemento) {
				var data = document.getElementById(elemento);
			  var ventana = window.open('', 'PRINT', 'height=400,width=600');
			  ventana.document.write('<html><head><title>' + document.title + '</title>');
			  ventana.document.write('<link rel="stylesheet" href="plugins/tpv_tactil/view/css/impresion.css">'); //Aquí agregué la hoja de estilos
			  ventana.document.write('</head><body >');
			  ventana.document.write(data.innerHTML);
			  ventana.document.write('</body></html>');
			  ventana.document.close();
			  ventana.focus();
			  ventana.onload = function() {
			 //  ventana.print();
			  //  ventana.close();
			  };
			  return true;
			}



			function imprimir(id) {


				var data = document.getElementById(id);
				var myWindow = window.open('','_blank','height: 900px;');
				myWindow.document.write(data.innerHTML);

				//myWindow.document.write("plugins/tpv_tactil/view/css/impresion.css");

				var css = myWindow.document.createElement("link");
				css.setAttribute("href", "plugins/tpv_tactil/view/css/impresion.css");
				css.setAttribute("rel", "stylesheet");
				css.setAttribute("type", "text/css");
				css.setAttribute("media", "print");

				myWindow.document.head.appendChild(css);

				myWindow.document.title="Impresion Tickets";



			//myWindow.print();
			//myWindow.close();


			}
			/* END PRINT */


			  window.onbeforeprint = function() {

				setInterval(function(){

					if(printWindow.document.title=="ventana_1"){

					}


				}, );




			}






			  window.onafterprint = function() {

			  setInterval(function(){


					if(!printWindow.document.title=="impresion Tickets"){
						//location.reload();
					}


				}, 10000);



			}













				  $(".cajas").click(function () {



					aparcar_ticket();

					document.getElementById("ac_cliente").focus();

				//	CajaSeleccion //= //$.trim($(this).text());
					localStorage.setItem("ultima_caja", $.trim($(this).text()));

					CajaSeleccion =	localStorage.getItem("ultima_caja");

					$.ajax({
					type: 'POST',
					url: tpv_url,
					dataType: 'html',
					data: 'buscar_cliente=' + CajaSeleccion,
					success: function (datos) {
						$("#MymodalCajas").modal('hide');

					//document.f_tpv.cliente.value = CajaSeleccion;
					//document.f_tpv.cliente.text = CajaSeleccion;

						//alert(document.f_tpv.cliente.value);
							$("#ac_cliente").focus();

						var ultimacaja=	localStorage.getItem("ultima_caja");

						$("#ac_cliente").val(ultimacaja);
						$("#ac_cliente").focus();


					}
				});



			});





				$("#tpv_tarjeta").click(function () {

					keyboard2_id = 'tpv_tarjeta';
					if (!tesoreria) {
						$("#tpv_efectivo").val(0);
						$("#tpv_cambio").val(0);
					} else if ($("#tpv_efectivo").val() == '') {
						$("#tpv_efectivo").val(0);

					}
					$("#tpv_tarjeta").val(number_format(parseFloat($("#tpv_total2").val() - parseFloat($("#tpv_efectivo").val())), 2, '.', ''));

					calcular_cambio_tarjeta();
				});
				$("#tpv_tarjeta").keyup(function (e) {
					calcular_cambio_tarjeta();
				});
				$('#tpv_efectivo, #tpv_tarjeta').keypress(function (e) {
					if (e.which == 13) {
						guardar_ticket();
					}
				});

				/// comprobamos si el navegador soporta localstorage
				if (typeof (Storage) !== "undefined" && usar_cache) {
					///localStorage.removeItem("tpv_tactil_lineas");
					lineas_cache = localStorage.getItem("tpv_tactil_lineas");
					if (typeof (lineas_cache) == "string") {
						if (lineas_cache.length > 4) {
							$("#tpv_albaran").html(lineas_cache);
						} else {
							localStorage.removeItem("tpv_tactil_lineas");
						}
					}
				}
			});
