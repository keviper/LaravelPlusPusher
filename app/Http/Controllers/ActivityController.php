<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class ActivityController extends Controller
{
    var $pusher;
    var $user;

    const DEFAULT_ACTIVITY_CHANNEL = 'activities';

    public function __construct()
    {
        $this->pusher = App::make('pusher');
        $this->user = Session::get('user');
        $this->activityChannel = self::DEFAULT_ACTIVITY_CHANNEL;
    }
    /**
     * Serve the example activities view
     */
    public function getIndex()
    {
        // If there is no user, redirect to GitHub login
        if(!$this->user)
        {
            return redirect('auth/github?redirect=/activities');
        }
        
        // TODO: provide some useful text
        $activity = [
            'text' => $this->user->getNickname() . ' has visited the page',
            'username' => $this->user->getNickname(),
            'avatar' => $this->user->getAvatar(),
            'id' => str_random()
        ];

        // TODO: trigger event
        $this->pusher->trigger($this->activityChannel, 'userVisit', $activity);
        return view('activities', ['actChannel' => $this->activityChannel]);
    }

    /**
     * A new status update has been posted
     * @param Request $request
     */
    public function postStatusUpdate(Request $request)
    {
        $statusText = e($request->input('status_text'));
         $activity = [
            'text' => $statusText,
            'username' => $this->user->getNickname(),
            'avatar' => $this->user->getAvatar(),
            'id' => str_random()
        ];

        
        $this->pusher->trigger($this->activityChannel, 'userVisit', $activity);
        // TODO: trigger event

    }   

    /**
     * Like an exiting activity
     * @param $id The ID of the activity that has been liked
     */
    public function postLike($id)
    {   $activity = [
            'id' => $id,
            'username' => $this->user->getNickname()
            
        ];
        // TODO: trigger event
        $this->pusher->trigger($this->activityChannel, 'liked', $activity);
    }
}
