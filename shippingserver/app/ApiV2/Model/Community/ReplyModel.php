<?php
namespace ApiV2\Model\Community;
use Illuminate\Database\Eloquent\Model;
use DB;
use App\User;
use ApiV2\Services\LogistiksCommonServices\NumberGeneratorServices;
use ApiV2\Services\LogistiksCommonServices\EncrptionTokenService;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\ApiV2\Events\BuyerPostCreatedEvent;

class ReplyModel extends Model 
{   

  protected $table = 'article_comment_reply';

  public function ReplyLikes()
     {
     	return $this->hasMany(LikeModel::class,'article_comment_reply_id')->where('type','=',3);
     }

     public function ReplyedUsers()
     {
     	return $this->belongsTo(User::class,'user_id')->select('id','username','logo','user_pic');
     }

}