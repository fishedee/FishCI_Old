<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class CI_Timer{
	var $CI;
	
	public function __construct()
    {
		$this->CI = & get_instance();
	}
	
	public function tick()
	{
		//���޽ű�����ʱ��
		set_time_limit(0); 
		//���������ж��ٷ���
		$now = time();
		$nowMinutes = $now/60;
		$nowSeconds = $now%60;
		//������������
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
		//����
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