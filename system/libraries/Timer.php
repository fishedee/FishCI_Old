<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class CI_Timer{
	var $CI;
	
	public function __construct()
    {
		$this->CI = & get_instance();
	}
	
	public function tick()
	{
		//不限脚本运行时间
		set_time_limit(0); 
		//计算现在有多少分钟
		$now = time();
		$nowMinutes = $now/60;
		$nowSeconds = $now%60;
		//遍历所有任务
		$timerTasks = $this->CI->config->item('timer');
		foreach( $timerTasks as $singleTimerTask ){
			$singleTimerTaskTime = $singleTimerTask['period'];
			$singleTimerTaskTask = $singleTimerTask['task'];
			$singleTimerTaskTime = $singleTimerTaskTime /60;
			if( $nowMinutes % $singleTimerTaskTime == 0 ){
				$result = $this->tickSingleTask($singleTimerTaskTask);
				if( $result['code'] != 0 )
					log_message('ERROR',$result['msg']);
			}
		}
		//调试
		log_message('DEBUG','timer tick finish');
		return array(
			'code'=>0,
			'msg'=>'',
			'data'=>''
		);
	}
	
	public function tickSingleTask($task)
	{
		$taskPath = APPPATH.'timer/'.$task.'.php';
		if( !file_exists($taskPath)){
			return array(
				'code'=>1,
				'msg'=>'unknown task path '.$taskPath,
				'data'=>''
			);
		}
		require_once($taskPath);
		return array(
			'code'=>0,
			'msg'=>'',
			'data'=>''
		);
	}
}
?>