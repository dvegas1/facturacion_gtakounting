<?php
/*
 * @license MIT License
 * */

class XLSXWriter
{
	//http://www.ecma-international.org/publications/standards/Ecma-376.htm
	//http://officeopenxml.com/SSstyles.php
	//------------------------------------------------------------------
	//http://office.microsoft.com/en-us/excel-help/excel-specifications-and-limits-HP010073849.aspx
	const EXCEL_2007_MAX_ROW=1048576;
	const EXCEL_2007_MAX_COL=16384;
	//------------------------------------------------------------------
	protected $title;
	protected $subject;
	protected $author;
	protected $company;
	protected $description;
	protected $keywords = array();
	
	protected $current_sheet;
	protected $sheets = array();
	protected $temp_files = array();
	protected $cell_styles = array();
	protected $number_formats = array();

	public function __construct()
	{
		if(!ini_get('date.timezone'))
		{
			//using date functions can kick out warning if this isn't set
			date_default_timezone_set('UTC');
		}
		$this->addCellStyle($number_format='GENERAL', $style_string=null);
		$this->addCellStyle($number_format='GENERAL', $style_string=null);
		$this->addCellStyle($number_format='GENERAL', $style_string=null);
		$this->addCellStyle($number_format='GENERAL', $style_string=null);
	}

	public function setTitle($title='') { $this->title=$title; }
	public function setSubject($subject='') { $this->subject=$subject; }
	public function setAuthor($author='') { $this->author=$author; }
	public function setCompany($company='') { $this->company=$company; }
	public function setKeywords($keywords='') { $this->keywords=$keywords; }
	public function setDescription($description='') { $this->description=$description; }
	public function setTempDir($tempdir='') { $this->tempdir=$tempdir; }

	public function __destruct()
	{
		if (!empty($this->temp_files)) {
			foreach($this->temp_files as $temp_file) {
				@unlink($temp_file);
			}
		}
	}

	protected function tempFilename()
	{
		$tempdir = !empty($this->tempdir) ? $this->tempdir : sys_get_temp_dir();
		$filename = tempnam($tempdir, "xlsx_writer_");
		$this->temp_files[] = $filename;
		return $filename;
	}

	public function writeToStdOut()
	{
		$temp_file = $this->tempFilename();
		self::writeToFile($temp_file);
		readfile($temp_file);
	}

	public function writeToString()
	{
		$temp_file = $this->tempFilename();
		self::writeToFile($temp_file);
		$string = file_get_contents($temp_file);
		return $string;
	}

	public function writeToFile($filename)
	{
		foreach($this->sheets as $sheet_name => $sheet) {
			self::finalizeSheet($sheet_name);//making sure all footers have been written
		}

		if ( file_exists( $filename ) ) {
			if ( is_writable( $filename ) ) {
				@unlink( $filename ); //if the zip already exists, remove it
			} else {
				self::log( "Error in " . __CLASS__ . "::" . __FUNCTION__ . ", file is not writeable." );
				return;
			}
		}
		$zip = new ZipArchive();
		if (empty($this->sheets))                       { self::log("Error in ".__CLASS__."::".__FUNCTION__.", no worksheets defined."); return; }
		if (!$zip->open($filename, ZipArchive::CREATE)) { self::log("Error in ".__CLASS__."::".__FUNCTION__.", unable to create zip."); return; }

		$zip->addEmptyDir("docProps/");
		$zip->addFromString("docProps/app.xml" , self::buildAppXML() );
		$zip->addFromString("docProps/core.xml", self::buildCoreXML());

		$zip->addEmptyDir("_rels/");
		$zip->addFromString("_rels/.rels", self::buildRelationshipsXML());

		$zip->addEmptyDir("xl/worksheets/");
		foreach($this->sheets as $sheet) {
			$zip->addFile($sheet->filename, "xl/worksheets/".$sheet->xmlname );
		}
		$zip->addFromString("xl/workbook.xml"         , self::buildWorkbookXML() );
		$zip->addFile($this->writeStylesXML(), "xl/styles.xml" );  //$zip->addFromString("xl/styles.xml"           , self::buildStylesXML() );
		$zip->addFromString("[Content_Types].xml"     , self::buildContentTypesXML() );

		$zip->addEmptyDir("xl/_rels/");
		$zip->addFromString("xl/_rels/workbook.xml.rels", self::buildWorkbookRelsXML() );
		$zip->close();
	}

