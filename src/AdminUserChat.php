<?php

namespace Towoju5\AdminUserChat;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminUserChat
{
    /**
     *  Configuration filename found in the config folder
     * @var string
     */
    protected $configFileName = "admin_user_chat";

    /**
     * Get all administrators from the table
     * @return mixed
     */
    public function getAdministrators()
    {
        return DB::select(
            'select * from ' . config($this->configFileName . '.table') .
                ' where ' . config($this->configFileName . '.column_name') . ' = :admin',
            ['admin' => config($this->configFileName . '.admin_role')]
        );
    }

    /**
     *  Send Message to all administrators
     * @param $sender_id
     * @param $message
     */
    public function sendMessageToAllAdministrators(Request $request)
    {
        $sender_id = auth()->id();
        $message = $request->message;
        foreach ($this->getAdministrators() as $administrator) {
            $this->send($sender_id, $this->validateAdminID($administrator), $message);
        }
    }

    /**
     *  Sends a message to the administrator
     * @param $sender
     * @param $recipient
     * @param $message
     * @return mixed
     */
    public function sendMessageToAdministrator(Request $request, $recipient)
    {
        $sender = auth()->id();
        $message = $request->message;
        return $this->send($sender, $recipient, $message);
    }
    /**
     * Check if the provided parameter is an object or a numeric value
     *  it returns a call to the id property on the param if it is an object
     *  and returns the parameter if its numeric
     * @param $administrator
     * @return mixed
     */
    protected function validateAdminID($administrator)
    {
        $get_id = config($this->configFileName . '.user_id');
        if (is_object($administrator)) {
            return $administrator->$get_id;
        } else if (is_numeric($administrator)) {
            return $administrator;
        }
    }

    /**
     * This inserts the message to the DB
     * @param $sender
     * @param $recipient
     * @param $message
     * @return mixed
     */
    public function send($sender, $recipient, $message)
    {
        return DB::insert(
            'insert into ' . config($this->configFileName . '.database') .
                '(sender,recipient,message,message_key,deleted_by_admin,deleted_by_user) values (?, ?, ?, ?, ?, ?)',
            [$sender, $recipient, $message, md5(Str::random(8) . time()), false, false]
        );
    }

    /**
     *  Sends message to a single user
     * @param $sender
     * @param $recipient
     * @param $message
     * @return mixed
     */
    public function sendMessageToUser(Request $request, $recipient)
    {
        $sender = 0;
        $msg = $this->send($sender, $recipient, $request->message);
        return self::response($msg);
    }

    /**
     *  Sends message to all users
     * @param $sender
     * @param $users
     * @param $message
     * @return $this
     */
    public function sendMessageToAllUsers(Request $request)
    {
        $sender = auth()->id();
        foreach (self::users() as $user) {
            $this->send($sender, $user->id, $request->message);
        }
        return self::response($this);
    }

    public function getUserMessages()
    {
        $userId = request()->user()->id;
        $messages = DB::table(config($this->configFileName . '.database'))
                        ->where('sender', $userId)
                        ->orWhere('recipient', $userId)
                        ->orderBy('created_at', 'desc')
                        ->paginate();
        return self::response($messages);
    }

    public function getMessagesByAdmin($userId)
    {
        $messages = DB::table(config($this->configFileName . '.database'))->where('sender', $userId)->groupBy('sender')->get();
        return self::response($messages);
    }

    public function send_message(Request $request)
    {
        $sender = $request->user()->id;
        $message = $request->message;
        $recipient = 0;
        $send = $this->send($sender, $recipient, $message);
        return self::response($send);
    }

    /**
     * Admin get all messages
     */
    public function getAllMessage()
    {
        $userId = request()->user()->id;
        $tbl = config($this->configFileName . '.database');
        DB::statement("SET SQL_MODE=''");
        $messages = DB::table($tbl)->join('users', "users.id", "=", "$tbl.sender")->groupBy("sender")->where('sender', $userId)->paginate()->through(fn($data) => [
            'id'            =>  $data->id,
            'sender_id'     =>  $data->sender,
            'sender'        =>  $data->name,
            'message'       =>  $data->message,
            'message_key'   =>  $data->message_key,
            'created_at'    =>  $data->created_at
        ]);
        return self::response($messages);
    }

    private function response($obj)
    {
        $result = [
            'status'    =>  true,
            'message'   =>  'success',
            'data'      =>  $obj
        ];

        return $result;
    }

    private function users()
    {
        return User::all();
    }
}
