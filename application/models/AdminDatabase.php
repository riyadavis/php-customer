<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminDatabase extends CI_Model {

    public function adminLogin($loginDetails)
    { 
        $this->db->trans_start();
            $loginStatus = $this->db->get_where('admin',$loginDetails)->result();
        $this->db->trans_complete();
        if($loginStatus)
        {
            return ["error"=>false, "reason"=>"Login Successful"];
        }
        else
        {
            return ["error"=>true, "reason"=>"Login Failed"];
        }
    }

    public function addProduct($product)
    {
        $this->db->trans_start();
            $this->db->insert('product',$product);
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            return ["error"=>false, "reason"=>"Product Added"];
        }
        else
        {
            return ["error"=>true, "reason"=>"Add Product Failed"];
        }
    }

    public function deleteProduct($deleteId)
    {
        $this->db->trans_start();
            $this->db->where('id',$deleteId);
            $this->db->delete('product');
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            return ["error"=>false,"reason"=>"Product Deleted"];
        }
        else
        {
            return ["error"=>true,"reason"=>"Delete Product Failed"];
        }
    }

    public function updateProduct($updateId,$product)
    {
        $this->db->trans_start();
            $this->db->where('id',$updateId);
            $this->db->update('product',$product);
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            return ["error"=>false,"reason"=>"Product Updated"];
        }
        else
        {
            return ["error"=>true,"reason"=>"Update Product Failed"];
        }
    }

    public function viewAllOrders()
    {
        $this->db->trans_start();
                        $this->db->order_by('time_stamp', 'DESC');
            $allOrder = $this->db->get('customer_order')->result_array();
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            if($allOrder)
            {
                return ["error"=>false, "reason"=>$allOrder];
            }
            else
            {
                return ["error"=>true, "reason"=>"No orders"];
            }
        }
        else
        {
            return ["error"=>true,"reason"=>"View All Order Failed"];
        }
    }

    public function viewAcceptedOrders()
    {
        $this->db->trans_start();
                $this->db->order_by('time_stamp','DESC');
                $this->db->where('Accepted','true');
            $acceptedOrders = $this->db->get('customer_order')->result_array();
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            if($acceptedOrders)
            {
                return ["error"=>false, "reason"=>$acceptedOrders];
            }
            else
            {
                return ["error"=>true, "reason"=>"No Accepted orders"];
            }
        }
        else
        {
            return ["error"=>true, "reason"=>"View Accepted Order Failed"];
        }
    }

    public function viewCancelledOrders()
	{
        $this->db->trans_start();
            $this->db->order_by('time_stamp','DESC');
            $this->db->where('Cancelled','true');
            $cancelledOrders = $this->db->get('customer_order')->result_array();
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            if($cancelledOrders)
            {
                return ["error"=>false, "reason"=>$cancelledOrders];
            }
            else
            {
                return ["error"=>true, "reason"=>"No Cancelled Orders"];
            }
        }
        else
        {
            return ["error"=>true, "reason"=>"Failed to load Cancelled Orders"];
        }
    }

    public function viewPendingOrders()
    {
        $whereClause = array('Accepted'=>'false',
                            'Cancelled'=>'false');
        $this->db->trans_start();
            $this->db->order_by('time_stamp','DESC');
            $this->db->where($whereClause);
            $pendingOrders = $this->db->get('customer_order')->result_array();
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            if($pendingOrders)
            {
                return ["error"=>false, "reason"=>$pendingOrders];
            }
            else
            {
                return ["error"=>true, "reason"=>"No Pending Orders"];
            }
        }
        else
        {
            return ["error"=>true, "reason"=>"Database Error"];
        }
    }

    public function cancelOrder($orderId)
    {
        $this->db->trans_start();
            $this->db->where('id',$orderId);
            $this->db->set('Cancelled','true');
            $this->db->set('Accepted', 'false');
            $this->db->update('customer_order');
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            return ["error"=>false, "reason"=>"Order Cancelled"];
        }
        else
        {
            return ["error"=>true, "reason"=>"Failed to cancel orders"];
        }
    }

    public function acceptOrder($orderId)
    {
        $this->db->trans_start();
            $this->db->where('id',$orderId);
            $this->db->set('Accepted','true');
            $this->db->set('Cancelled','false');
            $this->db->update('customer_order');
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            return ["error"=>false, "reason"=>"Order Accepted"];
        }
        else
        {
            return ["error"=>true, "reason"=>"Failed to Accept Order"];
        }
    }

    public function floatingCash($distId)
    {
        $this->db->trans_start();
            $this->db->where('dist_id',$distId);
            $this->db->where_not_in('hub_status',"amount sent");
            $this->db->select_sum('amount');
           $amount = $this->db->get('floating_cash')->result_array();
        $this->db->trans_complete();
        if($this->db->trans_status() === true)
        {
            return ["error"=>false, "reason"=> $amount];
        }
        else
        {
            return ["error"=>true, "reason"=>"Failed to Process Floating cash"];
        }
    }
}