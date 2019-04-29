$(document).ready(function() {


    $('.total_general_f').text(sumatoria('.t_general'));
    $('.total_general_f').val(sumatoria('.t_general'));


    $('.totalcventa').text(sumatoria('.t_coste'));
    $('.totalcventa').val(sumatoria('.t_coste'));


    $('.totalpventa').text(sumatoria('.t_iva'));
    $('.totalpventa').val(sumatoria('.t_iva'));

    function sumatoria(table) {
        var Qs = "";
        var sum = 0;
        $(table).each(function() {

            var Qs = $(this).text().replace('Q', '');
            var Qs1 = Qs.replace(/ /g, "");
            var Qs2 = Qs1.replace('.', '');
            var Qs3 = Qs2.replace(',', '.');




            sum += Number(Qs3);
        });

        //console.log(sum.toLocaleString().replace('.',' ')); // Displays "3,500" if in U.S. English locale

        console.log(number_format(sum, 2, ',', ' '));

        return number_format(sum, 2, ',', ' ');
    }


    function number_format(number, decimals, dec_point, thousands_point) {

        if (number == null || !isFinite(number)) {
            throw new TypeError("number is not valid");
        }

        if (!decimals) {
            var len = number.toString().split('.').length;
            decimals = len > 1 ? len : 0;
        }

        if (!dec_point) {
            dec_point = '.';
        }

        if (!thousands_point) {
            thousands_point = ',';
        }

        number = parseFloat(number).toFixed(decimals);

        number = number.replace(".", dec_point);

        var splitNum = number.split(dec_point);
        splitNum[0] = splitNum[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_point);
        number = splitNum.join(dec_point);

        return number;




    }




    /*
    $( "#generar" ).change(function() {


        $.ajax({
            type: 'POST',
            url: "plugins/facturacion_base/controller/ventas_diarias.php",
            dataType: 'html',
            data:  'get_value_download=' +  $(this).find('option:selected').val(),
            success: function (datos) {

                alert(datos);

            }
        });



      });
    */

});