<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Product;
use App\Models\Review;
use Auth;
use Validator;

class ReviewController extends Controller
{
    public function index(){
        try{
            $msg='All reviews';
            $reviews=Review::with('user','product')->get();
            return $this->successResponse($reviews,$msg);
        }
        catch (\Exception $ex){
            return $this->errorResponse($ex->getMessage(),500);
        } 
    }

    public function store(Request $request,$product){
        $validator =  $validator = Validator::make($request->all(), [
            'comment' => 'required|string|regex:/[a-zA-Z\s]+/',
            'start' => 'required|integer|max:5',
        ]);
        if($validator->fails()){
            return $this->errorResponse($validator->errors(),422);
        }
        try{
            $review = Review::create($request->all());
            $review->user()->associate(Auth::user())->save();
            $review->product()->associate(Product::find($product))->save();
            $data = $review;
            $msg = 'review is created successfully';

            return $this->successResponse($data,$msg,201);
        }catch(\Exception $ex){
            return $this->errorResponse($ex->getMessage(),500);
        }
    }

    public function show($id){
        try{

            $review = Review::with('user','product')->find($id);
            if(!$review){
                return $this->errorResponse('No review with such id',404);
            }
            $msg = 'Searched successfully';
            return $this->successResponse($review,$msg);
        }catch(\Exception $ex){

            return $this->errorResponse($ex->getMessage(),500);
        }
    }

    public function update(Request $request,$id){
        try{
            $review = Review::find($id);
            if(!$review){
                return $this->errorResponse('No review with such id', 404);
            }
            $review->update($request->all());
            $review->save();
            $msg='The review is updated successfully';
            return $this->successResponse($review,$msg);
        }
        catch(\Exception $ex){
            return $this->errorResponse($ex->getMessage(),500);
        }
    }

    public function destroy($id){
        try{
            $review = Review::find($id);
            if(!$review){
                return $this->errorResponse('No review with such id', 404);
            }
            $review->delete();
            $msg='The review is deleted successfully';
            return $this->successResponse($review,$msg);
        }catch(\Exception $ex){
            return $this->errorResponse($ex->getMessage(),500);
        }
    }

    public function reviewsOfUser($id){
        try{
            $review = Review::whereRelation('user','user_id','=',$id)->with('user','product')->get();
            $msg='These are search results';
            return $this->successResponse($review,$msg);
        }catch(\Exception $ex){
            return $this->errorResponse($ex->getMessage(),500);
        }
    }

    public function productReviews($id){
        try{
            $review = Review::whereRelation('product','product_id','=',$id)->with('user','product')->get();
            $msg='These are search results';
            return $this->successResponse($review,$msg);
        }catch(\Exception $ex){
            return $this->errorResponse($ex->getMessage(),500);
        }
    }
}
