<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Caja General
 *
 * @author Zapasoft
 */

require_model('cajas_general.php');
require_model('cajas_general_mov.php');

class caja_general extends fs_controller
{ 
   public $recogidas_model;
   public $resultado;
   public $almacenes;
   public $allow_delete;
   public $busqueda;
   public $offset;
   public $agente;
   public $cajamov_model;


   public function __construct() {
      parent::__construct(__CLASS__, 'Caja General', 'contabilidad', FALSE, TRUE);
      /// cualquier cosa que pongas aquí se ejecutará DESPUÉS de process()
   }

   /**
    * esta función se ejecuta si el usuario ha hecho login,
    * a efectos prácticos, este es el constructor
    */
   protected function private_core() {
        $this->busqueda = array(
            'contenido' => '',
            'filtro_almacen' => '',
            'desde' => '',
            'hasta' => '',
            'orden' => 'fecha'
        );
        /// ¿El usuario tiene permiso para eliminar en esta página?
        $this->allow_delete = $this->user->allow_delete_on(__CLASS__);

        //Consultamos almacenes existentes
        $almacenes = new almacen();
        $this->almacenes = $almacenes->all();
        //Conseguimos el agente
        $this->agente = $this->user->get_agente();

        //cargamos nuestro modelo vacio de tabla caja general
        $this->recogidas_model = new cajas_general();
        //Cargo el modelo de los movimientos
        $this->cajamov_model = new cajas_general_mov();        

            /************
            // BUSCAR CAJA
            * ********** */
        if (isset($_POST['filtro_almacen'])) {
            $this->busqueda['filtro_almacen'] = $_POST['filtro_almacen'];
            $this->busqueda['desde'] = $_POST['desde'];
            $this->busqueda['hasta'] = $_POST['hasta'];

            $this->resultado = $this->recogidas_model->search($this->busqueda['filtro_almacen'], $this->busqueda['desde'], $this->busqueda['hasta']);
            return;
        } elseif(isset($_POST['almacen'])) {
            /************
            // ABRIR CAJA
            * ********** */
            if ($this->recogidas_model->disponible($_POST['almacen'])) {
                $this->recogidas_model->codalmacen = $_POST['almacen'];
                $this->recogidas_model->d_inicio = floatval($_POST['d_inicio']);
                $this->recogidas_model->codagente = $this->agente->codagente;
                if ($this->recogidas_model->save()) {
                    //Genero una primera linea de entrada en la caja
                    $this->cajamov_model->concepto = 'Apertura de Caja';
                    $this->cajamov_model->apunte = floatval($_POST['d_inicio']);
                    $this->cajamov_model->caja_id = $this->recogidas_model->id;
                    $this->cajamov_model->codagente = $this->agente->codagente;
                    if($this->cajamov_model->save())
                        $this->new_message("Caja iniciada con " . $this->show_numero($this->recogidas_model->d_inicio, 2) . ' €');
                } else
                    $this->new_error_msg("¡Imposible guardar los datos de caja!");
            } else
                $this->new_error_msg("¡Caja ya abierta para este Almacen!");
        } else if (isset($_GET['delete'])) {
            /*             * ***********
              // ELIMINAR CAJA
             * ********** */
            $caja2 = $this->recogidas_model->get($_GET['delete']);
            if ($caja2) {
                //OK, ahora eliminamos todos sus apuntes
                $this->cajamov_model->delete_all($caja2->id);
                //Y ahora eliminamos
                if ($caja2->delete()) {
                    $this->new_log_msg('Caja Nº '.$_GET['delete'].' y apuntes eliminados correctamente...');
                    $this->new_message("Caja y Apuntes eliminados correctamente.");
                } else
                    $this->new_error_msg("¡Imposible eliminar la caja!");
            } else
                $this->new_error_msg("Caja no encontrada.");
        } else if (isset($_POST['cierre'])) {
            /*             * ***********
              // CERRAR CAJA
             * ********** */
            $caja2 = $this->recogidas_model->get($_POST['cierre']);
            if ($caja2) {
                $saldo = $this->cajamov_model->apuntes_suma($caja2->id);
                $contado = floatval($_POST['d_fin']);
                $descuadre = round(($contado - $saldo), 2);
                
                $caja2->f_fin = Date('d-m-Y H:i:s');
                $caja2->d_fin = floatval($_POST['d_fin']);
                $caja2->descuadre = $descuadre;
                $caja2->codagente_fin = $this->agente->codagente;
                $caja2->apuntes = $this->cajamov_model->apuntes_contar($caja2->id);

                if ($caja2->save()) {
                    $this->new_message("Caja cerrada correctamente.");
                    //Si hay descuadre lo aviso y genero linea en su caja
                    if(($descuadre)!= 0){
                        //Genero una linea del descuadre en la caja
                        $this->cajamov_model->concepto = 'Descuadre en Caja';
                        $this->cajamov_model->apunte = $descuadre;
                        $this->cajamov_model->caja_id = $caja2->id;
                        $this->cajamov_model->codagente = $this->agente->codagente;
                        if($this->cajamov_model->save())                        
                            $this->new_advice('DESCUADRE: Se ha anotado un apunte con el descuadre de la Caja Nº '.$caja2->id);
                    }
                } else
                    $this->new_error_msg("¡Imposible cerrar la caja!");
            } else
                $this->new_error_msg("Caja no encontrada.");
        }

        $this->offset = 0;
        if (isset($_GET['offset'])) {
            $this->offset = intval($_GET['offset']);
        }

        $this->resultado = $this->recogidas_model->get_all_offset($this->offset);
    }

    public function anterior_url()
   {
      $url = '';
      
      if($this->offset > 0)
      {
         $url = $this->url()."&offset=".($this->offset-FS_ITEM_LIMIT);
      }
      
      return $url;
   }
   
   public function siguiente_url()
   {
      $url = '';
      
      if( count($this->resultado) == FS_ITEM_LIMIT )
      {
         $url = $this->url()."&offset=".($this->offset+FS_ITEM_LIMIT);
      }
      
      return $url;
   }    

   private function new_log_msg($msg = FALSE, $tipo = 'caja', $alerta = FALSE)
   {
      if($msg)
      {
         $fslog = new fs_log();
         $fslog->tipo = $tipo;
         $fslog->detalle = $msg;
         $fslog->ip = $_SERVER['REMOTE_ADDR'];
         $fslog->alerta = $alerta;
         
         if($this->user)
         {
            $fslog->usuario = $this->user->nick;
         }
         
         $fslog->save();
      }
   }   
   
}
