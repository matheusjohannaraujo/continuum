<?php

namespace App\Controllers;

use OpenApi\Annotations as OA;
use App\Services\ContactService;

define("API_HOST", site_url());

/**
 * @OA\Info(
 *     title="Swagger UI",
 *     version="0.1"
 * )
 * @OA\Server(url=API_HOST)
 */
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
            | GET       | ContactController@new       | /contact/new
            | POST      | ContactController@create    | /contact/create
            | GET       | ContactController@show      | /contact/show/1
            | GET       | ContactController@edit      | /contact/edit/1
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

    // Redirect page - Create a single contact
    public function new(array $CONFIG = ["method" => "GET"])
    {
        return view("contact/new");
    }

    // Create a single contact
    public function create(array $CONFIG = ["method" => "POST", "csrf" => true])
    {
        $name = input_req("name");
        $email = input_req("email");
        $this->contactService->insert($name, $email);
        redirect()->action("contact.index");
    }
    
    // Get single contact
    public function show(int $id, array $CONFIG = ["method" => "GET"])
    {
        return $this->contactService->findId($id)->toArray();
    }

    // Redirect page - Update a single contact
    public function edit(int $id, array $CONFIG = ["method" => "GET"])
    {
        return view("contact/edit", ["contact" => $this->contactService->findId($id)]);
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

    /**
     * @OA\Get(
     *     tags={"/contact/index_api"},
     *     path="/contact/index_api/",
     *     summary="list contacts",
     *     @OA\Response(
     *         response="200",
     *         description="The contacts",
     *         @OA\MediaType(
     *              mediaType="application/json"
     *         )
     *     )
     * )
     */
    public function index_api(array $CONFIG = ["method" => "GET"])
    {
        return $this->contactService->all()->toArray();
    }

    /**
     * @OA\Get(
     *     tags={"/contact/show_api/{id}"},
     *     path="/contact/show_api/{id}",
     *     summary="show contact",
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Id Contact",
     *          @OA\Schema(
     *             type="integer"
     *          ) 
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="The contact",
     *         @OA\MediaType(
     *              mediaType="application/json"
     *         )
     *     )
     * )
     */
    public function show_api(int $id, array $CONFIG = ["method" => "GET"])
    {
        return $this->contactService->findId($id)->toArray();
    }

    /**
     * @OA\Post(
     *     tags={"/contact/create_api"},
     *     path="/contact/create_api",
     *     summary="create contact",
     *     @OA\Parameter(
     *          name="name",
     *          in="query",
     *          required=true,
     *          description="Name contact",
     *          @OA\Schema(
     *             type="string"
     *          ) 
     *     ),
     *     @OA\Parameter(
     *          name="email",
     *          in="query",
     *          required=true,
     *          description="E-mail contact",
     *          @OA\Schema(
     *             type="string"
     *          ) 
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="The contact",
     *         @OA\MediaType(
     *              mediaType="application/json"
     *         )
     *     )
     * )
     */
    public function create_api(array $CONFIG = ["method" => "POST"])
    {
        $name = input_req("name");
        $email = input_req("email");
        return $this->contactService->insert($name, $email);
    }

    /**
     * @OA\Put(
     *     tags={"/contact/update_api/{id}"},
     *     path="/contact/update_api/{id}",
     *     summary="update contact",
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Id contact",
     *          @OA\Schema(
     *             type="integer"
     *          ) 
     *     ),
     *     @OA\Parameter(
     *          name="name",
     *          in="query",
     *          required=true,
     *          description="Name contact",
     *          @OA\Schema(
     *             type="string"
     *          ) 
     *     ),
     *     @OA\Parameter(
     *          name="email",
     *          in="query",
     *          required=true,
     *          description="E-mail contact",
     *          @OA\Schema(
     *             type="string"
     *          ) 
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="The contact",
     *         @OA\MediaType(
     *              mediaType="application/json"
     *         )
     *     )
     * )
     */
    public function update_api(int $id, array $CONFIG = ["method" => "PUT"])
    {
        $name = input_req("name");
        $email = input_req("email");
        return $this->contactService->update($id, $name, $email);
    }

    /**
     * @OA\Delete(
     *     tags={"/contact/destroy_api/{id}"},
     *     path="/contact/destroy_api/{id}",
     *     summary="delete contact",
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Id contact",
     *          @OA\Schema(
     *             type="integer"
     *          ) 
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="The contact",
     *         @OA\MediaType(
     *              mediaType="application/json"
     *         )
     *     )
     * )
     */
    public function destroy_api(int $id, array $CONFIG = ["method" => "DELETE"])
    {
        return $this->contactService->delete($id);
    }    

}
