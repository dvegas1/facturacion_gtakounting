<?php

/**
 * @author Carlos García Gómez      neorazorx@gmail.com
 * @copyright 2015-2017  Carlos Garcia Gomez  neorazorx@gmail.com
 * @copyright 2015-2017, Jorge Casal Lopez. All Rights Reserved.
 */
class informe_tpv extends fs_controller
{

    public $desde;
    public $hasta;
    public $mostrar;
    public $resultados;

    public function __construct()
    {
        parent::__construct(__CLASS__, 'TPV', 'informes', FALSE, TRUE);
    }

    protected function private_core()
    {
        /// declaramos los objetos sólo para asegurarnos de que existen las tablas
        $comanda = new tpv_comanda();

        $this->mostrar = 'stats';
        if (isset($_REQUEST['mostrar'])) {
            $this->mostrar = $_REQUEST['mostrar'];
        }

        if ($this->mostrar == 'listado') {
            $this->desde = Date('1-m-Y');
            $this->hasta = Date('d-m-Y', mktime(0, 0, 0, date("m") + 1, date("1") - 1, date("Y")));

            if (isset($_POST['desde'])) {
                $this->desde = $_POST['desde'];
                $this->hasta = $_POST['hasta'];
            }

            $this->resultados = $comanda->all_desde($this->desde, $this->hasta);
        }
    }

    public function stats_last_days()
    {
        $stats = array();

        $stats_pre = $this->stats_last_days_aux('tpv_comandas');
        foreach ($stats_pre as $i => $value) {
            $stats[$i] = array(
                'day' => $value['day'],
                'total' => $value['total'],
            );
        }

        return $stats;
    }

    public function stats_last_days_aux($table_name = 'tpv_comandas', $numdays = 25)
    {
        $stats = array();
        $desde = Date('d-m-Y', strtotime(Date('d-m-Y') . '-' . $numdays . ' day'));

        foreach ($this->date_range($desde, Date('d-m-Y'), '+1 day', 'd') as $date) {
            $i = intval($date);
            $stats[$i] = array('day' => $i, 'total' => 0);
        }

        if (strtolower(FS_DB_TYPE) == 'postgresql')
            $sql_aux = "to_char(fecha,'FMDD')";
        else
            $sql_aux = "DATE_FORMAT(fecha, '%d')";

        $data = $this->db->select("SELECT " . $sql_aux . " as dia, sum(total) as total
         FROM " . $table_name . " WHERE fecha >= " . $this->empresa->var2str($desde) . "
         AND fecha <= " . $this->empresa->var2str(Date('d-m-Y')) . "
         GROUP BY " . $sql_aux . " ORDER BY dia ASC;");
        if ($data) {
            foreach ($data as $d) {
                $i = intval($d['dia']);
                $stats[$i] = array(
                    'day' => $i,
                    'total' => floatval($d['total'])
                );
            }
        }
        return $stats;
    }

    public function stats_last_months()
    {
        $stats = array();
        $stats_pre = $this->stats_last_months_aux('tpv_comandas');
        $meses = array(
            1 => 'ene',
            2 => 'feb',
            3 => 'mar',
            4 => 'abr',
            5 => 'may',
            6 => 'jun',
            7 => 'jul',
            8 => 'ago',
            9 => 'sep',
            10 => 'oct',
            11 => 'nov',
            12 => 'dic'
        );

        foreach ($stats_pre as $i => $value) {
            $stats[$i] = array(
                'month' => $meses[$value['month']],
                'total' => round($value['total'], 2),
            );
        }

        return $stats;
    }

    public function stats_last_months_aux($table_name = 'tpv_comandas', $num = 11)
    {
        $stats = array();
        $desde = Date('d-m-Y', strtotime(Date('01-m-Y') . '-' . $num . ' month'));

        foreach ($this->date_range($desde, Date('d-m-Y'), '+1 month', 'm') as $date) {
            $i = intval($date);
            $stats[$i] = array('month' => $i, 'total' => 0);
        }

        if (strtolower(FS_DB_TYPE) == 'postgresql')
            $sql_aux = "to_char(fecha,'FMMM')";
        else
            $sql_aux = "DATE_FORMAT(fecha, '%m')";

        $data = $this->db->select("SELECT " . $sql_aux . " as mes, sum(total) as total
         FROM " . $table_name . " WHERE fecha >= " . $this->empresa->var2str($desde) . "
         AND fecha <= " . $this->empresa->var2str(Date('d-m-Y')) . "
         GROUP BY " . $sql_aux . " ORDER BY mes ASC;");
        if ($data) {
            foreach ($data as $d) {
                $i = intval($d['mes']);
                $stats[$i] = array(
                    'month' => $i,
                    'total' => floatval($d['total'])
                );
            }
        }
        return $stats;
    }

    public function stats_last_years()
    {
        $stats = array();

        $stats_pre = $this->stats_last_years_aux('tpv_comandas');
        foreach ($stats_pre as $i => $value) {
            $stats[$i] = array(
                'year' => $value['year'],
                'total' => round($value['total'], 2),
            );
        }

        return $stats;
    }

    public function stats_last_years_aux($table_name = 'tpv_comandas', $num = 4)
    {
        $stats = array();
        $desde = Date('d-m-Y', strtotime(Date('d-m-Y') . '-' . $num . ' year'));

        foreach ($this->date_range($desde, Date('d-m-Y'), '+1 year', 'Y') as $date) {
            $i = intval($date);
            $stats[$i] = array('year' => $i, 'total' => 0);
        }

        if (strtolower(FS_DB_TYPE) == 'postgresql')
            $sql_aux = "to_char(fecha,'FMYYYY')";
        else
            $sql_aux = "DATE_FORMAT(fecha, '%Y')";

        $data = $this->db->select("SELECT " . $sql_aux . " as ano, sum(total) as total
         FROM " . $table_name . " WHERE fecha >= " . $this->empresa->var2str($desde) . "
         AND fecha <= " . $this->empresa->var2str(Date('d-m-Y')) . "
         GROUP BY " . $sql_aux . " ORDER BY ano ASC;");
        if ($data) {
            foreach ($data as $d) {
                $i = intval($d['ano']);
                $stats[$i] = array(
                    'year' => $i,
                    'total' => floatval($d['total'])
                );
            }
        }
        return $stats;
    }

    private function date_range($first, $last, $step = '+1 day', $format = 'd-m-Y')
    {
        $dates = array();
        $current = strtotime($first);
        $last = strtotime($last);

        while ($current <= $last) {
            $dates[] = date($format, $current);
            $current = strtotime($step, $current);
        }

        return $dates;
    }
}
