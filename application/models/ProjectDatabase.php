<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProjectDatabase extends CI_Model{

    public function insertProduct()
    {
        $insertProduct = $this->db->query("select * from product")->result_array();
        return ["error"=>false, "reason"=>$insertProduct];
    }

    public function addCart($iduser)
    {
        $source_id = 1;
         
        $this->db->query("select * from customer where id = '$iduser'")->result_array();
        if($this->db->affected_rows()>0)
        {
            $items = $this->db->query("select items_added from cart_table where customer_id = '$iduser'")->result_array();
            
            //if customer id and shop id already exists
            if($this->db->affected_rows()>0)
            {
                
                    $insert = json_decode(file_get_contents("php://input"), true);
                    $this->db->trans_start();
                        $this->db->where('customer_id',$iduser);
                        $this->db->where('source_id',$source_id);
                        $this->db->set('items_added',json_encode($insert));
                        $this->db->set('time_stamp',Date('Y-m-d h:i:s'));
                        $this->db->update('cart_table');
                    $this->db->trans_complete();
                    if($this->db->trans_status() === TRUE)
                    {
                        return ["error"=>false, "reason"=>"item inserted"];
                    }
                    else
                    {
                        return ["error"=>true, "reason"=>"insert failed"];
                    }
                    
                }               
                else
                {
                    $jsonArray = json_decode(file_get_contents("php://input"), true);
                    $addCart = array('customer_id'=>$this->session->userdata('userid'),
                                    'items_added'=>json_encode($jsonArray),
                                    'source_id'=>$source_id,
                                    'time_stamp'=>Date('Y-m-d h:i:s'));
                    $this->db->trans_start();
                        $this->db->insert('cart_table',$addCart);
                    $this->db->trans_complete();
                    if($this->db->trans_status() === TRUE)
                    {
                        return ["error"=>false, "reason"=>"item inserted"];
                    }
                    else
                    {
                        return ["error"=>true, "reason"=>"insert failed"];
                    }
                }
        }
        else
        {
            return ["error"=>true, "reason"=>"invalid user"];
        }
    //    return json_decode(file_get_contents("php://input"), true);
    }

    public function getCoupon()
    {
        $getCoupon = $this->db->query('select * from coupon')->result_array();
        return ["error"=>false, "reason"=>$getCoupon];
    }

    public function RetrieveCart($userid)
    {
            if($userid)
            {
                $RetrieveData = $this->db->query("select * from cart_table where customer_id = '$userid'")->result_array();
                return ["error"=>false, "reason"=>$RetrieveData];
            }
            
    }

    public function CouponActivate($arr)
    {
        $couponCode = $arr['couponCode'];
        $userid = $arr['userid'];
        
        //coupon code validation ie, whether the coupon code exists in coupon table
        $couponInfo = $this->db->query("select id,MaxusePC from coupon where coupon_code = '$couponCode'")->row();

        if($this->db->affected_rows()>0)
        { 
                    $couponCount = $this->db->query("select UseCount from couponsubscription where customer_id = '$userid' AND coupon_id = '$couponInfo->id'")->row();
                    $useCount =  $couponCount->UseCount;
                    $maxCount = $couponInfo->MaxusePC;
                    if($this->db->affected_rows() > 0)
                    {
                        if($useCount < $maxCount)
                        {
                            $useCount = $useCount+1;
                            $this->db->trans_start();
                                $this->db->set('Usecount', $useCount);
                                $this->db->set('time_stamp', Date('Y-m-d h:i:s'));
                                $this->db->update('couponsubscription');
                            $this->db->trans_complete();
                            return ["error"=>false,"reason"=>"coupon subscribed"];
                        }
                        else
                        {
                            return ["error"=>true, "reason"=>"max limit reached"];
                        }
                        
                    }
                    else
                    {
                        $insert = array('customer_id'=>$userid,
                                        'coupon_id'=>$couponInfo->id,
                                    'UseCount'=>1,
                                'time_stamp'=>Date('Y-m-d h:i:s'));

                        $this->db->trans_start();
                            $this->db->insert('couponsubscription',$insert);
                        $this->db->trans_complete();
                        return ["error"=>false, "reason"=>"coupon subscribed for first time"];
                    }
        }
        else
        {
            return ["error"=>true, "reason"=>"Invalid Coupon"];
        }
    }

    public function PlaceOrder($userid)
    {
        if($userid)
        {
            $getData = $this->db->query("select * from cart_table where customer_id = '$userid'")->result_array();
            return ["error"=>false, "reason"=>$getData];
        }
        else
        {
            return ["error"=>true, "reason"=>"invalid user"];
        }
        
    }

    public function InsertOrder($userid,$items,$cartId)
    {
    
        $deliveryBoyId = 1;
        $orderArray = array('time_stamp'=>Date('Y-m-d h:i:s'),
                        'customer_id'=>$userid,
                        'getItems'=>json_encode($items),
                        'deliveryBoyId'=>$deliveryBoyId);
        
        $orderId = $this->db->query("select * from customer_order where customer_id = '$userid'")->row();
        if($this->db->affected_rows()>0)
        {
            $this->db->trans_start();
            $this->db->where('customer_id',$userid);
                $this->db->update('customer_order',$orderArray);
            $this->db->trans_complete();
        }
        else
        {
            $this->db->trans_start();
                $this->db->insert('customer_order',$orderArray);
            $this->db->trans_complete();
        
        }
            $this->db->trans_start();
                $this->db->where('id',$cartId);
                $this->db->set('items_added',"");
                    $this->db->update('cart_table');
            $this->db->trans_complete();
        return ["error"=>false, "reason"=>"order placed"];
       
    }

    public function retrieveNumber($id)
    {
        if($id != null)
        {
            $mobile = $this->db->query("select customer_mobile from customer where id = '$id'")->result();
            if($this->db->affected_rows()>0)
            {
                return $mobile;
            }
            else
            {
                return ["error"=>true, "reason"=>"mobile error"];
            }
        }
        else
        {
            return ["error"=>true, "reason"=>"user error"];
        }
    }

    public function InsertAddress($id,$insertAddress)
    {
        $insert =  json_encode($insertAddress);
        
        
        $deliverTo = $this->input->post('deliverTo');

        if($this->input->post('mobileNumber')!=null)
        {
            $rows = $this->db->query("select * from deliveryinfo where customer_id = '$id'")->row();
            if($this->db->affected_rows()<0)
            {
                $idInput = array('customer_id'=>$id);
                $this->db->trans_start();
                    $this->db->insert('deliveryinfo',$idInput);
                $this->db->trans_complete();
            }
           
            if($deliverTo == 'Workplace')
            {
                $insertArray = array('customer_id'=>$id,
                            'WorkAddress'=>$insert);
                $this->db->trans_start();
                    $this->db->where('customer_id',$id);
                        $this->db->update('deliveryinfo',$insertArray);
                $this->db->trans_complete();
                if($this->db->trans_status() === TRUE)
                {
                    return ["error"=>false, "reason"=>"success"];
                }
                else
                {
                    return ["error"=>true, "reason"=>"update failed"];
                }
            }
            else if($deliverTo == 'Home')
            {
                $insertArray = array('customer_id'=>$id,
                            'HomeAddress'=>$insert);
                $this->db->trans_start();
                    $this->db->where('customer_id',$id);
                        $this->db->update('deliveryinfo',$insertArray);
                $this->db->trans_complete();
                if($this->db->trans_status() === TRUE)
                {
                    return ["error"=>false, "reason"=>"success"];
                }
                else
                {
                    return ["error"=>true, "reason"=>"update failed"];
                }
            }
            else
            {
                $insertArray = array('customer_id'=>$id,
                            'Other'=>$insert);
                $this->db->trans_start();
                    $this->db->where('customer_id',$id);
                        $this->db->update('deliveryinfo',$insertArray);
                $this->db->trans_complete();
                if($this->db->trans_status() === TRUE)
                {
                    return ["error"=>false, "reason"=>"success"];
                }
                else
                {
                    return ["error"=>true, "reason"=>"update failed"];
                }
            }
        }
        else
        {
            return ["error"=>true, "reason"=>"empty"];
        }       
    }

    public function searchResult($searchItem)
    {
        $searchShop = $this->db->query("select count(*) as count from distributor_hub where hub_name like '%$searchItem%'")->result_array();
        $shopSearchCount = $searchShop[0]['count'];
        $totalShop = $this->db->query("select count(*) as count from distributor_hub")->result_array();
        $totalShopCount = $totalShop[0]['count'];

        $searchProduct = $this->db->query("select count(*) as count from product where product_name like '%$searchItem%'")->result_array();
        $productSearchCount = $searchProduct[0]['count'];
        $totalProduct = $this->db->query("select count(*) as count from product")->result_array();
        $totalProductCount = $totalProduct[0]['count'];

        $searchProductTag = $this->db->query("select count(*) as count from product where MATCH (product_tags) AGAINST ('".$searchItem."')")->result_array();
        $ProductTagcount = $searchProductTag[0]['count'];
        
        $shopProbability = $shopSearchCount/$totalShopCount;
        $productProbability = $productSearchCount/$totalProductCount;
        $tagProbability = $ProductTagcount/$totalProductCount;

        $highestProbability = max($shopProbability,$productProbability,$tagProbability);

        if($highestProbability == $shopProbability)
        {
            $shopLists = $this->db->query("select id,hub_name as name,pickup_address,image from distributor_hub where hub_name like '%$searchItem%'")->result_array();
            return $shopLists;
        }
       else if($highestProbability == $productProbability)
       {
            $productLists = $this->db->query("select id,hub_id,product_name as name,product_image as image,product_price as price from product where product_name like '%$searchItem%'")->result_array();
            return $productLists;
       }
       else 
       {
           $tagLists = $this->db->query("select id,hub_id,product_name as name,product_image as image,product_price as price,product_tags as tags from product where MATCH(product_tags) AGAINST('$searchItem')")->result_array();
           return $tagLists;
       }
    }

    public function shopsWithinRadius($coor, $keyword)
    {
        $lat = $coor['lat'];
        $lng = $coor['lng'];
        $radius = 10;
        $start = 0;
        $end = 20;
        $distanceQuery = $this->db->query("SELECT id,(6371 * acos(cos( radians('".$lat."')) * 
                              cos(radians( JSON_EXTRACT(location_coordinate,'$.latitude') )) * 
                              cos(radians(JSON_EXTRACT(location_coordinate,'$.longitude')) - radians('".$lng."')) 
                              + sin(radians('".$lat."')) * sin(radians(JSON_EXTRACT(location_coordinate,'$.latitude') ))))
                              AS distance FROM distributor_hub HAVING distance < '".$radius."' ORDER BY
                              distance LIMIT $start,$end")->result_array();
    
        for($i = 0;$i < count($distanceQuery); $i++)
        {
            $nearShopId[$i] = json_decode($distanceQuery[$i]['id']);            
        }
        $productHub = $this->db->query("select hub_id from product where product_name = '$keyword'")->result_array();
        if($this->db->affected_rows()>0)
        {
            for($i = 0;$i < count($productHub); $i++)
            {
                $hubIdArray[$i] = $productHub[$i]['hub_id'];
            }
            $this->db->where_in('id',$nearShopId);
            $this->db->where_in('id',$hubIdArray);
                $availableShops = $this->db->get('distributor_hub')->result_array();
            if($availableShops){
                foreach($availableShops as &$val)
                {
                    $val['distance'] = $distanceQuery[array_search($val['id'],array_column($distanceQuery, 'id'))]['distance'];
                }
                return ["error"=>false, "reason"=>$availableShops];
            }
            else
            {
                return ["error"=>true, "reason"=>"product is not available near you"];
            }
        }
        else
        {
            $this->db->where_in('hub_name',$keyword);
            $this->db->where_in('id',$nearShopId);
            $searchedShop = $this->db->get('distributor_hub')->result_array();
            if($searchedShop)
            {
                foreach($searchedShop as &$val)
                {
                    $val['distance'] = $distanceQuery[array_search($val['id'],array_column($distanceQuery, 'id'))]['distance'];
                }
                return ["error"=>false, "reason"=>$searchedShop];
            }
            else
            {
                return ["error"=>true, "reason"=>"no shops near you"];
            }
        }
    }
}