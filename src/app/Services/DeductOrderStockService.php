<?php

namespace App\Services;

use App\Models\PaymentMethodType;
use App\Models\Pickup;

class DeductOrderStockService
{
    public function deduct_order_stock($post_data = [])
    {
        /*
        
        */
        $perPage = 1000;
        $page = 1;

        $query = Pickup::select("*");

        if (isset($post_data['id']) && $post_data['id'] != '') {
            $query = $query->where('id', $post_data['id']);
        }
        if (isset($post_data['pickup_group_id']) && $post_data['pickup_group_id'] != '') {
            $query = $query->where('pickup_group_id', $post_data['pickup_group_id']);
        }
        
        $query = $query->orderBy('name', 'asc')
                ->paginate($perPage, ['/*'], 'page', $page);

        foreach ($query as $key=>$row) {
            $query[$key]->displayname = $row->name;
        }

        $pickups = $query;

        
        
        return response()->json($pickups);
    }
    public function UpdateGroupValues()
    {
        $gid = $this->gid;
            
        $today = date("Y-m-d H:i:s"); 
        
        $sqlQuery = "SELECT CLMPRODGROUP_TYPE FROM tbpc_products_groups WHERE CLMPRODGROUP_ID = '{$gid}' LIMIT 1"; 
        $stmt1 = $this->conn->prepare($sqlQuery);
        $stmt1->execute();
        $myrow = $stmt1->fetch(PDO::FETCH_ASSOC);
        $group_type = $myrow['CLMPRODGROUP_TYPE'];		
        
        if ($group_type == 'm')
        {
            $sqlQuery = "UPDATE tbpc_products_groups p1 
                        INNER JOIN (SELECT 
                            CASE 
                                WHEN t2.CLMMEMBER_PRODUCT_ID IS NOT NULL THEN t3.CLMPRODUCT_ACTIVE 
                                ELSE t4.CLMPRODGROUP_ACTIVE 
                            END As Active,
                            t1.CLMPRODGROUP_ID As GroupID, 		
                            CASE 
                                WHEN t2.CLMMEMBER_PRODUCT_ID IS NOT NULL THEN t3.CLMTAX_PERCENTAGE 
                                ELSE t4.CLMPRODGROUP_TAX_PERCENTAGE
                            END As TaxPercentage,
                            CASE 
                                WHEN t2.CLMMEMBER_PRODUCT_ID IS NOT NULL THEN SUM(ROUND(t3.CLMPRODUCT_PRICE, 4)) 
                                ELSE SUM(ROUND(t4.CLMPRODGROUP_PRICE,4))
                            END As Price,
                            CASE 
                                WHEN t2.CLMMEMBER_PRODUCT_ID IS NOT NULL THEN ROUND(SUM(t3.CLMPRODUCT_PRICE * (100 + t3.CLMTAX_PERCENTAGE) / 100), 2) 
                                ELSE ROUND(SUM(t4.CLMPRODGROUP_PRICE * (100 + t4.CLMPRODGROUP_TAX_PERCENTAGE) / 100), 2)
                            END As PricewithTax, 
                            Count(*) AS NumberofProducts, 
                            Count(*) AS NumberofActiveProducts 							
                        FROM tbpc_products_groups t1 
                        LEFT JOIN  tbpc_products_groups_members t2 ON t1.CLMPRODGROUP_ID = t2.CLMMEMBER_PRODGROUP_ID 
                        LEFT JOIN tbpc_products t3 ON t3.CLMPRODUCT_ID = t2.CLMMEMBER_PRODUCT_ID 
                        LEFT JOIN tbpc_products_groups t4 ON t4.CLMPRODGROUP_ID = t2.CLMMEMBER_GROUP_ID 
                        WHERE t1.CLMPRODGROUP_TYPE = 'm' 
                        GROUP BY t1.CLMPRODGROUP_ID 
                        ORDER BY Active ASC) AS tbl_conn ON p1.CLMPRODGROUP_ID = tbl_conn.GroupID 
                        SET 
                            p1.CLMPRODGROUP_ACTIVE = CASE WHEN tbl_conn.Active = 1 THEN 1 ELSE 0 END, 
                            p1.CLMPRODGROUP_QTY = CASE WHEN tbl_conn.NumberofProducts > 0 THEN tbl_conn.NumberofProducts ELSE '0' END, 
                            p1.CLMPRODGROUP_PRICE = tbl_conn.Price,                     
                            p1.CLMPRODGROUP_PRICE_WITHTAX = tbl_conn.PricewithTax,  
                            p1.CLMPRODGROUP_TAX_PERCENTAGE = tbl_conn.TaxPercentage,  
                            p1.CLMPRODGROUP_LAST_UPD_DATETIME = NOW()
                        WHERE p1.CLMPRODGROUP_ID = '{$gid}'";
        }
        else if ($group_type == 'c')
        {
            $sqlQuery = "UPDATE tbpc_products_groups p1 LEFT JOIN (SELECT 
                        CASE 
                            WHEN t1.CLMPRODGROUP_ACTIVE = 0 THEN 0 
                            WHEN t2.NumberofProducts > 0 THEN 1
                            ELSE 0
                        END As Active,						
                        t1.CLMPRODGROUP_ID As GroupID, 
                        SUM(CASE WHEN t2.Price IS NULL THEN 0
                            ELSE t2.Price
                            END) As Price, 
                        SUM(CASE WHEN t2.OfferPrice IS NULL THEN 0
                            ELSE t2.OfferPrice
                            END) As OfferPrice,
                        NULL As OfferPrice2, 						
                        NULL As Price1, 
                        NULL As Price2, 
                        NULL As Price3, 
                        NULL As Price4, 
                        NULL As Price5, 
                        NULL As TaxPercentage, 
                        NULL As OfferExpDate, 
                        SUM(t2.NumberofProducts) AS NumberofProducts, 
                        SUM(t2.NumberofProducts) AS NumberofActiveProducts, 
                        SUM(CASE WHEN t2.PriceWithVAT IS NULL THEN 0
                            ELSE t2.PriceWithVAT
                            END) As PricewithTax, 
                        SUM(CASE WHEN t2.OfferPriceWithTax IS NULL THEN 0
                            ELSE t2.OfferPriceWithTax
                            END) As OfferPricewithTax,							
                        NULL As OfferPrice2withTax, 
                        NULL As Price1withTax, 
                        NULL As Price2withTax, 
                        NULL As Price3withTax, 
                        NULL As Price4withTax, 
                        NULL As Price5withTax 
                    FROM tbpc_products_groups t1 
                    LEFT JOIN (SELECT 
                                    j1.CLMPRODGROUP_COMP_ID, 
                                    j1.CLMPRODGROUP_GROUP_ID, 
                                    j1.CLMPRODGROUP_COMP_NAME, 
                                    (COUNT(j2.CLMPRODGROUP_MEMBER_PRODID) + COUNT(j2.CLMPRODGROUP_MEMBER_GROUPID)) As NumberofProducts, 
                                    MIN(CASE 
                                            WHEN j2.CLMPRODGROUP_MEMBER_PRODID IS NULL THEN ROUND(IFNULL(j4.CLMPRODGROUP_PRICE,0) * (100 + IFNULL(j4.CLMPRODGROUP_TAX_PERCENTAGE,0)) / 100, 2) 
                                            ELSE ROUND(j3.CLMPRODUCT_PRICE * (100 + j3.CLMTAX_PERCENTAGE) / 100, 2) 
                                        END) As Price,
                                    MIN(CASE 
                                            WHEN j2.CLMPRODGROUP_MEMBER_PRODID IS NULL THEN ROUND(IFNULL(j4.CLMPRODGROUP_PRICE,0) * (100 + IFNULL(j4.CLMPRODGROUP_TAX_PERCENTAGE,0)) / 100, 2) 
                                            ELSE ROUND(j3.CLMPRODUCT_PRICE * (100 + j3.CLMTAX_PERCENTAGE) / 100, 2) 
                                        END) As PriceWithVAT, 
                                    MIN(CASE
                                        WHEN j2.CLMPRODGROUP_MEMBER_PRODID IS NULL AND j4.CLMPRODGROUP_OFFERPRICE IS NULL THEN ROUND(j4.CLMPRODGROUP_PRICE * (100 + IFNULL(j4.CLMPRODGROUP_TAX_PERCENTAGE,0)) / 100, 2)  
                                        WHEN j2.CLMPRODGROUP_MEMBER_PRODID IS NULL AND j4.CLMPRODGROUP_OFFERPRICE = 0 THEN ROUND(j4.CLMPRODGROUP_PRICE * (100 + IFNULL(j4.CLMPRODGROUP_TAX_PERCENTAGE,0)) / 100, 2)  
                                        WHEN j2.CLMPRODGROUP_MEMBER_PRODID IS NULL AND j4.CLMPRODGROUP_OFFERPRICE > j4.CLMPRODGROUP_PRICE THEN ROUND(j4.CLMPRODGROUP_PRICE * (100 + IFNULL(j4.CLMPRODGROUP_TAX_PERCENTAGE,0)) / 100, 2) 
                                        WHEN j2.CLMPRODGROUP_MEMBER_PRODID IS NULL THEN ROUND(j4.CLMPRODGROUP_OFFERPRICE * (100 + IFNULL(j4.CLMPRODGROUP_TAX_PERCENTAGE,0)) / 100, 2) 
                                        WHEN j2.CLMPRODGROUP_MEMBER_PRODID IS NOT NULL AND j3.CLMPRODUCT_OFFERPRICE IS NULL THEN ROUND(j3.CLMPRODUCT_PRICE * (100 + j3.CLMTAX_PERCENTAGE) / 100, 2)  
                                        WHEN j2.CLMPRODGROUP_MEMBER_PRODID IS NOT NULL AND j3.CLMPRODUCT_OFFERPRICE = 0 THEN ROUND(j3.CLMPRODUCT_PRICE * (100 + j3.CLMTAX_PERCENTAGE) / 100, 2)  
                                        WHEN j2.CLMPRODGROUP_MEMBER_PRODID IS NOT NULL AND j3.CLMPRODUCT_OFFERPRICE > CLMPRODUCT_PRICE THEN ROUND(j3.CLMPRODUCT_PRICE * (100 + j3.CLMTAX_PERCENTAGE) / 100, 2) 
                                        ELSE ROUND(j3.CLMPRODUCT_OFFERPRICE * (100 + j3.CLMTAX_PERCENTAGE) / 100, 2) 
                                    END) As OfferPrice, 
                                    MIN(CASE
                                        WHEN j2.CLMPRODGROUP_MEMBER_PRODID IS NULL AND j4.CLMPRODGROUP_OFFERPRICE IS NULL THEN ROUND(j4.CLMPRODGROUP_PRICE * (100 + IFNULL(j4.CLMPRODGROUP_TAX_PERCENTAGE,0)) / 100, 2)  
                                        WHEN j2.CLMPRODGROUP_MEMBER_PRODID IS NULL AND j4.CLMPRODGROUP_OFFERPRICE = 0 THEN ROUND(j4.CLMPRODGROUP_PRICE * (100 + IFNULL(j4.CLMPRODGROUP_TAX_PERCENTAGE,0)) / 100, 2)  
                                        WHEN j2.CLMPRODGROUP_MEMBER_PRODID IS NULL AND j4.CLMPRODGROUP_OFFERPRICE > j4.CLMPRODGROUP_PRICE THEN ROUND(j4.CLMPRODGROUP_PRICE * (100 + IFNULL(j4.CLMPRODGROUP_TAX_PERCENTAGE,0)) / 100, 2) 
                                        WHEN j2.CLMPRODGROUP_MEMBER_PRODID IS NULL THEN ROUND(j4.CLMPRODGROUP_OFFERPRICE * (100 + IFNULL(j4.CLMPRODGROUP_TAX_PERCENTAGE,0)) / 100, 2) 
                                        WHEN j2.CLMPRODGROUP_MEMBER_PRODID IS NOT NULL AND j3.CLMPRODUCT_OFFERPRICE IS NULL THEN ROUND(j3.CLMPRODUCT_PRICE * (100 + j3.CLMTAX_PERCENTAGE) / 100, 2)  
                                        WHEN j2.CLMPRODGROUP_MEMBER_PRODID IS NOT NULL AND j3.CLMPRODUCT_OFFERPRICE = 0 THEN ROUND(j3.CLMPRODUCT_PRICE * (100 + j3.CLMTAX_PERCENTAGE) / 100, 2)  
                                        WHEN j2.CLMPRODGROUP_MEMBER_PRODID IS NOT NULL AND j3.CLMPRODUCT_OFFERPRICE > CLMPRODUCT_PRICE THEN ROUND(j3.CLMPRODUCT_PRICE * (100 + j3.CLMTAX_PERCENTAGE) / 100, 2) 
                                        ELSE ROUND(j3.CLMPRODUCT_OFFERPRICE * (100 + j3.CLMTAX_PERCENTAGE) / 100, 2) 
                                    END) As OfferPriceWithTax 														
                                FROM tbpc_products_groups_components j1
                                LEFT JOIN tbpc_products_groups_comp_members j2 ON j2.CLMPRODGROUP_MEMBER_COMPONENTID = j1.CLMPRODGROUP_COMP_ID
                                LEFT JOIN tbpc_products j3 ON j3.CLMPRODUCT_ID = j2.CLMPRODGROUP_MEMBER_PRODID AND j3.CLMPRODUCT_ACTIVE = 1 										 
                                LEFT JOIN tbpc_products_groups j4 ON j4.CLMPRODGROUP_ID = j2.CLMPRODGROUP_MEMBER_GROUPID AND j4.CLMPRODGROUP_ACTIVE = 1 
                                WHERE j1.CLMPRODGROUP_COMP_MANDATORY = 1 
                                GROUP BY CLMPRODGROUP_MEMBER_COMPONENTID) As t2 ON t1.CLMPRODGROUP_ID=t2.CLMPRODGROUP_GROUP_ID 
                                WHERE t1.CLMPRODGROUP_TYPE = 'c' 
                    GROUP BY t1.CLMPRODGROUP_ID 
                    ORDER BY Active ASC) AS tbl_conn ON p1.CLMPRODGROUP_ID = tbl_conn.GroupID  
                SET 
                    p1.CLMPRODGROUP_ACTIVE = Active, 
                    p1.CLMPRODGROUP_QTY = CASE WHEN tbl_conn.NumberofProducts > 0 THEN tbl_conn.NumberofProducts ELSE '0' END, 
                    p1.CLMPRODGROUP_PRICE = tbl_conn.Price,                     
                    p1.CLMPRODGROUP_OFFERPRICE = tbl_conn.OfferPrice, 
                    p1.CLMPRODGROUP_OFF_PRICE = tbl_conn.OfferPrice, 
                    p1.CLMPRODGROUP_OFF_EXPDATE = tbl_conn.OfferExpDate,  
                    p1.CLMPRODGROUP_TAX_PERCENTAGE = tbl_conn.TaxPercentage,  
                    p1.CLMPRODGROUP_LAST_UPD_DATETIME = '$today' 
                WHERE p1.CLMPRODGROUP_ID = '{$gid}'";
        }
        else
        {
            $sqlQuery = "UPDATE tbpc_products_groups p1 LEFT JOIN (SELECT 
                            p2.CLMPRODUCT_ACTIVE, 
                            p2.CLMPRODUCT_GROUPID As GroupID, 							
                            MIN(ROUND(p2.CLMPRODUCT_PRICE, 4)) As Price, 
                            MIN(ROUND(p2.CLMPRODUCT_OFF_PRICE, 4)) As OfferPrice, 
                            MIN(ROUND(p2.CLMPRODUCT_OFFERPRICE,4)) As OfferPrice2,
                            MIN(ROUND(p2.CLMPRODUCT_LOYPRICE,4)) As LoyaltyPrice, 
                            p2.CLMTAX_PERCENTAGE As TaxPercentage, 
                            p2.CLMPRODUCT_OFF_EXPDATE As OfferExpDate, 
                            COUNT(p2.CLMPRODUCT_ID) AS NumberofProducts, 
                            COUNT(p2.CLMPRODUCT_ID) AS NumberofActiveProducts, 							
                            MIN(ROUND(p2.CLMPRODUCT_PRICE * (100 + p2.CLMTAX_PERCENTAGE) / 100, 2)) As PricewithTax, 
                            MIN(ROUND(p2.CLMPRODUCT_OFF_PRICE * (100 + p2.CLMTAX_PERCENTAGE) / 100, 2)) As OfferPricewithTax, 
                            MIN(ROUND(p2.CLMPRODUCT_OFFERPRICE * (100 + p2.CLMTAX_PERCENTAGE) / 100, 2)) As OfferPrice2withTax  
                        FROM tbpc_products p2 
                        WHERE p2.CLMPRODUCT_ACTIVE=1 AND p2.CLMPRODUCT_GROUPID IS NOT NULL  
                        GROUP BY p2.CLMPRODUCT_GROUPID) AS tbl_conn ON p1.CLMPRODGROUP_ID = tbl_conn.GroupID  
                SET p1.CLMPRODGROUP_ACTIVE = CASE WHEN tbl_conn.CLMPRODUCT_ACTIVE = 1 THEN '1' ELSE '0' END, 
                    p1.CLMPRODGROUP_QTY = CASE WHEN tbl_conn.NumberofProducts > 0 THEN tbl_conn.NumberofProducts ELSE '0' END, 
                    p1.CLMPRODGROUP_PRICE = tbl_conn.Price,                     
                    p1.CLMPRODGROUP_OFFERPRICE = tbl_conn.OfferPrice2,   
                    p1.CLMPRODGROUP_LOYPRICE = tbl_conn.LoyaltyPrice,  
                    p1.CLMPRODGROUP_OFF_PRICE = tbl_conn.OfferPrice, 
                    p1.CLMPRODGROUP_OFF_EXPDATE = tbl_conn.OfferExpDate,  
                    p1.CLMPRODGROUP_TAX_PERCENTAGE = tbl_conn.TaxPercentage,				
                    p1.CLMPRODGROUP_LAST_UPD_DATETIME = '$today' 
                WHERE p1.CLMPRODGROUP_ID = '$gid'";
        }
        $stmt1 = $this->conn->prepare($sqlQuery);
        $stmt1->execute();
        return true;
    }
}

?>