<?php
namespace Dennis1804\IqSwagger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiDocController extends Controller {



	/**
     * function getDoc
     *
     * @consumes multipart/form-data
     *
     * 
     * @return html
     * @author Dennis
     **/

    public function getDoc()
    {
        return view('swagger::documentation');
    }



    public function getSwagger()
    {

        return file_get_contents(public_path('swagger.json'));
    	// return \yaml_parse(file_get_contents(public_path('swagger.yaml')));
    }
}