<?php

use App\Models\Settings;
use App\Models\Category;
use App\Models\Currency;
use App\Models\ProductMeta;
use App\Models\ProductSpecification;
use App\Models\AttributeSets;
use App\Models\Addresses;
use App\Models\ShopInfo;
use App\Models\WithdrawalRequest;
use App\Models\User;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Blog;
use App\Models\Order;
use App\Models\Brand;
use App\Models\Admins;
use App\Models\OrderDetails;
use App\Models\Trash;
use App\Models\SellerAccountHistory;
use App\Models\LoyaltyPurchases;
use App\Models\Account;
use App\Models\CurrentAsset;
use App\Models\Expense;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Carbon\Carbon;

class Helper
{

	public static function getStatusName($type = '', $id = '')
	{

		if ($type == 'order') {
			$statusArray = array(
				'0' => 'Pending',
				'1' => 'Processing',
				'2' => 'Hold',
				'3' => 'Canceled',
				'4' => 'Completed',
			);
		} elseif ($type == 'yesno') {
			$statusArray = array(
				'1' => 'Yes',
				'0' => 'No',
			);
		} elseif ($type == 'default') {
			$statusArray = array(
				'1' => 'Active',
				'0' => 'Inactive',
			);
		}

		foreach ($statusArray as $key => $val) {
			if ($key == $id) {
				return $val;
			}
		}
	}

	public static function deleteFile($filename, $filepath)
	{
		if (!is_null($filename)) {
			if (file_exists(public_path($filepath . $filename))) {
				unlink(public_path($filepath . $filename));
			}
		}
	}

	public static function getSettings($key)
	{
		$settings = Settings::where('key', $key)->first();
		if (!is_null($settings)) {
			return $settings->value;
		} else {
			return false;
		}
	}

	public static function getAllSettings()
	{
		$settings = Settings::all();
		$results = [];
		foreach ($settings as $s) {
			$results[$s->key] = $s->value;
		}
		return $results;
	}

	public static function expectedShipping($orderDetailsId)
	{

		$free_shipping_delay = 15;
		$standard_shipping_delay = 7;
		$express_shipping_delay = 3;

		$orderDetails = OrderDetails::find($orderDetailsId);
		if ($orderDetails) {
			$order = Order::find($orderDetails->order_id);
			if ($order) {
				if ($order->status == 2 || $order->status == 6) { //Processing or Completed
					$updated_at = $order->updated_at;
					if ($orderDetails->shipping_method == 'free_shipping') {
						return date('d M, Y', strtotime($updated_at . ' + ' . $free_shipping_delay . ' days'));
					} elseif ($orderDetails->shipping_method == 'standard_shipping') {
						return date('d M, Y', strtotime($updated_at . ' + ' . $standard_shipping_delay . ' days'));
					} elseif ($orderDetails->shipping_method == 'express_shipping') {
						return date('d M, Y', strtotime($updated_at . ' + ' . $express_shipping_delay . ' days'));
					}
				}
			}
		}

		return 'Payment is not confirmed or refunded!';
	}


	public static function biponi_varyfication($seller_id)
	{

		$settings = Settings::where('key', 'flagship_ids')->first();

		if (!is_null($settings)) {
			$ids = explode(",", $settings->value);
			if (in_array($seller_id, $ids)) {
				$data['if_veryfied'] = true;
				$data['veryfied_banner'] = \Helper::getsettings('flagship_banner') ?? 'logo.png';
				return $data;
			} else {
				$data['if_veryfied'] = false;
				$data['veryfied_banner'] = \Helper::getsettings('flagship_banner') ?? 'logo.png';
				return $data;
			}
		} else {
			return false;
		}
	}


	public static function getDefaultCurrency()
	{
		$default_currency = \Helper::getsettings('default_currency');
		$currency = Currency::find($default_currency);
		return $currency;
	}

	public static function getPriceHtml($price)
	{
		/*$default_currency = \Helper::getsettings('default_currency');
        $fixed = Currency::find($default_currency);

        $selectedCurrency =  session('selected_currency');
        if(!is_null($selectedCurrency)){
            $currencies = Currency::where('status',1)->get();
            foreach($currencies as $currency){
                if($currency->id == $selectedCurrency ){
                    $calculatedPriceHtml = $currency->currency_symbol.' '.round($price/$currency->exchange_rate,2);
                }
            }
        }else{
            $calculatedPriceHtml = $fixed->currency_symbol.' '.round($price/$fixed->exchange_rate,2);
        }*/

		return $price;
	}

	public static function getPrice($price)
	{
		/*$default_currency = \Helper::getsettings('default_currency');
        $fixed = Currency::find($default_currency);

        $selectedCurrency =  session('selected_currency');
        if(!is_null($selectedCurrency)){
            $currencies = Currency::where('status',1)->get();
            foreach($currencies as $currency){
                if($currency->id == $selectedCurrency ){
                    $calculatedPriceHtml = round($price/$currency->exchange_rate,2);
                }
            }
        }else{
            $calculatedPriceHtml = round($price/$fixed->exchange_rate,2);
        }*/

		return $price;
	}


	public static function selectedCurrency()
	{
		$default_currency = \Helper::getsettings('default_currency');
		$fixed = Currency::find($default_currency);
		$selectedCurrency =  session('selected_currency');
		if (!is_null($selectedCurrency)) {
			$currency = Currency::find($selectedCurrency)->currency_symbol;
		} else {
			$currency = $fixed->currency_symbol;
		}
		return $currency;
	}

