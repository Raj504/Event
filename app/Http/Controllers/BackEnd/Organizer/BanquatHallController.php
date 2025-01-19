<?php

namespace App\Http\Controllers\Backend\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Http\Requests\BanquatHallRequest;
use App\Models\Language;
use App\Models\BanquatHall;
// use App\Http\Helpers\Hel
use Illuminate\Http\Request;

class BanquatHallController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('organizer.banquat-hall.index',[
            'datas' => BanquatHall::all(),
            'i' => 1
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('organizer.banquat-hall.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BanquatHallRequest $request)
    {
        $data = $request->except([
            '_token',
            'images',
            'featured_image'
        ]);

        if($request->featured_image){
            $filename = uniqid() . '.' . $request->featured_image->getClientOriginalExtension();
            $request->featured_image->move(public_path('images/organizer/banquet/'), $filename);
            $data['featured_image'] = $filename;
        }
        $banquatHall = BanquatHall::create($data);
        store_images($request->images,'organizer/banquet/','BanquatHall',$banquatHall->id);
        return redirect()->back()->with('success','Banquat Hall Create successfully');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
