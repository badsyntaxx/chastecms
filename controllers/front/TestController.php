<?php 

/**
 * Test Controller
 * 
 * Have fun
 */
class TestController extends Controller
{
    public function select()
    {
        $model = $this->load->model('test');

        $model->testQuery();
    }

    public function index()
    {   
        $time = new DateTime('NOW');

        $date = $time->format('y.n.j-h.i.s');

        echo $date;
    }

    /**
     * Check Day
     *
     * Compare date against todays date and see if the date is today, tomorrow, yesterday or some other time.
     */
    public function checkDay()
    {
        $timestamp = new DateTime('2018-10-27');
        $timestamp->setTime(0, 0, 0); // reset time part, to prevent partial comparison

        $today = new DateTime(); // This object represents current date/time
        $today->setTime(0, 0, 0); // reset time part, to prevent partial comparison

        $diff = $today->diff($timestamp);
        $diffDays = (int)$diff->format('%R%a'); // Extract days count in interval

        switch($diffDays) {
            case 0:
                echo '//Today';
                break;
            case -1:
                echo '//Yesterday';
                break;
            case +1:
                echo '//Tomorrow';
                break;
            default:
                echo '//Sometime';
        }
    }

    public function tryCountdown()
    {
        $date = strtotime('December 25, 2018 12:00 am');
        $remaining = $date - time();
        $days_remaining = floor($remaining / 86400);
        $hours_remaining = floor(($remaining % 86400) / 3600);
        if ($days_remaining > 0) {
            echo 'There are ' . $days_remaining . ' days and ' . $hours_remaining . ' hours left';
        } else {
            echo 'The time is past already';
        }
    }
}