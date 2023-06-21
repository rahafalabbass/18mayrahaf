<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $msg = 'These are all orders';
            $orders = Order::with('user')->get();
            return $this->successResponse($orders,$msg);
        }catch(\Exception $ex){
            return $this->errorResponse($ex->getMessage(),500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        try{
            $data=Order::with('user')->find($order);
            if(!$data)
                return $this->errorResponse('Not found',404);


            $msg='Got you the order you are looking for';
            return $this->successResponse($data,$msg);
        }
        catch (\Exception $ex){
            return $this->errorResponse($ex->getMessage(),500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        $this->authorize('update',$order);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        try{
            $order=Order::find($order);
            if(!$order)
                return $this->errorResponse('No order with such id',404);

            $order->delete();
            $msg='The order is deleted successfully';
            return $this->successResponse($order,$msg);
        }
        catch (\Exception $ex){
            return $this->errorResponse($ex->getMessage(),500);
        }
    }

    public function ordersOfUser($id){
        $this->authorize('getOrder',$order);
        try{
            $review = Order::whereRelation('user','user_id','=',$id)->with('user')->get();
            $msg='These are search results';
            return $this->successResponse($review,$msg);
        }catch(\Exception $ex){
            return $this->errorResponse($ex->getMessage(),500);
        }
    }

    public function getOrderByUser($user){
        //this function for admin only
        // I wanted to implement a function getRouteKeyName(), but I didn't know
        try {
            $data= Order::whereRelation('user','user_name','like',$letter.'%')->with('user')->get();
            $msg='These are all results';
            return $this->successResponse($data,$msg);
        }
    catch (\Exception $ex)
    { 
        return $this->errorResponse($ex->getMessage(),500);
    }
    }
}