	public static function getTemplateName()
	{

		$filename = [];
		$filesInFolder = \File::files(resource_path('views/frontend/pages/templates'));

		foreach ($filesInFolder as $path) {
			$file = pathinfo($path);
			$filename[] = str_replace('.blade', '', $file['filename']);
		}
		return $filename;
	}
	public static function getWishlist()
	{
		if (Auth::check()) {
			$user_id = Auth::id();
			$data = \DB::table('users_metas')->where('meta_key', 'product_wishlist')->where('user_id', $user_id)->first();
			if ($data) {
				$wishlists = $data->meta_value;
				$lists = explode(',', $wishlists);
				return count($lists);
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}

	public static function getPriceById($id)
	{
		$data = \DB::table('products')
			->where('id', $id)->first();

		if ($data) {
			$special_price_start = Carbon::createFromFormat('Y-m-d H:i:s', $data->special_price_start);
			$special_price_end = Carbon::createFromFormat('Y-m-d H:i:s', $data->special_price_end);
			$currentDate = Carbon::now();

			if (($special_price_start <= $currentDate) && ($special_price_end >= $currentDate)) {
				if ($data->special_price > 0) {
					if ($data->special_price_type == 1) {
						return $data->price - $data->special_price;
					} elseif ($data->special_price_type == 2) {
						$percent = $data->special_price;
						$price = $data->price;
						$discount = $price * ($percent / 100);
						$calculatedPrice = $price - $discount;
						return $calculatedPrice;
					}
				} else {
					return $data->price;
				}
			}else{
				return $data->price;
			}
		} else {
			$data = \DB::table('products')->where('id', $id)->first();
			if ($data) {
				return $data->price;
			} else {
				return null;
			}
		}
	}

	public static function getPriceAfterDiscount($id)
	{
		$data = \DB::table('products')
			->where('id', $id)->first();

		if ($data) {
			$special_price_start = Carbon::createFromFormat('Y-m-d H:i:s', $data->special_price_start);
			$special_price_end = Carbon::createFromFormat('Y-m-d H:i:s', $data->special_price_end);
			$currentDate = Carbon::now();

			if (($special_price_start <= $currentDate) && ($special_price_end >= $currentDate)) {
				if ($data->special_price > 0) {
					if ($data->special_price_type == 1) {
						return $data->price - $data->special_price;
					} elseif ($data->special_price_type == 2) {
						$percent = $data->special_price;
						$price = $data->price;
						$discount = $price * ($percent / 100);
						$calculatedPrice = $price - $discount;
						return $calculatedPrice;
					}
				} else {
					return $data->price;
				}
			}else{
				return $data->price;
			}
		} else {
			$data = \DB::table('products')->where('id', $id)->first();
			if ($data) {
				return $data->price;
			} else {
				return null;
			}
		}
	}

	public static function getDiscountAmountById($id){
		$data = \DB::table('products')
			->where('id', $id)->first();

		if ($data) {
			$special_price_start = Carbon::createFromFormat('Y-m-d H:i:s', $data->special_price_start);
			$special_price_end = Carbon::createFromFormat('Y-m-d H:i:s', $data->special_price_end);
			$currentDate = Carbon::now();

			if (($special_price_start <= $currentDate) && ($special_price_end >= $currentDate)) {
				if ($data->special_price > 0) {
					if ($data->special_price_type == 1) {
						return $data->special_price;
					} elseif ($data->special_price_type == 2) {
						$percent = $data->special_price;
						$price = $data->price;
						$discount = $price * ($percent / 100);
						$calculatedPrice = $discount;
						return $calculatedPrice;
					}
				} else {
					return 0;
				}
			}else{
				return 0;
			}
		} else {
			return 0;
		}
	}

	public static function getDiscountByAditionalPrice($discount_type, $discount, $additional_price){
		if ($discount_type && $discount && $additional_price) {
			if ($discount_type == 1) {
				return $additional_price - $discount;
			}else if($discount_type == 2){
				$percent = $discount;
				$price = $additional_price;
				$discount = $price * ($percent / 100);
				$calculatedPrice = $price - $discount;
				return $calculatedPrice;
			}
		}else{
			return $additional_price;
		}
	}

	public static function getDiscountDifferentPriceById($id)
	{
		$data = \DB::table('products')
			->where('id', $id)->where('special_price_start', '<=', Carbon::now())
			->where('special_price_end', '>=', Carbon::now())
			->first();

		if ($data) {
			return true;
		}else{
			return false;
		}
	}

	public static function get_attribute_set_details_with_product($attributeSetId, $product_id)
	{

		$html = '';
		$spv = ProductSpecification::where('product_id', $product_id)->get();

		$attributeOptionsMeta_value = [];

		foreach ($spv as $sp) {
			$attributeOptionsMeta_value[$sp->meta_key] = $sp->meta_value;
		}


		$attributset = AttributeSets::where('id', $attributeSetId)->where('is_active', 1)->where('is_deleted', 0)->first();

		if ($attributset) {
			$attribute_ids_array = explode(',', $attributset->attribute_ids);
			foreach ($attribute_ids_array as $attribute_id) {
				$attribute = \DB::table('attributes')->where('id', $attribute_id)->where('is_active', 1)->where('is_deleted', 0)->first();

				if ($attribute) {

					$catalog_input_type = $attribute->catalog_input_type;
					$is_required = $attribute->is_required;


					if ($is_required == 1) {
						$requredText = '<span class="required">*</span>';
						$requiredValidate = 'required';
					} else {
						$requredText = '';
						$requiredValidate = '';
					}

					$databaseValue = isset($attributeOptionsMeta_value[$attribute->attribute_code]) ? $attributeOptionsMeta_value[$attribute->attribute_code] : '';


					if ($catalog_input_type == 'radio') {

						$attribute_values = unserialize($attribute->attribute_values);
						$html .= '<div class="form-group row">
                                <label class="col-sm-3 col-form-label">' . $attribute->title . $requredText . '</label>
                                <div class="col-sm-9">';
						foreach ($attribute_values as $av) {
							if ($av['value'] == $databaseValue) {
								$checked = 'checked';
							} else {
								$checked = '';
							}

							$html .= '<input type="radio" ' . $checked . ' name="specification[' . $attribute->attribute_code . ']" value="' . $av['value'] . '"' . $requiredValidate . ' > &nbsp;' . $av['label'] . '&nbsp;&nbsp;&nbsp;';
						}
						$html .= '</div>
                                </div>';
					} elseif ($catalog_input_type == 'checkbox') {
						$attribute_values = unserialize($attribute->attribute_values);
						$html .= '<div class="form-group row">
                        <label class="col-sm-3 col-form-label">' . $attribute->title . $requredText . '</label>
                        <div class="col-sm-9">';
						foreach ($attribute_values as $av) {

							if ($av['value'] == $databaseValue) {
								$checked = 'checked';
							} else {
								$checked = '';
							}

							$html .= '<input class="form-control" type="checkboox" ' . $checked . ' name="specification[' . $attribute->attribute_code . ']" value="' . $av['value'] . '"' . $requiredValidate . ' > &nbsp;&nbsp;' . $av['label'];
						}
						$html .= '</div>
                        </div>';
					} elseif ($catalog_input_type == 'dropdown') {
						$attribute_values = unserialize($attribute->attribute_values);
						$html .= '<div class="form-group row">
                                <label class="col-sm-3 col-form-label">' . $attribute->title . $requredText . '</label>
                                <div class="col-sm-9">';
						$html .= '<select class="form-control" name="specification[' . $attribute->attribute_code . ']"' . $requiredValidate . '>';
						foreach ($attribute_values as $av) {
							if ($av['value'] == $databaseValue) {
								$selected = 'selected';
							} else {
								$selected = '';
							}

							$html .= '<option ' . $selected . ' value="' . $av['value'] . '">' . $av['label'] . '</option>';
						}
						$html .= '</select>
                                </div>
                            </div>';
					} elseif ($catalog_input_type == 'textfield') {

						$html .= '<div class="form-group row">
                        <label class="col-sm-3 col-form-label">' . $attribute->title . $requredText . '</label>
                        <div class="col-sm-9">';
						$html .= '<input class="form-control" value="' . $databaseValue . '" type="text" name="specification[' . $attribute->attribute_code . ']"' . $requiredValidate . '>
                            </div>
                        </div>';
					} elseif ($catalog_input_type == 'textarea') {
						$html .= '<div class="form-group row">
                        <label class="col-sm-3 col-form-label">' . $attribute->title . $requredText . '</label>
                        <div class="col-sm-9">';
						$html .= '<textarea class="form-control" name="specification[' . $attribute->attribute_code . ']"' . $requiredValidate . '>' . $databaseValue . '</textarea>
                            </div>
                        </div>';
					} elseif ($catalog_input_type == 'textareawitheditor') {
						$html .= '<div class="form-group row">
                        <label class="col-sm-3 col-form-label">' . $attribute->title . $requredText . '</label>
                        <div class="col-sm-9">';
						$html .= '<textarea class="form-control textEditor" name="specification[' . $attribute->attribute_code . ']"' . $requiredValidate . '>' . $databaseValue . '</textarea>
                            </div>
                        </div>';
					}
				}
			}
		}
		return $html;
	}





	public static function getImageById($id)
	{
		$images = \DB::table('concave_media')->where('id', $id)->first();
		return $images;
	}




	public static function sendSms($mobile, $msg)
	{

		if (Helper::getsettings('sms_gateway_default_provider') == 'mimsms') {
			if (Helper::getsettings('sms_gateway_default_sender') == 'musking') {
				$api_key = \Helper::getsettings('sms_gateway_musking_api_key');
				$sender_id = \Helper::getsettings('sms_gateway_musking_sender_id');
			} else {
				$api_key = \Helper::getsettings('sms_gateway_non_musking_api_key');
				$sender_id = \Helper::getsettings('sms_gateway_non_musking_sender_id');
			}

			$url = 'https://esms.mimsms.com/smsapi';

			$data = [
				'api_key' => $api_key,
				'type' => 'text',
				'contacts' => $mobile, //'88017xxxxxxxx',
				'senderid' => $sender_id,
				'msg' => $msg
			];

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			$response = curl_exec($ch);
			curl_close($ch);
			return $response;
		} elseif (Helper::getsettings('sms_gateway_default_provider') == 'sslwireless') {

			if (Helper::getsettings('sms_gateway_default_sender') == 'musking') {
				$api_key = \Helper::getsettings('sms_gateway_musking_api_key');
				$sender_id = \Helper::getsettings('sms_gateway_musking_sender_id');
			} else {
				$api_key = \Helper::getsettings('sms_gateway_non_musking_api_key');
				$sender_id = \Helper::getsettings('sms_gateway_non_musking_sender_id');
			}

			$params = [
				"api_token" => $api_key,
				"sid" =>  $sender_id,
				"msisdn" => $mobile,
				"sms" => $msg,
				"csms_id" => rand(1000, 999999)
			];

			$url = trim('https://smsplus.sslwireless.com', '/') . "/api/v3/send-sms";
			$params = json_encode($params);

			$ch = curl_init(); // Initialize cURL
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen($params),
				'accept:application/json'
			));

			$response = curl_exec($ch);

			curl_close($ch);

			return $response;

		}else if(Helper::getsettings('sms_gateway_default_provider') == 'icombd'){

			if (Helper::getsettings('sms_gateway_default_sender') == 'musking') {
				$sender_id = \Helper::getsettings('sms_gateway_musking_sender_id');
			} else {
				$sender_id = \Helper::getsettings('sms_gateway_non_musking_sender_id');
			} 


			if(substr($mobile, 0, 3) == "+88") $mobile = '' . substr($mobile, 3);
			if(substr($mobile, 0, 2) == "88") $mobile = '' . substr($mobile, 2);

			$curl = curl_init();

			$to = '88'.$mobile;
			$messageBody = $msg;

			$data =[
				"username"=> \Helper::getsettings('sms_gateway_username'),
				"password"=> \Helper::getsettings('sms_gateway_password'),
				"sender"=>	$sender_id,
				"message"=> $msg,
				"to"=>$to
			];

			curl_setopt_array($curl, array(
			CURLOPT_URL => "http://api.icombd.com/api/v2/sendsms/plaintext",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_CONNECTTIMEOUT => 5,
			CURLOPT_TIMEOUT => 10,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_HTTPHEADER => array(
			"Content-Type: application/json",
			),
			));
			$response = curl_exec($curl);

			$err = curl_error($curl);
			curl_close($curl);

			return $response;
		}elseif (Helper::getsettings('sms_gateway_default_provider') == 'metrotel') {
			$api_key = \Helper::getsettings('sms_gateway_musking_api_key');
			$sender_id = \Helper::getsettings('sms_gateway_musking_sender_id');

			
			$to = $mobile;
			$messageBody = $msg;

			//new 
			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => 'http://joy.metrotel.com.bd/smspanel/smsapi',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_CONNECTTIMEOUT => 5,
			  CURLOPT_TIMEOUT => 10,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS => array('api_key' => $api_key,'type' => 'text','contacts' => $to,'senderid' => $sender_id,'msg' => $messageBody)
			));

			$response = curl_exec($curl);

