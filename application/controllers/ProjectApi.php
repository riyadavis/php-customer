<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProjectApi extends CI_Controller {

	public function __construct()
	{
		parent :: __construct();
		header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Credentials: true');    
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
		$this->load->model('ProjectDatabase');	
	}
	public function index()
	{
		
	}

	public function insertProduct()
	{
		$data['items'] = $this->ProjectDatabase->insertProduct();
		$this->load->view('API/json_data',$data);
	}

	public function addCart()
	{
		$iduser = $this->input->get('id'); 
		$data['items'] = $this->ProjectDatabase->addCart($iduser);
		$this->load->view('API/json_data',$data);
	}

	public function getCoupon()
	{
		$data['items'] = $this->ProjectDatabase->getCoupon();
		$this->load->view('API/json_data',$data);
	}

	public function RetrieveCart()
	{
		$userid = $this->input->get('q');
		$data['items'] = $this->ProjectDatabase->RetrieveCart($userid);
		$this->load->view('API/json_data',$data);
	}

	public function CouponActivate()
	{
		$arr = array('couponCode' => $this->input->post('couponCode'),
						'userid' => $this->input->post('userId'));
		$data['items'] = $this->ProjectDatabase->CouponActivate($arr);
		$this->load->view('API/json_data',$data);
	}

	public function PlaceOrder()
	{
		$userid = $this->input->get('q');
		$data['items'] = $this->ProjectDatabase->PlaceOrder($userid);
		$this->load->view('API/json_data',$data);
	}

	public function InsertOrder()
	{
		$items = json_decode(file_get_contents("php://input"), true);
		$userid = $this->input->get('q');
		$cartId = $this->input->post('cartId');
		$data['items'] = $this->ProjectDatabase->InsertOrder($userid,$items,$cartId);
		$this->load->view('API/json_data',$data);
	}

	public function confirmMessage()
	{
		$this->load->view('vendor/autoload.php');
		$options = array(
			'cluster' => 'ap2',
			'useTLS' => true
			);
			$pusher = new Pusher\Pusher(
			'e6256b34427ca9b29815',
			'e1a37e8c0910ae055d3b',
			'838370',
			$options
			);
	
			$data['message'] = 'Your Order is confirmed';
			$pusher->trigger('my-channel', 'my-event', $data);
	}

	public function InsertAddress()
    {
		$id = $this->input->get('id');
        
		$insertAddress = array( 'customerName'=>$this->input->post('customerName'),
								'customerAddress'=>$this->input->post('customerAddress'),
                                'deliveryPincode'=>$this->input->post('deliveryPincode'),
                                'landmark'=>$this->input->post('landmark'),
                                'mobileNumber'=>$this->input->post('mobileNumber'),
                               );
		$data['items'] = $this->ProjectDatabase->InsertAddress($id,$insertAddress);
		$this->load->view('API/json_data',$data);
	}
	
	public function retrieveNumber()
    {      
		$id = $this->input->get('q');
		$data['items'] = $this->ProjectDatabase->retrieveNumber($id);
        $this->load->view('API/json_data',$data);
	}
	
	public function searchResult()
	{
		$searchItem = $this->input->get('q');
		$data['items'] = $this->ProjectDatabase->searchResult($searchItem);
		$this->load->view('API/json_data',$data);
	}

	public function shopsWithinRadius()
	{
		$coor = array('lat'=> $this->input->post('lat'),
					'lng' => $this->input->post('long'));
		$keyword = $this->input->post('keyword');//keyword can be shop or product
		$data['items'] = $this->ProjectDatabase->shopsWithinRadius($coor,$keyword);
		$this->load->view('API/json_data',$data);
	}

}
