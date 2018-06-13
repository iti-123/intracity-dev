<?php

namespace ApiV2\Model\Community;
use Illuminate\Database\Eloquent\Model;
use ApiV2\Model\Community\ReplyModel;
use DB;
use ApiV2\Services\LogistiksCommonServices\NumberGeneratorServices;
use ApiV2\Services\LogistiksCommonServices\EncrptionTokenService;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\ApiV2\Events\BuyerPostCreatedEvent;


class EventsRegister extends Model 
{
    protected $table = 'events_register';
}