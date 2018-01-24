<?php
namespace App\Repositories\Debt;

use App\Models\Debt;
use Notifynder;
use Carbon;
use DB;

/**
 * Class DebtRepository
 * @package App\Repositories\Debt
 */
class DebtRepository implements DebtRepositoryContract
{
    /**
     *
     */
    const CREATED = 'created';
    /**
     *
     */
    const UPDATED_STATUS = 'updated_status';
    /**
     *
     */
    const UPDATED_DEADLINE = 'updated_deadline';
    /**
     *
     */
    const UPDATED_ASSIGN = 'updated_assign';
    /**
     *
     */
    const UPDATED_AGREEMENT = 'updated_agreement';

    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return Debt::findOrFail($id);
    }

    /**
     * @param $requestData
     * @return mixed
     */
    public function create($requestData)
    {
        $client_id = $requestData->get('client_id');
        $input = $requestData = array_merge(
            $requestData->all(),
            ['user_created_id' => \Auth::id()]
        );

        $debt = Debt::create($input);
        $insertedId = $debt->id;
        Session()->flash('flash_message', 'Debt successfully added!');

        event(new \App\Events\DebtAction($debt, self::CREATED));

        return $insertedId;
    }

      /**
       * @param $id
       * @param $requestData
       * @return mixed
       */
      public function update($id, $requestData)
      {
          $debt = Debt::findorFail($id);

          $debt->fill($requestData->all())->save();

          Session()->flash('flash_message', 'Debt successfully updated!');

          return $debt;
      }

    /**
     * @param $id
     * @param $requestData
     */
    public function updateStatus($id, $requestData)
    {
        $debt = Debt::findOrFail($id);

        $input = $requestData->get('status');
        $input = array_replace($requestData->all(), ['status' => 2]);
        $debt->fill($input)->save();
        event(new \App\Events\DebtAction($debt, self::UPDATED_STATUS));
    }

    /**
     * @param $id
     * @param $requestData
     */
    public function updateFollowup($id, $requestData)
    {
        $debt = Debt::findOrFail($id);
        $input = $requestData->all();
        $debt->fill($input)->save();
        event(new \App\Events\DebtAction($debt, self::UPDATED_DEADLINE));
    }

    /**
     * @param $id
     * @param $requestData
     */
    public function updateAssign($id, $requestData)
    {
        $debt = Debt::findOrFail($id);

        $input = $requestData->get('user_assigned_id');
        $input = array_replace($requestData->all());
        $debt->fill($input)->save();
        $insertedName = $debt->user->name;

        event(new \App\Events\DebtAction($debt, self::UPDATED_ASSIGN));
    }

    /**
     * @param $id
     * @param $requestData
     */
    public function updateAgreement($id, $requestData)
    {
        $debt = Debt::findOrFail($id);

        $input = $requestData->get('agreementId');
        $input = array_replace($requestData->all());
        $debt->fill($input)->save();
        //$insertedName = $debt->user->name;

        event(new \App\Events\DebtAction($debt, self::UPDATED_AGREEMENT));
    }
    /**
     * @return int
     */
    public function debts()
    {
        return Debt::all()->count();
    }

    /**
     * @return mixed
     */
    public function allCompletedDebts()
    {
        return Debt::where('status', 2)->count();
    }

    /**
     * @return float|int
     */
    public function percantageCompleted()
    {
        if (!$this->debts() || !$this->allCompletedDebts()) {
            $totalPercentageDebts = 0;
        } else {
            $totalPercentageDebts = $this->allCompletedDebts() / $this->debts() * 100;
        }

        return $totalPercentageDebts;
    }

    /**
     * @return mixed
     */
    public function completedDebtsToday()
    {
        return Debt::whereRaw(
            'date(updated_at) = ?',
            [Carbon::now()->format('Y-m-d')]
        )->where('status', 2)->count();
    }

    /**
     * @return mixed
     */
    public function createdDebtsToday()
    {
        return Debt::whereRaw(
            'date(created_at) = ?',
            [Carbon::now()->format('Y-m-d')]
        )->count();
    }

    /**
     * @return mixed
     */
    public function completedDebtsThisMonth()
    {
        return DB::table('debts')
            ->select(DB::raw('count(*) as total, updated_at'))
            ->where('status', 2)
            ->whereBetween('updated_at', [Carbon::now()->startOfMonth(), Carbon::now()])->get();
    }

    /**
     * @return mixed
     */
    public function createdDebtsMonthly()
    {
        return DB::table('debts')
            ->select(DB::raw('count(*) as month, updated_at'))
            ->where('status', 2)
            ->groupBy(DB::raw('YEAR(updated_at), MONTH(updated_at)'))
            ->get();
    }

    /**
     * @return mixed
     */
    public function completedDebtsMonthly()
    {
        return DB::table('debts')
            ->select(DB::raw('count(*) as month, created_at'))
            ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
            ->get();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function totalOpenAndClosedDebts($id)
    {
        $open_debts = Debt::where('status', 1)
        ->where('user_assigned_id', $id)
        ->count();

        $closed_debts = Debt::where('status', 2)
        ->where('user_assigned_id', $id)->count();

        return collect([$closed_debts, $open_debts]);
    }
}
