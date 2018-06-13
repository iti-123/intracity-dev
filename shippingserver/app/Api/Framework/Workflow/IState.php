<?php

namespace Api\Framework\Workflow;

/**
 * Created by PhpStorm.
 * User: 10325
 * Date: 20-02-2017
 * Time: 13:51
 */
interface IState
{
    const
        INITIAL_STATE = 'initial',
        NORMAL_STATE = 'normal',
        FINAL_STATE = 'final';

    public function isInitial();

    public function isFinal();

    public function isNormal();

    public function getName();

    public function getType();

    public function getLabel();

}