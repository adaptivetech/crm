<?php
namespace App\Repositories\Debt;

interface DebtRepositoryContract
{
    public function find($id);
    
    public function create($requestData);

    public function update($id, $requestData);

    public function updateStatus($id, $requestData);

    public function updateFollowup($id, $requestData);

    public function updateAssign($id, $requestData);

    public function updateAgreement($id, $requestData);

    public function debts();

    public function allCompletedDebts();

    public function percantageCompleted();

    public function completedDebtsToday();

    public function createdDebtsToday();

    public function completedDebtsThisMonth();

    public function createdDebtsMonthly();

    public function completedDebtsMonthly();

    public function totalOpenAndClosedDebts($id);
}