			curl_close($curl);
			return $response;
		}
	}

	public static function sendSmsNonMusking($mobile, $msg)
	{

		if (Helper::getsettings('sms_gateway_default_provider') == 'mimsms') {
			
			$api_key = \Helper::getsettings('sms_gateway_non_musking_api_key');
			$sender_id = \Helper::getsettings('sms_gateway_non_musking_sender_id');
		
			$url = 'https://esms.mimsms.com/smsapi';

			$data = [
				'api_key' => $api_key,
				'type' => 'text',
				'contacts' => $mobile, //'88017xxxxxxxx',
				'senderid' => $sender_id,
				'msg' => $msg
			];

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			$response = curl_exec($ch);
			curl_close($ch);
			return $response;
		} elseif (Helper::getsettings('sms_gateway_default_provider') == 'sslwireless') {

			$api_key = \Helper::getsettings('sms_gateway_non_musking_api_key');
			$sender_id = \Helper::getsettings('sms_gateway_non_musking_sender_id');
			
			$params = [
				"api_token" => $api_key,
				"sid" =>  $sender_id,
				"msisdn" => $mobile,
				"sms" => $msg,
				"csms_id" => rand(1000, 999999)
			];

			$url = trim('https://smsplus.sslwireless.com', '/') . "/api/v3/send-sms";
			$params = json_encode($params);

			$ch = curl_init(); // Initialize cURL
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen($params),
				'accept:application/json'
			));

			$response = curl_exec($ch);

			curl_close($ch);

			return $response;

		}else if(Helper::getsettings('sms_gateway_default_provider') == 'icombd'){

			$sender_id = \Helper::getsettings('sms_gateway_non_musking_sender_id');
			
			if(substr($mobile, 0, 3) == "+88") $mobile = '' . substr($mobile, 3);
			if(substr($mobile, 0, 2) == "88") $mobile = '' . substr($mobile, 2);

			$curl = curl_init();

			$to = '88'.$mobile;
			$messageBody = $msg;

			$data =[
				"username"=> \Helper::getsettings('sms_gateway_username'),
				"password"=> \Helper::getsettings('sms_gateway_password'),
				"sender"=>	$sender_id,
				"message"=> $msg,
				"to"=>$to
			];

			curl_setopt_array($curl, array(
			CURLOPT_URL => "http://api.icombd.com/api/v2/sendsms/plaintext",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_CONNECTTIMEOUT => 5,
			CURLOPT_TIMEOUT => 10,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_HTTPHEADER => array(
			"Content-Type: application/json",
			),
			));
			$response = curl_exec($curl);

			$err = curl_error($curl);
			curl_close($curl);

			return $response;
		}elseif (Helper::getsettings('sms_gateway_default_provider') == 'metrotel') {
			$api_key = \Helper::getsettings('sms_gateway_musking_api_key');
			$sender_id = \Helper::getsettings('sms_gateway_musking_sender_id');

			
			$to = $mobile;
			$messageBody = $msg;

			//new 
			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => 'http://joy.metrotel.com.bd/smspanel/smsapi',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_CONNECTTIMEOUT => 5,
			  CURLOPT_TIMEOUT => 10,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS => array('api_key' => $api_key,'type' => 'text','contacts' => $to,'senderid' => $sender_id,'msg' => $messageBody)
			));

			$response = curl_exec($curl);

			curl_close($curl);
			return $response;
		}
	}


	public static function sendEmail($email, $subject, $data, $template = 'default')
	{

		Mail::send('emails.' . $template, ['data' => $data], function ($message) use ($email, $subject) {
			$message->from(env('MAIL_FROM_ADDRESS'), $subject);
			$message->to($email)->subject($subject);
		});
	}



	public static function sendPushNotification($user_id, $user_type, $title, $notification, $message, $image = null, $link = null)
	{

		if ($user_type == 1) { //Customer
			$user = DB::table('users')->where('id', $user_id)->where('status', 1)->where('is_deleted', 0)->first();
		} else { //Seller
			$user = DB::table('admins')->where('id', $user_id)->where('status', 1)->first();
		}

		if ($user) {
			$allTokens = [$user->web_device_token, $user->device_token];

			foreach ($allTokens as $device_token) {
				if ($user) {
					$data = [
						'to' => $device_token,
						'content_available' => true,
						"sticky" => true,
						'notification' => [
							'title' => $title,
							'body' => $notification,
							'style' => 'picture',
							"image" => $image,
							"click_action" => $link ?? ''
						],
						'data' => [
							'title' => $title,
							'body' => $message,
							'status' => 'done',
							"image" => $image,
							"click_action" => $link ?? ''
						],

					];

					$curl = curl_init();
					curl_setopt_array($curl, array(
						CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_ENCODING => "",
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_CONNECTTIMEOUT => 5,
						CURLOPT_TIMEOUT => 10,
						CURLOPT_FOLLOWLOCATION => true,
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						CURLOPT_CUSTOMREQUEST => "POST",
						CURLOPT_POSTFIELDS => json_encode($data),
						CURLOPT_HTTPHEADER => array(
							"Authorization: key=AAAAGUrYIpU:APA91bFlwowrDtmYVVtyRpm2rdHufWeC0IsEPWjMZq8mNzzD79DnM-hs_8eHpw5-NgOdR6dgAWpGyOrVcPJ5RNWOXI1hi3Rt_-SqMtw5VqzmgJ_x1LPhy8_DnxBti_xVN81euDjmjSVu",
							"Content-Type: application/json"
						),
					));


					$nData = [];
					$nData['user_id'] = $user_id;
					$nData['user_type'] = $user_type;
					$nData['title'] = $title;
					$nData['description'] = $message;
					$nData['status'] = 1;
					DB::table('notifications')->insert($nData);

					$response = curl_exec($curl);
					curl_close($curl);
					return json_decode($response);
				} else {
					return 'No active user found!';
				}
			}
		}
	}


	public static function simple_product_stock($product_id, $qty)
	{
		$availabe = \DB::table('products')->where('id', $product_id)
			->where('product_type', 'simple')
			->orWhere('product_type', 'digital')
			->orWhere('product_type', 'service')
			->where('in_stock', 1)
			->where('qty', '>=', $qty)
			->where('is_deleted', 0)
			->first();

		if ($availabe) {
			return 1;
		} else {
			return 0;
		}
	}

	public static function product_stock($product_id, $qty)
	{
		$availabe = \DB::table('products')->where('id', $product_id)
			->where('in_stock', 1)
			->where('qty', '>=', $qty)
			->where('is_deleted', 0)
			->first();

		if ($availabe) {
			return true;
		} else {
			return false;
		}
	}

	public static function variable_product_stock($product_id, $qty, $options)
	{
		$result = false;
		$productAvailabe = \DB::table('products')->where('id', $product_id)
			->where('product_type', 'variable')
			->where('is_deleted', 0)
			->first();

		if ($productAvailabe) {
			$custionOptions = \DB::table('product_metas')->where('product_id', $product_id)->where('meta_key', 'custom_options')->first();
			if ($custionOptions) {
				$custionOptionsArray = unserialize($custionOptions->meta_value);
				$optionsExtractedData = [];
				foreach ($options as $key => $val) {
					foreach ($custionOptionsArray as $op) {
						if ($op['title'] == $key) {
							foreach ($op['value'] as $opKey => $opVal) {
								if ($opVal['title'] == $val) {
									$optionsExtractedData[] = $op['value'][$opKey];
								}
							}
						}
					}
				}

				$opCount = 0;
				foreach ($options as $op) $opCount++;


				if (count($optionsExtractedData) == $opCount) {
					$qtyArray = [];
					$totalAdditionalPrice = [];
					foreach ($optionsExtractedData as $exDataVal) {
						if ($exDataVal['qty']) {
							$qtyArray[] = $exDataVal['qty'];
						}

						if ($exDataVal['price']) {
							$totalAdditionalPrice[] = $exDataVal['price'];
						}
					}

					$optionsExtractedData['totalAdditional'] = array_sum($totalAdditionalPrice);

					if ($qtyArray) {
						$minimumQty = min($qtyArray);
					} else {
						$minimumQty = 20;
					}

					if ($qty <= $minimumQty) {
						$result = $optionsExtractedData;
					}
				}
			}
		}

		return $result;
	}

	public static function variable_product_stock_for_pos($product_id, $qty, $options)
	{
		$result = false;
		$productAvailabe = \DB::table('products')->where('id', $product_id)
			->where('product_type', 'variable')
			->where('is_deleted', 0)
			->first();

		if ($productAvailabe) {
			$custionOptions = \DB::table('product_metas')->where('product_id', $product_id)->where('meta_key', 'custom_options')->first();
			if ($custionOptions) {
				$custionOptionsArray = unserialize($custionOptions->meta_value);
				$optionsExtractedData = [];
				foreach ($options as $key => $val) {
					foreach ($val as $k => $v) {
						foreach ($custionOptionsArray as $op) {
							if ($op['title'] == $k) {
								foreach ($op['value'] as $opKey => $opVal) {
									if ($opVal['title'] == $v) {
										$optionsExtractedData[] = $op['value'][$opKey];
									}
								}
							}
						}
					}
				}

				$opCount = 0;
				foreach ($options as $op) $opCount++;


				if (count($optionsExtractedData) == $opCount) {
					$qtyArray = [];
					$totalAdditionalPrice = [];
					foreach ($optionsExtractedData as $exDataVal) {
						if ($exDataVal['qty']) {
							$qtyArray[] = $exDataVal['qty'];
						}

						if ($exDataVal['price']) {
							$totalAdditionalPrice[] = $exDataVal['price'];
						}
					}

					$optionsExtractedData['totalAdditional'] = array_sum($totalAdditionalPrice);

					if ($qtyArray) {
						$minimumQty = min($qtyArray);
					} else {
						$minimumQty = 20;
					}

					if ($qty <= $minimumQty) {
						$result = $optionsExtractedData;
					}
				}
			}
		}

		return $result;
	}


	public static function validateCoupon($coupon_code, $user_id)
	{

		$cartData = Cart::where('user_id', $user_id)->get();

		if (!$cartData) {
			$data['status'] = 0;
			$data['message'] = 'Empty cart. Please add products to your cart first!';
			return $data;
		}

		$sub_total = 0;
		$cartCategory = [];
		$cartBrands = [];
		$cartProducts = [];
		$cartSellers = [];
		foreach ($cartData as $single_item) {
			$product = Product::select('brand_id', 'category_id', 'seller_id')->where('id', $single_item->product_id)->where('is_deleted', 0)->first();
			if ($product) {
				$cartCategory[] = $product->category_id;
				$cartBrands[] = $product->brand_id;
				$cartSellers[] = $product->seller_id;
			}
			$cartProducts[] = $single_item->product_id;
			$sub_total += $single_item->price * $single_item->qty;
		}

		$coupon = DB::table('coupons')
			->where('code', $coupon_code)
			->where('is_deleted', 0)
			->where('quantity', '>', 1)
			->where('expire', '>=', Carbon::now())
			->first();

		if ($coupon) {

			//Minimum Amount Validation
			if ($minimum_amount = $coupon->minimum_amount) {
				if ($sub_total < $minimum_amount) {
					$data['status'] = 0;
					$data['message'] = 'Minimum purchase amount for this coupon is BDT ' . $minimum_amount;
					return $data;
				}
			}

			//Maximum Uses Per User
			if ($max_qty_per_user = $coupon->max_qty_per_user) {
				$usedCoupon = Order::where('user_id', $user_id)->where('coupon_code', $coupon_code)->count();
				if ($usedCoupon >= $max_qty_per_user) {
					$data['status'] = 0;
					$data['message'] = 'Maximum uses limit reached for this coupon.';
					return $data;
				}
			}

			//User Specific Use
			if ($user_ids = $coupon->user_ids) {
				$user_ids_array = explode(',', $user_ids);
				if (!in_array($user_id, $user_ids_array)) {
					$data['status'] = 0;
					$data['message'] = 'You are not eligible to use this coupon.';
					return $data;
				}
			}


			//Seller Specific Use
			if ($seller_ids = $coupon->seller_ids) {
				$seller_ids_array = explode(',', $seller_ids);
				$isAvailable = array_intersect($cartSellers, $seller_ids_array);
				if (!$isAvailable) {
					$data['status'] = 0;
					$data['message'] = 'Sorry! This coupun is used for other store\'s products that you haven\'t in your cart.';
					return $data;
				}
			}


			//Category Specific Use
			if ($category_ids = $coupon->category_ids) {
				$category_ids_array = explode(',', $category_ids);
				$isAvailable = array_intersect($cartCategory, $category_ids_array);
				if (!$isAvailable) {
					$data['status'] = 0;
					$data['message'] = 'Sorry! This coupun is used for other category\'s products that you haven\'t in your cart.';
					return $data;
				}
			}


			//Brand Specific Use
			if ($brand_ids = $coupon->brand_ids) {
				$brand_ids_array = explode(',', $brand_ids);
				$isAvailable = array_intersect($cartBrands, $brand_ids_array);
				if (!$isAvailable) {
					$data['status'] = 0;
					$data['message'] = 'Sorry! This coupun is used for other brand\'s products that you haven\'t in your cart.';
					return $data;
				}
			}

			//Product Specific Use
			if ($product_ids = $coupon->product_ids) {
				$product_ids_array = explode(',', $product_ids);
				$isAvailable = array_intersect($cartProducts, $product_ids_array);
				if (!$isAvailable) {
					$data['status'] = 0;
					$data['message'] = 'Sorry! This coupun is used for other products that you haven\'t in your cart.';
					return $data;
				}
			}

			if ($coupon->type == 1) {
				$data['amount'] = $sub_total * ($coupon->amount / 100);
			} else {
				$data['amount'] = $coupon->amount;
			}

			$data['code'] = $coupon->code;
			$data['status'] = 1;
			$data['message'] = 'Coupon applied successfully.';
		} else {
			$data['status'] = 0;
			$data['message'] = 'Sorry! Invalid or expired coupon code.';
		}

		return $data;
	}


	public static function availableLanguages()
	{
		return DB::table('languages')->where('is_active', 1)->get();
	}



	public static function validateVoucher($voucher_id, $user_id)
	{

		$cartData = Cart::where('user_id', $user_id)->get();

		if (!$cartData) {
			$data['status'] = 0;
			$data['message'] = 'Empty cart. Please add products to your cart first!';
			return $data;
		}

		$sub_total = 0;
		$cartCategory = [];
		$cartBrands = [];
		$cartProducts = [];
		$cartSellers = [];
		foreach ($cartData as $single_item) {
			$product = Product::select('brand_id', 'category_id', 'seller_id')->where('id', $single_item->product_id)->where('is_deleted', 0)->first();
			if ($product) {
				$cartCategory[] = $product->category_id;
				$cartBrands[] = $product->brand_id;
				$cartSellers[] = $product->seller_id;
			}
			$cartProducts[] = $single_item->product_id;
			$sub_total += $single_item->price * $single_item->qty;
		}

		$voucher = DB::table('vouchers')
			->where('id', $voucher_id)
			->where('is_deleted', 0)
			->where('quantity', '>', 1)
			->where('valid_from', '<=', Carbon::now())
			->where('valid_to', '>=', Carbon::now())
			->first();

		if ($voucher) {

			//Minimum Amount Validation
			if ($minimum_amount = $voucher->minimum_amount) {
				if ($sub_total < $minimum_amount) {
					$data['status'] = 0;
					$data['message'] = 'Minimum purchase amount for this voucher is BDT ' . $minimum_amount;
					return $data;
				}
			}

			//Maximum Uses Per User
			if ($max_qty_per_user = $voucher->max_qty_per_user) {
				$usedVoucher = Order::where('user_id', $user_id)->where('voucher_code', $voucher_id)->where('status', 1)->count();
				if ($usedVoucher >= $max_qty_per_user) {
					$data['status'] = 0;
					$data['message'] = 'Maximum uses limit reached for this voucher.';
					return $data;
				}
			}

			//User Specific Use
			if ($user_ids = $voucher->user_ids) {
				$user_ids_array = explode(',', $user_ids);
				if (!in_array($user_id, $user_ids_array)) {
					$data['status'] = 0;
					$data['message'] = 'You are not eligible to use this voucher.';
					return $data;
				}
			}


			//Seller Specific Use
			if ($seller_ids = $voucher->seller_ids) {
				$seller_ids_array = explode(',', $seller_ids);
				$isAvailable = array_intersect($cartSellers, $seller_ids_array);
				if (!$isAvailable) {
					$data['status'] = 0;
					$data['message'] = 'Sorry! This voucher is used for other store\'s products that you haven\'t in your cart.';
					return $data;
				}
			}


			//Category Specific Use
			if ($category_ids = $voucher->category_ids) {
				$category_ids_array = explode(',', $category_ids);
				$isAvailable = array_intersect($cartCategory, $category_ids_array);
				if (!$isAvailable) {
					$data['status'] = 0;
					$data['message'] = 'Sorry! This voucher is used for other category\'s products that you haven\'t in your cart.';
					return $data;
				}
			}


			//Brand Specific Use
			if ($brand_ids = $voucher->brand_ids) {
				$brand_ids_array = explode(',', $brand_ids);
				$isAvailable = array_intersect($cartBrands, $brand_ids_array);
				if (!$isAvailable) {
					$data['status'] = 0;
					$data['message'] = 'Sorry! This voucher is used for other brand\'s products that you haven\'t in your cart.';
					return $data;
				}
			}

			//Product Specific Use
			if ($product_ids = $voucher->product_ids) {
				$product_ids_array = explode(',', $product_ids);
				$isAvailable = array_intersect($cartProducts, $product_ids_array);
				if (!$isAvailable) {
					$data['status'] = 0;
					$data['message'] = 'Sorry! This voucher is used for other products that you haven\'t in your cart.';
					return $data;
				}
			}

			$data['amount'] = $voucher->amount;
			$data['code'] = $voucher->id;
			$data['status'] = 1;
			$data['message'] = 'Voucher applied successfully.';
		} else {
			$data['status'] = 0;
			$data['message'] = 'Sorry! Invalid or expired voucher.';
		}

		return $data;
	}




	public static function discount_amount_by_IDS($id)
	{
		$product = \DB::table('products')->where('id', $id)->where('special_price_start', '<=', Carbon::now())->where('special_price_end', '>=', Carbon::now())->first();
		
		if ($product) {
			$special_start = strtotime($product->special_price_start);
			$special_end   = strtotime($product->special_price_end);
			$differenceInSeconds = $special_end - $special_start;
			
			if ($product->special_price_type == 1) {
				if ($differenceInSeconds > 0) {
					$discount = $product->price - $product->special_price;
					return $discount;
				} else {
					return 0;
				}
			}
			if ($product->special_price_type == 2) {
				if ($differenceInSeconds > 0) {
					$discount = $product->special_price / 100 * $product->price;
					return number_format($discount, 0);
				} else {
					return 0;
				}
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}

	public static function price_before_offer($product_id)
	{
		$product = \DB::table('products')->where('id', $product_id)->first();
		return $product->price;
	}

	public static function price_after_offer($product_id)
	{
		return \Helper::getPriceById($product_id);
	}


	public static function get_special_price($product_id)
	{
		return \Helper::getPriceById($product_id);
	}


	public static function offer_percentage_byID($id)
	{
		$product = \DB::table('products')->where('id', $id)->where('special_price_start', '<=', Carbon::now())->where('special_price_end', '>=', Carbon::now())->first();
		if ($product) {
			if ($product->special_price) {
				if ($product->special_price_type == 1) {
					$discount = $product->price - $product->special_price;
					$percentage = $discount / $product->price * 100;
					return number_format($percentage, 0);
				} elseif ($product->special_price_type == 2) {
					return $product->special_price;
				}
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}

	public static function generateUniqueSlug($modelName, $inputName, $inputValue)
	{
		$modelArray = [
			'product' => 'Product',
			'category' => 'Category',
			'brand' => 'Brand',
			'seller' => 'ShopInfo',
			'blog' => 'Blog',
			'blogcategory' => 'BlogCategory',
			'flash_deal' => 'FlashDeal',
			'single_product_offer' => 'SingleProductOffer',
		];
		$model = "App\Models\\" . $modelArray[$modelName];

		$slug = '';
		if ($inputName == 'title') {
			$slug = str_replace(' ', '-', trim(strtolower($inputValue)));
			$slug = str_replace("'", '-', $slug);
			$slug = str_replace(".", '', $slug);
			$slug = str_replace("%", '-', $slug);
		} elseif ($inputName == 'slug') {
			$slug = $inputValue;
		}

		if (!is_null($dbSlug = $model::where('slug', $slug)->first())) {
			$slug = $dbSlug->slug . '-1';
		}

		if ($dbSlugNew = $model::where('slug', $slug)->first()) {
			$slug = $dbSlugNew->slug . '-' . rand(1111, 9999);
		}

		return $slug;
	}



	public static function get_product_by_slug($slug)
	{
		$product = \DB::table('products')->where('slug', $slug)->where('is_active', 1)->where('is_deleted', 0)->first();
		if ($product) {
			return $product;
		} else {
			return null;
		}
	}

	public static function get_product_by_id($id)
	{
		$product = \DB::table('products')->where('id', $id)->where('is_active', 1)->where('is_deleted', 0)->first();
		if ($product) {
			return $product;
		} else {
			return null;
		}
	}

	public static function get_product_by_id_array($id)
	{
		$product = \DB::table('products')->where('id', $id)->where('is_active', 1)->where('is_deleted', 0)->get();
		if ($product) {
			return $product;
		} else {
			return null;
		}
	}

	public static function reveiwByProductId($productId)
	{
		$comments = DB::table('comments')->where('commentable_id', $productId)->where('approved', 1)->get();
		foreach ($comments as $key => $item) {
			$unserialize_comnt = unserialize($item->comment);
			$item->comment_text = $unserialize_comnt['comment'];

			if (array_key_exists('image', $unserialize_comnt)) {
				if ($unserialize_comnt['image']) {

					if (gettype($unserialize_comnt['image']) == 'object') {
						$x = serialize($unserialize_comnt['image']);
						$item->image = explode(",", $x);
					} else {
						$item->image = explode(",", $unserialize_comnt['image']);
					}
				} else {
					$item->image = [];
				}
			}
			$user = User::where('id', $item->commented_id)->first();
			$item->user_name = $user->name ?? '';
		}
		return response()->json($comments, 200);
	}



	public static function reveiwByProductId_api_throw($productId)
	{
		$comments = DB::table('comments')->where('commentable_id', $productId)->where('approved', 1)->where('parent_id', NULL)->get();
		foreach ($comments as $key => $item) {
			$unserialize_comnt = unserialize($item->comment);
			$item->comment_text = $unserialize_comnt['comment'];

			if (array_key_exists('image', $unserialize_comnt)) {
				if ($unserialize_comnt['image']) {
					$item->image = explode(",", $unserialize_comnt['image']);
				} else {
					$item->image = [];
				}
			}
			$user = User::where('id', $item->commented_id)->first();
			$item->user_name = $user->name ?? '';
			$rep = DB::table('comments')->where('parent_id', $item->id)->where('approved', 1)->get();

			foreach ($rep as $k => $v) {
				$unserialize_comnt = unserialize($v->comment);
				$v->comment_text = $unserialize_comnt['comment'];

				if (array_key_exists('image', $unserialize_comnt)) {
					if ($unserialize_comnt['image']) {
						$v->image = $unserialize_comnt['image'];
						//$v->replay_image_type = var_dump($unserialize_comnt['image']);
					} else {
						$v->image = [];
					}
				}
				$user = User::where('id', $v->commented_id)->first();
				$v->user_name = $user->name ?? '';
			}
			$item->replay = $rep;
		}
		return response()->json($comments, 200);
	}





	public static function update_product_quantity($product_id, $qty, $options, $calculation = 'subtraction')
	{

		$productAvailabe = \DB::table('products')->where('id', $product_id)->where('is_deleted', 0)->first();
		$result = false;
		if ($productAvailabe) {
			// if ($productAvailabe->product_type == 'variable') {
			// 	$options = json_decode($options);
			// 	if ($productAvailabe->manage_stock == 1) {
			// 		$custionOptions = \DB::table('product_metas')->where('product_id', $product_id)->where('meta_key', 'custom_options')->first();
			// 		if ($custionOptions) {
			// 			$custionOptionsArray = unserialize($custionOptions->meta_value);
			// 			//$optionsExtractedData = [];
			// 			foreach ($options as $key => $val) {
			// 				foreach ($custionOptionsArray as $k => $op) {
			// 					if ($op['title'] == $key) {
			// 						foreach ($op['value'] as $opKey => $opVal) {
			// 							if ($opVal['title'] == $val) {
			// 								if ($calculation == 'subtraction') {
			// 									$custionOptionsArray[$k]['value'][$opKey]['qty'] = (int) $opVal['qty'] - $qty;
			// 								} elseif ($calculation == 'addition') {
			// 									$custionOptionsArray[$k]['value'][$opKey]['qty'] = (int) $opVal['qty'] + $qty;
			// 								}
			// 							}
			// 						}
			// 					}
			// 				}
			// 			}

			// 			$custionOptions = serialize($custionOptionsArray);
			// 			$updated = \DB::table('product_metas')->where('product_id', $product_id)->where('meta_key', 'custom_options')->update(['meta_value' => $custionOptions]);
			// 			if ($updated) {
			// 				$result = true;
			// 			}
			// 		}
			// 	}
			// } else {
				$old_qty = $productAvailabe->qty;
				$updateable_qty = $old_qty - $qty;
				if($updateable_qty > 0){
					$updated = \DB::table('products')->where('id', $product_id)->update(['qty' => $updateable_qty]);
					if ($updated) {
						$result = true;
					}
				}
				
			// }
		}

		return $result;
	}

	public static function get_meta_by_id($prodct_id)
	{
		$data = \DB::table('product_metas')->where('product_id', $prodct_id)->where('meta_key', 'custom_options')->first();
		if ($data) {
			return $data;
		} else {
			return null;
		}
	}

	public static function get_shipping_cost($user_id, $seller_id, $product_id, $district_id = null)
	{
		$meta = \DB::table('product_metas')->where('product_id', $product_id)->where('meta_key', 'product_shipping_option')->first();
		$result = [];
		$result['free_shipping'] = 0;
		$result['standard_shipping'] = 150;
		$result['express_shipping'] = 250;

		if ($meta) {
			if ($district_id) {
				$userDistrictId = $district_id;
				$sellerAddress = ShopInfo::where('seller_id', $seller_id)->first();
				if ($sellerAddress) {
					$sellerDistrictId = $sellerAddress->district;
					$shipping_option = unserialize($meta->meta_value);
					if ($userDistrictId == $sellerDistrictId) {
						//Deliver to origin

						if (isset($shipping_option['inside_origin'])) {
							$result['free_shipping'] =  $shipping_option['inside_origin']['inside_allow_free_shipping'] ?? null;
							$result['standard_shipping'] = $shipping_option['inside_origin']['inside_standard_shipping'] ?? null;
							$result['express_shipping'] = $shipping_option['inside_origin']['inside_express_shipping'] ?? null;
						}
					} else {
						//Deliver to outside of origin

						if (isset($shipping_option['outside_origin'])) {
							$result['free_shipping'] =  $shipping_option['outside_origin']['outside_allow_free_shipping'] ?? null;
							$result['standard_shipping'] = $shipping_option['outside_origin']['outside_standard_shipping'] ?? null;
							$result['express_shipping'] = $shipping_option['outside_origin']['outside_express_shipping'] ?? null;
						}
					}
				}
			} else {
				$user = User::find($user_id);
				$userAddress = Addresses::find($user->default_address_id);

				if ($userAddress) {
					$userDistrictId = $userAddress->shipping_district;
					$sellerAddress = ShopInfo::where('seller_id', $seller_id)->first();
					if ($sellerAddress) {
						$sellerDistrictId = $sellerAddress->district;
						$shipping_option = unserialize($meta->meta_value);
						if ($userDistrictId == $sellerDistrictId) {
							//Deliver to origin

							if (isset($shipping_option['inside_origin'])) {
								$result['free_shipping'] =  $shipping_option['inside_origin']['inside_allow_free_shipping'] ?? null;
								$result['standard_shipping'] = $shipping_option['inside_origin']['inside_standard_shipping'] ?? null;
								$result['express_shipping'] = $shipping_option['inside_origin']['inside_express_shipping'] ?? null;
							}
						} else {
							//Deliver to outside of origin

							if (isset($shipping_option['outside_origin'])) {
								$result['free_shipping'] =  $shipping_option['outside_origin']['outside_allow_free_shipping'] ?? null;
								$result['standard_shipping'] = $shipping_option['outside_origin']['outside_standard_shipping'] ?? null;
								$result['express_shipping'] = $shipping_option['outside_origin']['outside_express_shipping'] ?? null;
							}
						}
					}
				}
			}
		}
		return $result;
	}


	public static function shipping_cost($type,$weight,$unit){
		$csv = public_path() . '/files/sample-csv/shipping_cost.csv';
        $shipping_cost = Helper::csv_to_array($csv);

        $inside_origin_standard = 0;
        $outside_origin_standard = 0;
        $inside_origin_express = 0;
        $outside_origin_express = 0;

        $prime_unit = '';
        $prime_weight = 0;

        if ($unit == 'gram') {
            $prime_unit = 'kg';
            $prime_weight = ceil(($weight / 1000));
        } elseif ($unit == 'ml') {
            $prime_unit = 'l';
            $prime_weight = ceil(($weight / 1000));
        } else {
            $prime_unit = $unit;
            $prime_weight = $weight;
        }


if($shipping_cost!= null){
	foreach ($shipping_cost as $key => $value) {
		if ($type == $value['product_type'] && $prime_weight == $value['weight'] && $prime_unit == $value['weight_unit']) {
			$inside_origin_standard = ($value['inside_origin_standard'] != '') ? $value['inside_origin_standard'] : 0;
			$outside_origin_standard = ($value['outside_origin_standard'] != '') ? $value['outside_origin_standard'] : 0;
			$inside_origin_express = ($value['inside_origin_express'] != '') ? $value['inside_origin_express'] : 0;
			$outside_origin_express = ($value['outside_origin_express'] != '') ? $value['outside_origin_express'] : 0;
		}
	}
}
        

		return array('inside_origin_standard' => $inside_origin_standard,
		'outside_origin_standard' => $outside_origin_standard,
		'inside_origin_express' => $inside_origin_express,
		'outside_origin_express' => $outside_origin_express);
	} 

	public static function getSellerPendingMaturationBalance($seller_id = null)
	{
		if ($seller_id) {
			return \DB::table('seller_account_history')->where('seller_id', $seller_id)->where('status', 1)->sum('seller_amount');
		} else {
			return \DB::table('seller_account_history')->where('status', 1)->sum('seller_amount');
		}
	}

	public static function get_seller_balance($seller_id = null)
	{
		if ($seller_id) {
			$sellerRevenue  = \DB::table('seller_account_history')->where('seller_id', $seller_id)->where('status', 2)->sum('seller_amount');

			$withdrawalBalance = \DB::table('withdrawal_requests')->where('seller_id', $seller_id)
				->where(function ($query) {
					$query->where('status', 6)
						->orWhere('status', 1);
				})
				->sum('amount_to_pay');

			$refund_amount = \DB::table('product_return')->where('seller_id', $seller_id)->where('return_type', 'refund')->where('status',6)->sum('refund_amount');

			$availabeBalance =  ($sellerRevenue - $withdrawalBalance) - $refund_amount;
			return $availabeBalance;
		} else {
			$sellerRevenue  = \DB::table('seller_account_history')->where('status', 2)->sum('seller_amount');
			$withdrawalBalance = \DB::table('withdrawal_requests')->where('status', '=', 6)->sum('requested_amount');
			$availabeBalance =  $sellerRevenue - $withdrawalBalance;
			return $availabeBalance;
		}
	}

	public static function getSellerWithdrawalAmount($seller_id = null)
	{
		if ($seller_id) {
			return  \DB::table('withdrawal_requests')->where('seller_id', $seller_id)->where('status', '!=', 5)->sum('requested_amount');
		} else {
			return  \DB::table('withdrawal_requests')->where('status', '!=', 5)->sum('requested_amount');
		}
	}

	public static function get_seller_revenue($seller_id = null)
	{
		if ($seller_id) {
			$refund_amount = \DB::table('product_return')->where('seller_id', $seller_id)->where('return_type', 'refund')->sum('refund_amount');
			$seller_amount =
				\DB::table('seller_account_history')->where('seller_id', $seller_id)->where('status', 2)->sum('seller_amount');
			return  $seller_amount - $refund_amount;
		} else {

			$refund_amount = \DB::table('product_return')->where('return_type', 'refund')->sum('refund_amount');
			$seller_amount =
				\DB::table('seller_account_history')->where('status', 2)->sum('seller_amount');
			return  $seller_amount - $refund_amount;

			// return  \DB::table('seller_account_history')->where('status', 2)->sum('seller_amount');
		}
	}

	public static function get_seller_products($seller_id = null)
	{
		if ($seller_id) {
			return  \DB::table('products')->where('seller_id', $seller_id)->where('is_active', 1)->where('is_deleted', 0)->count();
		} else {
			return 0;
		}
	}

	public static function get_seller_sale_amount($seller_id = null)
	{
		if ($seller_id) {
			return  \DB::table('seller_account_history')->where('seller_id', $seller_id)->sum('seller_amount');
		} else {
			return 0;
		}
	}


	public static function get_company_revenue()
	{
		$refund_amount = \DB::table('product_return')->where('return_type', 'refund')->sum('refund_amount');
		$revenue = \DB::table('seller_account_history')->where('status', 2)->sum('price');
		return $revenue - $refund_amount;
	}

	public static function get_company_profit()
	{
		// $refund = \DB::table('product_return')->where('return_type', 'refund')->sum('refund_amount');

		$sellerRevenue = \DB::table('seller_account_history')->where('status', 2)->sum('seller_amount');
		$totalRevenue = \DB::table('seller_account_history')->where('status', 2)->sum('price');
		return $totalRevenue - $sellerRevenue;
	}


	public static function get_status_by_status_id($status_id)
	{
		if ($status_id) {
			$data = \DB::table('statuses')->where('id', $status_id)->first();
			return $data;
		} else {
			return null;
		}
	}

	public static function category_title_by_id($category_id)
	{
		if ($category_id) {
			$data = Category::where('id', $category_id)->first();
			return $data->title;
		} else {
			return null;
		}
	}

	public static function get_blog_category_by_id($category_id)
	{
		if ($category_id) {
			$data = DB::table('blog_categories')->where('id', $category_id)->where('is_active', 1)->where('is_deleted', 0)->first();
			if ($data) {
				return $data->title;
			} else {
				return null;
			}
		} else {
			return null;
		}
	}



	public static function sellerRatingCalculation($seller_id)
	{
		$data = [];
		$shop = ShopInfo::select('name', 'slug')->where('seller_id', $seller_id)->first();

		$product_ids = Product::select('id')->where('seller_id', $seller_id)->where('is_active', 1)->where('is_deleted', 0)->where('product_qc', 1)->where('is_promotion', 0)->get();

		$avg = \DB::table('comments')->whereIn('commentable_id', $product_ids)->avg('rate');;
		$orderDetails = \DB::table('order_details')
			->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
			->where('order_details.seller_id', $seller_id)
			->where('order_details.shipping_method', '!=', 'free_shipping')
			->where('order_details.status', 6)
			->select(
				'order_details.shipping_method',
				'order_details.updated_at',
				'orders.id as related_order_id',
				'orders.updated_at as order_updated_at'
			)
			->get();
		$calculation = [];

		if ($orderDetails) {
			foreach ($orderDetails as $order) {
				if (!is_null($order->related_order_id)) {
					$orderDate = Carbon::parse($order->order_updated_at);
					$orderCompletationDate = Carbon::parse($order->updated_at);;

					if ($order->shipping_method == 'standard_shipping') {
						// max 7 days
						if ($orderDate->diffInDays($orderCompletationDate) < 8) {
							$calculation[] = 1;
						} else {
							$calculation[] = 0;
						}
					} elseif ($order->shipping_method == 'express_shipping') {
						// max 3 days
						if ($orderDate->diffInDays($orderCompletationDate) < 4) {
							$calculation[] = 1;
						} else {
							$calculation[] = 0;
						}
					}
				}
			}
		}

		$countCalculation = (count($calculation) > 0) ?  count($calculation) : 1;
		$calculationSum = array_sum($calculation) * 100 / $countCalculation;
		$data['seller_ratings'] =  number_format($avg * 100 / 5, 0);
		$data['shipped_on_time'] =  number_format($calculationSum, 0);
		$data['chat_response_rate'] =  90;

		return $data;
	}



	public static function setSellerBalance($order_details_id)
	{
		$orderDetails = OrderDetails::where('id', $order_details_id)->first();
		if ($orderDetails) {

			$productPrice = \Helper::getPriceById($orderDetails->product_id);
			$ShopInfo = \DB::table('shop_info')->where('seller_id', $orderDetails->seller_id)->first();
			$order = \DB::table('orders')->where('id', $orderDetails->order_id)->first();

			if ($ShopInfo) {
				$commission_percent = $ShopInfo->commission_percent;
				$commission = $commission_percent / 100;
			} else {
				$commission_percent = 10;
				$commission = $commission_percent / 100;
			}

			$commission_amount = $productPrice * $orderDetails->product_qty * $commission;
			$originalPrice = $productPrice * $orderDetails->product_qty;
			$sellerAmount = $originalPrice - $commission_amount;

			$sellerAccountHistory = new SellerAccountHistory;
			$sellerAccountHistory->seller_id = $orderDetails->seller_id;
			$sellerAccountHistory->order_id = $orderDetails->order_id;
			$sellerAccountHistory->product_id = $orderDetails->product_id;
			$sellerAccountHistory->order_details_id = $order_details_id;
			$sellerAccountHistory->qty = $orderDetails->product_qty;
			$sellerAccountHistory->price = $originalPrice;
			$sellerAccountHistory->seller_amount = $sellerAmount;
			$sellerAccountHistory->commission_percent = $commission_percent;
			$sellerAccountHistory->commission_amount = $commission_amount;
			$sellerAccountHistory->source_payment_id = $order->payment_id;
			$sellerAccountHistory->payment_method = $order->payment_method;
			$sellerAccountHistory->status = 0;
			$sellerAccountHistory->save();
		}
	}


	public static function csv_to_array($filename = '', $delimiter = ',')
	{
		if (!file_exists($filename) || !is_readable($filename)) return FALSE;
		$header = NULL;
		$data = array();
		$x = [];
		if (($handle = fopen($filename, 'r')) !== FALSE) {
			while (($row = fgetcsv($handle, 10000, $delimiter)) !== FALSE) {
				if (!$header)
					$header = $row;
				else {
					if (count($header) == count($row)) {
						$data[] = array_combine($header, $row);
					}
				}
			}
			fclose($handle);
		}
		return $data;
	}

	function mysql_escape($string)
	{

		return preg_replace('/[\x00-\x1F\x7F]/u', '', $string);
	}

	public static function calculateAditionalCost($product_id, $aditional_option)
	{
		$aditional_cost = 0;
		$variableOptions = json_decode($aditional_option);

		$product = Product::find($product_id);
		if ($product->product_type == 'variable') {
			$product_meta = ProductMeta::where('product_id', $product->id)->where('meta_key', 'custom_options')->first();

			$meta_value = unserialize($product_meta->meta_value);

			foreach ($meta_value as $key => $value) {
				foreach ($value['value'] as $row) {
					foreach ($variableOptions as $opk => $option) {
						if ($value['title'] == $opk && $row['title'] == $option) {
							$aditional_cost += $row['price'];
						}
					}
				}
			}
		}
		return $aditional_cost;
	}


	public static function setTrashInfo($type, $type_id, $reason, $data)
	{
		$trash = new Trash();
		$trash->type = $type;
		$trash->type_id = $type_id;
		$trash->deleted_by = \Auth::id();
		$trash->reason = $reason;
		$trash->data = json_encode($data);
		$trash->save();
	}

	public static function validateAndDelete($type, $type_id)
	{
		$result['status'] = false;
		$result['message'] = '';


		if ($type == 'product') {
			$product_id = $type_id;
			if (Product::where('id', $product_id)->where('is_deleted', 1)->exists()) {

				if ($OrderDetails = OrderDetails::where('product_id', $product_id)->first()) {
					$result['message'] = 'Order ID MS' . $OrderDetails->order_id . ' has found related with this product! You can not delete a product when it has orders!';
				} else {

					Product::where('id', $product_id)->where('is_deleted', 1)->delete();
					ProductMeta::where('product_id', $product_id)->delete();
					ProductSpecification::where('product_id', $product_id)->delete();

					$result['status'] = true;
					$result['message'] = 'Product has been successfully deleted from your trash!';
				}
			} else {
				$result['message'] = 'Product is not found or not deleted yet!';
			}
		} elseif ($type == 'order') {
			$order_id = $type_id;
			$order = Order::where('id', $order_id)->where('is_deleted', 1)->first();

			if ($order) {
				if ($order->status == 1 || $order->status == 5) {
					//1 = Pending , 5 = Canceled
					OrderDetails::where('order_id', $order_id)->delete();
					Order::where('id', $order_id)->delete();
					SellerAccountHistory::where('order_id', $order_id)->delete();

					$result['status'] = true;
					$result['message'] = 'Order has been successfully deleted from your trash!';
				} else {
					$result['message'] = 'Order ID MS' . $order_id . ' has not payment status Pending or Canceled! You can delete an order when it has payment status Pending or Canceled!';
				}
			} else {
				$result['message'] = 'Order is not found or not deleted yet!';
			}
		} elseif ($type == 'seller') {
			$seller_id = $type_id;
			$seller = Admins::where('id', $seller_id)->where('is_deleted', 1)->first();

			if ($seller) {

				$seller_order = OrderDetails::where('seller_id', $seller_id)->first();
				$seller_product = Product::where('seller_id', $seller_id)->first();

				if ($seller_order) {
					$result['message'] = 'Order ID MS' . $seller_order->id . ' is associated with this seller! You can not delete a seller when he has any order!';
				} elseif ($seller_product) {
					$result['message'] = 'Product ID ' . $seller_product->id . ' is associated with this seller! You can not delete a seller when he has any product!';
				} else {
					Admins::where('id', $seller_id)->delete();
					ShopInfo::where('seller_id', $seller_id)->delete();
					\Helper::deleteFile($seller->avatar, 'uploads/images/administrators/');
					$result['status'] = true;
					$result['message'] = 'Seller has been successfully deleted from your trash!';
				}
			} else {
				$result['message'] = 'Seller is not found or not deleted yet!';
			}
		} elseif ($type == 'admin') {
			$seller_id = $type_id;
			$seller = Admins::where('id', $seller_id)->where('is_deleted', 1)->first();

			if ($seller) {
				Admins::where('id', $seller_id)->delete();
				$result['status'] = true;
				$result['message'] = 'Admin has been successfully deleted from your trash!';
			} else {
				$result['message'] = 'Admin is not found or not deleted yet!';
			}
		} elseif ($type == 'customer') {
			$user_id = $type_id;
			$user = User::where('id', $user_id)->where('is_deleted', 1)->first();

			if ($user) {

				$order = Order::where('user_id', $user_id)->first();

				if ($order) {
					$result['message'] = 'Order ID MS' . $order->id . ' is associated with this customer! You can not delete a customer when he has any order!';
				} else {
					User::where('id', $user_id)->where('is_deleted', 1)->delete();
					\Helper::deleteFile($user->avatar, 'uploads/images/users/');
					$result['status'] = true;
					$result['message'] = 'Customer has been successfully deleted from your trash!';
				}
			} else {
				$result['message'] = 'Customer is not found or not deleted yet!';
			}
		} elseif ($type == 'withdrawal') {
			$withdrawal_id = $type_id;
			$withdrawal = WithdrawalRequest::where('id', $withdrawal_id)->where('is_deleted', 1)->first();

			if ($withdrawal) {
				if ($withdrawal->status == 1 || $withdrawal->status == 5) {
					WithdrawalRequest::where('id', $withdrawal_id)->delete();
					$result['status'] = true;
					$result['message'] = 'Withdrawal request has been successfully deleted from your trash!';
				} else {
					$result['message'] = 'Withdrawal request ID ' . $withdrawal_id . ' has not status with Pending or Canceled! You can delete a withdrawal request  when it has status Pending or Canceled!';
				}
			} else {
				$result['message'] = 'Withdrawal request is not found or not deleted yet!';
			}
		} elseif ($type == 'category') {
			$categoty_id = $type_id;
			$category = Category::where('id', $categoty_id)->where('is_deleted', 1)->first();
			if ($category) {
				$product = Product::whereRaw("FIND_IN_SET($categoty_id,category_id)")->first();
				if ($product) {
					$result['message'] = 'Product ID ' . $product->id . ' is associated with this category! You can not delete a category when it has any product!';
				} else {
					Category::where('id', $categoty_id)->delete();
					$result['status'] = true;
					$result['message'] = 'Category has been successfully deleted from your trash!';
				}
			} else {
				$result['message'] = 'Category is not found or not deleted yet!';
			}
		} elseif ($type == 'blog') {
			$blog_id = $type_id;
			$blog = Blog::where('id', $blog_id)->where('is_deleted', 1)->first();

			if ($blog) {
				Blog::where('id', $blog_id)->where('is_deleted', 1)->delete();
				$result['status'] = true;
				$result['message'] = 'Blog has been successfully deleted from your trash!';
			} else {
				$result['message'] = 'Blog is not found or not deleted yet!';
			}
		} elseif ($type == 'brand') {
			$brand_id = $type_id;
			$brand = Brand::where('id', $brand_id)->where('is_deleted', 1)->first();

			if ($brand) {
				Brand::where('id', $brand_id)->where('is_deleted', 1)->delete();
				$result['status'] = true;
				$result['message'] = 'Brand has been successfully deleted from your trash!';
			} else {
				$result['message'] = 'Brand is not found or not deleted yet!';
			}
		} elseif ($type == 'media') {
			$media_id = $type_id;
			$data = \DB::table('concave_media')->where('id', $media_id)->where('is_deleted', 1)->first();


			if ($data) {

				//Remove Image
				$fileUrl = $data->file_url;
				$Originalfile =  public_path() . '/' . $fileUrl;
				if (file_exists($Originalfile)) {
					unlink($Originalfile);
				}

				//Remove Thumbnail Image
				$thumbnail_url = $data->thumbnail_url;
				if ($thumbnail_url) {
					$OriginalfileThumbnail =  public_path() . '/' . $thumbnail_url;
					if (file_exists($OriginalfileThumbnail)) {
						unlink($OriginalfileThumbnail);
					}
				}

				DB::table('concave_media')->where('id', $media_id)->where('is_deleted', 1)->delete();
				$result['status'] = true;
				$result['message'] = 'Media/File has been successfully deleted from your trash!';
			} else {
				$result['message'] = 'Media/File is not found or not deleted yet!';
			}
		} else {
			$result['message'] = ucfirst($type) . ' is not identified by system. It can not be deleted!';
		}

		return $result;
	}


	public static function setOrderLog($order_id, $order_details_id, $generated_text, $action_by, $reason)
	{
		\DB::table('order_log')->insert([
			'order_id' => $order_id,
			'order_details_id' => $order_details_id,
			'generated_text' => $generated_text,
			'action_by' => $action_by,
			'reason' => $reason,
		]);
	}


	public static function random_string($length)
	{
		$str = "";
		$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		$size = strlen($chars);
		for ($i = 0; $i < $length; $i++) {
			$str .= $chars[rand(0, $size - 1)];
		}
		return $str;
	}

	public static function UserLoyaltyPoits($user_id)
	{

		$loyalty_points = 0;
		$used_point = 0;

		$order_details = OrderDetails::where('user_id', $user_id)->get();
		$total_point_spend = LoyaltyPurchases::where('user_id', $user_id)->sum('usges_point');
		$loyalty_point_validity_days = \Helper::getSettings('loyalty_point_validity_days') ?? 365;

		foreach ($order_details as $row) {
			$date = $row->updated_at;
			$after_add_date = date('Y-m-d', strtotime($date . ' + ' . $loyalty_point_validity_days . ' days'));

			if ($date <= $after_add_date) {
				$loyalty_points += $row->loyalty_point;
			}
		}
		return $loyalty_points - $total_point_spend;
	}


	public static function get_division_title_by_id($id){
		$data = \DB::table('divisions')->where('id', $id)->first();
		if($data){
			return $data->title;
		}else{
			return null;
		}
	}
	public static function get_district_title_by_id($id){
		$data = \DB::table('districts')->where('id', $id)->first();
		if($data){
			return $data->title;
		}else{
			return null;
		}
	}
	
	public static function get_upazila_title_by_id($id){
		$data = \DB::table('upazilas')->where('id', $id)->first();
		if($data){
			return $data->title;
		}else{
			return null;
		}
	}
	public static function get_union_title_by_id($id){
		$data = \DB::table('unions')->where('id', $id)->first();
		if($data){
			return $data->title;
		}else{
			return null;
		}
	}



	public static function validatePartialPayment($payment_amount,$sub_total){
		
		$partial = [];
		$partial['status'] = 1;
		$partial['message'] = '';
	
		$partial_payment_enable = (int)\Helper::getsettings('partial_payment_enable') ?? null;
		$partial_payment_type = \Helper::getsettings('partial_payment_type') ?? null;
		$partial_payment_fixed_or_percentage_amount = \Helper::getsettings('partial_payment_fixed_or_percentage_amount') ?? 10;
		$partial_payment_minimum_amount =  (int)\Helper::getsettings('partial_payment_minimum_amount') ?? null;
		
		if($partial_payment_enable == 1){
			if($partial_payment_type == 'fixed'){

				$validation_amount = $partial_payment_fixed_or_percentage_amount;
				if($payment_amount < $validation_amount){
					$partial['status'] = 0;
					$partial['message'] = 'Minimum partial payment amount for this order is '.$validation_amount;
				}

			}elseif($partial_payment_type == 'percentage'){
				
				$validation_amount = ($partial_payment_fixed_or_percentage_amount / 100)*$sub_total;

				if($payment_amount < $validation_amount){

					if($partial_payment_minimum_amount){
						if($payment_amount < $partial_payment_minimum_amount){
							$partial['status'] = 0;
							$partial['message'] = 'Minimum partial payment amount for this order is BDT '.$partial_payment_minimum_amount;
						}
					}

					$partial['status'] = 0;
					$partial['message'] = 'Minimum partial payment amount for this order is BDT '.$validation_amount;
					
				}

			}else{
				$partial['status'] = 0;
				$partial['message'] = 'Partial payment is not allowed for this order.';
			}
		}else{
			$partial['status'] = 0;
			$partial['message'] = 'Partial payment is not allowed for this order.';
		}

		return $partial;

	}
	
	

	public static function accountHistory($branch_id, $transaction_id,$manage_type , $debit, $credit, $amount, $payment_method, $date, $common_id, $balance, $invoice_no = null){
		$account = new Account();
		$account->branch_id = $branch_id;
		$account->transaction_id = ($transaction_id != '') ? $transaction_id : uniqid();
		$account->manage_type = $manage_type;
		$account->debit = $debit;
		$account->credit = $credit;
		$account->amount = $amount;
		$account->payment_method = $payment_method;
		$account->date = ($date != '') ? $date : date('Y-m-d');
		$account->common_id = $common_id;
		$account->balance = $balance;
		$account->invoice_number = $invoice_no;
		$account->save();
	}

	public static function getBusinessBalance($id = null){
		if($id != null){
			return CurrentAsset::where('branch_id', $id)->sum('amount');
		}else{
			return CurrentAsset::sum('amount');
		}
	}

	public static function getBusinessExpense($id = null){
		if($id != null){
			return Expense::where('status', 6)->where('branch_id', $id)->sum('amount');
		}else{
			return Expense::where('status', 6)->sum('amount');
		}
		
	}

	public static function getBusinessPurchase($id = null){
		if($id != null){
			return Purchase::where('payment_status', 1)->where('shop_id', $id)->sum('paid_amount');
		}else{
			return Purchase::where('payment_status', 1)->sum('paid_amount');
		}
	}

	public static function getBusinessSale($id = null){
		if($id != null){
			return Order::where('status', 6)->sum('total_amount');
		}else{
			return Order::where('status', 6)->sum('total_amount');
		}
	}

	public static function  debitAssetBalance($asset_id, $amount){
		$asset = CurrentAsset::find($asset_id);
		$asset->amount = ($asset->amount - $amount);
		$asset->save();
	}

	public static function  creditAssetBalance($asset_id, $amount){
		$asset = CurrentAsset::find($asset_id);
		$asset->amount = ($asset->amount + $amount);
		$asset->save();
	}

	public static function  debitSupplierBalance($id, $amount){
		$asset = Supplier::find($id);
		$asset->balance = ($asset->balance - $amount);
		$asset->save();
	}

	public static function  creditSupplierBalance($id, $amount){
		$asset = Supplier::find($id);
		$asset->balance = ($asset->balance + $amount);
		$asset->save();
	}

	public static function supplierPayments($supplier_id, $payment_method, $amount, $references_no){
		$payments = new SupplierPayment();
		$payments->supplier_id = $supplier_id;
		$payments->payment_method = $payment_method;
		$payments->amount = $amount;
		$payments->references_no = $references_no;
		$payments->save();
	}

	public static function customerBalanceDebit($customer_id, $amount){
		$user = User::find($customer_id);
		if($user){
			$user->due_balance = ($user->due_balance + $amount);
			$user->save();
		}
	}

	public static function customerBalanceCredit($customer_id, $amount){
		$user = User::find($customer_id);
		if($user){
			$user->due_balance = ($user->due_balance - $amount);
			$user->save();
		}
	}


	public static function convertProductWeightToKg($product_id){
		$product = \DB::table('products')->where('id', $product_id)->first();
		$kg = 0;
		if($product->weight_unit == 'kg' || $product->weight_unit == 'l'){
			$kg += $product->weight;
		}elseif($product->weight_unit == 'gram' || $product->weight_unit == 'ml'){
			$gram = $product->weight;
			$converted_kg = ($gram / 1000);
			$kg += $converted_kg;
		}
		return $kg;
	}


	// pathao api for delivery
	public static function getPathaoAccessTocken(){
		$url = trim('https://api-hermes.pathao.com', '/') . "/aladdin/api/v1/issue-token";

		$params = [
			'client_id' => env('PATHAO_CLIENT_ID'),
			'client_secret' => env('PATHAO_CLIENT_SECRET'),
			'username' => env('PATHAO_CLIENT_USER_NAME'),
			'password' => env('PATHAO_CLIENT_PASSWORD'),
			'grant_type' => 'password'
		];

		$ch = curl_init(); // Initialize cURL
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'accept:application/json'
		));

		$response = curl_exec($ch);
		curl_close($ch);
		$array = json_decode($response);

		if (!$array || empty($array->access_token)) {
			return (object) ['type' => 'error'];
		}

		Session::put('session_access_tocken', $array->access_token);

		return $array;
	}

	public static function getRefreshPathaoAccessTocken(){
		$url = trim('https://api-hermes.pathao.com', '/') . "/aladdin/api/v1/issue-token";

		$token = self::getPathaoAccessTocken();

		$params = [
			'client_id' => env('PATHAO_CLIENT_ID'),
			'client_secret' => env('PATHAO_CLIENT_SECRET'),
			'refresh_token' => $token->refresh_token,
			'grant_type' => 'refresh_token'
		];

		$ch = curl_init(); // Initialize cURL
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'accept:application/json'
		));

		$response = curl_exec($ch);
		curl_close($ch);
		$array = json_decode($response);
		return $array;

	}

	
	public static function getPathaoCity(){
		$url = trim('https://api-hermes.pathao.com', '/') . "/aladdin/api/v1/countries/1/city-list";
		
		$token = self::getPathaoAccessTocken();
		$ch = curl_init(); // Initialize cURL
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type:'. 'application/json',
			'Accept:'.'application/json',
			'Authorization: Bearer '.$token->access_token
		));
		$response = curl_exec($ch);
		curl_close($ch);

		$array = json_decode($response);
		return $array;
	}

	public static function getPathaoZone($city_id = null){
		$url = trim('https://api-hermes.pathao.com', '/') . "/aladdin/api/v1/cities/".$city_id."/zone-list";
		
		if(!empty(Session::get('session_access_tocken'))){
			$token = Session::get('session_access_tocken');
		}else{
			$access_tocken = self::getRefreshPathaoAccessTocken();
			$token = $access_tocken->access_token;
		}

		$ch = curl_init(); // Initialize cURL
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type:'. 'application/json',
			'Accept:'.'application/json',
			'Authorization: Bearer '.$token
		));
		$response = curl_exec($ch);
		curl_close($ch);

		$array = json_decode($response);
		return $array;
	}

	public static function getPathaoAreas($zone_id = null){
		$url = trim('https://api-hermes.pathao.com', '/') . "/aladdin/api/v1/zones/".$zone_id."/area-list";
		
		if(!empty(Session::get('session_access_tocken'))){
			$token = Session::get('session_access_tocken');
		}else{
			$access_tocken = self::getPathaoAccessTocken();
			$token = Session::get('session_access_tocken');
		}

		$ch = curl_init(); // Initialize cURL
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type:'. 'application/json',
			'Accept:'.'application/json',
			'Authorization: Bearer '.$token
		));
		$response = curl_exec($ch);
		curl_close($ch);

		$array = json_decode($response);
		return $array;
	}


	public static function createPathaoStore($name, $contact_name, $contact_number, $address, $secondary_contact, $city_id, $zone_id, $area_id){
		$url = trim('https://api-hermes.pathao.com', '/') . "/aladdin/api/v1/stores";
		$data = [
			'name' => $name,
			'contact_name' => $contact_name, 
			'contact_number' => $contact_number, 
			'address' => $address,
			'secondary_contact' => $secondary_contact, 
			'city_id' => $city_id, 
			'zone_id' => $zone_id, 
			'area_id' => $area_id 
		];

		$token = self::getPathaoAccessTocken();

		$ch = curl_init(); // Initialize cURL
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type:'. 'application/json',
			'Accept:'.'application/json',
			'Authorization: Bearer '.$token->access_token
		));

		$response = curl_exec($ch);

		curl_close($ch);

		return json_decode($response);

	}

	public static function getPathaoStores(){
		$url = trim('https://api-hermes.pathao.com', '/') . "/aladdin/api/v1/stores";
		
		$token = self::getPathaoAccessTocken();

		$ch = curl_init(); // Initialize cURL
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type:'. 'application/json',
			'Accept:'.'application/json',
			'Authorization: Bearer '.$token->access_token
		));
		$response = curl_exec($ch);
		curl_close($ch);

		$array = json_decode(response);
		
		return $array;
	}


	public static function calculateShippingCost($store_id, $item_type, $delivery_type, $item_weight, $recipient_city, $recipient_zone){
		$url = trim('https://api-hermes.pathao.com', '/') . "/aladdin/api/v1/merchant/price-plan";

		$data = [
			'store_id' => $store_id,
			'item_type' => $item_type, 
			'delivery_type' => $delivery_type, 
			'item_weight' => $item_weight,
			'recipient_city' => $recipient_city, 
			'recipient_zone' => $recipient_zone 
		];

		$token = self::getPathaoAccessTocken();
		if (!$token || empty($token->access_token)) {
			return (object) ['type' => 'error'];
		}

		$ch = curl_init(); // Initialize cURL
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type:'. 'application/json',
			'Accept:'.'application/json',
			'Authorization: Bearer '.$token->access_token
		));

		$response = curl_exec($ch);

		curl_close($ch);

		$pricePlan = json_decode($response);

		return $pricePlan ?: (object) ['type' => 'error'];
	}


	public static function buildPathaoOrderPayload($store_id, $merchant_order_id, $recipient_name, $recipient_phone, $recipient_address, $recipient_city, $recipient_zone, $recipient_area, $delivery_type, $item_type, $special_instruction, $item_quantity, $item_weight, $amount_to_collect, $item_description){
		if (trim((string) $recipient_address) === '') {
			throw new \InvalidArgumentException('Recipient address is required.');
		}

		$data = [
			'store_id' => $store_id,
			'merchant_order_id' => $merchant_order_id,
			'recipient_name' => $recipient_name,
			'recipient_phone' => $recipient_phone,
			'recipient_address' => $recipient_address,
			'delivery_type' => $delivery_type,
			'item_type' => $item_type,
			'special_instruction' => $special_instruction,
			'item_quantity' => $item_quantity,
			'item_weight' => $item_weight,
			'amount_to_collect' => $amount_to_collect,
			'item_description' => $item_description
		];

		$mappedLocation = [$recipient_city, $recipient_zone, $recipient_area];
		$hasMappedLocation = count(array_filter($mappedLocation, function ($value) {
			return $value !== null && $value !== '';
		})) > 0;
		$hasValidMappedLocation = count(array_filter($mappedLocation, function ($value) {
			return is_numeric($value) && (int) $value > 0;
		})) === 3;

		if ($hasMappedLocation && !$hasValidMappedLocation) {
			throw new \InvalidArgumentException('Complete Pathao city, zone and area mappings are required for mapped courier orders.');
		}

		if ($hasValidMappedLocation) {
			$data['recipient_city'] = $recipient_city;
			$data['recipient_zone'] = $recipient_zone;
			$data['recipient_area'] = $recipient_area;
		}

		return $data;
	}


	public static function pathaoMakeOrder($store_id, $merchant_order_id, $recipient_name, $recipient_phone, $recipient_address, $recipient_city, $recipient_zone, $recipient_area, $delivery_type, $item_type, $special_instruction, $item_quantity, $item_weight, $amount_to_collect, $item_description){
		if (!self::pathaoCurlRuntimeAvailable()) {
			return (object) [
				'type' => 'error',
				'_local_outcome' => 'pre_network_failure',
				'_local_consignment_id' => null,
			];
		}

		$courierRequestStarted = false;
		$ch = null;
		try {
			$url = trim('https://api-hermes.pathao.com', '/') . "/aladdin/api/v1/orders";
			$data = self::buildPathaoOrderPayload(
			$store_id,
			$merchant_order_id,
			$recipient_name,
			$recipient_phone,
			$recipient_address,
			$recipient_city,
			$recipient_zone,
			$recipient_area,
			$delivery_type,
			$item_type,
			$special_instruction,
			$item_quantity,
			$item_weight,
			$amount_to_collect,
			$item_description
			);

			$token = self::getPathaoAccessTocken();
			if (!$token || empty($token->access_token)) {
				return (object) [
					'type' => 'error',
					'_local_outcome' => 'pre_network_failure',
					'_local_consignment_id' => null,
				];
			}

			$ch = curl_init(); // Initialize cURL
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type:'. 'application/json',
			'Accept:'.'application/json',
			'Authorization: Bearer '.$token->access_token
		));

			$courierRequestStarted = true;
			$response = curl_exec($ch);
			$curlErrno = curl_errno($ch);
			$httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			$ch = null;

			$result = json_decode($response);
			$classification = self::classifyPathaoOrderResponse($result, $httpCode, $curlErrno);
			if (is_object($result)) {
				$result->_local_outcome = $classification['outcome'];
				$result->_local_consignment_id = $classification['consignment_id'];
				return $result;
			}

			return (object) [
				'type' => 'error',
				'_local_outcome' => $classification['outcome'],
				'_local_consignment_id' => null,
			];
		} catch (\Throwable $exception) {
			if ($ch) {
				curl_close($ch);
			}
			\Log::warning('Pathao courier runtime failure.', [
				'phase' => $courierRequestStarted ? 'request_started' : 'pre_network',
				'exception' => get_class($exception),
			]);

			return (object) [
				'type' => 'error',
				'_local_outcome' => $courierRequestStarted ? 'uncertain' : 'pre_network_failure',
				'_local_consignment_id' => null,
			];
		}
	}

	public static function pathaoCurlRuntimeAvailable(): bool
	{
		foreach (['curl_init', 'curl_setopt', 'curl_exec', 'curl_errno', 'curl_getinfo', 'curl_close'] as $function) {
			if (!function_exists($function)) {
				return false;
			}
		}

		return true;
	}

	public static function classifyPathaoOrderResponse($result, int $httpCode, int $curlErrno): array
	{
		if ($curlErrno !== 0 || $httpCode === 0 || $httpCode >= 500 || !is_object($result)) {
			return ['outcome' => 'uncertain', 'consignment_id' => null];
		}

		if (($result->type ?? null) === 'success') {
			$consignmentId = $result->data->consignment_id ?? null;
			if (is_scalar($consignmentId) && trim((string) $consignmentId) !== '') {
				return ['outcome' => 'confirmed', 'consignment_id' => (string) $consignmentId];
			}

			return ['outcome' => 'uncertain', 'consignment_id' => null];
		}

		return ['outcome' => 'rejected', 'consignment_id' => null];
	}
	

}
