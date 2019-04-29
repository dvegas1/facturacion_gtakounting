

$(function() {
    $('.password-group').find('.password-box').each(function(index, input) {
        var $input = $(input);
        $input.parent().find('.password-visibility').click(function() {
            var change = "";
            if ($(this).find('i').hasClass('fa-eye')) {
                $(this).find('i').removeClass('fa-eye')
                $(this).find('i').addClass('fa-eye-slash')
                change = "text";
            } else {
                $(this).find('i').removeClass('fa-eye-slash')
                $(this).find('i').addClass('fa-eye')
                change = "password";
            }
            var rep = $("<input type='" + change + "' />")
                .attr('id', $input.attr('id'))
                .attr('name', $input.attr('name'))
                .attr('class', $input.attr('class'))
                .val($input.val())
                .insertBefore($input);
            $input.remove();
            $input = rep;
        }).insertAfter($input);
    });




        $('#guardar_seguridad').click(function() {

            lbl_pass =[];

            pass = $('.password-box');

              for (var i = 0; i < pass.length; i++) {
                if ($(pass[i]).val() != "undefined") {
                lbl_pass[i] = $(pass[i]).val();
                }

                console.log(lbl_pass[i]);


              }

              enviar_p(lbl_pass);

        });


        function enviar_p(array) {

                            $.ajax({

                type: 'POST',
                url: "index.php?page=tpv_tactil",
                data: 'tp_seguridad=' + array + '&accion=' + 1,
                datatype:'JSON',
                success: function(sugerencia) {

                }

            });


        }

        function cargar_pass(){


                var array =["2","3"];

                $.ajax({
                type: 'POST',
                url: 'index.php?page=tpv_tactil',
                data: 'tp_seguridad=' + array + '&accion=' + 2,
              //  async:    false,
                 datatype: 'JSON',
                 success: function(datos) {
                // alert(datos[0].password + datos.length);


                pass = $('.password-box');


                for (var i = 0; i < datos.length; i++) {
                  $(pass[i]).val(datos[i].password);
                }






        }

        });


        }


        $('#cargar_seguridad').click(function() {

            cargar_pass();


                });








});
