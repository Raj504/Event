<?php

namespace App\Http\Controllers\BackEnd\Venu;

use App\Http\Controllers\Controller;
use App\Http\Helpers\UploadFile;
use App\Models\Guest;
use App\Notifications\PushNotification;
use App\Rules\ImageMimeTypeRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\VenuCategory;
use App\Models\Venue;
use App\Models\VenueType;
use App\Models\Venue\VenueServices;
use Illuminate\Support\Facades\Auth;



class VenuController extends Controller
{
   public function VenuePage(){
    $venuCategoryData=VenuCategory::all();
    return view('backend.Venu.venuCategory.index',compact('venuCategoryData'));
   }

   public function storeVenuCategory(Request $request){
    $request->validate([
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        'name' => 'required|string|max:255',
       
    ]);

    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('Venuecategories'), $imageName);
        $imagePath = 'Venuecategories/' . $imageName;
        VenuCategory::create([
            'image' => $imagePath,
            'name' => $request->input('name'),
        ]);

        return back()->with('success', 'Image uploaded successfully.')->with('path', $imagePath);
    }

    return back()->with('error', 'Image upload failed.');
}
 public function addVenue(){
     $venueTypeData=DB::table('venue_types')->get();
     $venuCategoryData=VenuCategory::all();
     $allVenuData=Venue::all();
    //  print_r($venueType);
    //  exit;
    return view('backend.Venu.venues.index',compact('venueTypeData','venuCategoryData','allVenuData'));
 }
    public function storeVenue(Request $request)
    {
         $validatedData = $request->validate([
         'organizer_id' => 'required',
         'name' => 'required|string|max:255',
         'type' => 'required|max:255',
         'category' => 'required|max:255',
         'description' => 'nullable|string',
         'price' => 'nullable',
         'veg_price' => 'nullable',
         'non_veg_price' => 'nullable',
         'capacity' => 'nullable',
         'total_rooms' => 'nullable',
         'parking' => 'nullable|boolean',
         'wifi' => 'nullable|boolean',
         'ac' => 'nullable|boolean',
         'bar' => 'nullable|boolean',
         'decoration' => 'nullable|boolean',
         'location' => 'nullable|string|max:255',
         'cancellation_policy' => 'required',
         'images' => 'nullable|array',
         'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
     ]);
 
     $validatedData['wifi'] = $request->has('wifi') ? 1 : 0;
     $validatedData['ac'] = $request->has('ac') ? 1 : 0;
     $validatedData['parking'] = $request->has('parking') ? 1 : 0;
     $validatedData['bar'] = $request->has('bar') ? 1 : 0;
     $validatedData['decoration'] = $request->has('decoration') ? 1 : 0;
     $venue = Venue::create($validatedData);
 
     if ($request->hasFile('images')) {
         foreach ($request->file('images') as $image) {
             $originalName = $image->getClientOriginalName();
             $imageName = $venue->id . '_' . $originalName;
             $image->move(public_path('venue_images'), $imageName);

             $imageUrl = "https://event.apnademand.com/public/venue_images/" . $imageName;
             DB::table('venue_images')->insert([
                 'venue_id' => $venue->id,
                 'image' => $imageName,
                 'image_url'=> $imageUrl,
             ]);
         }
     }
     return redirect()->back()->with('success', 'Venue created successfully.');
 }
 
    public function editVenue(Request $request){

        $validatedData = $request->validate([
            'data_id'=>'required',
            'organizer_id'=>'required',
            'name' => 'required|string|max:255',
            'type' => 'required|max:255',
            'category' => 'required|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable',
            'veg_price' => 'nullable',
            'non_veg_price' => 'nullable',
            'capacity' => 'nullable',
            'total_rooms' => 'nullable',
            'parking' => 'nullable|boolean',
            'wifi' => 'nullable|boolean',
            'ac' => 'nullable|boolean',
            'bar' => 'nullable|boolean',
            'decoration' => 'nullable|boolean',
            'location' => 'nullable|string|max:255',
            'cancellation_policy' => 'nullable|max:255',

        ]);
    
        $validatedData['wifi'] = $request->has('wifi') ? 1 : 0;
        $validatedData['ac'] = $request->has('ac') ? 1 : 0;
        $validatedData['parking'] = $request->has('parking') ? 1 : 0;
        $validatedData['bar'] = $request->has('bar') ? 1 : 0;
        $validatedData['decoration'] = $request->has('decoration') ? 1 : 0;
      
        $venue = Venue::find($validatedData['data_id']);
        if ($venue) {
            $venue->update($validatedData);
            return redirect()->back()->with('success', 'Venue updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Venue not found.');
        }
    }
    public function deleteVenue(Request $request,$id){
        $venue = Venue::find($id);

        if ($venue) {
            $venue->delete();
            return redirect()->back()->with('success', 'Venue deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'Venue not found.');
        }
    }

    // Venue Type
    public function addVenueType(){
        $venuTypeData=VenueType::all();
        return view('backend.Venu.venueType.index',compact('venuTypeData'));
    }

    public function storeVenueType(Request $request){
        $validatedData = $request->validate([
            'type' => 'required|string|max:255',
        ]);
    
        VenueType::create($validatedData);
    
        return redirect()->back()->with('success', 'Venue Type created successfully.');
    }

    public function editVenueType(Request $request){
        $validatedData = $request->validate([
            'type' => 'required|string|max:255',
            'data_id'=>'required'
        ]);
        $venueType = VenueType::find($validatedData['data_id']);
        if ($venueType) {
            $venueType->update($validatedData);
            return redirect()->back()->with('success', 'Venue Type updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Venue Type not found.');
        }
    }
    public function deleteVenueType(Request $request,$id){
        $venueType = VenueType::find($id);

        if ($venueType) {
            $venueType->delete();
            return redirect()->back()->with('success', 'Venue Type deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'Venue Type not found.');
        }
    }

    public function addVenueService(){
        $venuServiceData=VenueServices::all();
        // $user = Auth::user();
        // $userId = $user->id;
        // dd($userId);
        
        return view('backend.Venu.VenueService.index',compact('venuServiceData'));
    }

    public function storeVenueService(Request $request){
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255',
            'description'=>'required',
            'price'=>'required',
           
        ]);
    
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('VenueServices'), $imageName);
            $imagePath = $imageName;
            VenueServices::create([
                'organizer_id'=>"33",
                'image' => $imagePath,
                'name' => $request->input('name'),
                'description'=>$request->input('description'),
                'price'=>$request->input('price'),
            ]);
    
            return back()->with('success', 'Image uploaded successfully.')->with('path', $imagePath);
    }
}
      public function editVenueService(Request $request){
        $validatedData = $request->validate([
            'data_id'=>'required',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255',
            'description' => 'required',
            'price' => 'required',
        ]);
    
        $venueService = VenueServices::find($validatedData['data_id']);
        if (!$venueService) {
            return redirect()->back()->with('error', 'Venue service not found.');
        }
    
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if (file_exists(public_path('VenueServices/' . $venueService->image))) {
                unlink(public_path('VenueServices/' . $venueService->image));
            }
    
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('VenueServices'), $imageName);
            $venueService->image = $imageName;
        }
    
        $venueService->update([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'price' => $validatedData['price'],
        ]);
    
        return redirect()->back()->with('success', 'Venue service updated successfully.');
      }

      public function deleteVenueService(Request $request,$id){
        $venueType = VenueServices::find($id);

        if ($venueType) {
            $venueType->delete();
            return redirect()->back()->with('success', 'Venue Type deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'Venue Type not found.');
        }
    }

}
