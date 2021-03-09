<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\VendorRepository;
use Flash;
use App\DataTables\UnapprovedVendorDataTable;
use App\Models\User;
use App\Balance;
class UnapprovedVendorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */



    private $vendorRepository;

    /**
     * @var RoleRepository
     */
    private $roleRepository;

    public function __construct(VendorRepository $vendorRepo)
    {
    parent::__construct();
    $this->vendorRepository = $vendorRepo;
    }

    public function index(UnapprovedVendorDataTable $vendorDataTable)
    {
        if(!auth()->user()->hasPermissionTo('unapprovedServiceProvider.index')){
            return view('vendor.errors.page', ['code' => 403, 'message' => trans('lang.Right_Permission')]);
        }

        return $vendorDataTable->render('unapprovedVendor.index');
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return "sss";
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
            if(!auth()->user()->hasPermissionTo('unapprovedServiceProvider.destroy')){
                return view('vendor.errors.page', ['code' => 403, 'message' => trans('lang.Right_Permission')]);
            }

        $user = $this->vendorRepository->findWithoutFail($id);

        if (empty($user)) {
            Flash::success(trans('lang.user_not_found'));
            return redirect(route('unapprovedServiceProvider.index'));
        }

        if ($user->balance_id != null) {
            Balance::find($user->balance_id)->delete();
        }

        try{ unlink(public_path('storage/Avatar').'/'.$user->avatar);}
        catch (\Exception $e) {}

        $this->vendorRepository->delete($id);

        Flash::success(trans('lang.delete_operation'));

        return redirect(route('unapprovedServiceProvider.index'));
    }

    public function approved($id,Request $request)
    {

        if(!auth()->user()->hasPermissionTo('unapprovedServiceProvider.approve')){
            return view('vendor.errors.page', ['code' => 403, 'message' => trans('lang.Right_Permission')]);
        }

        try {
      
            $user = User::find($id);
            $user->approved_vendor = 1;
            $user->save();
  
            Flash::success(trans('lang.store_operation'));

            return redirect(route('unapprovedServiceProvider.index'));
        } catch (ValidatorException $e) {
        Flash::error($e->getMessage());
        return redirect(route('unapprovedServiceProvider.index'));
   }
 }

}
