<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller {

    public function index($location = null) {
        //Get inventory
        $data = DB::table('items')->get();
    
    //if location slug was passed in url, pass it to view as well
    /*if ($location) {
        $data['location_slug'] = $location;
    } else {
        $data['location_slug'] = '';
    }*/

        //load view
        return $data;
    }

    //fetch items for location based on slug
    public function getItems() {
        return 0;
    }

    public function newLocation(Request $request) {
        return 0;
    }

    //create a new item
    public function newItem(Request $request) {
        //Get inventory
        $title = $request->input('title');
        $count = $request->input('count');
        //$location_id = $request->input('location');
        //$picture = $request->input('image');

        DB::table('items')->insert([
            'title' => $title,
            'count' => $count,
            'type' => 'default',
            'folder' => 'default'
        ]);

        $data = DB::table('items')->get();

        //flash message
        //$request->session()->flash('status', 'The Item "' . $name . '" was successfully created');
        //return result body
        return $data;
    }

    public function editItem(Request $request) {


        //flash message
        $request->session()->flash('status', 'The Item was successfully edited!');
        //return result body
        return $result->getBody();
    }

    public function deleteItem(Request $request, $slug) {


        //flash message
        $request->session()->flash('status', 'The Item was successfully deleted!');
        return $result;
    }

}
