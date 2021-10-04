<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OTPConfiguration extends Model
{
    protected $table='otp_configuration';
    protected $fillable=['twilio_sid','twilio_auth_token','twilio_mobile_number','msg_authkey','msg_template_id'];

}
