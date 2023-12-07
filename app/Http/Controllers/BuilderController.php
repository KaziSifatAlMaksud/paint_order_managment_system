<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Builder;
use App\Models\BuilderModel;
use Illuminate\Http\Request;
use App\Http\Requests\adminbulderRequest;
use Illuminate\Support\Facades\Validator;

class BuilderController extends Controller
{
    //
    public function index()
    {
        $builders = BuilderModel::with('brand')->get();
        return view('admin.show_builder', ['builders' => $builders]);
    }
    public function create()
    {

        $brands = Brand::all();
        return view('admin.add_builder', array('brands' => $brands));
    }

    public function store(adminbulderRequest $request)
    {
        // Create Builder account using Eloquent ORM
        BuilderModel::create([
            'company_name' => $request->input('company_name'),
            'builder_email' => $request->input('builder_email'),
            'account_type' => $request->input('account_type') ?? 'account_type',
            'brand_id' => $request->input('brand_id'),
            'phone_number' => $request->input('phone_number'),
            'address' => $request->input('address'),
            'abn' => $request->input('abn'),
            'gate' => $request->input('gate'),
        ]);
        return redirect('admin/admin_builder')->with('success', 'Builder account created successfully.');
    }
    public function show()
    {
        dd('show');
    }

    public function edit($admin_builder)
    {
        $brands = Brand::all();
        $builders = BuilderModel::findOrFail($admin_builder);
        return view('admin.edit_builder', ['builders' => $builders, 'brands' => $brands]);
    }
    public function update(adminbulderRequest $request, $admin_builder)
    {
        BuilderModel::findOrFail($admin_builder)->update($request->all());

        return true;
    }

    public function destroy($admin_builder)
    {
        BuilderModel::findOrFail($admin_builder)->delete();
    }
}
