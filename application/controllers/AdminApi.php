<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminApi extends CI_Controller {

	public function __construct()
	{
		parent :: __construct();
		header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Credentials: true');    
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
		$this->load->model('AdminDatabase');	
	}

	public function index()
	{
		
	}

	public function adminLogin()
	{
		$loginDetails = array('userName'=>$this->input->post('username'),
								'password'=>$this->input->post('password')
							);
		$data['items'] = $this->AdminDatabase->adminLogin($loginDetails);
		$this->load->view('API/json_data',$data);
	}

	public function addProduct()
	{
		$config['upload_path'] = './assets/images/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = 2000;
        $config['max_width'] = 1500;
        $config['max_height'] = 1500;

        $this->load->library('upload', $config);

		if (!$this->upload->do_upload('image'))
		{
            $error['items'] = array('error' => $this->upload->display_errors());

            $this->load->view('API/json_data', $error);
		} 
		else
		{
			$imageData = array('image_metadata' => $this->upload->data());
			$product = array('category_id'=>$this->input->post('category_id'),
						  'dist_id'=>$this->input->post('dist_id'),
						  'product_name'=>$this->input->post('product_name'),
						  'product_image'=>$imageData['image_metadata']['file_name'],
						  'product_price'=>$this->input->post('product_price'),
						  'max_discount'=>$this->input->post('max_discount'),
						  'min_discount'=>$this->input->post('min_discount'),
						  'product_tags'=>$this->input->post('product_tags')
						  );
			$data['items'] = $this->AdminDatabase->addProduct($product);
			$this->load->view('API/json_data',$data);
		}
	}

	public function deleteProduct()
	{
		$deleteId = $this->input->post('productId');
		$data['items'] = $this->AdminDatabase->deleteProduct($deleteId);
		$this->load->view('API/json_data',$data);
	}

	public function updateProduct()
	{
		$updateId = $this->input->post('productId');

		$config['upload_path'] = './assets/images/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = 2000;
        $config['max_width'] = 1500;
        $config['max_height'] = 1500;

        $this->load->library('upload', $config);

		if (!$this->upload->do_upload('image'))
		{
            $error['items'] = array('error' => $this->upload->display_errors());

            $this->load->view('API/json_data', $error);
		} 
		else
		{
			$imageData = array('image_metadata' => $this->upload->data());
			$product = array('category_id'=>$this->input->post('category_id'),
							'dist_id'=>$this->input->post('dist_id'),
							'product_name'=>$this->input->post('product_name'),
							'product_image'=>$imageData['image_metadata']['file_name'],
							'product_price'=>$this->input->post('product_price'),
							'max_discount'=>$this->input->post('max_discount'),
							'min_discount'=>$this->input->post('min_discount'),
							'product_tags'=>$this->input->post('product_tags')
							);
			$data['items'] = $this->AdminDatabase->updateProduct($updateId,$product);
			$this->load->view('API/json_data',$data);
		}
	}

	public function viewAllOrders()
	{
		$data['items'] = $this->AdminDatabase->viewAllOrders();
		$this->load->view('API/json_data',$data);
	}

	public function viewAcceptedOrders()
	{
		$data['items'] = $this->AdminDatabase->viewAcceptedOrders();
		$this->load->view('API/json_data',$data);
	}

	public function viewCancelledOrders()
	{
		$data['items'] = $this->AdminDatabase->viewCancelledOrders();
		$this->load->view('API/json_data',$data);
	}

	public function viewPendingOrders()
	{
		$data['items'] = $this->AdminDatabase->viewPendingOrders();
		$this->load->view('API/json_data',$data);
	}

	public function cancelOrder()
	{
		$orderId = $this->input->post('orderId');
		$data['items'] = $this->AdminDatabase->cancelOrder($orderId);
		$this->load->view('API/json_data',$data);
	}

	public function acceptOrder()
	{
		// $orderId = $this->input->post('orderId');
		// $data['items'] = $this->AdminDatabase->acceptOrder($orderId);
		$this->load->library('Pusher');
		$data = "Hello World";
		$pusher = $this->pusher->push($data);
		// $this->load->view('API/json_data',$data);
	}

	public function push()
	{
		$this->load->view('acceptOrder');
	}

	public function floatingCash()
	{
		$distId = $this->input->post('distId');
		$data['items'] = $this->AdminDatabase->floatingCash($distId);
		$this->load->view('API/json_data',$data);
	}

}
