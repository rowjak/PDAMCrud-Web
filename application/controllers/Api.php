<?php

class Api extends CI_Controller {

	public function __construct(){
		parent::__construct();
	}

	function user_login(){
		$username = $this->input->post('username');
		$password = $this->input->post('password');

		$where = array (
			'username' => $username,
			'password' => sha1($password)
		);

		$cek = $this->db->where($where)->get('user')->row_array();
		if($cek){
			$resp = array(
				'status' => true,
				'message' => 'Berhasil Login',
				'data' => array(
					'username' => $cek['username'],
					'nama' => $cek['nama'],
					'level_user' => $cek['level_user']
				)
			);
		}else{
			$resp = array(
				'status' => false,
				'message' => 'Maaf, Username atau Password Salah',
			);
		}

		header('Content-Type: application/json');
		echo json_encode($resp, JSON_PRETTY_PRINT);
	}

	function load_pelanggan(){
		$lat = $this->input->get('lat');
		$lng = $this->input->get('lng');
		$plg = $this->db->get('pelanggan')->result_array();

		foreach ($plg as $p) {
			$jarak = distance($lat,$lng,$p['latitude'],$p['longitude']);

			$data[] = array(
				'id' => $p['id'],
				'id_plg' => $p['id_plg'],
				'nama' => $p['nama'],
				'alamat' => $p['alamat'],
				'email' => $p['email'],
				'mobile' => $p['mobile'],
				'latitude' => $p['latitude'],
				'longitude' => $p['longitude'],
				'jarak' => number_format((float)$jarak, 2, '.', '')." km"
			);
		}

		sort_array_by_value('jarak',$data);
		$data = array_values($data);

		// $index = 0;

		// 
		

		$hasil = array(
			'status' => true,
			'data' => $data
		);


		header('Content-Type: application/json');
		echo json_encode($hasil, JSON_PRETTY_PRINT);
	}

	function save_pelanggan(){
		$data = $this->input->post();

		if($this->db->insert('pelanggan',$data)){
			$resp = array(
				'status' => true,
				'message' => 'Berhasil Menambahkan Data'
			);
		}else{
			$resp = array(
				'status' => false,
				'message' => 'Gagal Menambahkan Data'
			);
		}

		header('Content-Type: application/json');
		echo json_encode($resp, JSON_PRETTY_PRINT);
	}

	function detail_pelanggan(){
		$id = $this->input->get('id');
		$lat = $this->input->get('latitude');
		$lng = $this->input->get('longitude');
		$data = $this->db->where('id',$id)->get('pelanggan')->row_array();

		if($data){
			$jarak = distance($lat,$lng,$data['latitude'],$data['longitude']);
			$resp = array(
				'status' => true,
				'message' => 'Data Berhasil Ditemukan',
				'data' => array(
					"id" => $data['id'],
					"id_plg" => $data['id_plg'],
					"nama"  => $data['nama'],
					"alamat"  => $data['alamat'],
					"email" => $data['email'],
					"mobile" => $data['mobile'],
					"latitude" => $data['latitude'],
					"longitude" => $data['longitude'],
					'jarak' => number_format((float)$jarak, 2, '.', '')." km"
				)
			);
		}else{
			$resp = array(
				'status' => true,
				'message' => 'Data Tidak Ditemukan'
			);
		}

		header('Content-Type: application/json');
		echo json_encode($resp, JSON_PRETTY_PRINT);
	}

	function update_pelanggan(){
		$data = $this->input->post();

		if($this->db->where('id',$data['id'])->update('pelanggan',$data)){
			$resp = array(
				'status' => true,
				'message' => 'Berhasil Memperbarui Data'
			);
		}else{
			$resp = array(
				'status' => false,
				'message' => 'Gagal Memperbarui Data'
			);
		}

		header('Content-Type: application/json');
		echo json_encode($resp, JSON_PRETTY_PRINT);
	}

	function delete_pelanggan(){
		$id = $this->input->get('id');

		if($this->db->where('id',$id)->delete('pelanggan')){
			$resp = array(
				'status' => true,
				'message' => 'Data Pelanggan Berhasil Dihapus!'
			);
		}else{
			$resp = array(
				'status' => false,
				'message' => 'Gagal Menghapus Data'
			);
		}

		header('Content-Type: application/json');
		echo json_encode($resp, JSON_PRETTY_PRINT);
	}

	function load_carousel(){
		$data['data'] = $this->db->get('carousel')->result_array();
		header('Content-Type: application/json');
		echo json_encode($data, JSON_PRETTY_PRINT);
	}
	
}
