<?php

namespace App\Services;

use App\Models\Contact;
use Ramsey\Uuid\Uuid;

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

    public function findId(string $id)
    {
        return $this->contact->where("id", $id)->orWhere("uuid", $id)->first();
    }

    public function delete(string $id)
    {
        $this->contact = $this->findId($id);
        if ($this->contact->delete()) {
            return $this->contact->toArray();
        } else {
            return [];
        }
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
        $this->contact->uuid = Uuid::uuid4();
        $this->contact->name = $name;
        $this->contact->email = $email;
        $this->contact->save();
        if ($this->contact->save()) {
            return $this->contact->toArray();
        } else {
            return [];
        }
    }

    public function update(string $id, string $name, string $email)
    {
        $this->validateNameEmail($name, $email);
        $this->contact = $this->findId($id);
        $this->contact->name = $name;
        $this->contact->email = $email;
        if ($this->contact->save()) {
            return $this->contact->toArray();
        } else {
            return [];
        }        
    }

}