	protected function initializeSheet($sheet_name, $col_widths=array(), $auto_filter=false, $freeze_rows=false, $freeze_columns=false )
	{
		//if already initialized
		if ($this->current_sheet==$sheet_name || isset($this->sheets[$sheet_name]))
			return;

		$sheet_filename = $this->tempFilename();
		$sheet_xmlname = 'sheet' . (count($this->sheets) + 1).".xml";
		$this->sheets[$sheet_name] = (object)array(
			'filename' => $sheet_filename,
			'sheetname' => $sheet_name,
			'xmlname' => $sheet_xmlname,
			'row_count' => 0,
			'file_writer' => new XLSXWriter_BuffererWriter($sheet_filename),
			'columns' => array(),
			'merge_cells' => array(),
			'max_cell_tag_start' => 0,
			'max_cell_tag_end' => 0,
			'auto_filter' => $auto_filter,
			'freeze_rows' => $freeze_rows,
			'freeze_columns' => $freeze_columns,
			'finalized' => false,
		);
		$sheet = &$this->sheets[$sheet_name];
		$tabselected = count($this->sheets) == 1 ? 'true' : 'false';//only first sheet is selected
		$max_cell=XLSXWriter::xlsCell(self::EXCEL_2007_MAX_ROW, self::EXCEL_2007_MAX_COL);//XFE1048577
		$sheet->file_writer->write('<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n");
		$sheet->file_writer->write('<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">');
		$sheet->file_writer->write(  '<sheetPr filterMode="false">');
		$sheet->file_writer->write(    '<pageSetUpPr fitToPage="false"/>');
		$sheet->file_writer->write(  '</sheetPr>');
		$sheet->max_cell_tag_start = $sheet->file_writer->ftell();
		$sheet->file_writer->write('<dimension ref="A1:' . $max_cell . '"/>');
		$sheet->max_cell_tag_end = $sheet->file_writer->ftell();
		$sheet->file_writer->write(  '<sheetViews>');
		$sheet->file_writer->write(    '<sheetView colorId="64" defaultGridColor="true" rightToLeft="false" showFormulas="false" showGridLines="true" showOutlineSymbols="true" showRowColHeaders="true" showZeros="true" tabSelected="' . $tabselected . '" topLeftCell="A1" view="normal" windowProtection="false" workbookViewId="0" zoomScale="100" zoomScaleNormal="100" zoomScalePageLayoutView="100">');
		if ($sheet->freeze_rows && $sheet->freeze_columns) {
			$sheet->file_writer->write(      '<pane ySplit="'.$sheet->freeze_rows.'" xSplit="'.$sheet->freeze_columns.'" topLeftCell="'.self::xlsCell($sheet->freeze_rows, $sheet->freeze_columns).'" activePane="bottomRight" state="frozen"/>');
			$sheet->file_writer->write(      '<selection activeCell="'.self::xlsCell($sheet->freeze_rows, 0).'" activeCellId="0" pane="topRight" sqref="'.self::xlsCell($sheet->freeze_rows, 0).'"/>');
			$sheet->file_writer->write(      '<selection activeCell="'.self::xlsCell(0, $sheet->freeze_columns).'" activeCellId="0" pane="bottomLeft" sqref="'.self::xlsCell(0, $sheet->freeze_columns).'"/>');
			$sheet->file_writer->write(      '<selection activeCell="'.self::xlsCell($sheet->freeze_rows, $sheet->freeze_columns).'" activeCellId="0" pane="bottomRight" sqref="'.self::xlsCell($sheet->freeze_rows, $sheet->freeze_columns).'"/>');
		}
		elseif ($sheet->freeze_rows) {
			$sheet->file_writer->write(      '<pane ySplit="'.$sheet->freeze_rows.'" topLeftCell="'.self::xlsCell($sheet->freeze_rows, 0).'" activePane="bottomLeft" state="frozen"/>');
			$sheet->file_writer->write(      '<selection activeCell="'.self::xlsCell($sheet->freeze_rows, 0).'" activeCellId="0" pane="bottomLeft" sqref="'.self::xlsCell($sheet->freeze_rows, 0).'"/>');
		}
		elseif ($sheet->freeze_columns) {
			$sheet->file_writer->write(      '<pane xSplit="'.$sheet->freeze_columns.'" topLeftCell="'.self::xlsCell(0, $sheet->freeze_columns).'" activePane="topRight" state="frozen"/>');
			$sheet->file_writer->write(      '<selection activeCell="'.self::xlsCell(0, $sheet->freeze_columns).'" activeCellId="0" pane="topRight" sqref="'.self::xlsCell(0, $sheet->freeze_columns).'"/>');
		}
		else { // not frozen
			$sheet->file_writer->write(      '<selection activeCell="A1" activeCellId="0" pane="topLeft" sqref="A1"/>');
		}
		$sheet->file_writer->write(    '</sheetView>');
		$sheet->file_writer->write(  '</sheetViews>');
		$sheet->file_writer->write(  '<cols>');
		$i=0;
		if (!empty($col_widths)) {
			foreach($col_widths as $column_width) {
				$sheet->file_writer->write(  '<col collapsed="false" hidden="false" max="'.($i+1).'" min="'.($i+1).'" style="0" customWidth="true" width="'.floatval($column_width).'"/>');
				$i++;
			}
		}
		$sheet->file_writer->write(  '<col collapsed="false" hidden="false" max="1024" min="'.($i+1).'" style="0" customWidth="false" width="11.5"/>');
		$sheet->file_writer->write(  '</cols>');
		$sheet->file_writer->write(  '<sheetData>');
	}

	private function addCellStyle($number_format, $cell_style_string)
	{
		$number_format_idx = self::add_to_list_get_index($this->number_formats, $number_format);
		$lookup_string = $number_format_idx.";".$cell_style_string;
		$cell_style_idx = self::add_to_list_get_index($this->cell_styles, $lookup_string);
		return $cell_style_idx;
	}

	private function initializeColumnTypes($header_types)
	{
		$column_types = array();
		foreach($header_types as $v)
		{
			$number_format = self::numberFormatStandardized($v);
			$number_format_type = self::determineNumberFormatType($number_format);
			$cell_style_idx = $this->addCellStyle($number_format, $style_string=null);
			$column_types[] = array('number_format' => $number_format,//contains excel format like 'YYYY-MM-DD HH:MM:SS'
									'number_format_type' => $number_format_type, //contains friendly format like 'datetime'
									'default_cell_style' => $cell_style_idx,
									);
		}
		return $column_types;
	}

	public function writeSheetHeader($sheet_name, array $header_types, $col_options = null)
	{
		if (empty($sheet_name) || empty($header_types) || !empty($this->sheets[$sheet_name]))
			return;

		$suppress_row = isset($col_options['suppress_row']) ? intval($col_options['suppress_row']) : false;
		if (is_bool($col_options))
		{
			self::log( "Warning! passing $suppress_row=false|true to writeSheetHeader() is deprecated, this will be removed in a future version." );
			$suppress_row = intval($col_options);
		}
    $style = &$col_options;

		$col_widths = isset($col_options['widths']) ? (array)$col_options['widths'] : array();
		$auto_filter = isset($col_options['auto_filter']) ? intval($col_options['auto_filter']) : false;
		$freeze_rows = isset($col_options['freeze_rows']) ? intval($col_options['freeze_rows']) : false;
		$freeze_columns = isset($col_options['freeze_columns']) ? intval($col_options['freeze_columns']) : false;
		self::initializeSheet($sheet_name, $col_widths, $auto_filter, $freeze_rows, $freeze_columns);
		$sheet = &$this->sheets[$sheet_name];
		$sheet->columns = $this->initializeColumnTypes($header_types);
		if (!$suppress_row)
		{
			$header_row = array_keys($header_types);      

			$sheet->file_writer->write('<row collapsed="false" customFormat="false" customHeight="false" hidden="false" ht="12.1" outlineLevel="0" r="' . (1) . '">');
			foreach ($header_row as $c => $v) {
				$cell_style_idx = empty($style) ? $sheet->columns[$c]['default_cell_style'] : $this->addCellStyle( 'GENERAL', json_encode(isset($style[0]) ? $style[$c] : $style) );
				$this->writeCell($sheet->file_writer, 0, $c, $v, $number_format_type='n_string', $cell_style_idx);
			}
			$sheet->file_writer->write('</row>');
			$sheet->row_count++;
		}
		$this->current_sheet = $sheet_name;
	}

	public function writeSheetRow($sheet_name, array $row, $row_options=null)
	{
		if (empty($sheet_name))
			return;

		self::initializeSheet($sheet_name);
		$sheet = &$this->sheets[$sheet_name];
		if (count($sheet->columns) < count($row)) {
			$default_column_types = $this->initializeColumnTypes( array_fill($from=0, $until=count($row), 'GENERAL') );//will map to n_auto
			$sheet->columns = array_merge((array)$sheet->columns, $default_column_types);
		}
		
		if (!empty($row_options))
		{
			$ht = isset($row_options['height']) ? floatval($row_options['height']) : 12.1;
			$customHt = isset($row_options['height']) ? true : false;
			$hidden = isset($row_options['hidden']) ? (bool)($row_options['hidden']) : false;
			$collapsed = isset($row_options['collapsed']) ? (bool)($row_options['collapsed']) : false;
			$sheet->file_writer->write('<row collapsed="'.($collapsed).'" customFormat="false" customHeight="'.($customHt).'" hidden="'.($hidden).'" ht="'.($ht).'" outlineLevel="0" r="' . ($sheet->row_count + 1) . '">');
		}
		else
		{
			$sheet->file_writer->write('<row collapsed="false" customFormat="false" customHeight="false" hidden="false" ht="12.1" outlineLevel="0" r="' . ($sheet->row_count + 1) . '">');
		}

		$style = &$row_options;
		$c=0;
		foreach ($row as $v) {
			$number_format = $sheet->columns[$c]['number_format'];
			$number_format_type = $sheet->columns[$c]['number_format_type'];
			$cell_style_idx = empty($style) ? $sheet->columns[$c]['default_cell_style'] : $this->addCellStyle( $number_format, json_encode(isset($style[0]) ? $style[$c] : $style) );
			$this->writeCell($sheet->file_writer, $sheet->row_count, $c, $v, $number_format_type, $cell_style_idx);
			$c++;
		}
		$sheet->file_writer->write('</row>');
		$sheet->row_count++;
		$this->current_sheet = $sheet_name;
	}

	public function countSheetRows($sheet_name = '')
	{
		$sheet_name = $sheet_name ?: $this->current_sheet;
		return array_key_exists($sheet_name, $this->sheets) ? $this->sheets[$sheet_name]->row_count : 0;
	}

	protected function finalizeSheet($sheet_name)
	{
		if (empty($sheet_name) || $this->sheets[$sheet_name]->finalized)
			return;

		$sheet = &$this->sheets[$sheet_name];

		$sheet->file_writer->write(    '</sheetData>');

		if (!empty($sheet->merge_cells)) {
			$sheet->file_writer->write(    '<mergeCells>');
			foreach ($sheet->merge_cells as $range) {
				$sheet->file_writer->write(        '<mergeCell ref="' . $range . '"/>');
			}
			$sheet->file_writer->write(    '</mergeCells>');
		}

		$max_cell = self::xlsCell($sheet->row_count - 1, count($sheet->columns) - 1);

		if ($sheet->auto_filter) {
			$sheet->file_writer->write(    '<autoFilter ref="A1:' . $max_cell . '"/>');			
		}

		$sheet->file_writer->write(    '<printOptions headings="false" gridLines="false" gridLinesSet="true" horizontalCentered="false" verticalCentered="false"/>');
		$sheet->file_writer->write(    '<pageMargins left="0.5" right="0.5" top="1.0" bottom="1.0" header="0.5" footer="0.5"/>');
		$sheet->file_writer->write(    '<pageSetup blackAndWhite="false" cellComments="none" copies="1" draft="f