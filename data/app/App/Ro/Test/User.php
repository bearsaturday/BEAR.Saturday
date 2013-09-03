<?php

class App_Ro_Test_User extends App_Ro
{
    /**
     * @var array
     */
    protected static $userData = array(
        array('id' => 0, 'name' => 'World', 'age' => 1.37E10),
        array('id' => 1, 'name' => 'BEAR', 'age' => 8)
    );

    /**
     * Read
     *
     * @required id
     *
     * @return array
     */
    public function onRead($values)
    {
        $id = $values['id'];
        $this->assert(isset(self::$userData[$id]));

        return self::$userData[$id];
    }

}