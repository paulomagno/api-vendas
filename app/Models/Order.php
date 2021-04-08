<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'orders';
    protected $primaryKey = 'orderNumber';

   /*
    *  Returns a customer's order details
    *  @param int customerNumber - Customer identifier
    *  @return db object
    */
    public function getOrdersByCustomer($customerNumber) {

        $dbData = DB::select('SELECT    cust.customerName,
                                        orders.orderNumber,
                                        orders.orderDate,
                                        orders.`status`,
                                        detail.productCode,
                                        products.productName,
                                        detail.quantityOrdered,
                                        detail.priceEach,
                                        detail.quantityOrdered * detail.priceEach AS amount
                            FROM orders
                            INNER JOIN orderdetails detail ON detail.orderNumber = orders.orderNumber
                            INNER JOIN customers cust ON cust.customerNumber = orders.customerNumber
                            INNER JOIN products ON products.productCode = detail.productCode
                            WHERE cust.customerNumber = :customerNumber

                            ORDER BY orders.orderNumber', ['customerNumber' => $customerNumber ]);

        return $dbData;
    } // END getOrdersByCustomer

}
