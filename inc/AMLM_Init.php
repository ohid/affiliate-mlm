<?php

namespace AMLM;

final class AMLM_Init
{

    /**
     * Store all the classes inside an array
     *
     * @return array full list of classes
     */
    public static function get_services()
    {
        return array(
            Classes\Class_Main::class,
            Classes\Class_MyAccount_Tabs::class,
            Classes\Class_User_Rank::class,
            Classes\Class_Earning_Calculator::class,
            Classes\Class_Enqueue::class,
        );
    }

    
    /**
     * Loop through the classes, initialize them
     * and call the register() method if exists
     *
     * @return
     */
    public static function register_classes()
    {
        foreach( self::get_services() as $service )  {
            $service = self::instantiate($service);
            if( method_exists( $service, 'register' ) ) {
                $service->register();
            }
        }
    }


    /**
     * Initialize the classes
     *
     * @param class $class from the service array  
     * @return class instance new instance of the new class
     */
    public static function instantiate( $class )
    {
        return new $class;
    }
}