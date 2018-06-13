<?php
namespace ApiV2\Model\Community;
use Illuminate\Database\Eloquent\Model;
use DB;
use App\User;
use ApiV2\Services\LogistiksCommonServices\NumberGeneratorServices;
use ApiV2\Services\LogistiksCommonServices\EncrptionTokenService;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\ApiV2\Events\BuyerPostCreatedEvent;

class LikeModel extends Model 
{   

  protected $table = 'article_likes';
  
  public function LikedUsers()
     {
     	return $this->belongsTo(User::class,'user_id')->select('id','username');
     }

}