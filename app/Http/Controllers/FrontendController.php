<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;


class FrontendController extends Controller{

  
  // public function affiliate(Request $request){
  //     $referer = $request->referer;
  //     if($referer){
        
  //     }
  // }

  public function saveToken(Request $request){
      \DB::table('users')->where('id',$request->user_id)->update(['web_device_token'=>$request->token]);
      return response()->json(1,200);
  }

  public function paymentSuccess(Request $request){
    $response = $this->_callCurl($request->all(),'/api/v1/order-status-success');
    return view('master', ['meta' => $this->getMeta(),'response' => $response]);
  }

  public function paymentFailed(Request $request){
    $response = $this->_callCurl($request->all(),'/api/v1/order-status-failed');
    return view('master', ['meta' => $this->getMeta()]);
  }

  public function paymentCanceled(Request $request){
    $response = $this->_callCurl($request->all(),'/api/v1/order-status-canceled');
    return view('master', ['meta' => $this->getMeta()]);
  }

  public function paymentIpn(Request $request){
    $response = $this->_callCurl($request->all(),'/api/v1/order-status-ipn');
  }

  private function getMeta(): array
  {
      $meta['title'] = '';
      $meta['keyword'] = '';
      $meta['description'] = '';
      $meta['image'] = '';
      $meta['ssr'] = '';

      return $meta;
  }

  private function _callCurl($data,$url){
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => env('APP_ADMIN_URL').$url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $data,
      ));

      $response = curl_exec($curl);
      curl_close($curl);
      return $response;
  }

 

}
