<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

use App\Contact;
class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('welcome');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
       $input = $request->all();
       $input['photo'] = null;

       if ($request->hasFile('photo')) {
           $input['photo'] = '/upload/photo'. str_slug($input['name'], '-').'.'.$request->photo->getClientOriginalExtension();
           $request->photo->move(public_path('/upload/photo/'), $input['photo']);
       }

       Contact::create($input);

       return response()->json([
            'success' => true
       ]);
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
        $contact = Contact::findOrFail($id);
        return $contact;
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
       $input = $request->all();
       $contact = Contact::findOrFail($id);

       $input['photo'] = $contact->photo;
       if ($request->hasFile('photo')) {
          if ($contact->photo != null) {
              unlink(public_path($contact->photo));
          }
          $input['photo'] = '/upload/photo'. str_slug($input['name'], '-').'.'.$request->getClientOriginalExtension();
          $request->photo->move(public_path('/upload/photo/'), $input['photo']);
       }
       $contact->update($input);

       return response()->json([
            'success' => true

       ]);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        if ($contact->photo != null) {
            unlink(public_path($contact->photo));
        }
        Contact::destroy($id);

        return response()->json([
            'success' => true
        ]);
    }

    public function apiContact()
    {
        $contact = Contact::all();

        return Datatables::of($contact)
        ->addColumn('show_photo', function($contact){
            if ($contact->photo == NULL) {
                return 'No Images';
            }
            return '<img class="rounded-square" width="60" height="60 src="'. url($contact->photo) .'" alt="">';
        })
        ->addColumn('action', function($contact){
            $form = '<a href="#" class="btn btn-info btn-xs"><i class="glyphicon glyphicon-eye-open"></i> Show</a>&nbsp';
            $form .= '<a onclick="editForm('. $contact->id .')" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-edit"></i> Edit </a>&nbsp;';
            $form .= '<a onclick="deleteData('. $contact->id .')" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i> Delete </a>';
            return $form;
        })->rawColumns(['show_photo','action'])->make(true);
    }
}
