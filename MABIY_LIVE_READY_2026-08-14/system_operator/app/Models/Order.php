<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Order extends Model
{
    use LogsActivity;

    protected static $logFillable = true;
    protected static $logUnguarded = true;

    public $fillable = [
        'user_id',
        'address_id',
        'shipping_address_snapshot',
        'payment_id',
        'ip_address',
        'phone_number',
        'email',
        'shipping_method',
        'payment_method',
        'message',
        'status'
    ];

    protected $casts = [
        'shipping_address_snapshot' => 'array',
    ];

    public static function shippingAddressSnapshot(Addresses $address): array
    {
        $address->loadMissing('division', 'district', 'upazila', 'union');

        return [
            'shipping_first_name' => $address->shipping_first_name,
            'shipping_last_name' => $address->shipping_last_name,
            'shipping_phone' => $address->shipping_phone,
            'shipping_email' => $address->shipping_email,
            'shipping_address' => $address->shipping_address,
            'shipping_postcode' => $address->shipping_postcode,
            'shipping_division' => $address->shipping_division,
            'shipping_district' => $address->shipping_district,
            'shipping_thana' => $address->shipping_thana,
            'shipping_union' => $address->shipping_union,
            'division_title' => optional($address->division)->title,
            'district_title' => optional($address->district)->title,
            'upazila_title' => optional($address->upazila)->title,
            'union_title' => optional($address->union)->title,
            'city_id' => optional($address->district)->city_id,
            'zone_id' => optional($address->upazila)->zone_id,
            'area_id' => optional($address->union)->area_id,
        ];
    }

    public function getHistoricalShippingAddressAttribute()
    {
        $snapshot = $this->shipping_address_snapshot;
        if (!$snapshot && $this->address) {
            $snapshot = static::shippingAddressSnapshot($this->address);
        }
        if (!$snapshot) return null;

        $snapshot['division'] = (object) ['id' => $snapshot['shipping_division'] ?? null, 'title' => $snapshot['division_title'] ?? null];
        $snapshot['district'] = (object) ['id' => $snapshot['shipping_district'] ?? null, 'title' => $snapshot['district_title'] ?? null, 'city_id' => $snapshot['city_id'] ?? null];
        $snapshot['upazila'] = (object) ['id' => $snapshot['shipping_thana'] ?? null, 'district_id' => $snapshot['shipping_district'] ?? null, 'title' => $snapshot['upazila_title'] ?? null, 'zone_id' => $snapshot['zone_id'] ?? null];
        $snapshot['union'] = ($snapshot['shipping_union'] ?? null) === null ? null : (object) ['id' => $snapshot['shipping_union'], 'upazila_id' => $snapshot['shipping_thana'] ?? null, 'title' => $snapshot['union_title'] ?? null, 'area_id' => $snapshot['area_id'] ?? null];

        return (object) $snapshot;
    }

    // public function getIdAttribute()
    // {
    //   return 'MS' . date('y', strtotime($this->created_at)). $this->id;
    // }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function cart()
    {
        return $this->hasMany(Cart::class);
    }

    public function order_details()
    {
        return $this->hasMany(OrderDetails::class);
    }
    public function orderdetails()
    {
        return $this->hasOne(OrderDetails::class, 'order_id');
    }

    public function address()
    {
        return $this->belongsTo(Addresses::class, 'address_id', 'id');
    }

    public function pickpoint_address()
    {
        return $this->belongsTo(Pickpoints::class, 'address_id', 'id');
    }


    public function auto_renewal()
    {
        return $this->belongsTo(OrderAutoRenewal::class, 'id', 'order_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payments::class);
    }

    public function statuses()
    {
        return $this->hasOne(Status::class, 'id', 'status');
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id');
    }

    public function product_return_by_order_id()
    {
        return $this->hasMany(ReturnRequest::class, 'order_id');
    }

    public static function newOrders()
    {
        $orders = Order::where('status', 0)->orWhere('status', 1)->get();
        if (!is_null($orders)) {
            return count($orders);
        } else {
            return 0;
        }
    }
}
