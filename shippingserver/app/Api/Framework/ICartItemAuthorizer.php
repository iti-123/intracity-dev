<?php

namespace Api\Framework;

interface ICartItemAuthorizer
{
    function authorizeGet();

    function authorizeSave();

    function authorizeDelete();

}