<?php

/*
 * This file is part of FacturaSctipts
 * Copyright (C) 2017  Jcanda info@zapasoft.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_model('agente.php');
require_model('articulo.php');
require_model('cliente.php');
require_model('tarifas_clientes.php');

class tarifas_cliente extends fs_controller {

    public $agente;
    public $articulos;
    public $offset;
    public $cliente;
    public $tarifas_all;
    public $tarifas_select;
    public $tarifas;
    public $allow_delete;

    public function __construct() {
        parent::__construct(__CLASS__, 'Tarifas de cliente', 'ventas', FALSE, FALSE);
    }

    protected function private_core() {
        
        $this->offset = 0;
        $this->agente = new agente();
        $this->tarifas = new tarifas_clientes();
        $this->share_extension();
        
        /// ¿El usuario tiene permiso para eliminar en esta página?
        $this->allow_delete = $this->user->allow_delete_on(__CLASS__);  
        
        if( isset($_GET['codcliente']) )
        {
            //Primero seleccionamos cliente
            $cliente = new cliente();
            $this->cliente = $cliente->get($_GET['codcliente']);    
            
            //Ahora buscamos un articulo
            if( isset($_REQUEST['buscar_articulo']) ) {
                /// desactivamos la plantilla HTML
                $this->template = FALSE;
                $json = array();
                
                $articulo = new articulo();
                $this->articulos = $articulo->search($_REQUEST['buscar_articulo']);
                        
                foreach ($this->articulos as $art) {
                    $json[] = array('value' => $art->referencia.'-'.$art->descripcion, 'data' => $art->referencia);
                }

                header('Content-Type: application/json');
                echo json_encode(array('query' => $_REQUEST['buscar_articulo'], 'suggestions' => $json));

            }else if (isset($_POST['tarifa_pvp'])){
                $this->nueva_tarifa();
                //Luego Seleccionamos las tarifas mas recientes de los articulos para este cliente
                $this->tarifas_select = $this->tarifas->get_tarifas_cliente_select($_GET['codcliente']);
                //Por ultimo seleccinamos todas las tarias de precios para este cliente
                $this->tarifas_all = $this->tarifas->get_tarifas_cliente($_GET['codcliente']);  
                
            //Para eliminar 
            }else if (isset($_GET['delete_tarifa'])) {
                $tarifa = $this->tarifas->get($_GET['delete_tarifa']);
                
                if ($tarifa) {
                    if ($tarifa->delete()) {
                        $this->new_message('Tarifa eliminada correctamente.');
                    } else
                        $this->new_error_msg('Imposible eliminar la tarifa.');
                }else
                    $this->new_error_msg('Tarifa no encontrada.');

                //Luego Seleccionamos las tarifas mas recientes de los articulos para este cliente
                $this->tarifas_select = $this->tarifas->get_tarifas_cliente_select($_GET['codcliente']);
                //Por ultimo seleccinamos todas las tarias de precios para este cliente
                $this->tarifas_all = $this->tarifas->get_tarifas_cliente($_GET['codcliente']);                
                
            }else{
                //Luego Seleccionamos las tarifas mas recientes de los articulos para este cliente
                $this->tarifas_select = $this->tarifas->get_tarifas_cliente_select($_GET['codcliente']);
                //Por ultimo seleccinamos todas las tarias de precios para este cliente
                $this->tarifas_all = $this->tarifas->get_tarifas_cliente($_GET['codcliente']);                
            }
        }else{
            $this->new_error_msg('Imposible enseñar tarifas, cliente no seleccionado.');
        } 
        
    }
    
    protected function nueva_tarifa() {
        $this->agente = $this->user->get_agente();
        
        //----------------------------------------------
        // agrega una tarifa nueva para este cliente
        //----------------------------------------------
        if(!empty($_POST['tarifa_pvp'])){
            $this->tarifas->tarifa_pvp = $_POST['tarifa_pvp'];
            $this->tarifas->tarifa_codarticulo = $_POST['tarifa_codarticulo'];
            $this->tarifas->tarifa_codcliente = $this->cliente->codcliente;
            $this->tarifas->tarifa_motivo = $_POST['tarifa_motivo'];
            $this->tarifas->tarifa_agente = $this->agente->nombre;
            
            if ($this->tarifas->save()) {
                $this->new_message('Nueva Tarifa guardada correctamente.');
            } else {
                $this->new_error_msg('Imposible guardar la nueva Tarifa.');
                return FALSE;
            }            
        }else{
             $this->new_error_msg('Tarifa no guardada: Precio no especificado.');
            return FALSE;
        }
        
    }
    
    private function share_extension() {
        /// añadimos las extensiones para clientes y js para HEAD
        $extensiones = array(
            array(
                'name' => 'tarifas_cliente',
                'page_from' => __CLASS__,
                'page_to' => 'ventas_cliente',
                'type' => 'button',
                'text' => '<span class="glyphicon glyphicon-tags" title="Tarifas"></span> &nbsp; Tarifas',
                'params' => ''
            ),
            array(
                'name' => 'tablas_ordenadas_tarifas',
                'page_from' => __CLASS__,
                'page_to' => __CLASS__,
                'type' => 'head',
                'text' => '<script src="plugins/tarifas_cliente/view/js/jquery.tablesorter.min.js"></script>
                           <script src="plugins/tarifas_cliente/view/js/jquery.tablesorter.widgets.min.js"></script>',
                'params' => ''
            ),            
        );
        foreach ($extensiones as $ext) {
            $fsext = new fs_extension($ext);
            $fsext->save();
        }
    }

}
