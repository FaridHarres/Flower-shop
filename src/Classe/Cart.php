<?php

namespace App\Classe;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{

    private $session;


    public function __construct(RequestStack $stack)
 
    {
        return $this->stack = $stack;
    }




    public function add($id)
    {

        $this->session->set('cart',[
            [
                'id'=> $id,
                'quantity'=> 1,
                
            ]

        
            
            ]);
            
    }


    public function get()
    {
        $methodget = $this->stack->getSession();
        return $this->session->get('cart');
    }

    public function remove()
    {
        $methodget = $this->stack->getSession();
        return $this->session->remove('cart');

        

    }
    
}