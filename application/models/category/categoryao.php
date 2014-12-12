<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CategoryAo extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->model('category/categoryDb','categoryDb');
	}

	public function search($where,$limit){
		return $this->categoryDb->search($where,$limit);
	}

	public function get($categoryId){
		return $this->categoryDb->get($categoryId);
	}
	
	public function del($categoryId){
		return $this->categoryDb->del($categoryId);
	}
	
	public function add($data){
		return $this->categoryDb->add($data);
	}
	
	public function mod($categoryId,$data){
		return $this->categoryDb->mod($categoryId,$data);
	}

}
