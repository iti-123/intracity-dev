<?php
namespace ApiV2\Controllers\Community;

use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use ApiV2\Controllers\BaseController;
use ApiV2\Model\Community\EventsRegister;
use ApiV2\Model\Community\CommunityPost;



class EventsRegisterController extends BaseController
{

    public function register(Request $request)
    {
        try {
            $data = $request->data;
            
            $events = new EventsRegister;
            $events->name = $data['name'];
            $events->email = $data['email'];
            $events->mobile = $data['mobile'];
            $events->address = $data['address'];
            $events->save();

           return response()->json([
            'status' => 'success',
            'payload' => $events->save() ? $events : false
        ], 200);

    } catch (Exception $e) {
       
        return $this->errorResponse($e);

    }

    }


    public function eventDetails(Request $request)
    {
        try {

            $title = $request->title;
            $title = str_replace('-', ' ', $title);
            $community = CommunityPost::select('title', 'heading', 'event_start_date', 
                                        'event_end_date', 'event_start_time', 'event_end_time')
                                        ->where('status', 1)
                                        ->where('title', $title)
                                        ->first();

            return $community;
            
        } catch (Exception $e){

        }
    }

}