<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

use App\Models\Order;
use App\Models\Customer;

use App\Http\Services\CountryCapital;

class ApiController extends Controller
{
    private $orders = NULL;
    private $customer = NULL;


    // Construct class
    public function __construct(Order $orders, Customer $customer) {
        $this->orders = $orders;
        $this->customer = $customer;
    } // end construct class

    // Groups the customers of an order
    public function groupedOrdersCustomer($orders) {

        $ordersGrouped = array();

        foreach ( $orders as $order) {

            $ordersGrouped[$order->customerName]
                          [$order->orderNumber][] = array('orderDate' => $order->orderDate,
                                                          'status' => $order->status,
                                                          'productCode' => $order->productCode,
                                                          'productName' => $order->productName,
                                                          'quantityOrdered' => $order->quantityOrdered,
                                                          'priceEach' => $order->priceEach,
                                                          'amount' => $order->amount
                                                        );

        }

        return $ordersGrouped;
    } // end groupedOrdersCustomer

    // Return order details from a customer
    public function getOrdersByCustomer(Request $request) {

        // Data Validation
        $rules = ['customerNumber' => 'required|integer|gte:0'];

        $validator = Validator::make($request->all(),$rules);

        if($validator->fails()) {

            return response()->json([
                'message' =>  $validator->messages()
            ]);
        }

        if(count($this->orders->getOrdersByCustomer($request->customerNumber)) == 0) {

            return response()->json([
                'message' =>  'No orders found for this customer'
            ]);
        }

        // Groups the customers of an order
        $ordersGrouped = $this->groupedOrdersCustomer($this->orders->getOrdersByCustomer($request->customerNumber));
        $orders = array();

        foreach ($ordersGrouped as $customer => $detailOrder) {

            $ordersCustomer = array();

            foreach ($detailOrder as $orderNumber => $items) {
                $itemsOrder = array();

                foreach ($items as $index => $data) {
                   $itemsOrder[] = $data;
                }

                $ordersCustomer[] = array('orderNumber' => $orderNumber,
                                          'items' => $itemsOrder);
            }

            $orders[] = array('customerName' => $customer,
                              'orders' => $ordersCustomer);
        }

        return response()->json($orders);

    } // end getOrdersByCustomer

    //  Returns a customer's record
    public function getCustomerRecord(Request $request) {

        // Data Validation
        $rules = ['customerNumber' => 'required|integer|gte:0'];

        $validator = Validator::make($request->all(),$rules);

        if($validator->fails()) {

            return response()->json([
                'message' =>  $validator->messages()
            ]);
        }

        if(count($this->customer->getCustomerRecord($request->customerNumber)) == 0) {

            return response()->json([
                'message' =>  'No Customer found for this customer number'
            ]);
        }

        return response()->json($this->customer->getCustomerRecord($request->customerNumber));
    } // end getCustomerRecord

    //  Returns total customers
    public function getCustomersTotal(Request $request) {

        $customers = $this->customer->getCustomersTotal();
        $customersList = array();
        $totalCustomers = 0;

        foreach ($customers as $customer) {

            $totalCustomers += $customer->customerTotal;

            // Returns the name of the capital
            $capital = CountryCapital::getCountryCapital(trim($customer->country));

            $customersList['countrys'][] = array('countryName' => $customer->country,
                                                'totalCustomersCountry' => $customer->customerTotal,
                                                'capitalName' => $capital[0]['capital']);
        }

        $customersList['totalCustomers'] = $totalCustomers;

        return response()->json($customersList);

    } // end getCustomersTotal
}
