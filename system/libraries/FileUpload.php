<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class CI_FileUpload{
	var $CI;
	public function __construct(){
		$this->CI = & get_instance();
	}
	
	//上传文件，移动到默认的上传位置，并生成上传的URL
	public function simpleFile( $field,$allowTypes ){
		$option = array(
			'upload_path'=>$this->CI->config->item('upload_path'),
			'max_size'=>$this->CI->config->item('upload_max_size'),
		);
		$option['field'] = $field;
		$option['allowed_types'] = $allowTypes;
		
		$result = $this->file($option);
		if( $result['code'] != 0 )
			return $result;
			
		return array(
			'code'=>0,
			'msg'=>'',
			'data'=>$this->CI->config->item('upload_url').$result['data']['file_name']
		);
	}
	
	//上传文件
	public function file( $option ){
		if( !isset($option['upload_path']))
			return array(
				'code'=>1,
				'msg'=>'缺少上传保存路径',
				'data'=>''
			);
		if( !isset($option['field']))
			return array(
				'code'=>1,
				'msg'=>'缺少上传字段',
				'data'=>''
			);
		$config = array();
		$config['upload_path'] = $option['upload_path'];
		$config['encrypt_name'] = true;
		$config['overwrite'] = false;
		if( isset($option['allowed_types']) )
			$config['allowed_types'] = $option['allowed_types'];
		if( isset($option['max_size']))
			$config['max_size'] = $option['max_size']/1024;
		$this->CI->load->library('upload', $config);
		if( ! $this->CI->upload->do_upload($option['field'])){
			return array(
				'code' =>1,
				'msg'=>$this->CI->upload->display_errors(),
				'data'=>''
			);
		}
		
		$data = $this->CI->upload->data();
		return array(
			'code'=>0,
			'msg'=>'',
			'data'=>$data,
		);
	}
	
	private function imageBase64Upload( $option ){
		//校验文件字段
		if( !isset($option['upload_path']))
			return array(
				'code'=>1,
				'msg'=>'缺少上传保存路径',
				'data'=>''
			);
		if( isset($_POST[$option['field']]) == false )
			return array(
				'code'=>1,
				'msg'=>'请选择文件上传',
				'data'=>''
			);
		$data = base64_decode($_POST[$option['field']]);
		
		//校验文件大小
		if( isset($option['max_size']) && $option['max_size'] < strlen($data))
			return array(
				'code'=>1,
				'msg'=>'文件超过'.($option['max_size']/1024).'KB',
				'data'=>''
			);
			
		//保存文件
		$uniqueName = md5(uniqid());
		$fileName = $uniqueName;
		$fileAddress = $option['upload_path'].'/'.$fileName ;
		$file = fopen($fileAddress,"wb");
		fwrite($file,$data);
		fclose($file);
		
		//读取图片文件信息
		$result = @getimagesize($fileAddress);
		if( $result == false ){
			$isImage = false;
			$imageWidth = 0;
			$imageHeight = 0;
			$imageFormat = '';
			$imageSizeStr = '';
			$fileType = '';
			$fileExt = '';
		}else{
			$isImage = true;
			$imageWidth = $result[0];
			$imageHeight = $result[1];
			if( $result[2] == 1 )
				$imageFormat = 'gif';
			else if( $result[2] == 2 )
				$imageFormat = 'jpg';
			else
				$imageFormat = 'png';
			$fileType = 'image/'.$imageFormat;
			$fileExt = '.'.$imageFormat;
			$imageSizeStr = $result[3];
			@rename($fileAddress,$fileAddress.'.'.$imageFormat);
			$fileAddress = $fileAddress.'.'.$imageFormat;
			$fileName = $fileName.'.'.$imageFormat;
		}
		
		return array(
			'code'=>0,
			'msg'=>'',
			'data'=>array(
				'file_name'=>$fileName,
				'file_type'=>$fileType,
				'file_path'=>$option['upload_path'],
				'full_path'=>$fileAddress,
				'raw_name'=>$uniqueName,
				'orig_name'=>$fileName,
				'client_name'=>$fileName,
				'file_ext'=>$fileExt,
				'file_size'=>strlen($data),
				'is_image'=>$isImage,
				'image_width'=>$imageWidth,
				'image_height'=>$imageHeight,
				'image_type'=>$imageFormat,
				'image_size_str'=>$imageSizeStr,
			)
		);
	}
	
	//上传图片，移动到默认的上传位置，并生成上传的URL
	public function simpleImage($field){
		$option = array(
			'upload_path'=>$this->CI->config->item('upload_path'),
			'max_size'=>$this->CI->config->item('upload_max_size'),
		);
		$option['field'] = $field;
		
		$result = $this->image($option);
		if( $result['code'] != 0 )
			return $result;
			
		return array(
			'code'=>0,
			'msg'=>'',
			'data'=>$this->CI->config->item('upload_url').$result['data']['file_name']
		);
	}
	
	//上传图片
	public function image( $option ){
		if( !isset($option['field']))
			return array(
				'code'=>1,
				'msg'=>'缺少上传字段',
				'data'=>''
			);
		$option['allowed_types'] = 'jpg|jpeg|gif|bmp|png|gif';
		if( isset($_FILES[$option['field']]))
			$result = $this->file( $option );
		else
			$result = $this->imageBase64Upload( $option );
		
		if( $result['code'] != 0 )
			return $result;
		
		if( $result['data']['is_image'] != true ){
			@unlink($result['data']['full_path']);
			return array(
				'code'=>1,
				'msg'=>'上传的是非图片文件',
				'data'=>'',
			);
		}
		
		return $result;
	}
}
?>