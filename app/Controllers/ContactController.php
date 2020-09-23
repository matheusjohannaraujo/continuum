<?php

namespace App\Controllers;

use App\Services\ContactService;

class ContactController
{

    private $contactService;
            
    public function __construct()
    {
        $this->contactService = new ContactService;
    }

    /*

        Creating routes from the methods of a controller dynamically
        ------------------------------------------------------------------------------------------------
        This array below configures how the route works
        ------------------------------------------------------------------------------------------------
        array $CONFIG = [
            'method' => 'POST',
            'csrf' => false,
            'jwt' => false,
            'cache' => -1,
            'name' => 'test.create'
        ]
        ------------------------------------------------------------------------------------------------
        To use the route, it is necessary to inform the name of the Controller, the name of the Method 
        and the value of its parameters, the `array parameter $CONFIG` being only for configuration
        ------------------------------------------------------------------------------------------------
        Examples of use the routes:

            Controller = ContactController
            Method = action
            Call = ContactController@action(...params)
        ------------------------------------------------------------------------------------------------
            | HTTP Verb | ContactController@method   | PATH ROUTE
        ------------------------------------------------------------------------------------------------
            | GET       | ContactController@index     | /contact/index
            | GET       | ContactController@index_raw | /contact/index_raw
            | POST      | ContactController@create    | /contact/create
            | GET       | ContactController@new       | /contact/new
            | GET       | ContactController@edit      | /contact/edit/1
            | GET       | ContactController@show      | /contact/show/1
            | PUT       | ContactController@update    | /contact/update/1
            | DELETE    | ContactController@destroy   | /contact/destroy/1
        ------------------------------------------------------------------------------------------------
            
    */

    // This variable informs that the public methods of this controller must be automatically mapped in routes
    private $generateRoutes;

    // List all contact
    public function index(array $CONFIG = ["method" => "GET"])
    {
        return view("contact/index", ["contacts" => $this->contactService->all()]);
    }

    // List all contact raw array
    public function index_raw(array $CONFIG = ["method" => "GET"])
    {
        return $this->contactService->all()->toArray();
    }

    // Create a single contact
    public function create(array $CONFIG = ["method" => "POST", "csrf" => true])
    {
        $name = input_req("name");
        $email = input_req("email");
        $this->contactService->insert($name, $email);
        redirect()->action("contact.index");
    }

    // Redirect page - Create a single contact
    public function new(array $CONFIG = ["method" => "GET"])
    {
        return view("contact/new");
    }

    // Redirect page - Update a single contact
    public function edit(int $id, array $CONFIG = ["method" => "GET"])
    {
        return view("contact/edit", ["contact" => $this->contactService->findId($id)]);
    }   

    // Get single contact
    public function show(int $id, array $CONFIG = ["method" => "GET"])
    {
        return $this->contactService->findId($id)->toArray();
    }   

    // Update a single contact
    public function update(int $id, array $CONFIG = ["method" => "PUT", "csrf" => true])
    {
        $name = input_req("name");
        $email = input_req("email");
        $this->contactService->update($id, $name, $email);
        redirect()->action("contact.index");
    }

    // Destroy a single contact
    public function destroy(int $id, array $CONFIG = ["method" => "DELETE", "csrf" => true])
    {
        $this->contactService->delete($id);
        redirect()->action("contact.index");
    }

}
