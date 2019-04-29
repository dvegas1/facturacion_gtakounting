<?php

/*
 * This file is part of Akounting
 * Copyright (C) 2007-2018  Soles 
 */

require_model('divisa.php');
require_model('pais.php');
require_model('impuesto.php');
require_model('ejercicio.php');
require_model('subcuenta.php');




/**
 * Description of admin_guatemala
 *
 * 
 */
class admin_guatemala extends fs_controller
{

   public function __construct()
   {
      parent::__construct(__CLASS__, 'Guatemala', 'admin');
   }
   
   protected function private_core()
   {
           
      if( isset($_GET['opcion']) )
      {
         if($_GET['opcion'] == 'moneda')
         {
            $div0 = new divisa();
            $divisa = $div0->get('GTQ');
            if(!$divisa)
            {
               $div0->coddivisa = 'GTQ';
               $div0->codiso = '320';
               $div0->descripcion = 'QUETZAL GUATEMALTECO.';
               $div0->simbolo = 'Q.';
               $div0->tasaconv = 7.5;
               $div0->save();
            }
            
            $this->empresa->coddivisa = 'GTQ';
            if( $this->empresa->save() )
            {
               $this->new_message('Datos guardados correctamente.');
            }
         }
         else if($_GET['opcion'] == 'pais')
         {
            $pais0 = new pais();
            $pais = $pais0->get('GTM');
            if(!$pais)
            {
               $pais0->codpais = 'GTM';
               $pais0->codiso = 'GT';
               $pais0->nombre = 'Guatemala';
               $pais0->save();
            }
            
            $this->empresa->codpais = 'GTM';
            if( $this->empresa->save() )
            {
               $this->new_message('Datos guardados correctamente.');
            }
         }
         else if($_GET['opcion'] == 'regimenes')
         {
            $fsvar = new fs_var();
            if( $fsvar->simple_save('cliente::regimenes_iva', 'General,Exento,Trimestral') )
            {
               $this->new_message('Datos guardados correctamente.');
            }
         }

         else if($_GET['opcion'] == 'impuestos')
         {
             $this->set_impuestos();            
         }

      }
      // Agregar plan contable
      
   }   
   

   public function regimenes_ok()
   {
      $fsvar = new fs_var();
      $regimenes = $fsvar->simple_get('cliente::regimenes_iva');
      
      if($regimenes == 'General,Exento,Trimestral')
      {
         return TRUE;
      }
      else
      {
         return FALSE;
      }
   }

   public function impuestos_ok()
   {
      $ok = FALSE;
      
      $imp0 = new impuesto();
      foreach($imp0->all() as $i)
      {
         if($i->codimpuesto == 'IVA12')
         {
            $ok = TRUE;
            break;
         }
      }
      
      return $ok;
   }

   private function set_impuestos()
   {
      /// eliminamos los impuestos que ya existen (los de España)
      $imp0 = new impuesto();
      foreach($imp0->all() as $impuesto)
      {
         $this->desvincular_articulos($impuesto->codimpuesto);
         $impuesto->delete();
      }
      
      /// añadimos los de Guatemala
      $codimp = array("IVA12");
      $desc = array("12%");
      $recargo = 0;
      $iva = array(12);
      $cant = count($codimp);
      for($i=0; $i<$cant; $i++)
      {
         $impuesto = new impuesto();
         $impuesto->codimpuesto = $codimp[$i];
         $impuesto->descripcion = $desc[$i];
         $impuesto->recargo = $recargo;
         $impuesto->iva = $iva[$i];
         $impuesto->save();
      }
      
      $this->impuestos_ok = TRUE;
      $this->new_message('Impuestos de Guatemala añadidos.');
      
   }

   private function desvincular_articulos($codimpuesto)
   {
      $sql = "UPDATE articulos SET codimpuesto = null WHERE codimpuesto = "
              .$this->empresa->var2str($codimpuesto).';';
      
      if( $this->db->table_exists('articulos') )
      {
         $this->db->exec($sql);
      }
   }

   public function formato_divisa_ok()
   {
      if(FS_POS_DIVISA == 'left')
      {
         return TRUE;
      }
      else
      {
         return FALSE;
      }
   }
}
