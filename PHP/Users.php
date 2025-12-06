<?php
class Users {
    private $userid;
    private $username;
    private $email;

    public function __construct($userid, $username, $email) {
        $this->userid = $userid;
        $this->username = $username;
        $this->email = $email;
    }

    public function getUserId() {
        return $this->userid;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getEmail() {
        return $this->email;
    }
}
?>