<?php 

/**
 * Profile Controller Class
 *
 * This class gets a users data for display on the profile page so it can be 
 * viewed by the public. If the user has marked their profile private, the 
 * public cannot see the profile.
 */
class ProfileController extends Controller
{
    /**
     * Init method
     *
     * The init methods in controller classes will be called automatically when a 
     * controller is loaded. 
     *
     * Routes
     * - http://root/profile
     * - http://root/profile/init
     *
     * The profile init method uses the url username parameter to find a user
     * in the database. If the user is found then the user data is prepared 
     * for the view.
     * 
     * @param string $user
     */
    public function init($username = '')
    {      
        $profile = $this->load->model('users')->getUser('username', $username);
        $country = explode(', ', $profile['country']);
        $la_days_ago = getDaysAgo($profile['last_active']);
        $sd_days_ago = getDaysAgo($profile['signup_date']);

        $view['logged'] = $this->session->isLogged();
        $view['id'] = $profile['users_id'];
        $view['group'] = $profile['group']; 
        $view['firstname'] = $profile['firstname'];
        $view['lastname'] = $profile['lastname'];
        $view['username'] = $profile['username'];
        $view['email'] = $profile['email'];
        $view['registered'] = isset($profile['signup_date']) ? date('D d F, Y', strtotime($profile['signup_date'])) : '';
        $view['last_active'] = isset($profile['last_active']) ? date('D d F, Y', strtotime($profile['last_active'])) : '';
        $view['avatar'] = $profile['avatar'];
        $view['bio'] = $profile['bio'];
        $view['birthday'] = isset($profile['birthday']) ? date('D d F, Y', strtotime($profile['birthday'])) : '';
        $view['website'] = $profile['website'];
        $view['gender'] = $profile['gender'];
        $view['country'] = isset($country[1]) ? $country[1] : $country[0];
        $view['profile_private'] = $profile['privacy'] == 1 ? 1 : 0;
        $view['la_days_ago'] = round($la_days_ago) != 0 ? round($la_days_ago) . ' days ago' : 'Today';
        $view['sd_days_ago'] = round($sd_days_ago) . ' days ago';

        $data['title'] = $profile['username'];
        $data['description'] = substr($profile['bio'], 0, -200);

        $view['header'] = $this->load->controller('header')->init($data);
        $view['footer'] = $this->load->controller('footer')->init();

        exit($this->load->view('profile/profile', $view));
    }

    private function daysAgo($date)
    {
        $today = date('c');
        $diff = strtotime($today) - strtotime($date);
        $days_ago = (int) $diff / (60 * 60 * 24);

        return $days_ago;
    }
}