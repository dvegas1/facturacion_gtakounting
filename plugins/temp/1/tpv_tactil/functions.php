<?php

/**
 * @author Carlos García Gómez      neorazorx@gmail.com
 * @copyright 2017, Carlos García Gómez. All Rights Reserved. 
 * @copyright 2017, Jorge Casal Lopez. All Rights Reserved.
 */

if( !function_exists('tpv_recambios_new_search') )
{
   function tpv_recambios_new_search(&$db, &$results)
   {
      require_model('articulo_codbarras.php');
      
      $art0 = new articulo();
      $artcod = new articulo_codbarras();
      foreach($artcod->search($_REQUEST['query']) as $ac)
      {
         $articulo = $art0->get($ac->referencia);
         if($articulo)
         {
            $articulo->codbarras = $ac->codbarras;
            
            $results[] = $articulo;
            break;
         }
      }
   }
}