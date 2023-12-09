<?php

namespace App\Http\Controllers;

use App\Http\Requests\adminbulderRequest;
use Exception;
use Carbon\Carbon;
use App\Models\Shop;
use App\Models\User;
use App\Models\PjItem;
use App\Models\Product;
use App\Models\PainterJob;
use Illuminate\Http\Request;
use App\Models\WebsiteSetting;
use App\Mail\NewPhotoOrderMail;
use App\Mail\JobBuilderOrderMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use App\Http\Requests\StoreUserRequest;
use App\Models\PainterBoss;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\BuilderModel;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Notifications\WelcomeEmailNotification;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{

    public function index(Request $request)
    {
        $products =  Product::where('status', 1)->orderBy('order', "DESC")->get();
        $banner =  WebsiteSetting::where('key', 'shop_banner')->first();
        return view('new_shop.index', ['products' => $products, 'banner' => $banner]);
    }

    public function garagepaint(Request $request)
    {

        return view('new_shop.garage_paint');
    }



    public function main(Request $request)
    {
        $company_name = $request->user()->company_name;
        $jobs = PainterJob::with('gallaryPlan', 'admin_builders', 'superviser', 'poitem', 'users')
            ->where('user_id', $request->user()->id)
            ->whereNull('parent_id')
            ->get();
        $admin_builders = BuilderModel::all();
        return view('new_shop.navigation', ['jobs' => $jobs, 'admin_builders' => $admin_builders]);
    }

    public function main_invoices(Request $request)
    {
        $user_id = $request->user()->id;
        $invoices = Invoice::with('customer')
            ->where('user_id', $user_id)
            ->orderBy('updated_at', 'desc')->get();

        $inv_numbers = Invoice::max('id') ?? 0;

        $due_invoice = Invoice::where('user_id', $user_id)
            ->whereIn('status', [2])
            ->count();

        return view('new_shop.invoice.main_invices', compact('invoices', 'due_invoice', 'inv_numbers'));
    }


    public function invoice_create(Request $request)
    {
        $customers = Customer::all()->where('user_id', $request->user()->id);
        $admin_buliders = BuilderModel::all();
        $jobs = PainterJob::with('gallaryPlan', 'admin_builders', 'superviser', 'poitem')
            ->where('user_id', $request->user()->id)
            ->whereNull('parent_id')
            ->get();
        $inv_numbers = Invoice::max('id') ?? 0;

        return view('new_shop.invoice.add_invices', compact('customers', 'jobs', 'admin_buliders', 'inv_numbers'));
    }

    public function invoice_create2(Request $request, $jobs_id)
    {
        $customers = Customer::all()->where('user_id', $request->user()->id);
        $admin_buliders = BuilderModel::all();
        $jobs = PainterJob::with('gallaryPlan', 'builder', 'superviser', 'poitem')
            ->where('id', $jobs_id)
            ->where('user_id', $request->user()->id)
            ->whereNull('parent_id')
            ->get();
        $job_number = $jobs_id;
        $inv_numbers = Invoice::max('id') ?? 0;

        return view('new_shop.invoice.add_invices', compact('customers', 'jobs', 'admin_buliders', 'inv_numbers', 'job_number'));
    }




    public function jobs(Request $request)
    {
        $jobs = PainterJob::with('GallaryPlan', 'builder')
            ->where('user_id', $request->user()->id)
            ->whereNull('parent_id')
            ->where('start_date', '>=', date('Y-m-d'))
            ->where('status', 1)
            ->paginate(1); // Paginate the results with 1 job per page

        return view('new_shop.main', ['jobs' => $jobs]);
    }


    public function show($id)
    {
        $job = PainterJob::with('GallaryPlan', 'admin_builders')->find($id);

        if (!$job) {
            abort(404);
        }

        return view('new_shop.jobshow', ['job' => $job]);
    }


    public function jobFiles($id)
    {
        $job = PainterJob::findOrFail($id);

        $files = [];
        if ($job->plan) {
            $files['plan'] = Storage::url('uploads/' . $job->plan);
        }
        if ($job->plan_granny) {
            $files['plan_granny'] = Storage::url('uploads/' . $job->plan_granny);
        }

        return view('new_shop.fileshow', compact('job', 'files'));
    }
    public function showinvoiceing($id, Request $request)
    {
        $jobs = PainterJob::findOrFail($id);
        $status = Invoice::all();
        return view('new_shop.invoice.invoicing_page', compact('jobs', 'status'));
    }


    public function login(Request $request)
    {
        return view('new_shop.login');
    }

    public function loginPost(Request $request)
    {
        $input = $request->all();
        $user = User::where('email', $input['email'])->first();
        if ($user) {
            if ($user->status == 1) {
                $userdata = array(
                    'email' => $request->email,
                    'password' => $request->password
                );
                if (Auth::attempt($userdata)) {
                    return Redirect::route('main')->withErrors(['email' => 'Please check your password!']);
                } else {
                    return Redirect::route('login')->withErrors(['email' => 'Please check your password!']);
                }
            } else {
                return Redirect::route('login')->withErrors(['approval' => 'Your account will be approved soon. Please wait for admin approval.']);
            }
        } else {
            return Redirect::route('login')->withErrors(['email' => 'Please check your email!']);
        }
    }

    public function register(Request $request)
    {
        return view('new_shop.register');
    }

    public function registerPost(StoreUserRequest $request)
    {
        $user = new User();
        $data = $request->only($user->getFillable());
        $data['password'] = Hash::make($data['password']);
        $data['latitude'] = '31.582045';
        $data['longitude'] = '74.329376';
        $data['status'] = '1';
        $user->fill($data)->save();
        $ins_builder = [];
        $ins_builder['user_id'] = $user->id;
        $ins_builder['status'] = 1;
        $ins_builder['account_no'] = $request->account_no;
        DB::table('builders')->insert($ins_builder);
        $user->notify(new WelcomeEmailNotification($request->password));
        $loginuser = User::where('email',  $request->email)->first();
        if ($loginuser->status == 1) {
            $userdata = array(
                'email' => $request->email,
                'password' => $request->password
            );
            Auth::attempt($userdata);
            return Redirect::route('painter.profile');
        } else {
            return Redirect::route('login')->withErrors(['You are not Active User Please Contact Admin']);
        }
    }

    public function  insidePaintUndercoat(Request $request, PainterJob $painterjob)
    {
        if ($painterjob->parent_id == '' || $painterjob->parent_id == NULL) {
            $painterjobData = $painterjob;
        } else {
            $painterjobData = PainterJob::where('id', $painterjob->parent_id)->first();
        }
        $painterjobData->update(['value' => $request->value]);
        $new = $request->new ? $request->new : '';
        $outside = $painterjobData->outside()->with('brand')->get();
        $inside = $painterjobData->inside()->with('brand')->get();
        return view('new_shop.jobs.order_details_new', ['inside' => $inside, 'outside' => $outside, 'painterjob' => $painterjobData, 'type' => $request->type, 'new' => $new, 'id' => $painterjob->id]);
    }

    public function showJobonMap(Request $request, $id)
    {
        $painterJob = PainterJob::where('id', $request->id)->whereNull('parent_id')->first();
        // Get latitude and longitude from the geodata
        $latitudeFrom    = $painterJob->latitude;
        $longitudeFrom    = $painterJob->longitude;
        $latitudeTo        = Auth::user()->latitude;
        $longitudeTo    = Auth::user()->longitude;;
        // Calculate distance between latitude and longitude
        $theta    = $longitudeFrom - $longitudeTo;
        $dist    = sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo)) +  cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta));
        $dist    = acos($dist);
        $dist    = rad2deg($dist);
        $miles    = $dist * 60 * 1.1515;
        $unit = 'K';
        // Convert unit and return distance
        $unit = strtoupper($unit);
        if ($unit == "K") {
            $distance = round($miles * 1.609344, 2) . ' km';
        } elseif ($unit == "M") {
            $distance = round($miles * 1609.344, 2) . ' meters';
        } else {
            $distance = round($miles, 2) . ' miles';
        }
        return view('new_shop.jobs.order_view_onmap', ['latlong' => $painterJob, 'distance' => $distance, 'latitudeTo' => $latitudeTo, 'longitudeTo' => $longitudeTo]);
    }

    public function  jobVerify(Request $request, PainterJob $painterjob)
    {
        $sections = $painterjob->items()->where(function ($query) {
            $query->where('size', '!=', 100);
        })->with('brand')->get()->groupBy('type');
        return view('new_shop.jobs.jobVerify', ['sections' => $sections, 'painterjob' => $painterjob]);
    }

    public function  jobCancel(Request $request, PainterJob $painterjob)
    {
        $painterjob->update(['status' => 3]);
        Session::flash('message', 'job has been Canceled.');
        Session::flash('alert-class', 'alert-success');
        return Redirect::route('jobs');
    }



    public function updateJobData(Request $request)
    {
        $pjItemData = PjItem::where('id', $request->id)->first();
        $painterJob = PainterJob::where('id', $pjItemData->job_id)->first();
        $painterJobNew = PainterJob::where('parent_id', $pjItemData->job_id)->where('key', 1)->first();
        $painterJobData = PainterJob::where('parent_id', $pjItemData->job_id)->first();
        if ($painterJobNew) {
            $input = ['size' => $request->la_size, 'qty' => $request->la_qty];
            PjItem::where([['job_id', $painterJobNew->id], ['area', $request->key_val]])->update($input);
        } else {
            if (isset($painterJobData)  && !empty($painterJobData->key)) {
                $input = ['size' => $request->la_size, 'qty' => $request->la_qty];
                PjItem::where([['job_id', $painterJobData->id], ['area', $request->key_val]])->update($input);
                Session::put('id', $painterJobData->id);
            } else {
                $newid =  PainterJob::orderBy('created_at', 'DESC')->first();
                $se = Session::get('id');
                if ($newid->id == $se) {
                    $input = ['size' => $request->la_size, 'qty' => $request->la_qty];
                    PjItem::where([['job_id', $newid->id], ['area', $request->key_val]])->update($input);
                    Session::put('id', $newid->id);
                } else {
                    $job =  $this->checkpainter($painterJob, $request, 1);
                    Session::put('id', $job);
                }
            }
        }
    }

    public function  completeJob(Request $request, PainterJob $painterJob)
    {
        $idNew = Session::get('id');
        $painterJobNew = PainterJob::where('id', $painterJob->id)->first();
        if (!$request->new) {
            $jobExists = PainterJob::where('parent_id', $painterJobNew->id)->first();
            if (!$jobExists) {
                $job = $this->checkpainter($painterJobNew, $request, NULL);
            }
        } else {
            if (!$idNew) {
                $job = $this->checkpainter($painterJobNew, $request, NULL);
            }
        }
        $ids = [];
        $job = PainterJob::where('id', $painterJobNew->id)->orWhere('parent_id', $painterJobNew->id)->get();
        $jobData = PainterJob::where('parent_id', $painterJobNew->parent_id)->first();
        foreach ($job as $value) {
            $ids[] = $value->id;
        }
        $id = Session::get('id');
        if (empty($request->new)) {
            PainterJob::where('id', $painterJobNew->id)->orWhere('parent_id', $painterJobNew->id)->update(['status' => 2, 'shop_id' => $request->shop_id, 'key' => NULL]);
            PjItem::whereIn('job_id', $ids)->update(['pickup' => $request->pickup]);
        } else {
            PainterJob::where('id', $id)->update(['status' => 2, 'shop_id' => $request->shop_id, 'key' => NULL]);
            PjItem::where('job_id', $id)->update(['pickup' => $request->pickup]);
        }
        $painterJobData = PainterJob::where('id', $id)->first();
        $shop = Shop::find($painterJobData->shop_id);
        if ($shop) {
            $ordersdetails  = DB::table('painter_jobs')
                ->leftJoin('users', 'painter_jobs.user_id', '=', 'users.id')
                ->leftJoin('builders', 'painter_jobs.builder_id', '=', 'builders.id')
                ->leftJoin('shop', 'painter_jobs.shop_id', '=', 'shop.id')
                ->leftJoin('p_job_items', 'painter_jobs.id', '=', 'p_job_items.job_id')
                ->leftJoin('brands', 'p_job_items.brand_id', '=', 'brands.id')
                ->select(
                    'users.first_name',
                    'users.last_name',
                    'users.email',
                    'users.company_name as customer_name',
                    'builders.account_no',
                    'builders.name as builder_name',
                    'builders.brand as builder_brand',
                    'users.phone',
                    'shop.name',
                    'painter_jobs.*',
                    'p_job_items.*',
                    'brands.name as b_name'
                )->where('painter_jobs.id', '=', $painterJobData->id)->get();
            try {
                // Notification::route('mail', $shop->email)
                // ->notify(new NewPhotoOrderNotification($ordersdetails));
                Mail::to([$shop->email, 'finalcoat2@bigpond.com'])->send(new NewPhotoOrderMail($ordersdetails));
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
        Session::forget('id');
        Session::flash('message', 'job has been completed.');
        Session::flash('alert-class', 'alert-success');
        return Redirect::route('completeJobInfo', ['painterjob' => $painterJobNew->id]);
    }

    public function  completeJobInfo(Request $request, PainterJob $painterjob)
    {
        return view('new_shop.jobs.thanks', ['painterjob' => $painterjob]);
    }

    public function  choseShope(Request $request, PainterJob $painterJob)
    {
        // $shops = Shop::all();
        $user_shops  = DB::table('user_shops')->where('user_id', Auth::id())->get('shop_id');
        $ids = [];
        foreach ($user_shops as $mvalue) {
            $ids[] = $mvalue->shop_id;
        }
        $shops  = DB::table('shop')->where('status', 1)->whereIn('id', $ids)->get();
        $job = PjItem::where('job_id', $painterJob->id)->first();
        $new = $request->new ? $request->new : '';
        // $boss = PainterBoss::where('painter_id', Auth::id())->get('boss_id');
        // $bossId = [];
        // foreach ($boss as $value) {
        //     $bossId[] = $value->boss_id;
        // }
        // $painters = User::whereIn('id', $bossId)->get();
        // return view('new_shop.jobs.choseShope', ['bosses' => $painters, 'painterJob' => $painterJob, 'shops' => $shops, 'job' => $job, 'new' => $new, 'type' => $request->type]);
        return view('new_shop.jobs.choseShope', ['painterJob' => $painterJob, 'shops' => $shops, 'job' => $job, 'new' => $new, 'type' => $request->type]);
    }

    public function insertPainterJob($painterJob, $request)
    {
        // $newJob = PainterJob::where('parent_id', $painterJob->id)->orderBy('created_at', 'DESC')->first();
        // if ($newJob) {
        //     $parent = $newJob->parent_id;
        //     $index = $newJob->index + 1;
        // } else {
        //     $index = $painterJob->parent_id ? $painterJob->index + 1 : 1;
        //     $parent = $painterJob->parent_id ? $painterJob->parent_id : $painterJob->id;
        // }

        $newJob = PainterJob::where('parent_id', $painterJob->id)->orderBy('created_at', 'DESC')->first();
        $parent = isset($newJob->parent_id) ? $newJob->parent_id : $painterJob->id;
        $index = isset($newJob->index) ? $newJob->index + 1 : 1;
        $painterjobData = [
            'order_id' => $painterJob->order_id,
            'title' => $painterJob->title,
            'address' => $painterJob->address,
            'start_date' => $painterJob->start_date,
            'received_date' => $painterJob->received_date,
            'stairs_stained' => $painterJob->stairs_stained,
            'cladding' => $painterJob->cladding,
            'render' => $painterJob->render,
            'status' => $painterJob->status,
            'latitude' => $painterJob->latitude,
            'longitude' => $painterJob->longitude,
            'user_id' => $painterJob->user_id,
            'shop_id' => $painterJob->shop_id,
            'builder_id' => $painterJob->builder_id,
            'plan' => $painterJob->plan,
            'colors' => $painterJob->colors,
            'po' => $painterJob->po,
            'area' => $painterJob->area,
            'price' => $painterJob->price,
            'house_size' => $painterJob->house_size,
            'parent_id' => $parent,
            'index' => $index,
            'brand_id' => $painterJob->brand_id,
            // 'key' => $id
        ];
        $jobId = PainterJob::create($painterjobData);
        return $jobId->id;
    }

    public function insertPJob($job, $value)
    {
        $input = [
            'job_id' => $job, 'key' => $value->key,  'area' => $value->area, 'product' => $value->product,
            'color' => $value->color, 'main_area' => $value->main_area, 'painter_edit' => $value->painter_edit, 'note' => $value->note,
            'type' => $value->type, 'brand_id' => $value->brand_id, 'size' => $value->size, 'qty' => $value->qty
        ];
        return $input;
    }

    public function checkpainter($painterJob, $request, $id)
    {
        $idd = $id ? $id : NULL;
        $job = $this->insertPainterJob($painterJob, $request, $idd);
        Session::put('id', $job);
        $pjItem = PjItem::where('job_id', $painterJob->id);
        if ($painterJob->value != 1) {
            $pjItem->where('type', '!=', 'inside');
        }
        $pjItem = $pjItem->get();
        if ($pjItem) {
            foreach ($pjItem as $value) {
                $input = $this->insertPJob($job, $value);
                PjItem::create($input);
            }
        }
        return $job;
    }

    public function updateJob(Request $request, $id)
    {
        $painter = PainterJob::where('id', $id)->first();
        $job = $this->insertPainterJob($painter, $request);
        Session::put('job_id', $job);
        if ($request->outside) {
            $pjItem = PjItem::where('job_id', $id)->where('type', 'outside')->get();
            $ids = [];
            foreach ($pjItem as $value) {
                $input = [
                    'job_id' => $job, 'key' => $value->key,  'area' => $value->area, 'product' => $value->product,
                    'color' => $value->color, 'main_area' => $value->main_area, 'painter_edit' => $value->painter_edit, 'note' => $value->note,
                    'type' => $value->type, 'brand_id' => $value->brand_id, 'size' => $value->size, 'qty' => $value->qty
                ];
                $idPjItem = PjItem::create($input);
                $ids[] = $idPjItem->id;
            }
            foreach ($request->outside as $key => $value) {
                $size = isset($value['size']) ? $value['size'] : 100;
                $qty = isset($value['qty']) ? $value['qty'] : Null;
                $input = [
                    'size' => $size,
                    'qty' => $qty
                ];
                $idPjItem = PjItem::where('key', $key)->whereIn('id', $ids)->update($input);
            }
        }
        if ($request->inside) {
            $pjItem = PjItem::where('job_id', $id)->where('type', 'inside')->get();
            $ids = [];
            foreach ($pjItem as $value) {
                $input = [
                    'job_id' => $job, 'key' => $value->key,  'area' => $value->area, 'product' => $value->product,
                    'color' => $value->color, 'main_area' => $value->main_area, 'painter_edit' => $value->painter_edit, 'note' => $value->note,
                    'type' => $value->type, 'brand_id' => $value->brand_id, 'size' => $value->size, 'qty' => $value->qty
                ];
                $idPjItem = PjItem::create($input);
                $ids[] = $idPjItem->id;
            }
            foreach ($request->inside as $key => $value) {
                $size = isset($value['size']) ? $value['size'] : 100;
                $qty = isset($value['qty']) ? $value['qty'] : Null;
                $input = [
                    'size' => $size,
                    'qty' => $qty
                ];
                $idPjItem = PjItem::where('key', $key)->whereIn('id', $ids)->update($input);
            }
        }
        // Session::flash('message', 'job has been completed.');
        // Session::flash('alert-class', 'alert-success');
        return Redirect::route('choseShop', ['painterJob' => $id]);
    }



    public function painterCompleteJob(Request $request, PainterJob $painterJob)
    {
        $job_id = Session::get('job_id');
        $ids = [];
        $job = PainterJob::where('id', $painterJob->id)->orWhere('parent_id', $painterJob->id)->get();
        foreach ($job as $value) {
            $ids[] = $value->id;
        }
        $jobData = PainterJob::where([['id', $job_id], ['index', 1]])->first();
        if ($jobData) {
            PainterJob::where('id', $painterJob->id)->orWhere('parent_id', $painterJob->id)->update([
                'status' => 2, 'shop_id' => $request->shop_id,
                // 'boss_id'=>$request->boss_id
            ]);
            PjItem::whereIn('job_id', $ids)->update(['pickup' => $request->pickup]);
        } else {
            PainterJob::where('id', $job_id)->update([
                'status' => 2, 'shop_id' => $request->shop_id
                // 'boss_id'=>$request->boss_id
            ]);
            PjItem::where('job_id', $job_id)->update(['pickup' => $request->pickup]);
        }
        $painterJobData = PainterJob::where('id', $job_id)->first();
        $shop = Shop::find($painterJobData->shop_id);
        if ($shop) {
            $ordersdetails  = DB::table('painter_jobs')
                ->leftJoin('users', 'painter_jobs.user_id', '=', 'users.id')
                ->leftJoin('builders', 'painter_jobs.builder_id', '=', 'builders.id')
                ->leftJoin('shop', 'painter_jobs.shop_id', '=', 'shop.id')
                ->leftJoin('p_job_items', 'painter_jobs.id', '=', 'p_job_items.job_id')
                ->leftJoin('brands', 'p_job_items.brand_id', '=', 'brands.id')
                ->select(
                    'users.first_name',
                    'users.last_name',
                    'users.email',
                    'users.company_name as customer_name',
                    'builders.account_no',
                    'builders.name as builder_name',
                    'builders.brand as builder_brand',
                    'users.phone',
                    'shop.name',
                    'painter_jobs.*',
                    'p_job_items.*',
                    'brands.name as b_name'
                )->where('painter_jobs.id', '=', $painterJobData->id)->where('p_job_items.size', '!=', 101)->where('p_job_items.qty', '!=', 0)->get();
            try {
                // Notification::route('mail', $shop->email)
                // ->notify(new NewPhotoOrderNotification($ordersdetails));
                // if ($request->boss_id) {
                //     $userEmail = User::where('id', $request->boss_id)->first();
                //     $email = $userEmail->email;
                // } else {
                $email = $shop->email;
                // }
                Mail::to($email)->send(new NewPhotoOrderMail($ordersdetails));
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }
        Session::forget('job_id');
        Session::flash('message', 'job has been completed.');
        Session::flash('alert-class', 'alert-success');
        return Redirect::route('completeJobInfo', ['painterjob' => $painterJob->id]);
    }
}
