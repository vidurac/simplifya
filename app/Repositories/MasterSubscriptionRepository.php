<?php
/**
 * Created by PhpStorm.
 * User: Harsha
 * Date: 6/3/2016
 * Time: 1:44 PM
 */

namespace App\Repositories;
use Bosnadev\Repositories\Eloquent\Repository;

class MasterSubscriptionRepository extends Repository
{
    /**
     * @return string
     */
    public function model()
    {
        return 'App\Models\MasterSubscription';
    }

    /**
     * Subscription fee by entity type
     * @param $entity_type
     * @param $valid_period
     * @return mixed
     */
    public function subscriptionFeeByEntityType($entity_type, $valid_period)
    {
        return $this->model
                    ->where('entity_type_id', $entity_type)
                    ->where('validity_period_id', 1)
                    ->where('status', 1)
                    ->get();
    }

    public function getMonthlySubscriptionFee()
    {
        return $this->model
                    ->where('validity_period_id', 1)
                    ->where('status', 1)
                    ->get();
    }

    public function getSubscriptionFee($id)
    {
        return $this->model
            ->where('id', $id)
            ->where('status', 1)
            ->first();
    }

    /**
     * Subscription fee by entity
     * @param $entity_type
     * @return mixed
     */
    public function getSubscriptionFeeByEntity($entity_type)
    {
        return $this->model->where('entity_type_id', $entity_type)->where('status', 1)->get();
    }

    /**
     * Return monthly subscription detail by entity type and validity period
     * @param $entity_type
     * @param $validity_period
     * @return mixed
     */
    public function getMonthlySubscriptionByEntity($entity_type, $validity_period) {
        return $this->model->where('entity_type_id', $entity_type)
            ->where('status', 1)
            ->where('validity_period_id', $validity_period)
            ->first();
    }
}