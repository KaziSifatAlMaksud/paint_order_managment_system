<?php

namespace App\Http\Controllers;

use App\Models\BuilderModel;
use App\Models\Customer;
use App\Mail\SendInvoice;
use App\Models\Invoice;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\PainterJob;
use App\Models\PoItems;
use App\Models\User;
use GuzzleHttp\Psr7\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Models\WebsiteSetting;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BuilderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PainterJobController;
use App\Http\Controllers\WebsiteSettingController;
use App\Http\Controllers\Admin\AdminAccountController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SubcustomerController;
use App\Http\Controllers\PainterJobPlanController;


class InvoiceController extends Controller
{
    public function store(Request $request)
    {

        $user_id = $request->user()->id;
        $validatedData = $request->validate([
            'customer_id' => 'nullable|string',
            'send_email' => 'required|email',
            'inv_number' => 'required|string',
            'date' => 'required|date',
            'purchase_order' => 'nullable|string',
            'job_id' => 'nullable|string',
            'description' => 'nullable|string',
            'address' => 'required|string',
            'job_details' => 'nullable|string',
            'amount' => 'required|numeric',
            'gst' => 'required|numeric',
            'total_due' => 'required|numeric',
            'status' => 'required|numeric',
        ]);
        $validatedData['user_id'] = $user_id;

        if ($request->input('action') == 'send&save') {
            // Prepare data for the PDF

            $painterUser = $request->user();
            $user = User::find($painterUser->id);
            $company_name = $user->company_name;
            $user_address = $user->address;

            $data = [
                'user_id' => $request->user()->id,
                'company_name' =>  $company_name,
                'user_address' =>  $user_address,
                'customer_id' => $request->customer_id,
                'send_email' => $request->send_email,
                'inv_number' =>  $request->inv_number,
                'date' =>  $request->date,
                'purchase_order' =>  $request->purchase_order,
                'job_id' =>  $request->job_id,
                'description' =>   $request->description,
                'address' =>  $request->address,
                'job_details' =>  $request->job_details,
                'amount' =>  $request->amount,
                'gst' =>  $request->gst,
                'total_due' =>  $request->total_due,
                'status' =>  $request->status,
            ];

            try {

                // Generate the PDF
                $pdf = PDF::loadView('new_shop.invoice.invices_pdf', $data);
                $attachmentPath = null;
                if ($request->hasFile('attachment')) {
                    $file = $request->file('attachment');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $attachmentPath = $file->storeAs('', $fileName, 'public');
                    $validatedData['attachment'] = $attachmentPath;
                }

                Mail::send('new_shop.invoice.invices_pdf', $data, function ($message) use ($data, $pdf, $attachmentPath) {
                    $message->to($data["send_email"])
                        ->subject("Your Invoice - " . $data['address'])
                        ->attachData($pdf->output(), "invoice.pdf");
                    if ($attachmentPath) {
                        $fullPath = public_path('uploads/' . $attachmentPath);
                        $message->attach($fullPath);
                    }
                });
                $validatedData['status'] = 2;

                $invoice = Invoice::create($validatedData);
                // $poitem = Poitem::find($poitemId);
                return redirect()->back()->with('go_back', true)->with('success', 'Invoice created & Send successfully.');
            } catch (\Exception $e) {
                // Handle the exception
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        if ($request->input('action') == 'save') {
            if ($request->hasFile('attachment')) {
                $fileName = time() . '_' . $request->file('attachment')->getClientOriginalName();
                $validatedData['attachment'] = $request->file('attachment')->storeAs('', $fileName, 'public');
            }
            $invoice = Invoice::create($validatedData);
            return redirect()->back()->with('go_back', true)->with('success', 'Invoice created successfully.');
        }
    }


    public function invoice_send(Request $request, $jobs_id, $poItem_id)
    {
        $customers = Customer::all()->where('user_id', $request->user()->id);
        $jobs = PainterJob::with('superviser', 'poitem')
            ->where('id', $jobs_id)
            ->where('user_id', $request->user()->id)
            ->whereNull('parent_id')
            ->first();
        $admin_builders = BuilderModel::where('company_name', $jobs->builder->name)->first();
        $poItem = $jobs->poitem()->where('id', $poItem_id)->first();
        $invoice = Invoice::where('id', $poItem->invoice_id)->first();
        return view('new_shop.invoice.send_invices', compact('customers', 'jobs', 'poItem', 'admin_builders', 'invoice'));
    }

    public function invoice_savesend(Request $request, $jobs_id, $poItem_id)
    {
        $validatedData = $request->validate([
            'user_id' => 'nullable|string',
            'customer_id' => 'nullable|string',
            'send_email' => 'required|email',
            'inv_number' => 'required|string',
            'date' => 'required|date',
            'purchase_order' => 'nullable|string',
            'job_id' => 'nullable|string',
            'description' => 'nullable|string',
            'address' => 'required|string',
            'job_details' => 'nullable|string',
            'amount' => 'required|numeric',
            'gst' => 'required|numeric',
            'total_due' => 'required|numeric',
            'status' => 'required|numeric',
        ]);

        if ($request->input('action') == 'send&save') {

            // Prepare data for the PDF

            $painterUser = $request->user();
            $user = User::find($painterUser->id);
            $company_name = $user->company_name;
            $user_address = $user->address;


            $data = [
                'user_id' => $request->user()->id,
                'company_name' =>  $company_name,
                'user_address' =>  $user_address,
                'customer_id' => $request->customer_id,
                'send_email' => $request->send_email,
                'inv_number' =>  $request->inv_number,
                'date' =>  $request->date,
                'purchase_order' =>  $request->purchase_order,
                'job_id' =>  $request->job_id,
                'description' =>   $request->description,
                'address' =>  $request->address,
                'job_details' =>  $request->job_details,
                'amount' =>  $request->amount,
                'gst' =>  $request->gst,
                'total_due' =>  $request->total_due,
                'status' =>  $request->status,
            ];

            try {
                // Initialize PDF
                $pdf = PDF::loadView('new_shop.invoice.invices_pdf', $data);

                // Initialize attachment path
                $attachmentPath = null;

                // Check if the status is not 2
                if ($request->status !== 2) {
                    // Validate and set the status to 2
                    $validatedData = $request->validate([
                        'status' => 'required|numeric',
                    ]);
                    $validatedData['status'] = 2;

                    // Check if there's an attachment file
                    if ($request->hasFile('attachment')) {
                        $file = $request->file('attachment');
                        $fileName = time() . '_' . $file->getClientOriginalName();
                        $attachmentPath = $file->storeAs('', $fileName, 'public');
                        $validatedData['attachment'] = $attachmentPath;
                    }

                    // Create a new invoice
                    $invoice = Invoice::create($validatedData);

                    // Update the related PoItems
                    $poitem = PoItems::find($poItem_id);
                    $poitem->update([
                        "job_id" => $jobs_id,
                        "ponumber" => $request->purchase_order,
                        "description" => $request->description,
                        "price" => $request->amount,
                        "invoice_id" => $invoice->id,
                    ]);
                }

                // Send the email with the PDF and attachment (if available)
                Mail::send('new_shop.invoice.invices_pdf', $data, function ($message) use ($data, $pdf, $attachmentPath) {
                    $message->to($data["send_email"])
                        ->subject("Your Invoice - " . $data['address'])
                        ->attachData($pdf->output(), "invoice.pdf");
                    if ($attachmentPath) {
                        $fullPath = public_path('uploads/' . $attachmentPath);
                        $message->attach($fullPath);
                    }
                });

                // Redirect with success message
                return redirect()->back()->with('success', 'Invoice saved and email sent successfully.');
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        if ($request->input('action') == 'send') {

            $painterUser = $request->user();
            $user = User::find($painterUser->id);
            $company_name = $user->company_name;
            $user_address = $user->address;
            // Prepare data for the PDF
            $data = [
                'user_id' => $request->user()->id,
                'company_name' => $company_name,
                'user_address' => $user_address,
                'customer_id' => $request->customer_id,
                'send_email' => $request->send_email,
                'inv_number' =>  $request->inv_number,
                'date' =>  $request->date,
                'purchase_order' =>  $request->purchase_order,
                'job_id' =>  $request->job_id,
                'description' =>   $request->description,
                'address' =>  $request->address,
                'job_details' =>  $request->job_details,
                'amount' =>  $request->amount,
                'gst' =>  $request->gst,
                'total_due' =>  $request->total_due,
                'status' =>  $request->status,
            ];

            try {

                $pdf = PDF::loadView('new_shop.invoice.invices_pdf', $data);
                $attachmentPath = null;

                if ($request->status !== 2) {
                    $validatedData = $request->validate([
                        'status' => 'required|numeric',
                    ]);

                    $validatedData['status'] = 2;
                    $poItem = PoItems::find($poItem_id);
                    $invoice = Invoice::find($poItem->invoice_id);
                    if ($invoice) {
                        $invoice->update($validatedData);
                    }
                    if ($request->hasFile('attachment')) {
                        $file = $request->file('attachment');
                        $fileName = time() . '_' . $file->getClientOriginalName();
                        $attachmentPath = $file->storeAs('', $fileName, 'public');
                        $validatedData['attachment'] = $attachmentPath;
                    }
                    $invoice = Invoice::create($validatedData);

                    $poitem = PoItems::find($poItem_id);
                    $poitem->update([
                        "job_id" => $jobs_id,
                        "ponumber" => $request->purchase_order,
                        "description" => $request->description,
                        "price" => $request->amount,
                        "invoice_id" => $invoice->id,
                    ]);
                }
                Mail::send('new_shop.invoice.invices_pdf', $data, function ($message) use ($data, $pdf, $attachmentPath) {
                    $message->to($data["send_email"])
                        ->subject("Your Invoice - " . $data['address'])
                        ->attachData($pdf->output(), "invoice.pdf");

                    if ($attachmentPath) {
                        $fullPath = public_path('uploads/' . $attachmentPath);
                        $message->attach($fullPath);
                    }
                });

                // Redirect with success message
                return redirect()->back()->with('success', 'Invoice Sent to Email & updated successfully.');
            } catch (\Exception $e) {
                // Handle the exception
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        if ($request->input('action') == 'save') {
            if ($request->hasFile('attachment')) {
                $fileName = time() . '_' . $request->file('attachment')->getClientOriginalName();
                $validatedData['attachment'] = $request->file('attachment')->storeAs('', $fileName, 'public');
            }
            $invoice = Invoice::create($validatedData);
            $poitem = PoItems::find($poItem_id);
            $poitem->update([
                "job_id" => $jobs_id,
                "ponumber" => $request->purchase_order,
                "description" => $request->description,
                "price" => $request->amount,
                "invoice_id" => $invoice->id,
            ]);
            return redirect()->back()->with('success', 'Invoice created successfully.');
        }

        if ($request->input('action') == 'update') {
            $poItem = PoItems::find($poItem_id);
            $invoice = Invoice::find($poItem->invoice_id);
            if ($invoice) {
                $validatedData['status'] = 1;
                if ($request->hasFile('attachment')) {
                    $fileName = time() . '_' . $request->file('attachment')->getClientOriginalName();
                    $path = $request->file('attachment')->storeAs('public', $fileName);
                    $invoice->update(['attachment' => $path]);
                }
                $invoice->update($validatedData);
            }
            $poitem = PoItems::find($poItem_id);
            if ($poitem) {
                $poitem->update([
                    "job_id" => $jobs_id,
                    "ponumber" => $request->purchase_order,
                    "description" => $request->description,
                    "price" => $request->amount,
                    "invoice_id" => $invoice->id,
                ]);
            }
            return redirect()->back()->with('success', 'Invoice updated successfully.');
        }
        if ($request->input('action') == 'paid') {
            $validatedData = $request->validate([
                'status' => 'required|numeric',
            ]);
            $validatedData['status'] = 3;
            $poItem = PoItems::find($poItem_id);
            $invoice = Invoice::find($poItem->invoice_id);
            if ($invoice) {
                $invoice->update($validatedData);
            }
            return redirect()->back()->with('success', 'Customer Successfully Received Payment.');
        }

        if ($request->input('action') == 'delete') {
            $poItem = PoItems::find($poItem_id);
            $invoice = Invoice::find($poItem->invoice_id);

            if ($invoice) {
                $invoice->delete();
                return redirect()->back()->with('success', 'Invoice deleted successfully.');
            }
            if ($poItem) {
                $poItem->update([
                    "ponumber" => null,
                    "description" => null,
                    "price" => null,
                    "invoice_id" => null,
                ]);
            }

            return redirect()->back()->with('error', 'Invoice not found.');
        }
    }


    public function manual_invoice(Request $request, $id)
    {
        $customers = Customer::all()->where('user_id', $request->user()->id);
        $admin_builders = BuilderModel::all();
        $invoice = Invoice::where('id', $id)->first();
        return view('new_shop.invoice.send_manual_invoices', compact('customers', 'admin_builders', 'invoice'));
    }


    public function manual_invoice_store(Request $request, $invoice_id)
    {
        $user_id = $request->user()->id;
        $validatedData = $request->validate([
            'user_id' => 'nullable|string',
            'customer_id' => 'nullable|string',
            'send_email' => 'required|email',
            'inv_number' => 'required|string',
            'date' => 'required|date',
            'purchase_order' => 'nullable|string',
            'job_id' => 'nullable|string',
            'description' => 'nullable|string',
            'address' => 'required|string',
            'job_details' => 'nullable|string',
            'amount' => 'required|numeric',
            'gst' => 'required|numeric',
            'total_due' => 'required|numeric',
            'status' => 'required|numeric',
        ]);
        $validatedData['user_id'] = $user_id;


        if ($request->input('action') == 'send') {

            $painterUser = $request->user();
            $user = User::find($painterUser->id);
            $company_name = $user->company_name;
            $user_address = $user->address;
            // Prepare data for the PDF
            $data = [
                'user_id' => $request->user()->id,
                'company_name' => $company_name,
                'user_address' => $user_address,
                'customer_id' => $request->customer_id,
                'send_email' => $request->send_email,
                'inv_number' =>  $request->inv_number,
                'date' =>  $request->date,
                'purchase_order' =>  $request->purchase_order,
                'job_id' =>  $request->job_id,
                'description' =>   $request->description,
                'address' =>  $request->address,
                'job_details' =>  $request->job_details,
                'amount' =>  $request->amount,
                'gst' =>  $request->gst,
                'total_due' =>  $request->total_due,
                'status' =>  $request->status,
            ];

            try {


                $pdf = PDF::loadView('new_shop.invoice.invices_pdf', $data);
                $attachmentPath = null;
                if ($request->status !== 2) {
                    $validatedData = $request->validate([
                        'status' => 'required|numeric',
                    ]);

                    $validatedData['status'] = 2;
                    $invoice = Invoice::find($invoice_id);
                    if ($invoice) {
                        $invoice->update($validatedData);
                    }
                    if ($request->hasFile('attachment')) {
                        $file = $request->file('attachment');
                        $fileName = time() . '_' . $file->getClientOriginalName();
                        $attachmentPath = $file->storeAs('', $fileName, 'public');
                        $validatedData['attachment'] = $attachmentPath;
                    }
                    $invoice = Invoice::create($validatedData);
                }
                Mail::send('new_shop.invoice.invices_pdf', $data, function ($message) use ($data, $pdf, $attachmentPath) {
                    $message->to($data["send_email"])
                        ->subject("Your Invoice - " . $data['address'])
                        ->attachData($pdf->output(), "invoice.pdf");

                    if ($attachmentPath) {
                        $fullPath = storage_path('app/public/' . $attachmentPath);
                        $message->attach($fullPath);
                    }
                });

                // Redirect with success message
                return redirect()->back()->with('success', 'Invoice Sent to Email & updated successfully.');
            } catch (\Exception $e) {
                // Handle the exception
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        if ($request->input('action') == 'update') {
            $invoice = Invoice::find($invoice_id);
            if ($invoice) {

                $validatedData['status'] = 1;

                if ($request->hasFile('attachment')) {
                    $fileName = time() . '_' . $request->file('attachment')->getClientOriginalName();
                    $path = $request->file('attachment')->storeAs('public', $fileName);
                    $invoice->update(['attachment' => $path]);
                }
                $invoice->update($validatedData);
                // No need to update the invoice again, it's already updated
                return redirect()->back()->with('success', 'Invoice updated successfully.');
            }
        }
        if ($request->input('action') == 'paid') {
            $validatedData = $request->validate([
                'status' => 'required|numeric',
            ]);
            $validatedData['status'] = 3;

            $invoice = Invoice::find($invoice_id);
            if ($invoice) {
                $invoice->update($validatedData);
            }
            return redirect()->back()->with('success', 'Customer Successfully Received Payment.');
        }

        if ($request->input('action') == 'delete') {
            $invoice = Invoice::find($invoice_id);

            if ($invoice) {
                $invoice->delete();
                return redirect()->route('invoice')->with('success', 'Invoice deleted successfully.');
            }

            return redirect()->back()->with('error', 'Invoice not found.');
        }
    }

    public function late(Request $request)
    {

        $invoices = Invoice::all();
        return view('new_shop.invoice.late_invices', compact('invoices'));
    }
}
