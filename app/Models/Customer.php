<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Customer extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'customers';
    protected $primaryKey = 'customerNumber';

   /*
    *  Returns a customer's record
    *  @param int customerNumber - Customer identifier
    *  @return db object
    */
    public function getCustomerRecord($customerNumber) {

        $dbData = DB::select(' SELECT   cust.customerNumber,
                                        cust.customerName,
                                        cust.contactLastName,
                                        cust.contactFirstName,
                                        cust.phone,
                                        cust.addressLine1,
                                        cust.addressLine2,
                                        cust.city,
                                        cust.state,
                                        cust.postalCode,
                                        cust.country,
                                        cust.salesRepEmployeeNumber,
                                        concat(emp.lastName,emp.firstName) AS employeeName
                                FROM customers cust
                                INNER JOIN employees emp ON emp.employeeNumber = cust.salesRepEmployeeNumber
                                WHERE cust.customerNumber = :customerNumber',
                            ['customerNumber' => $customerNumber ]);

        return $dbData;
    } // END getCustomerRecord

   /*
    *  Returns total customers
    *  @param none
    *  @return db object
    */
    public function getCustomersTotal () {

        $dbData = DB::select(' SELECT COUNT(cust.customerNumber) as customerTotal ,
                                      cust.country
                               FROM customers cust
                               GROUP BY cust.country');

        return $dbData;
    } // END getCustomersTotal

}
