<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Caja General Mov
 *
 * @author Zapasoft
 */

require_model('cajas_general_mov.php');

class caja_general_mov extends fs_controller
{ 
   public $caja_model;
   public $cajamov_model;
   public $cajaopen;
   public $cajaid;
   public $almacen;
   public $resultados;
   public $tipo;
   public $allow_delete;
   public $agente;
   public $apunte;

   public function __construct() {
      parent::__construct(__CLASS__, 'Caja General', 'contabilidad', FALSE, FALSE);
      /// cualquier cosa que pongas aquí se ejecutará DESPUÉS de process()
   }

   /**
    * esta función se ejecuta si el usuario ha hecho login,
    * a efectos prácticos, este es el constructor
    */
   protected function private_core() {

        /// ¿El usuario tiene permiso para eliminar en esta página?
        $this->allow_delete = $this->user->allow_delete_on(__CLASS__);

        //Cargo el modelo de la caja y la selecciono
        $this->caja_model = new cajas_general();
        //Cargo el modelo de los movimientos
        $this->cajamov_model = new cajas_general_mov();

        //Conseguimos el agente
        $this->agente = $this->user->get_agente();        

        if ($_GET['id'] != '') {
            $this->cajaid = $_GET['id'];
            $caja = $this->caja_model->get($this->cajaid);
            if ($caja) {

                //Compruebo si la caja esta o no Abierta
                $this->cajaopen = $caja->abierta();
                //Consultamos almacenes existentes
                $almacenes = new almacen();
                $this->almacen = $almacenes->get($caja->codalmacen);

                /* **********
                // MODAL APUNTE
                * ********** */
                if(isset($_REQUEST['idapunte'])){
                    $this->apunte = $this->cajamov_model->get($_REQUEST['idapunte']);
                    
                    // VISUALIZAR MODAL APUNTE
                    if (isset($_POST['ajax'])) {
                         $this->template = 'ajax_apunte';
                    }                
                    /* **********
                    // MODAL EDITAR APUNTE
                    * ********** */
                    if (isset($_POST['importe'])) {
                        if($this->apunte->concepto == 'Apertura de Caja') {
                            $this->resultados = $this->cajamov_model->get_all($this->cajaid);
                            $this->new_error_msg('OJO! Apunte inicial: NO EDITABLE...');
                            return;
                        }
                        $this->apunte->concepto = $_POST['concepto'];
                        $this->apunte->apunte = floatval($_POST['importe']);
                        $this->apunte->caja_id = $this->cajaid;
                        $this->apunte->codagente = $this->agente->codagente;
                        if( $this->apunte->save()){
                           $this->new_message('Apunte EDITADO correctamente.');
                           $this->new_log_msg('Apunte Nº '.$_REQUEST['idapunte'].' de la CAJA Nº '.$this->cajaid.' EDITADO correctamente.');
                         }
                        else
                            $this->new_error_msg('Imposible EDITAR apunte Nº '.$_REQUEST['idapunte']);
                    }
                } else if (isset($_GET['delete'])) {
                    /* **********
                    // ELIMINAMOS APUNTE
                    * ********** */                    
                    $apunte = $this->cajamov_model->get($_GET['delete']);
                    if ($apunte) {
                        if($apunte->concepto == 'Apertura de Caja') {
                            $this->resultados = $this->cajamov_model->get_all($this->cajaid);
                            $this->new_error_msg('OJO! Apunte incicial: NO se puede eliminar');
                            return;
                        }                        
                        if ($apunte->delete()) {
                            $this->new_message('Apunte ' . $_GET['delete'] . ' eliminado correctamente.');
                            $this->new_log_msg('Apunte Nº '.$_GET['delete'].' de la CAJA Nº '.$this->cajaid.' eliminado correctamente.');
                        } else
                            $this->new_error_msg('Error al eliminar el apunte ' . $_GET['delete']);
                    } else
                        $this->new_error_msg('Apunte ' . $_GET['delete'] . ' no encontrado.');
                }else if (isset($_POST['ingreso']) AND $_POST['ingreso']>0) {
                    /* **********
                    // CREAMOS APUNTE INGRESO POSITIVO
                    * ********** */  
                    $this->cajamov_model->concepto = $_POST['nota'];
                    $this->cajamov_model->apunte = floatval($_POST['ingreso']);
                    $this->cajamov_model->caja_id = $this->cajaid;
                    $this->cajamov_model->codagente = $this->agente->codagente;
                    if( $this->cajamov_model->save() ){
                        $this->new_message('Ingreso apuntado correctamente.');
                    }
                    else
                        $this->new_error_msg('Imposible guardar el ingreso.');
                }else if (isset ($_POST['pago']) AND $_POST['pago']>0) {
                    /* **********
                    // CREAMOS APUNTE PAGO CONVERTIMOS A NEGATIVO
                    * ********** */         
                    $this->cajamov_model->concepto = $_POST['nota'];
                    $apunte = floatval($_POST['pago']);
                    $this->cajamov_model->apunte = $apunte*= -1;
                    $this->cajamov_model->caja_id = $this->cajaid;
                    $this->cajamov_model->codagente = $this->agente->codagente;
                    if( $this->cajamov_model->save() ){
                        $this->new_message('Pago apuntado correctamente.');
                    }
                    else
                        $this->new_error_msg('Imposible guardar el pago.');
                }

                $this->tipo = FALSE;

                if (isset($_REQUEST['tipo'])) {
                    if ($_REQUEST['tipo'] == 'ingresos') {
                        $this->tipo = 'ingresos';
                        $this->resultados = $this->cajamov_model->ingresos($this->cajaid);
                    } else if ($_REQUEST['tipo'] == 'pagos') {
                        $this->tipo = 'pagos';
                        $this->resultados = $this->cajamov_model->pagos($this->cajaid);
                    }
                } else
                    $this->resultados = $this->cajamov_model->get_all($this->cajaid);
            } else
                $this->new_error_msg('Caja no existe, ha sido eliminada anteriormente !', 'cajamov');
        } else
            $this->new_error_msg('Caja NO seleccionada correctamente!', 'cajamov');
    }
    
   private function new_log_msg($msg = FALSE, $tipo = 'cajamov', $alerta = FALSE)
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

