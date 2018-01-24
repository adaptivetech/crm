<?php
namespace App\Http\Controllers;

use DB;
use Auth;
use Carbon;
use Session;
use Datatables;
use App\Models\Debt;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Requests\Debt\StoreDebtRequest;
use App\Http\Requests\Debt\UpdateDebtRequest;
use App\Repositories\Debt\DebtRepositoryContract;
use App\Repositories\User\UserRepositoryContract;
use App\Repositories\Client\ClientRepositoryContract;
use App\Repositories\Setting\SettingRepositoryContract;
use KevinEm\AdobeSignLaravel\Facades\AdobeSignLaravel;

class DebtsController extends Controller
{
    protected $debts;
    protected $clients;
    protected $settings;
    protected $users;

    public function __construct(
        DebtRepositoryContract $debts,
        UserRepositoryContract $users,
        ClientRepositoryContract $clients,
        SettingRepositoryContract $settings
    )
    {
        $this->users = $users;
        $this->settings = $settings;
        $this->clients = $clients;
        $this->debts = $debts;
        $this->middleware('debt.create', ['only' => ['create']]);
        $this->middleware('debt.assigned', ['only' => ['updateAssign']]);
        $this->middleware('debt.update.status', ['only' => ['updateStatus']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('debts.index');
    }

    /**
     * Data for Data tables
     * @return mixed
     */
    public function anyData()
    {
        $debts = Debt::select(
            ['id', 'title', 'user_created_id', 'client_id', 'user_assigned_id', 'created_at']
        )->where('status', 1)->get();
        return Datatables::of($debts)
            ->addColumn('titlelink', function ($debts) {
                return '<a href="debts/' . $debts->id . '" ">' . $debts->title . '</a>';
            })
            ->editColumn('user_created_id', function ($debts) {
                return $debts->creator->name;
            })
            ->editColumn('user_assigned_id', function ($debts) {
                return $debts->user->name;
            })->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('debts.create')
            ->withUsers($this->users->getAllUsersWithDepartments())
            ->withClients($this->clients->listAllClients());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreDebtRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDebtRequest $request)
    {
        $getInsertedId = $this->debts->create($request);
        Session()->flash('flash_message', 'Debt is created');
        return redirect()->route('debts.show', $getInsertedId);
    }

    public function updateAssign($id, Request $request)
    {
        $this->debts->updateAssign($id, $request);
        Session()->flash('flash_message', 'New user is assigned');
        return redirect()->back();
    }

    public function updateAgreement($id, Request $request)
    {
        $this->debts->updateAgreement($id, $request);
        Session()->flash('flash_message', 'New agreement has been created. Please prefill on Adobe Sign');

        return redirect()->back();
    }

    /**
     * Update the follow up date (Deadline)
     * @param UpdateDebtFollowUpRequest $request
     * @param $id
     * @return mixed
     */
    public function updateFollowup(UpdateDebtFollowUpRequest $request, $id)
    {
        $this->debts->updateFollowup($id, $request);
        Session()->flash('flash_message', 'New follow up date is set');
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('debts.show')
            ->withDebt($this->debts->find($id))
            ->withUsers($this->users->getAllUsersWithDepartments())
            ->withCompanyname($this->settings->getCompanyName());
    }

    /**
     * Complete Debt
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function updateStatus($id, Request $request)
    {
        $this->debts->updateStatus($id, $request);
        Session()->flash('flash_message', 'Debt is completed');
        return redirect()->back();
    }


      /**
       * @param $id
       * @param UpdateDebtRequest $request
       * @return mixed
       */
      public function update($id, UpdateDebtRequest $request)
      {
          $this->debts->update($id, $request);
          Session()->flash('flash_message', 'Debt successfully updated');
          return redirect()->back()->withInput();
      }

    public function getAuthUrl() {
        return AdobeSignLaravel::getAuthorizationUrl();
    }

    public function getAgreements() {
        dd(AdobeSignLaravel::getAuthorizationUrl());
        dd(AdobeSignLaravel::getAgreements([
            'query' => 'CRM',
        ]));
    }

    public function sendagreement() {
        AdobeSignLaravel::createAgreement([
            'documentCreationInfo' => [
                'fileInfos' => [
                    'libraryDocumentId' => 'adobe_sign_contract_id'
                ],
                'name' => 'Default Contract',
                'signatureType' => 'ESIGN',
                'recipientSetInfos' => [
                    'recipientSetMemberInfos' => [
                        'email' => 'email@gmail.com'
                    ],
                    'recipientSetRole' => [
                        'SIGNER'
                    ]
                ],
                'mergeFieldInfo' => [
                    [
                        'fieldName' => 'AddressStreet1',
                        'defaultValue' => ''
                    ],
                    [
                        'fieldName' => 'AddressStreet2',
                        'defaultValue' => ''
                    ],
                    [
                        'fieldName' => 'AddressCity',
                        'defaultValue' => ''
                    ],
                    [
                        'fieldName' => 'AddressState',
                        'defaultValue' => ''
                    ],
                    [
                        'fieldName' => 'AddressPostal',
                        'defaultValue' => ''
                    ],
                ],
                'signatureFlow' => 'SENDER_SIGNATURE_NOT_REQUIRED'
            ]
        ]);

    }
}
