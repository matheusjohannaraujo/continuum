<?php

namespace App\Services;

use App\Models\Contact;

class ContactService
{

    private $contact;

    public function __construct()
    {
        $this->contact = new Contact();
    }

    public function all()
    {
        return $this->contact::orderBy('id', 'ASC')->get();
    }

    public function findId(int $id)
    {
        return $this->contact->find($id);
    }

    public function delete(int $id)
    {
        $this->contact = $this->contact->find($id);
        return $this->contact->delete();
    }  

    private function validateNameEmail(string $name, string $email)
    {
        $back = false;
        if (empty($name) || strlen($name) < 3) {
            $back = true;
            message("name", "Please enter a 'NAME' of at least 3 characters");
        }
        if (empty($email)) {
            $back = true;
            message("email", "Enter a valid 'EMAIL' address");
        }
        if ($back) {
            redirect()->withInput()->back();
        }
        return !$back;
    }

    public function insert(string $name, string $email)
    {
        $this->validateNameEmail($name, $email);
        $this->contact->name = $name;
        $this->contact->email = $email;
        $this->contact->save();
    }

    public function update(int $id, string $name, string $email)
    {
        $this->validateNameEmail($name, $email);
        $this->contact = $this->contact->find($id);
        $this->contact->name = $name;
        $this->contact->email = $email;
        $this->contact->save();
    }

}
