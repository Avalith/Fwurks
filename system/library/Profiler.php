<?php

class Profiler
{
	public $time_start;
	public $time_finished;
	
	public $data;
	
	protected $db_save = false;
	protected $db;
	protected $uuid;
	
	public static $col_themes = array
	(
		'full' 				=> array('state','time','time_elapsed','time_elapsed2','load','load5','load15','memory','memory_peak','location'),
		'simple' 			=> array('state','time_elapsed2','memory'),
		'simple_location' 	=> array('state','time_elapsed2','memory','location'),
	);
	
	public function __construct($state = '', $db_save = false, $db = null)
	{
		$state || $state = 'Profiling started';
		
		$db && $this->db_save = $db_save;
		$this->db = $db;
		
		$this->uuid = sprintf( 
			'%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand( 0, 0xffff ), 
			mt_rand( 0, 0xffff ), 
			mt_rand( 0, 0xffff ),
			mt_rand( 0, 0x0fff ) | 0x4000,
			mt_rand( 0, 0x3fff ) | 0x8000,
			mt_rand( 0, 0xffff ), 
			mt_rand( 0, 0xffff ), 
			mt_rand( 0, 0xffff ) 
		);
		
		$this->time_start = $this->time();
		$trace = debug_backtrace(false);
		$load = sys_getloadavg();
		
		$data = (object)array
		(
			'state' 		=> $state, 
			'time' 			=> $this->time_start,
			'time_elapsed'	=> 0,
			'time_elapsed2'	=> 0,
			'load'			=> $load[0],
			'load5'			=> $load[1],
			'load15'		=> $load[2],
			'memory'		=> memory_get_usage(),
			'memory_peak'	=> memory_get_peak_usage(),
			'location'		=> $trace[0]['file'].': '.$trace[0]['line'], 
		);
		
		$this->data[] = $data;
		$this->add_db($data);
	}
	
	public function add($state)
	{
		$time = $this->time();
		$trace = debug_backtrace(false);
		$load = sys_getloadavg();
		
		$data = (object)array
		(
			'state' 		=> $state, 
			'time' 			=> $time,
			'time_elapsed'	=> sprintf('%.6f', $time - $this->time_start),
			'time_elapsed2'	=> sprintf('%.6f', sprintf('%.6f', $time - $this->time_start) - sprintf('%.6f', $this->data[count($this->data)-1]->time_elapsed)),
			'load'			=> $load[0],
			'load5'			=> $load[1],
			'load15'		=> $load[2],
			'memory'		=> memory_get_usage(),
			'memory_peak'	=> memory_get_peak_usage(),
			'location'		=> $trace[0]['file'].': '.$trace[0]['line'], 
		);
		
		$this->data[] = $data;
		$this->add_db($data);
	}
	
	protected function add_db($data)
	{
		if(!$this->db_save){ return; }
		
		$data->time = strftime('%Y-%m-%d %T', $data->time) . substr($data->time, strpos($data->time, '.'));
		$data->time_elapsed = $data->time_elapsed;
		$data->time_elapsed2 = $data->time_elapsed2;
		
		$this->db->query("INSERT INTO profiler VALUES ('{$this->uuid}', '{$data->state}', '{$data->time}', '{$data->time_elapsed}', '{$data->time_elapsed2}', '{$data->load}', '{$data->load5}', '{$data->load15}', '{$data->memory}', '{$data->memory_peak}', '{$data->location}')");
	}
	
	
	public function report($state = '', $cols = array())
	{
		$state || $state = 'Profiling finished';
		
		$time = $this->time();
		$trace = debug_backtrace(false);
		$load = sys_getloadavg();
		
		$data = (object)array
		(
			'state' 		=> $state, 
			'time' 			=> $time,
			'time_elapsed'	=> sprintf('%.6f', $time - $this->time_start),
			'time_elapsed2'	=> sprintf('%.6f', sprintf('%.6f', $time - $this->time_start) - sprintf('%.6f', $this->data[count($this->data)-1]->time_elapsed)),
			'load'			=> $load[0],
			'load5'			=> $load[1],
			'load15'		=> $load[2],
			'memory'		=> memory_get_usage(),
			'memory_peak'	=> memory_get_peak_usage(),
			'location'		=> $trace[0]['file'].': '.$trace[0]['line'], 
		);
		$this->data[] = $data;
		$this->add_db($data);
		
		if($this->save_db){ return; }
		
		$col_titles = (object)array
		(
			'state' 		=> 'State', 
			'time' 			=> 'Date Time',
			'time_elapsed'	=> 'Time',
			'time_elapsed2'	=> 'Time Elapsed',
			'load'			=> 'Load',
			'load5'			=> 'Load 5',
			'load15'		=> 'Load 15',
			'memory'		=> 'Memory',
			'memory_peak'	=> 'Memory Peak',
			'location'		=> 'Location',
		);
		$cols || $cols = self::$col_themes['full'];
		
		echo '<table style="clear: both; margin: 20px; font-size: 11px; font-family: Tahome, sans-serif; background: #EEE; border: 1px solid #999; border-collapse: collapse;">';
		
		echo '<thead style="background: #FFF; font-weight: bold; font-size: 130%; border-bottom: 1px solid #999;"><tr>';
		echo '<td colspan="'.count($cols).'" style="padding: 5px; text-align: center; font-size: 110%; background: #FF9;">'.$state.'</td></tr><tr>';
		foreach($cols as $c){ echo '<td style="padding: 2px 20px 2px 3px; background: #DDD; border: 1px solid #999;">'.$col_titles->$c.'</td>'; }
		echo '</tr></thead>';
		
		echo '<tbody>';
		foreach($this->data as $i => $d)
		{
			echo '<tr>';
			foreach($cols as $c){ echo '<td style="padding: 1px 20px 1px 3px; background: #'.($i%2 ? '' : 'FFF').'; border: 1px solid #999;">'.$this->{'format_'.$c}($d->$c).'</td>'; }
			echo '</tr>';
		}
		echo '</tbody>';
		
		echo '<tfoot style="background: #FF9;"><tr>';
		echo '<td colspan="'.count($cols).'" style="height: 5px"></td></tr><tr>';
		echo '</tr></tfoot>';
		
		
		echo '</table>';
	}
	
	private function format_state($s){ return '<strong>'.$s.'</strong>'; }
	private function format_time($s){ return strftime('%Y-%m-%d %T', $s) . substr($s, strpos($s, '.')); }
	private function format_time_elapsed($s){ return $s.'s'; }
	private function format_time_elapsed2($s){ return $s.'s'; }
	private function format_load($s){ return $s.'%'; }
	private function format_load5($s){ return $s.'%'; }
	private function format_load15($s){ return $s.'%'; }
	private function format_memory($s){ return sprintf('%.3f', $s/1024) . ' KB'; }
	private function format_memory_peak($s){ return sprintf('%.3f', $s/1024) . ' KB'; }
	private function format_location($s){ return substr($s, strlen(getcwd())+1); }
	
	protected function time(){ return microtime(true); }
	
	
	private function creat_table()
	{
		return 'CREATE TABLE `profiler` (`uuid` CHAR(36) NOT NULL, `state` VARCHAR(255), `time` CHAR(24), `time_elapsed` DOUBLE(10,2), `time_elapsed2` DOUBLE(10,2), `load` FLOAT(5,2), `load5` FLOAT(5,2), `load15` FLOAT(5,2), `memory` INT, `memory_peak` INT, `location` VARCHAR(255));';
	}
}

?>