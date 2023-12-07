<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Brand;
use App\Models\Account;
use App\Models\PainterJob;
use App\Models\GallaryPlan;
use Illuminate\Http\Request;
use App\Http\Requests\AccountRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PainterJobPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, PainterJob $painterJob)
    {
        $gallaryPlans = GallaryPlan::where('Job_id', $painterJob->id)->orderBy('order')->get();
        return view('admin.painter_jobs.plans.index', array('painterJob' => $painterJob, 'gallaryPlans' => $gallaryPlans));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    // In PainterJobPlanController.php

    public function addPhoto($id)
    {
        $painterJob = PainterJob::findOrFail($id);
        return view('painter.add_photo', compact('painterJob'));
    }


    public function storePhoto(Request $request, $id)
    {
        // Validate the request
        $validatedData = $request->validate([
            'photo' => 'required|image|max:10240', // for example, max 10MB
            'order' => 'sometimes|integer' // 'order' is an integer that may not be required
        ]);

        // Handle the image upload
        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('/gallery_images'), $imageName);
            $path = $imageName;
        } else {
            // Handle the error if the file wasn't uploaded
            return redirect()->back()->with('error', 'You must provide a photo.');
        }

        // Create the GalleryPlan record
        $galleryPlan = new GallaryPlan();
        $galleryPlan->job_id = $id;
        $galleryPlan->img_url = $path; // Use the path that we've just created
        $galleryPlan->order = $validatedData['order'] ?? 0; // Default to 0 if not provided
        $galleryPlan->save();

        // Redirect back or to another page with a success message
        return redirect()->route('jobs.show', ['id' => $id])->with('success', 'Photo uploaded successfully!');
    }




    public function create(PainterJob $painterJob)
    {
        $gallaryPlan = new GallaryPlan();
        return view('admin.painter_jobs.plans.create', array('painterJob' => $painterJob, 'gallaryPlan' => $gallaryPlan));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, PainterJob $painterJob)
    {
        $validator = Validator::make($request->all(), [
            'img' => 'dimensions:max_width=500,max_height=500,min_width=500,min_height=500',
        ], [
            'img.dimensions' => 'Image maximum width and height must be 500*500 px and minimum width and height must be 300*300 px.',
        ]);
        if ($validator->fails()) {
            return redirect('admin/painterJob/' . $painterJob->id . '/plans/create')
                ->withErrors($validator)
                ->withInput();
        }
        $gallaryPlan = new GallaryPlan();
        $data = $request->only($gallaryPlan->getFillable());
        $data['job_id'] = $painterJob->id;
        if ($request->hasFile('img')) {
            $image = $request->file('img');
            $imageName = rand(111, 9999999) . $image->getClientOriginalName();
            $image->move(public_path('/gallery_images/'), $imageName);
            $data['img_url'] = $imageName;
        }
        $gallaryPlan->fill($data)->save();
        return redirect()->route("admins.plans.index", ['painterJob' => $painterJob->id])->with("status", "Plan Added successfully");
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
    public function edit(Request $request,  User $painter, Account $account)
    {
        $brands = Brand::all();
        return View::make('admins.account.create', array('account' => $account, 'brands' => $brands, 'painter' => $painter));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AccountRequest $request,  User $painter, Account $account)
    {
        $data = $request->all();
        $account->fill($data)->save();
        Session::flash('message', 'Account updated successfully');
        Session::flash('alert-class', 'alert-success');
        return redirect()->route('admins.accounts.index', ['painter' => $painter->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy(Request $request, PainterJob $painterJob,  GallaryPlan $plan)
    {
        $plan->delete();
        Session::flash('message', 'Plan removed successfully');
        Session::flash('alert-class', 'alert-success');
        return redirect()->route("admins.plans.index", ['painterJob' => $painterJob])->with("status", "Plan Removed successfully");
    }
}
