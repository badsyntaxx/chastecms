<?php 

/**
 * Message Controller Class
 */
class MessagesController extends Controller
{
    public function init()
    {
        $this->gusto->authenticate(1);

        exit($this->load->controller('list')->drawList());
    }

    public function drawTable()
    {
        $paginated = $this->load->model('pagination')->paginate('messages', $_POST['orderby'], $_POST['direction'], $_POST['page'], $_POST['limit']);
        $users_model = $this->load->model('users');

        foreach ($paginated['list'] as $message) {

            $sender = $users_model->getUser('users_id', $message['sender']);
            $receiver = $users_model->getUser('users_id', $message['receiver']);
           
            $view['messages'][] = [
                'id' => $message['id'],
                'chain_id' => $message['chain_id'],
                'subject' => $message['subject'],
                'sender' => $sender['username'],
                'receiver' => $receiver['username'],
                'message' => $message['message'],
                'timestamp' => $message['timestamp'],
                'viewed' => $message['viewed'],
                'preview_text' => $message['preview_text']
            ];
        }

        $output = [
            'table' => $this->load->view('messages/list', $view), 
            'start' => $paginated['start']
        ];

        $this->output->json($output, 'exit');
    }

    public function validate($message_type = null)
    {
        if ($this->session->isLogged()) {
            
            $message_model = $this->load->model('message');
            $chain_id = $message_model->getChainId();

            if (!$chain_id) {
                $chain_id = 1;
            } else {
                $chain_id = ++$chain_id;
            }

            $subject = $_POST['subject'];
            $sender = $this->logged_user['username'];
            $receiver = $_POST['receiver'];
            $message = $_POST['message'];

            if ($message_model->insertMessage($chain_id, $subject, $sender, $receiver, $message)) {
                exit('New Message Sent!');
            }
        }  
    }

    private function processMessages($messages)
    {
        foreach ($messages as $m) {
            $arr[] = [
                'id' => $m['message_id'],
                'chain_id' => $m['chain_id'],
                'subject' => $m['subject'],
                'sender' => $m['sender'],
                'receiver' => $m['receiver'],
                'message' => $m['message'],
                'timestamp' => $m['timestamp'],
                'viewed' => $m['viewed'],
                'preview_text' => substr($m['message'], 0, 100)
            ];
        }
        return $arr;
    }

    public function inbox()
    {
        $messages_model = $this->load->model('message');
        $messages = $this->processMessages($messages_model->getMessages('receiver', $this->logged_user['id']));

        $this->output->json($messages);
    }

    public function sent()
    {
        $messages_model = $this->load->model('message');
        $messages = $this->processMessages($messages_model->getMessages('sender', $this->logged_user['id']));

        $this->output->json($messages);
    }
}