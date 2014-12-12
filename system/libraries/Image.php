<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class CI_Image{
	var $CI;
	
	public function __construct(){
		$this->CI = & get_instance();
	}
	private function compressWithImageInfo($image,$imageInfo){
		if( $imageInfo[2] == 1 ){
			//gif图片
			$cmd = "/usr/bin/gifsicle -o $image $image >/dev/null";
		}else if( $imageInfo[2] == 2 ){
			//jpg图片
			$cmd = "/usr/bin/jpegtran -optimize -progressive -copy none -outfile $image $image >/dev/null";
		}else if( $imageInfo[2] == 3 ){
			//png图片
			$cmd = "/usr/bin/pngcrush -rem alla -brute -reduce $image $image >/dev/null";
		}else{
			return array(
				'code'=>0,
				'msg'=>'',
				'data'=>''
				);
		}
		exec($cmd);
		return array(
			'code'=>0,
			'msg'=>'',
			'data'=>''
		);
	}
	
	public function compress( $image ){
		$imageInfo = @getimagesize($image);
		if( $imageInfo == false ){
			return array(
				'code'=>1,
				'msg'=>'读取图像失败',
				'data'=>''
			);
		}
		return $this->compressWithImageInfo($image,$imageInfo);
	}
	
	public function getSizeInfoByURL($address){
		$fileAddressPathInfo = pathinfo($address);
		$fileName = $fileAddressPathInfo['basename'];
		$address = $this->CI->config->item('upload_path').'/'.$fileName;
		return $this->getSizeInfo($address);
	}
	
	public function getSizeInfo($address){
		$result = @getimagesize($address);
		if( $result == false ){
			return array(
				'code'=>1,
				'msg'=>'获取图像数据失败',
				'data'=>''
			);
		}
		
		$imageInfo = $result;
		return array(
			'code'=>0,
			'msg'=>'',
			'data'=>array(
				'width'=>$imageInfo[0],
				'height'=>$imageInfo[1]
			)
		);
	}
	
	public function resizeByURL($option){
		if( isset($option['image']) == false )
			return array(
				'code'=>1,
				'msg'=>'缺少file参数',
				'data'=>''
			);
		$fileAddressPathInfo = pathinfo($option['image']);
		$fileName = $fileAddressPathInfo['basename'];
		$option['image'] = $this->CI->config->item('upload_path').'/'.$fileName;
		
		$result = $this->resize($option);
		if( $result['code'] != 0 )
			return $result;
			
		return array(
			'code'=>0,
			'msg'=>'',
			'data'=>array(
				'url'=>$this->CI->config->item('upload_url').$result['data']['file_name'],
				'width'=>$result['data']['width'],
				'height'=>$result['data']['height']
			)
		);
	}
	
	public function resize($option){
		//校验输入参数
		if( isset($option['width']) == false 
			&& isset($option['height']) == false )
			return array(
				'code'=>1,
				'msg'=>'缺少width或height参数',
				'data'=>''
			);
		if( isset($option['image']) == false )
			return array(
				'code'=>1,
				'msg'=>'缺少file参数',
				'data'=>''
			);
		$fileAddress = $option['image'];
		
		//获取原来图像的长和宽
		$result = @getimagesize($fileAddress);
		if( $result == false ){
			return array(
				'code'=>1,
				'msg'=>'图像格式错误',
				'data'=>''
			);
		}
		$imageWidth = $result[0];
		$imageHeight = $result[1];
		
		//计算放缩的图像大小
		if( isset($option['width']) && !isset($option['height'])){
			$isOnlyResizeWidth = true;
			$isOnlyResizeHeight = false;
		}else if( !isset($option['width']) && isset($option['height'] )){
			$isOnlyResizeWidth = false;
			$isOnlyResizeHeight = true;
		}else{
			$isOnlyResizeWidth = false;
			$isOnlyResizeHeight = false;
		}
		if( $isOnlyResizeWidth ){
			$newImageWidth = $option['width'];
			$newImageHeight = $imageHeight*$newImageWidth/$imageWidth;
			$newImageCropX = 0;
			$newImageCropY = 0;
			$newImageCropWidth = 0;
			$newImageCropHeight = 0;
		}else if( $isOnlyResizeHeight ){
			$newImageHeight = $option['height'];
			$newImageWidth = $imageWidth*$newImageHeight/$imageHeight;
			$newImageCropX = 0;
			$newImageCropY = 0;
			$newImageCropWidth = 0;
			$newImageCropHeight = 0;
		}else{
			if( $imageWidth/$imageHeight > $option['width']/$option['height']){
				$newImageHeight = $option['height'];
				$newImageWidth = $imageWidth*$newImageHeight/$imageHeight;
				$newImageCropX = ($newImageWidth-$option['width'])/2;
				$newImageCropY = 0;
				$newImageCropWidth = $option['width'];
				$newImageCropHeight = $option['height'];
			}else{
				$newImageWidth = $option['width'];
				$newImageHeight = $imageHeight*$newImageWidth/$imageWidth;
				$newImageCropX = 0;
				$newImageCropY = ($newImageHeight-$option['height'])/2;
				$newImageCropWidth = $option['width'];
				$newImageCropHeight = $option['height'];
			} 
		}
		
		//放缩图像
		$fileAddressPathInfo = pathinfo($fileAddress);
		$newFileName = md5(uniqid()).'.'.$fileAddressPathInfo['extension'];
		$newFileAddress = $fileAddressPathInfo['dirname'].'/'.$newFileName;
		$config = array();
		$config['image_library'] = 'gd2';
		$config['source_image'] = $fileAddress;
		$config['new_image'] = $newFileAddress;
		$config['quality'] = '80';
		$config['width'] = $newImageWidth;
		$config['height'] = $newImageHeight;
		$config['maintain_ratio'] = true;
		$this->CI->load->library('image_lib');
		$this->CI->image_lib->initialize($config); 
		if( !$this->CI->image_lib->resize() ){
			return array(
				'code'=>1,
				'msg'=>$this->CI->image_lib->display_errors(),
				'data'=>''
			);
		}
		
		//剪裁图像
		if( $newImageCropX != 0 || $newImageCropY != 0 ){
			$config = array();
			$config['image_library'] = 'gd2';
			$config['source_image'] = $newFileAddress;
			$config['quality'] = '80';
			$config['width'] = $newImageCropWidth;
			$config['height'] = $newImageCropHeight;
			$config['x_axis'] = $newImageCropX;
			$config['y_axis'] = $newImageCropY;
			$config['maintain_ratio'] = false;
			$this->CI->image_lib->initialize($config); 
			if( !$this->CI->image_lib->crop() ){
				return array(
					'code'=>1,
					'msg'=>$this->CI->image_lib->display_errors(),
					'data'=>''
				);
			}
		}
		
		//读取新图像文件大小
		$result = @getimagesize($newFileAddress);
		if( $result == false ){
			return array(
				'code'=>1,
				'msg'=>'转换图像失败',
				'data'=>''
			);
		}
		$imageInfo = $result;
		$imageWidth = $imageInfo[0];
		$imageHeight = $imageInfo[1];
		
		//优化压缩图片大小
		$result = $this->compressWithImageInfo($newFileAddress,$imageInfo);
		if( $result['code'] != 0 )
			return $result;
		
		return array(
			'code'=>0,
			'msg'=>'',
			'data'=>array(
				'file_name'=>$newFileName,
				'file_path'=>$newFileAddress,
				'full_path'=>$newFileAddress,
				'width'=>$imageWidth,
				'height'=>$imageHeight
			)
		);
	}
}
?>