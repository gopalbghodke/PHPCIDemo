<?php

namespace Brunoocto\Sample\Facades;

use Illuminate\Support\Facades\Facade;

class SampleFacade extends Facade
{
    /**
    * Get the registered name of the component.
    *
    * @return string
    */
    protected static function getFacadeAccessor()
    {
        return 'sample_interface';
    }
}
