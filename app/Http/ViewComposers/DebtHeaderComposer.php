<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Repositories\Debt\DebtRepositoryContract;

class DebtHeaderComposer
{
    /**
     * The task repository implementation.
     *
     * @var taskRepository
     */
    protected $debt;

    /**
     * Create a new profile composer.
     * DebtHeaderComposer constructor.
     * @param DebtRepositoryContract $debt
     */
    public function __construct(DebtRepositoryContract $debt)
    {
        $this->debt = $debt;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $debt = $this->debt->find($view->getData()['debt']['id']);
        /**
         * [User assigned the debt]
         * @var contact
         */
       
        $contact = $debt->user;
        $client = $debt->client;
        
        $view->with('contact', $contact);
        $view->with('client', $client);
    }
}
