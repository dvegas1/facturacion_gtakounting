<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Codigofacilito extends CI_Controller {

	function  __construct() {
		parent:: __construct();

		$this->load->helper('form');

		//$this->load->helper('mi');
	}

	function index(){
		$this->load->library('menu',array('inicio','contactos','cursos'));
		$data['mi_menu'] = $this->menu->construirMenu();
		$this->load->view('codigofacilito/bienvenido',$data);

	}

	function holamundo(){
		$this->load->view('codigofacilito/bienvenido');
		$this->load->view('codigofacilito/header');
	}

	function curso(){
		$this->load->view('codigofacilito/header');
		$this->load->view('codigofacilito/formulario');
	}




}

?>