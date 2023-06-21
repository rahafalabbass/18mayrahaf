<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\Product;

class VendorController extends Controller
{
    public function index(){
        try{
            $msg='All vendors';
            $vendors=Vendor::all();
            return $this->successResponse($vendors,$msg);
        }
        catch (\Exception $ex){
            return $this->errorResponse($ex->getMessage(),500);
        }
    }

    public function store(Request $request){
        $validator =  $validator = Validator::make($request->all(), [
            'name' => 'required|string|regex:/[a-zA-Z\s]+/'
        ]);
        if($validator->fails()){
            return $this->errorResponse($validator->errors(),422);
        }
        try{
            $vendor = Vendor::create($request->all());
            $msg = 'vendor is created successfully';

            return $this->successResponse($vendor,$msg,201);
        }catch(\Exception $ex){
            return $this->errorResponse($ex->getMessage(),500);
        }
    }

    public function allVendors($product){
        try{
            $vendors = Vendor::whereHas('products', function ($query) use($product){
                $query->where('products.id', '=', $productId);})->get();
            $msg = 'these are all vendors that sell this product';
            return $this->successRespone($vendors,$msg);    
        }catch(\Exception $ex){
            return $this->errorResponse($ex->getMessage(),500);
        }
    }

    public function allProducts($vendor){
        try{
            $products = Product::whereHas('vendors', function ($query) use ($vendorId) {
                $query->where('vendors.id', '=', $vendorId); })->with('vendors')->get();
            $msg='these are the products that the vendor sell them';
            return $this->successResponse($products,$msg);
        }catch(\Exception $ex){
            return $this->errorResponse($ex->getMessage(),500);
        }
    }

    public function famousVendors($product){
        try{
            $result = DB::table('product_vendor')
            ->join('vendors', 'product_vendor.vendor_id', '=', 'vendors.id')
            ->select('vendors.name', DB::raw('COUNT(*) as num_products'))
            ->where('product_vendor.product_id', '=', [$product])
            ->groupBy('vendors.name')
            ->orderBy('num_products', 'desc')
            ->get();
        }catch(\Exception $ex){
            return $this->errorResponse($ex->getMessage(),500);
        }
    }
}
