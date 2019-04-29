/**
 * @author Carlos García Gómez      neorazorx@gmail.com
 * @copyright 2015-2017, Carlos García Gómez. All Rights Reserved.
 * @copyright 2015-2017, Jorge Casal Lopez. All Rights Reserved.
 */

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

function get_articulos(codfamilia) {
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

function get_combinaciones(ref) {
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


function add_referencia(ref) {



    var divs = document.getElementsByName('almacen');

    var codalmacen_r = document.getElementsByName("almacen")[0].value;
    //var cantidad_art = document.getElementsByName("span#referencia_pastetermi2gr")[0].value;
    //$('#referencia_pastetermi2gr002').val("hola");


    var totalcanntidad = $('#referencia_' + ref + codalmacen_r).val();

    $('#referencia_' + ref + codalmacen_r).val(totalcanntidad - 1)

    console.log(totalcanntidad);



    //$('#referencia_' + codalmacen_r + ref).text(totalcanntidad - 1)

    // var cantidad_art = $(".referencia_pastetermi2gr").val();


    // var valor = $("almacen").val();
    //var valor1 = 1;

    //   alert(cantidad_art);

    $.ajax({
        type: 'POST',
        url: tpv_url,

        data: 'add_ref_validar=' + ref,
        success: function (datos) {
            var valor = parseInt(datos);

            if (datos) {

                if (valor < 1) {
                    bootbox.alert('Articulo no disponible o no se encuentra en almacenes.');
                } else {

                    añadirproducto(ref);

                }
            } else {
                bootbox.alert('Articulo no disponible o no se encuentra en almacenes.');
            }
        }
    })



}





/*  alert();

  listamin = [];
  listamin_miniatura = [];

  var min = $('.estadisticas_s');
  var miniaturas = $('.thumbnail_min');


  for (var i = 0; i < miniaturas.length; i++) {
  listamin[i] = $(miniaturas[i]).val();
  }

  for (var i = 0; i < listamin.length; i++) {
  console.log(listamin[i])
}*/




function añadirproducto(ref) {


    $.ajax({
        type: 'POST',
        url: tpv_url,
        dataType: 'html',
        data: 'add_ref=' + ref + '&numlineas=' + numlineas + '&codcliente=' + document.f_tpv.cliente.value,
        success: function (datos) {
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
            if (volver_familias) {
                // $('#tabs_catalogo a:first').tab('show');
            }

            document.f_tpv.codbar.focus();
        }
    });

    return false;

}

function add_combinacion(ref, desc, pvp, dto, codimpuesto, codigo) {
    $.ajax({
        type: 'POST',
        url: tpv_url,
        dataType: 'html',
        data: 'add_ref=' + ref + '&desc=' + desc + '&pvp=' + pvp + '&dto=' + dto + '&codimpuesto=' + codimpuesto
            + '&numlineas=' + numlineas + '&codcliente=' + document.f_tpv.cliente.value + '&codcombinacion=' + codigo,
        success: function (datos) {
            if (datos.indexOf('<!--no_encontrado-->') != -1) {
                alert('¡Artículo no encontrado!');
            } else {
                $('#tabs_tpv a:first').tab('show')

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
        }
    });

    return false;
}

function recalcular() {
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

function set_cache_lineas() {
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

function clean_cache_lineas() {
    /// comprobamos si el navegador soporta localstorage
    if (typeof (Storage) !== "undefined") {
        lineas_cache = '';
        localStorage.removeItem("tpv_tactil_lineas");
    }
}

function set_pvpi(num) {
    l_pvpi = parseFloat($("#pvpi_" + num).val());
    l_iva = parseFloat($("#iva_" + num).val());

    $("#pvp_" + num).val(l_pvpi * 100 / (100 + l_iva));
    recalcular();
}

function set_pvpi_factura(num) {
    l_pvpi = parseFloat($("#f_pvpi_" + num).val());
    l_iva = parseFloat($("#f_iva_" + num).val());

    $("#f_pvp_" + num).val(l_pvpi * 100 / (100 + l_iva));
    recalcular_factura();
}

function recalcular_factura() {
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

function linea_sum_ud(num, value) {
    var udl = parseInt($("#cantidad_" + num).val()) + parseInt(value);
    $("#cantidad_" + num).val(udl);
    recalcular();
    document.f_tpv.codbar.focus();
}

function send_ticket() {
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

function save_modal() {
    $("#modal_guardar").modal('show');
    document.f_tpv.tpv_efectivo.focus();
}

function aparcar_ticket() {
    if (numlineas > 1) {
        document.f_tpv.aparcar.value = 'TRUE';
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

function preimprimir_ticket() {
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

function guardar_ticket() {
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

function mostrar_factura(id) {
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

function buscar_articulos() {
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
               <th class=\"text-left\">Referencia + descripción</th><th class=\"text-right\">Precio</th>\n\
               <th class=\"text-right\">Stock</th></tr></thead>" + items.join('') + "</table></div>");
            }
        });
    }
}

function show_pvp_iva(pvp, codimpuesto) {
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

function get_keyboard(id, tipo, num) {
    keyboard_id = id;
    keyboard_tipo = tipo;
    keyboard_num = num;

    $("#modal_keyboard").modal('show');
    $("#i_keyboard").val($("#" + keyboard_id).val());
}

function set_keyboard(key) {
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

function set_keyboard2(key) {
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

function calcular_cambio_efectivo() {
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

function calcular_cambio_tarjeta() {
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
