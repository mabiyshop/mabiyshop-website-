<?php

namespace App\Http\Controllers\Backend;

use App\Models\Upazila;
use App\Models\Union;
use App\Models\District;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Image;
use Auth;

class UpazilaController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }

    public function index()
    {
        if (is_null($this->user) || !$this->user->can('location.view')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }
        $data = Upazila::where('is_deleted', 0)->paginate(30);
        return view('backend.pages.location.upazila.list', compact('data'));
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('location.create')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $data = Upazila::where('is_deleted', 0)->get();

        return view('backend.pages.location.union.create', compact('data'));
    }


    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('location.create')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $request->validate([
            'title' => 'required|max:255',
            'district_id' => 'required'
            // 'is_active' => 'required',
            // 'slug'  => 'required | unique:blogs'
        ]);

        $location = new Upazila();
        $location->title = $request->title;
        $location->district_id = $request->district_id;


        $location->save();


        return redirect()->route('admin.location.upazila')->with('success', 'Upazila successfully created!');
    }

    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('location.edit')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $data = Upazila::find($id);
        return view('backend.pages.location.upazila.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('location.edit')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $request->validate([
            'title' => 'required|max:255'
        ]);

        $location = Upazila::find($id);
        $location->title = $request->title;
        $location->save();

        return redirect()->route('admin.location.upazila')->with('success', 'Upazila successfully updated!');
    }

    public function delete(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('location.delete')) {
            return redirect()->route('admin.index')->with('failed', 'You don\'t have enough privileges to perform this action!');
        }

        $location = Upazila::find($id);

        //Insert Trash Data
        $type = 'location';
        $type_id = $id;
        $reason = $request->reason ?? '';
        $data = $location;
        \Helper::setTrashInfo($type, $type_id, $reason, $data);

        $location->is_deleted = 1;
        $location->save();

        return redirect()->route('admin.location.upazila')->with('success', 'Upazila successfully deleted!');
    }

    public function getUpazila()
    {
        $data = Upazila::where('is_deleted', 0);
        return DataTables::of($data)->addIndexColumn()
            ->addColumn('district_title', function ($row) {
                $district = District::where('id', $row->district_id)
                    ->first();
                return $district->title ?? null;
            })
            ->addColumn('action', function ($row) {
                $btn = '';
                if($row->generated != 1){
                    $btn = '<a class="icon_btn text-success" title="Generate Area" href="' . route('admin.location.upazila.generate.single', $row->id) . '"><i class="mdi mdi-eye"></i></a>';
                }
                if (Auth::user()->can('location.edit')) {
                    $btn = $btn . '<a class="icon_btn text-info" href="' . route('admin.location.upazila.edit', $row->id) . '"><i class="mdi mdi-playlist-edit"></i></a>';
                }
                if (Auth::user()->can('location.delete')) {
                    $btn = $btn . '<a class="icon_btn text-danger delete_btn" data-url="' . route('admin.location.upazila.delete', $row->id) . '" data-toggle="modal" data-target="#deleteModal" href="#"><i class="mdi mdi-delete"></i></a>';
                }

                return $btn;
            })

            ->rawColumns(['district_title', 'action'])->make(true);
    }

    public function generate(){
        $districts = District::all();
        if($districts){
            foreach($districts as $district){
                $upazilas_pathao = \Helper::getPathaoZone($district->city_id);
                $upazilas = $upazilas_pathao->data;
                if($upazilas){
                    foreach($upazilas->data as $upazila){
                        if(!Upazila::where('title', $upazila->zone_name)->where('zone_id', $upazila->zone_id)->exists()){
                            $newupazila = new Upazila();
                            $newupazila->zone_id = $upazila->zone_id;
                            $newupazila->district_id = $district->id;
                            $newupazila->title = $upazila->zone_name;
                            $newupazila->save();
                        }
                    }
                    $districts->generated = 1;
                    $districts->save();
                }
            }
        }
        return redirect()->route('admin.location.upazila')->with('success', 'Upazila successfully generated!');
    }

    public function generateSingleArea($upazila_id){
        $upazilas = Upazila::find($upazila_id);
        if($upazilas){
            $areas_pathao = \Helper::getPathaoAreas($upazilas->zone_id);
            $areas = $areas_pathao->data;
            if($areas){
                foreach($areas->data as $area){
                    if(!Union::where('title', $area->area_name)->where('area_id', $area->area_id)->exists()){
                        $Union = new Union();
                        $Union->area_id = $area->area_id;
                        $Union->upazila_id = $upazilas->id;
                        $Union->title = $area->area_name;
                        $Union->save();
                    }
                }
                $upazilas->generated = 1;
                $upazilas->save();
            }
            
        }
       
        return redirect()->route('admin.location.upazila')->with('success', 'Areas successfully generated!');
    }
}